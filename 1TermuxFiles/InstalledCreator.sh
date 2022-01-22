pkg list-installed >pkg.txt && sed -i 's/\/.*//' pkg.txt && sed -i 's/^/apt satisfy /' pkg.txt && gawk -i inplace 'NR>1' pkg.txt && cat pkg.txt
