@echo off
setlocal
set "destinazione=%~1"
if not defined destinazione set "destinazione=aurora"
if exist "%destinazione%" (
    echo La destinazione esiste gia: "%destinazione%". Scegli una cartella nuova.
    exit /b 1
)

mkdir "%destinazione%" || exit /b 1
for %%D in (equipaggio missioni\luna missioni\marte "rapporti di bordo" comunicazioni\archivio manutenzione\temporanei carico analisi backup) do mkdir "%destinazione%\%%~D"

>"%destinazione%\.stazione-aurora" echo AURORA-LAB-V1
>"%destinazione%\istruzioni.txt" (
echo BENVENUTO SULLA STAZIONE AURORA
echo Esplora le cartelle, leggi i turni e confronta i rapporti delle missioni.
echo Prima di modificare un documento, conservane una copia in backup.
echo Nel settore analisi trovi i log. I file temporanei sono prove eliminabili.
)
>"%destinazione%\diario.txt" (
echo Stazione Aurora
echo Stato: operativo
)
>"%destinazione%\equipaggio\turni.txt" (
echo Ada - navigazione - 08:00-12:00
echo Linus - comunicazioni - 12:00-16:00
echo Grace - manutenzione - 16:00-20:00
)
>"%destinazione%\missioni\luna\rapporto.txt" (
echo Missione: Luna
echo Stato: in attesa di partenza
echo Carico: strumenti di misura
)
>"%destinazione%\missioni\marte\rapporto.txt" (
echo Missione: Marte
echo Stato: preparazione del carico
echo Carico: campioni e provviste
)
>"%destinazione%\rapporti di bordo\bozza.txt" (
echo Rapporto di bordo
echo Stato: da revisionare
echo Operatore: da assegnare
)
>"%destinazione%\comunicazioni\benvenuto.txt" (
echo Il registro dei turni indica chi segue le comunicazioni.
echo Un messaggio precedente si trova nella sottocartella archivio.
)
>"%destinazione%\comunicazioni\archivio\messaggio-01.txt" (
echo Messaggio dalla squadra esterna.
echo Antenna senza risposta. Consultare i log prima di intervenire.
)
>"%destinazione%\manutenzione\registro.txt" (
echo Controllo motore: completato
echo Controllo antenna: da verificare
)
>"%destinazione%\manutenzione\temporanei\cache-01.tmp" echo Cache di prova: navigazione
>"%destinazione%\manutenzione\temporanei\cache-02.tmp" echo Cache di prova: comunicazioni
>"%destinazione%\carico\inventario.csv" (
echo Nome,Quantita
echo Strumenti,4
echo Provviste,12
echo Campioni,3
)
>"%destinazione%\analisi\eventi.txt" (
echo INFO motore pronto
echo ERROR antenna offline
echo INFO rotta stabile
echo ERROR antenna offline
echo WARN ossigeno basso
)
>"%destinazione%\analisi\destinazioni.txt" (
echo luna
echo marte
echo luna
echo venere
echo marte
)
>"%destinazione%\analisi\rapporto-[1].txt" echo Questo nome contiene parentesi quadre reali.
>"%destinazione%\analisi\telemetria.txt" (
for /l %%N in (1,1,80) do echo INFO campione %%N temperatura=21 pressione=101 stato=nominale
)

echo Stazione Aurora creata: "%destinazione%"
