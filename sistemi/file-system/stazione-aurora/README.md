# Stazione Aurora

Gli script creano la cartella `aurora` con 16 documenti didattici e un file marcatore. Scegli **una sola** delle tre alternative: il risultato è lo stesso. Non servono Docker, npm o pacchetti aggiuntivi; serve soltanto la shell scelta.

## Che cosa dovrai vedere

```text
aurora/
├── .stazione-aurora
├── istruzioni.txt
├── diario.txt
├── equipaggio/turni.txt
├── missioni/
│   ├── luna/rapporto.txt
│   └── marte/rapporto.txt
├── rapporti di bordo/bozza.txt
├── comunicazioni/
│   ├── benvenuto.txt
│   └── archivio/messaggio-01.txt
├── manutenzione/
│   ├── registro.txt
│   └── temporanei/
│       ├── cache-01.tmp
│       └── cache-02.tmp
├── carico/inventario.csv
├── analisi/
│   ├── eventi.txt
│   ├── destinazioni.txt
│   ├── rapporto-[1].txt
│   └── telemetria.txt
└── backup/
```

`backup` è inizialmente vuota. I due rapporti di missione hanno contenuti diversi. Il log `analisi/eventi.txt` contiene cinque righe, con `ERROR` alle righe 2 e 4; `telemetria.txt` è più grande di 1 KB. I testi usano caratteri ASCII, leggibili dalle tre shell.

Gli script non sovrascrivono una cartella `aurora` già esistente. Gli script di pulizia controllano il marcatore `.stazione-aurora` e chiedono di scrivere **ELIMINA**: un'altra risposta annulla l'operazione. La pulizia elimina tutta `aurora`, comprese le modifiche e i file aggiunti, senza passare normalmente dal Cestino. Non modificare o copiare il marcatore in altre cartelle.

## Alternativa 1: cmd.exe su Windows

**Partenza:** apri il Prompt dei comandi nella radice del repository, la cartella **`school`** che contiene `README.md`, `info` e `sistemi`. Da lì esegui:

```text
cd /d "sistemi\file-system\stazione-aurora"
crea-aurora.cmd
dir "aurora"
```

Ora ti trovi in `school\sistemi\file-system\stazione-aurora`; la stazione è nella sua sottocartella `aurora`. Puoi entrarci con `cd aurora` e leggerne i documenti con `type`.

**Pulizia:** apri un nuovo Prompt dei comandi nella radice **`school`** ed esegui:

```text
cd /d "sistemi\file-system\stazione-aurora"
elimina-aurora.cmd
```

Scrivi `ELIMINA` quando richiesto. Alla fine la sottocartella `aurora` non deve più esserci; i sei script restano disponibili per ricrearla.

## Alternativa 2: PowerShell su Windows

**Partenza:** apri PowerShell nella radice del repository **`school`**. Da lì esegui:

```powershell
Set-Location -LiteralPath "sistemi\file-system\stazione-aurora"
powershell.exe -NoProfile -ExecutionPolicy Bypass -File .\crea-aurora.ps1
Get-ChildItem -LiteralPath .\aurora -Force
```

L'opzione `-ExecutionPolicy Bypass` riguarda solo il processo avviato, non cambia le impostazioni permanenti e non prevale su eventuali criteri imposti dalla scuola. Non richiede l'avvio come amministratore.

La stazione è in `school\sistemi\file-system\stazione-aurora\aurora`. Puoi entrarci con `Set-Location aurora` e leggerne i documenti con `Get-Content`.

**Pulizia:** apri un nuovo PowerShell nella radice **`school`** ed esegui:

```powershell
Set-Location -LiteralPath "sistemi\file-system\stazione-aurora"
powershell.exe -NoProfile -ExecutionPolicy Bypass -File .\elimina-aurora.ps1
```

Scrivi `ELIMINA` quando richiesto. Deve sparire soltanto la sottocartella `aurora`, lasciando disponibili gli script.

## Alternativa 3: Bash

Usa Bash su Linux/macOS oppure Git Bash su Windows.

**Partenza:** apri Bash nella radice del repository **`school`**. Da lì esegui:

```bash
cd "sistemi/file-system/stazione-aurora"
bash crea-aurora.sh
ls -a aurora
```

La stazione è in `school/sistemi/file-system/stazione-aurora/aurora`. Puoi entrarci con `cd aurora` e leggerne i documenti con `cat`. Non serve usare `chmod`, perché gli script vengono avviati esplicitamente con `bash`.

**Pulizia:** apri un nuovo Bash nella radice **`school`** ed esegui:

```bash
cd "sistemi/file-system/stazione-aurora"
bash elimina-aurora.sh
```

Scrivi `ELIMINA` quando richiesto. Deve sparire soltanto la sottocartella `aurora`, lasciando disponibili gli script.

Se hai scaricato soltanto i sei script anziché l'intero repository, mettili tutti nella stessa cartella e apri lì il terminale: **salta soltanto il primo comando `cd` o `Set-Location` di ciascun blocco**. Gli altri comandi rimangono identici e la stazione nasce in quella cartella.
