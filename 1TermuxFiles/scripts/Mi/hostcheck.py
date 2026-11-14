#!/usr/bin/env python3
# hostcheck: fast online/offline filter for huge host lists
# Author: by_[MiSi], developed with the help of Claude (Anthropic AI)
#
# Instead of asking hosts one by one, thousands of hosts are checked at the same time:
#   1. DNS: one UDP query per host straight to fast public resolvers (no ping, no threads).
#      Dead domains answer NXDOMAIN within milliseconds and are finished right there.
#   2. TCP: only the hosts that resolve get a connection test on port 443, then 80.
# Results go into a small SQLite file, so a re-run only checks new/old/inconclusive hosts,
# and you can filter the results any time without checking anything again.
#
# Usage:
#   python hostcheck.py hosts.txt                  check + write online.txt / offline.txt / timeout.txt
#   python hostcheck.py hosts.txt --dns-only       fastest: "online" = the name resolves
#   python hostcheck.py --stats                    counts from the database
#   python hostcheck.py --list online --grep cdn   colored listing from the database
#   python hostcheck.py --list offline --limit 50
# Input: one host per line; plain names, hosts files (0.0.0.0 name), ||name^ lists and URLs all work.
# Standard library only.

import argparse
import asyncio
import ipaddress
import os
import random
import re
import signal
import socket
import sqlite3
import struct
import sys
import time
from collections import Counter

DEFAULT_DNS = "1.1.1.1,8.8.8.8,9.9.9.10"   # plain, non-filtering resolvers (so blocked names are not faked offline)
IP_RE = re.compile(r"^(\d{1,3}\.){3}\d{1,3}$")
HOST_RE = re.compile(r"^(?=.{1,253}$)[a-z0-9_](?:[a-z0-9_-]{0,61}[a-z0-9_])?"
                     r"(?:\.[a-z0-9_](?:[a-z0-9_-]{0,61}[a-z0-9_])?)+$")
SKIP_NAMES = {"localhost", "localhost.localdomain", "local", "broadcasthost", "ip6-localhost",
              "ip6-loopback", "ip6-localnet", "ip6-mcastprefix", "ip6-allnodes", "ip6-allrouters"}

USE_COLOR = sys.stdout.isatty()
COLORS = {"online": "\033[94m", "offline": "\033[91m", "timeout": "\033[93m"}


def paint(status, text):
    return f"{COLORS.get(status, '')}{text}\033[0m" if USE_COLOR and status in COLORS else text


# ---------------------------------------------------------------------------
# input parsing
# ---------------------------------------------------------------------------
def normalize(line):
    line = line.strip()
    if not line or line[0] in "#!;[":
        return None
    line = line.split("#", 1)[0].strip()
    if not line:
        return None
    parts = line.split()
    tok = parts[1] if len(parts) > 1 and (IP_RE.match(parts[0]) or ":" in parts[0]) else parts[0]
    if "://" in tok:
        tok = tok.split("://", 1)[1]
    tok = re.sub(r"^[|*.]+", "", tok)
    tok = re.split(r"[/^$:?#]", tok, maxsplit=1)[0].strip(".").lower()
    if not tok or IP_RE.match(tok) or tok in SKIP_NAMES:
        return None
    if not tok.isascii():
        try:
            tok = tok.encode("idna").decode("ascii")
        except UnicodeError:
            return None
    return tok if HOST_RE.match(tok) else None


def ingest(db, paths):
    """Read the input files line by line and add new hosts to the database."""
    before = db.total_changes
    seen_lines = 0
    for path in paths:
        batch = []
        with open(path, encoding="utf-8", errors="replace") as f:
            for line in f:
                seen_lines += 1
                host = normalize(line)
                if host:
                    batch.append((host,))
                if len(batch) >= 20000:
                    db.executemany("INSERT OR IGNORE INTO hosts(host) VALUES (?)", batch)
                    batch = []
        if batch:
            db.executemany("INSERT OR IGNORE INTO hosts(host) VALUES (?)", batch)
    db.commit()
    return seen_lines, db.total_changes - before


