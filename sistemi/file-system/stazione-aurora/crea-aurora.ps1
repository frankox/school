#requires -Version 5.1
param([string]$Percorso = 'aurora')
$ErrorActionPreference = 'Stop'

if (Test-Path -LiteralPath $Percorso) {
    throw "La destinazione esiste gia: $Percorso. Scegli una cartella nuova."
}
$null = New-Item -ItemType Directory -Path $Percorso
foreach ($cartella in @('equipaggio', 'missioni/luna', 'missioni/marte', 'rapporti di bordo', 'comunicazioni/archivio', 'manutenzione/temporanei', 'carico', 'analisi', 'backup')) {
    $null = New-Item -ItemType Directory -Path (Join-Path $Percorso $cartella) -Force
}

# Ogni elemento di Testo diventa una riga. ASCII permette la lettura anche da cmd.
function Scrivi($Nome, [string[]]$Testo) {
    Set-Content -LiteralPath (Join-Path $Percorso $Nome) -Value $Testo -Encoding ASCII
}
Scrivi '.stazione-aurora' @('AURORA-LAB-V1')
Scrivi 'istruzioni.txt' @('BENVENUTO SULLA STAZIONE AURORA', 'Esplora le cartelle, leggi i turni e confronta i rapporti delle missioni.', 'Prima di modificare un documento, conservane una copia in backup.', 'Nel settore analisi trovi i log. I file temporanei sono prove eliminabili.')
Scrivi 'diario.txt' @('Stazione Aurora', 'Stato: operativo')
Scrivi 'equipaggio/turni.txt' @('Ada - navigazione - 08:00-12:00', 'Linus - comunicazioni - 12:00-16:00', 'Grace - manutenzione - 16:00-20:00')
Scrivi 'missioni/luna/rapporto.txt' @('Missione: Luna', 'Stato: in attesa di partenza', 'Carico: strumenti di misura')
Scrivi 'missioni/marte/rapporto.txt' @('Missione: Marte', 'Stato: preparazione del carico', 'Carico: campioni e provviste')
Scrivi 'rapporti di bordo/bozza.txt' @('Rapporto di bordo', 'Stato: da revisionare', 'Operatore: da assegnare')
Scrivi 'comunicazioni/benvenuto.txt' @('Il registro dei turni indica chi segue le comunicazioni.', 'Un messaggio precedente si trova nella sottocartella archivio.')
Scrivi 'comunicazioni/archivio/messaggio-01.txt' @('Messaggio dalla squadra esterna.', 'Antenna senza risposta. Consultare i log prima di intervenire.')
Scrivi 'manutenzione/registro.txt' @('Controllo motore: completato', 'Controllo antenna: da verificare')
Scrivi 'manutenzione/temporanei/cache-01.tmp' @('Cache di prova: navigazione')
Scrivi 'manutenzione/temporanei/cache-02.tmp' @('Cache di prova: comunicazioni')
Scrivi 'carico/inventario.csv' @('Nome,Quantita', 'Strumenti,4', 'Provviste,12', 'Campioni,3')
Scrivi 'analisi/eventi.txt' @('INFO motore pronto', 'ERROR antenna offline', 'INFO rotta stabile', 'ERROR antenna offline', 'WARN ossigeno basso')
Scrivi 'analisi/destinazioni.txt' @('luna', 'marte', 'luna', 'venere', 'marte')
Scrivi 'analisi/rapporto-[1].txt' @('Questo nome contiene parentesi quadre reali.')
Scrivi 'analisi/telemetria.txt' @(1..80 | ForEach-Object { 'INFO campione {0} temperatura=21 pressione=101 stato=nominale' -f $_ })

Write-Host "Stazione Aurora creata: $Percorso"
