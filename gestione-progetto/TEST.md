# Eseguire i test di Gestione progetto

## Con Docker

Segui direttamente la [guida Docker, sezione «Eseguire i test»](../DOCKER.md#eseguire-i-test). Apri il terminale nella cartella dell'esercizio che contiene `compose.yaml`. La guida spiega il comando comune a Gestione progetto e agli esercizi di Informatica; qui non viene duplicato.

Per completamento e analisi statica di TypeScript in VS Code, segui anche la [guida Docker, sezione «Usare VS Code nel container»](../DOCKER.md#usare-vs-code-nel-container).

## Alternativa su Windows senza Docker

Questi passaggi servono se vuoi eseguire localmente i test degli esercizi TypeScript di Gestione progetto. In questo caso devi avere **Node.js 20 o successivo**; npm viene installato insieme a Node.js.

### 1. Apri PowerShell nella cartella dell'esercizio

In Esplora file entra nella cartella dell'esercizio, scrivi `powershell` nella barra dell'indirizzo e premi Invio. La cartella giusta contiene `package.json` e `package-lock.json`. Verificalo con:

```powershell
Test-Path .\package.json
Test-Path .\package-lock.json
```

Entrambi i comandi devono mostrare `True`. Se appare `False`, spostati nella cartella dell'esercizio prima di continuare.

### 2. Verifica Node.js e npm

```powershell
node --version
npm.cmd --version
```

La versione di Node.js deve essere almeno 20. Se un comando non viene riconosciuto, installa Node.js dal [sito ufficiale](https://nodejs.org/en/download), chiudi e riapri PowerShell, poi ripeti i due controlli. `npm.cmd` usa l'eseguibile Windows di npm e funziona anche se PowerShell blocca gli script `.ps1`.

### 3. Installa le dipendenze ed esegui i test

Sempre nella stessa cartella:

```powershell
npm.cmd ci
npm.cmd test
```

`npm.cmd ci` installa le versioni indicate nel file `package-lock.json`, comprese TypeScript e le librerie necessarie ai test. Richiede Internet la prima volta. `npm.cmd test` compila il codice e avvia la suite; nell'output trovi i test superati e quelli falliti. Negli esercizi con parti ancora da completare, alcuni fallimenti iniziali sono previsti.

Dopo ogni modifica al codice, esegui di nuovo **solo** `npm.cmd test`. Non occorre reinstallare i pacchetti, salvo che cambino `package.json` o `package-lock.json`. Se PowerShell mostra `Could not find package.json` o un errore simile, verifica di essere nella cartella dell'esercizio con i comandi del primo passo.