# ---------------------------------------------------------------------------
# minimal asynchronous DNS client (UDP, A and AAAA)
# ---------------------------------------------------------------------------
def build_query(qid, name, qtype):
    out = struct.pack("!HHHHHH", qid, 0x0100, 1, 0, 0, 0)
    for label in name.split("."):
        raw = label.encode("ascii")
        out += bytes([len(raw)]) + raw
    return out + b"\x00" + struct.pack("!HH", qtype, 1)


def skip_name(data, pos):
    while True:
        length = data[pos]
        if length == 0:
            return pos + 1
        if length & 0xC0 == 0xC0:
            return pos + 2
        pos += 1 + length


def parse_response(data):
    """-> (rcode, [ip, ...]) or None if the packet is garbage."""
    try:
        _qid, flags, qdcount, ancount = struct.unpack("!HHHH", data[:8])
        pos = 12
        for _ in range(qdcount):
            pos = skip_name(data, pos) + 4
        ips = []
        for _ in range(ancount):
            pos = skip_name(data, pos)
            rtype, _cls, _ttl, rdlen = struct.unpack("!HHIH", data[pos:pos + 10])
            pos += 10
            rdata = data[pos:pos + rdlen]
            pos += rdlen
            if rtype == 1 and rdlen == 4:
                ips.append(socket.inet_ntoa(rdata))
            elif rtype == 28 and rdlen == 16:
                ips.append(socket.inet_ntop(socket.AF_INET6, rdata))
        return flags & 0xF, ips
    except (IndexError, struct.error):
        return None


class Resolver(asyncio.DatagramProtocol):
    def __init__(self, servers, timeout, retries):
        self.servers = servers          # [(ip, port)]
        self.timeout = timeout
        self.retries = retries
        self.pending = {}               # query id -> (future, server ip)
        self.transport = None
        self.turn = 0

    def connection_made(self, transport):
        self.transport = transport

    def datagram_received(self, data, addr):
        if len(data) < 2:
            return
        entry = self.pending.get((data[0] << 8) | data[1])
        if entry and entry[1] == addr[0] and not entry[0].done():
            entry[0].set_result(data)

    def error_received(self, exc):
        pass

    async def query(self, name, qtype):
        loop = asyncio.get_running_loop()
        for _ in range(self.retries + 1):
            self.turn += 1
            ip, port = self.servers[self.turn % len(self.servers)]
            qid = random.randrange(65536)
            while qid in self.pending:
                qid = random.randrange(65536)
            fut = loop.create_future()
            self.pending[qid] = (fut, ip)
            try:
                self.transport.sendto(build_query(qid, name, qtype), (ip, port))
                data = await asyncio.wait_for(fut, self.timeout)
            except (asyncio.TimeoutError, OSError):
                continue
            finally:
                self.pending.pop(qid, None)
            parsed = parse_response(data)
            if parsed:
                return parsed
        return None


async def resolve(resolver, host):
    """-> (state, ips); state: ok | nxdomain | servfail | noaddr | timeout"""
    answer = await resolver.query(host, 1)
    if answer is None:
        return "timeout", []
    rcode, ips = answer
    if rcode == 3:
        return "nxdomain", []
    if rcode != 0:
        return "servfail", []
    if not ips:
        answer6 = await resolver.query(host, 28)
        if answer6 is None:
            return "timeout", []
        ips = answer6[1]
        if not ips:
            return "noaddr", []
    return "ok", ips


# ---------------------------------------------------------------------------
# checking one host
# ---------------------------------------------------------------------------
async def tcp_probe(ips, ports, timeout):
    """-> (ok, port, ms, reason) trying the first two addresses."""
    reason = "tcp-timeout"
    for ip in ips[:2]:
        for port in ports:
            start = time.monotonic()
            try:
                _r, writer = await asyncio.wait_for(asyncio.open_connection(ip, port), timeout)
                ms = int((time.monotonic() - start) * 1000)
                writer.transport.abort()
                return True, port, ms, f"tcp/{port}"
            except asyncio.TimeoutError:
                reason = "tcp-timeout"
                break                       # address does not answer at all: skip its other ports
            except ConnectionRefusedError:
                reason = "refused"
            except OSError:
                reason = "unreachable"
                break
    return False, None, None, reason


