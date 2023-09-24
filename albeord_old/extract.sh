#!/bin/bash

export LC_ALL=C

src_directory=$(pwd)  # Imposta la directory di origine come la directory corrente
dst_directory="../extraction"  # Imposta la directory di destinazione come la directory superiore
output_file="concatenated.php"  # Nome del file di output

# Crea la directory di destinazione se non esiste
mkdir -p "$dst_directory"

# Inizializza un file vuoto per la concatenazione
> "$dst_directory/$output_file"

# Trova tutti i file PHP e li concatena in un unico file con i placeholder
find "$src_directory" -name "*.php" | while read -r file; do
    filename=$(basename "$file")
    echo "##start $filename" >> "$dst_directory/$output_file"
    cat "$file" >> "$dst_directory/$output_file"
    echo "##end $filename" >> "$dst_directory/$output_file"
done

# Suddivide il file di output in pezzi di 4000 caratteri
# split -b 4000 "$dst_directory/$output_file" "${dst_directory}/${output_file%.*}_"
