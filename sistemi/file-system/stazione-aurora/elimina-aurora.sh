#!/usr/bin/env bash
set -e

destinazione=${1:-aurora}
# Non accetta collegamenti simbolici o cartelle prive del marcatore del laboratorio.
if [ -L "$destinazione" ] || [ ! -f "$destinazione/.stazione-aurora" ] ||
   [ -L "$destinazione/.stazione-aurora" ] ||
   [ "$(tr -d '\r\n' < "$destinazione/.stazione-aurora")" != 'AURORA-LAB-V1' ]; then
    printf 'Non riconosco una stazione Aurora in: %s\n' "$destinazione" >&2
    exit 1
fi

printf 'Eliminare "%s" e TUTTO il suo contenuto? Scrivi ELIMINA: ' "$destinazione"
read -r conferma
if [ "$conferma" != 'ELIMINA' ]; then
    printf 'Annullato.\n'
    exit 0
fi
rm -rf -- "$destinazione"
printf 'Stazione eliminata.\n'
