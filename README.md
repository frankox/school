# Eserciziario scolastico

Questo repository raccoglie esercizi per gli studenti, organizzati per materia e argomento.

```text
school/
└── info/
    ├── sql/
    │   └── fondamenti-sql/
    └── API/
        ├── 00-example-books-api/
        ├── 01-csv-to-sql-api/
        ├── 02-json-student-api/
        ├── 03-json-batch-transaction/
        └── 04-order-transaction/
```

Per iniziare, apri [Informatica](info/README.md), scegli un argomento e leggi il `README.md` del materiale. Ogni esercizio spiega obiettivo, preparazione e comandi per provarlo.

## Comandi rapidi per le API

Apri un terminale nella cartella `school`. Con Node.js e PHP installati, `npm install` prepara il progetto; qui non ci sono pacchetti npm esterni da scaricare. I comandi seguenti sono scorciatoie per avviare PHP:

```bash
npm run doctor
npm run test:2
npm run serve:2
```

`doctor` mostra quale PHP viene usato. `test:2` esegue i test dell'esercizio 02 senza database. `serve:2` avvia l'API sulla porta 8000; ferma il server con `Ctrl+C`. Sostituisci `2` con `1`, `3` o `4` per gli altri esercizi; per l'esempio completo usa `npm run serve:example`. I test incompleti falliscono finché non vengono implementati i metodi richiesti.

## Debug dei test in VS Code

1. Apri **l'intera cartella `school`** in VS Code e installa l'estensione consigliata **PHP Debug** (`xdebug.php-debug`).
2. Apri il file PHP in cui vuoi fermarti e clicca a sinistra del numero di riga: appare un punto rosso, il *breakpoint*.
3. Nella sezione **Esegui e debug** scegli **Test API 02** (oppure 01, 03, 04) e premi **F5**. VS Code avvia il test e si ferma sul breakpoint. Usa i pannelli **Variabili** e **Stack chiamate** per vedere cosa sta succedendo; con **F10** passi alla riga successiva, con **F5** riprendi l'esecuzione.

Non serve aprire prima una sessione «ascolta» né usare comandi di debug nel terminale. La configurazione F5 avvia insieme il test e il listener di VS Code. Sul primo avvio con **Windows, XAMPP e PHP 8.0 a 64 bit**, se l'estensione PHP Xdebug manca, viene scaricata nella cartella locale `.runtime/xdebug/` del progetto: non servono privilegi di amministratore e non viene modificato `php.ini`. Serve una connessione Internet solo per questo primo download. L'estensione di VS Code e l'estensione PHP Xdebug sono due componenti diversi; entrambe servono per vedere i breakpoint nell'IDE.

Se XAMPP è in `C:\xampp`, il PHP viene trovato automaticamente. Se è altrove, imposta la variabile d'ambiente `PHP_BIN` al percorso di `php.exe` **prima di aprire VS Code**. La [guida ufficiale di PHP Debug](https://github.com/xdebug/vscode-php-debug) spiega i profili F5; la [pagina ufficiale di Xdebug](https://xdebug.org/download/historical) elenca le versioni per PHP.