async def check_host(host, resolver, args):
    """-> (host, status, reason, ip, ms)"""
    state, ips = await resolve(resolver, host)
    if state == "timeout":
        return host, "timeout", "dns-timeout", "", None
    if state != "ok":
        return host, "offline", state, "", None

    ips = sorted(ips, key=lambda x: ":" in x)           # IPv4 first
    if not args.allow_private:
        public = [x for x in ips if ipaddress.ip_address(x).is_global]
        if not public:
            return host, "offline", "sinkhole", ips[0], None
        ips = public
    if args.dns_only:
        return host, "online", "dns", ips[0], None

    ok, _port, ms, reason = await tcp_probe(ips, args.ports, args.tcp_timeout)
    return host, ("online" if ok else "offline"), reason, ips[0], ms


# ---------------------------------------------------------------------------
# main loop
# ---------------------------------------------------------------------------
def flush(db, rows):
    if not rows:
        return
    now = int(time.time())
    db.executemany("UPDATE hosts SET status=?, reason=?, ip=?, ms=?, checked=? WHERE host=?",
                   [(st, why, ip, ms, now, host) for host, st, why, ip, ms in rows])
    db.commit()


def fmt_time(seconds):
    seconds = int(seconds)
    if seconds < 90:
        return f"{seconds}s"
    if seconds < 5400:
        return f"{seconds // 60}m{seconds % 60:02d}s"
    return f"{seconds // 3600}h{(seconds % 3600) // 60:02d}m"


def raise_fd_limit(want):
    """Every in-flight connection needs a file descriptor. Returns how many we can use."""
    try:
        import resource
        soft, hard = resource.getrlimit(resource.RLIMIT_NOFILE)
        target = want if hard == resource.RLIM_INFINITY else min(want, hard)
        if soft < target:
            resource.setrlimit(resource.RLIMIT_NOFILE, (target, hard))
            soft = target
        return soft
    except (ImportError, ValueError, OSError):
        return 1024


