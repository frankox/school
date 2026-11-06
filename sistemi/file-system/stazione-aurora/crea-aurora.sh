#!/usr/bin/env bash
set -e

# Il percorso e facoltativo; senza argomenti crea aurora nella cartella corrente.
destinazione=${1:-aurora}
if [ -e "$destinazione" ] || [ -L "$destinazione" ]; then
    printf 'La destinazione esiste gia: %s. Scegli una cartella nuova.\n' "$destinazione" >&2
    exit 1
fi

mkdir -p -- "$destinazione"/{equipaggio,missioni/luna,missioni/marte,"rapporti di bordo",comunicazioni/archivio,manutenzione/temporanei,carico,analisi,backup}

# Ogni argomento dopo il nome del file diventa una riga di testo.
scrivi() { printf '%s\r\n' "${@:2}" > "$destinazione/$1"; }
scrivi .stazione-aurora 'AURORA-LAB-V1'
scrivi istruzioni.txt 'BENVENUTO SULLA STAZIONE AURORA' 'Esplora le cartelle, leggi i turni e confronta i rapporti delle missioni.' 'Prima di modificare un documento, conservane una copia in backup.' 'Nel settore analisi trovi i log. I file temporanei sono prove eliminabili.'
scrivi diario.txt 'Stazione Aurora' 'Stato: operativo'
scrivi equipaggio/turni.txt 'Ada - navigazione - 08:00-12:00' 'Linus - comunicazioni - 12:00-16:00' 'Grace - manutenzione - 16:00-20:00'
scrivi missioni/luna/rapporto.txt 'Missione: Luna' 'Stato: in attesa di partenza' 'Carico: strumenti di misura'
scrivi missioni/marte/rapporto.txt 'Missione: Marte' 'Stato: preparazione del carico' 'Carico: campioni e provviste'
scrivi 'rapporti di bordo/bozza.txt' 'Rapporto di bordo' 'Stato: da revisionare' 'Operatore: da assegnare'
scrivi comunicazioni/benvenuto.txt 'Il registro dei turni indica chi segue le comunicazioni.' 'Un messaggio precedente si trova nella sottocartella archivio.'
scrivi comunicazioni/archivio/messaggio-01.txt 'Messaggio dalla squadra esterna.' 'Antenna senza risposta. Consultare i log prima di intervenire.'
scrivi manutenzione/registro.txt 'Controllo motore: completato' 'Controllo antenna: da verificare'
scrivi manutenzione/temporanei/cache-01.tmp 'Cache di prova: navigazione'
scrivi manutenzione/temporanei/cache-02.tmp 'Cache di prova: comunicazioni'
scrivi carico/inventario.csv 'Nome,Quantita' 'Strumenti,4' 'Provviste,12' 'Campioni,3'
scrivi analisi/eventi.txt 'INFO motore pronto' 'ERROR antenna offline' 'INFO rotta stabile' 'ERROR antenna offline' 'WARN ossigeno basso'
scrivi analisi/destinazioni.txt 'luna' 'marte' 'luna' 'venere' 'marte'
scrivi 'analisi/rapporto-[1].txt' 'Questo nome contiene parentesi quadre reali.'
for numero in {1..80}; do
    printf 'INFO campione %d temperatura=21 pressione=101 stato=nominale\r\n' "$numero"
done > "$destinazione/analisi/telemetria.txt"

printf 'Stazione Aurora creata: %s\n' "$destinazione"
