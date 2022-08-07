for i in `cat hosts.txt`; do   ping -c1 -w2 $i >/dev/null 2>&1; echo $i $? >>20table.txt && echo $i; done