async def run_checks(db, args):
    cutoff = int(time.time()) - int(args.max_age * 3600)
    if args.force:
        cutoff = int(time.time()) + 10
    where = "(checked IS NULL OR checked < ? OR status = 'timeout')"
    total = db.execute(f"SELECT COUNT(*) FROM hosts WHERE {where}", (cutoff,)).fetchone()[0]
    if not total:
        print("Nothing to check: every host has a fresh result (use --force to check again).")
        return
    fd_limit = raise_fd_limit(args.concurrency + 200)
    workers_n = max(10, min(args.concurrency, fd_limit - 100, total))
    print(f"Checking {total} host(s), {workers_n} at a time "
          f"({'DNS only' if args.dns_only else 'DNS + TCP ' + ','.join(map(str, args.ports))}) ...")

    loop = asyncio.get_running_loop()
    resolver = Resolver(args.dns_servers, args.dns_timeout, args.retries)
    await loop.create_datagram_endpoint(lambda: resolver, local_addr=("0.0.0.0", 0))

    stop = asyncio.Event()
    try:
        loop.add_signal_handler(signal.SIGINT, stop.set)
    except (NotImplementedError, RuntimeError):
        pass

    queue = asyncio.Queue(maxsize=workers_n * 2)
    results, counts = [], Counter()
    started = time.monotonic()

    async def producer():
        last = 0
        while True:
            rows = db.execute(f"SELECT rowid, host FROM hosts WHERE rowid > ? AND {where} "
                              f"ORDER BY rowid LIMIT 5000", (last, cutoff)).fetchall()
            if not rows:
                break
            for _rowid, host in rows:
                await queue.put(host)
            last = rows[-1][0]
        for _ in range(workers_n):
            await queue.put(None)

    async def worker():
        while True:
            host = await queue.get()
            if host is None:
                return
            try:
                res = await check_host(host, resolver, args)
            except Exception:
                res = (host, "timeout", "error", "", None)
            results.append(res)
            counts[res[1]] += 1
            if args.verbose:
                print(paint(res[1], f"{res[1].upper():8} {res[0]}  {res[2]}"))

    async def reporter():
        nonlocal results
        last_print = 0.0
        while True:
            await asyncio.sleep(1.0)
            batch, results = results, []
            flush(db, batch)
            done = sum(counts.values())
            if time.monotonic() - last_print >= (0.5 if USE_COLOR else 10):
                last_print = time.monotonic()
                rate = done / max(0.001, time.monotonic() - started)
                eta = (total - done) / rate if rate else 0
                line = (f"{done}/{total} ({100 * done // total}%)  "
                        f"{paint('online', 'online ' + str(counts['online']))}  "
                        f"{paint('offline', 'offline ' + str(counts['offline']))}  "
                        f"{paint('timeout', 'timeout ' + str(counts['timeout']))}  "
                        f"{rate:.0f}/s  ETA {fmt_time(eta)}")
                print("\r" + line + "   " if USE_COLOR else line,
                      end="" if USE_COLOR else "\n", flush=True)

    prod = asyncio.create_task(producer())
    workers = [asyncio.create_task(worker()) for _ in range(workers_n)]
    rep = asyncio.create_task(reporter())
    all_done = asyncio.gather(*workers)
    stopper = asyncio.create_task(stop.wait())
    await asyncio.wait([all_done, stopper], return_when=asyncio.FIRST_COMPLETED)

    interrupted = stop.is_set()
    if interrupted:
        prod.cancel()
        for w in workers:
            w.cancel()
    stopper.cancel()
    rep.cancel()
    await asyncio.gather(prod, rep, stopper, *workers, return_exceptions=True)
    flush(db, results)
    if USE_COLOR:
        print()
    elapsed = time.monotonic() - started
    done = sum(counts.values())
    print(("Interrupted, progress saved. " if interrupted else "Finished. ") +
          f"{done} host(s) in {fmt_time(elapsed)} ({done / max(elapsed, 0.001):.0f}/s).")
    resolver.transport.close()


# ---------------------------------------------------------------------------
# output
# ---------------------------------------------------------------------------
def export(db, outdir, as_hosts):
    os.makedirs(outdir, exist_ok=True)
    for status in ("online", "offline", "timeout"):
        path = os.path.join(outdir, f"{status}.txt")
        cur = db.execute("SELECT host FROM hosts WHERE status = ? ORDER BY host", (status,))
        n = 0
        with open(path, "w", encoding="utf-8") as f:
            for (host,) in cur:
                f.write(("0.0.0.0 " + host if as_hosts else host) + "\n")
                n += 1
        if n or status != "timeout":
            print(f"  {paint(status, status):>8}: {n:>8}  ->  {path}")


def show_stats(db):
    rows = dict(db.execute("SELECT COALESCE(status, 'unchecked'), COUNT(*) FROM hosts GROUP BY 1").fetchall())
    total = sum(rows.values())
    print(f"{total} host(s) in the database")
    for status in ("online", "offline", "timeout", "unchecked"):
        if status in rows:
            print(f"  {paint(status, status):>9}: {rows[status]}")
    reasons = db.execute("SELECT status, reason, COUNT(*) FROM hosts WHERE status IS NOT NULL "
                         "GROUP BY 1, 2 ORDER BY 3 DESC LIMIT 12").fetchall()
    if reasons:
        print("Reasons:")
        for status, reason, n in reasons:
            print(f"  {paint(status, status):>9}  {reason or '-':<12} {n}")
    oldest = db.execute("SELECT MIN(checked) FROM hosts WHERE checked IS NOT NULL").fetchone()[0]
    if oldest:
        print(f"Oldest result: {fmt_time(time.time() - oldest)} ago")


