for file in dhost.txt; do
    while IFS= read -r host; do
        ping -qc4 "$host" && echo "$host" >> result.txt
    done < "$file"
done