def list_hosts(db, status, grep, limit):
    sql, params = "SELECT host, status, reason, ip, ms FROM hosts WHERE status = ?", [status]
    if grep:
        sql += " AND host LIKE ?"
        params.append(f"%{grep}%")
    sql += " ORDER BY host"
    if limit:
        sql += f" LIMIT {int(limit)}"
    n = 0
    for host, st, reason, ip, ms in db.execute(sql, params):
        extra = " ".join(x for x in (reason, ip or "", f"{ms}ms" if ms is not None else "") if x)
        print(paint(st, f"{st.upper():8} {host}") + f"  {extra}")
        n += 1
    print(f"-- {n} host(s)")


def parse_servers(text):
    servers = []
    for item in text.split(","):
        item = item.strip()
        if not item:
            continue
        ip, _, port = item.partition(":")
        servers.append((ip, int(port) if port else 53))
    return servers


def main():
    ap = argparse.ArgumentParser(description="Fast online/offline filter for huge host lists.")
    ap.add_argument("inputs", nargs="*", help="host list file(s); leave empty to only list/export from the database")
    ap.add_argument("--db", default="hostcheck.db", help="database file (default: hostcheck.db)")
    ap.add_argument("-o", "--outdir", default=".", help="folder for online.txt / offline.txt / timeout.txt")
    ap.add_argument("--as-hosts", action="store_true", help="write the txt files in hosts format (0.0.0.0 name)")
    ap.add_argument("--dns-only", action="store_true", help="only DNS: online = the name resolves (fastest)")
    ap.add_argument("-c", "--concurrency", type=int, default=300, help="hosts checked at the same time (default 300)")
    ap.add_argument("--dns", default=DEFAULT_DNS, help=f"DNS servers, comma separated (default {DEFAULT_DNS})")
    ap.add_argument("--dns-timeout", type=float, default=1.5, help="seconds per DNS try (default 1.5)")
    ap.add_argument("--retries", type=int, default=1, help="extra DNS tries on another server (default 1)")
    ap.add_argument("--tcp-timeout", type=float, default=1.5, help="seconds per TCP connect (default 1.5)")
    ap.add_argument("--ports", default="443,80", help="TCP ports to try (default 443,80)")
    ap.add_argument("--max-age", type=float, default=24, help="re-check results older than this many hours (default 24)")
    ap.add_argument("--force", action="store_true", help="check every host again")
    ap.add_argument("--allow-private", action="store_true", help="accept private/loopback answers instead of calling them sinkholes")
    ap.add_argument("--verbose", action="store_true", help="print every result while checking")
    ap.add_argument("--no-color", action="store_true")
    ap.add_argument("--stats", action="store_true", help="show database statistics")
    ap.add_argument("--list", choices=["online", "offline", "timeout"], help="list hosts from the database")
    ap.add_argument("--grep", help="with --list: only hosts containing this text")
    ap.add_argument("--limit", type=int, help="with --list: maximum number of lines")
    args = ap.parse_args()

    global USE_COLOR
    if args.no_color:
        USE_COLOR = False
    args.dns_servers = parse_servers(args.dns)
    args.ports = [int(p) for p in args.ports.split(",") if p.strip()]

    db = sqlite3.connect(args.db)
    db.execute("PRAGMA journal_mode=WAL")
    db.execute("PRAGMA synchronous=NORMAL")
    db.execute("CREATE TABLE IF NOT EXISTS hosts (host TEXT PRIMARY KEY, status TEXT, reason TEXT, "
               "ip TEXT, ms INTEGER, checked INTEGER)")
    db.execute("CREATE INDEX IF NOT EXISTS idx_status ON hosts(status)")

    if args.list:
        list_hosts(db, args.list, args.grep, args.limit)
        return
    if args.stats and not args.inputs:
        show_stats(db)
        return

    if args.inputs:
        for path in args.inputs:
            if not os.path.isfile(path):
                print(f"File not found: {path}")
                sys.exit(1)
        lines, new = ingest(db, args.inputs)
        print(f"Read {lines} line(s), {new} new host(s) added to {args.db}.")
        asyncio.run(run_checks(db, args))

    print("Result files:")
    export(db, args.outdir, args.as_hosts)
    if args.stats:
        show_stats(db)


if __name__ == "__main__":
    main()
