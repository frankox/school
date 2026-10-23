# Eserciziario scolastico

Questo repository raccoglie esercizi per gli studenti, organizzati per materia e argomento.

```text
school/
├── info/
│   ├── sql/
│   │   ├── DDL/  # 12 esercizi su struttura, vincoli, relazioni e indici
│   │   ├── DML/  # sezione predisposta
│   │   ├── DQL/  # sezione predisposta
│   │   └── fondamenti-sql/
│   └── API/
│       ├── 00-example-books-api/
│       ├── 01-csv-to-sql-api/
│       ├── 02-create-student-api/
│       ├── 02b-get-students-api/
│       ├── 03-json-batch-transaction/
│       └── 04-order-transaction/
└── gestione-progetto/
    └── valutazione-azienda/
```

Per SQL, parti dall’[introduzione](info/sql/README.md) e dai [12 esercizi DDL](info/sql/DDL/README.md), ciascuno con test e ambiente MySQL indipendente, oltre al setup quando necessario. Le sezioni DML e DQL sono predisposte per i prossimi esercizi.

Per iniziare, scegli [Informatica](info/README.md) o [Gestione progetto](gestione-progetto/README.md) e leggi il `README.md` del materiale. Ogni esercizio spiega obiettivo, preparazione e comandi per provarlo.

## Avvio con solo Docker

Ogni esercizio ha un `compose.yaml` nella propria cartella. Con Docker e `docker compose` puoi eseguire test, API e database senza installare PHP, Node.js, npm, MariaDB o curl sul computer. Apri un terminale nella cartella dell'esercizio e segui [la guida Docker](DOCKER.md) o il suo README. Il codice resta nei file del progetto e viene letto dai container; la prima esecuzione richiede Internet per scaricare immagini e dipendenze.

Per avere anche il completamento e l'analisi statica di VS Code senza installare le dipendenze sul computer, segui la [guida Docker, sezione «Usare VS Code nel container»](DOCKER.md#usare-vs-code-nel-container). Puoi aprire **l'intera cartella `school`** nel container di sviluppo e consultare tutti gli esercizi e i README; il solo avvio dei test Docker non rende visibili all'editor locale le librerie conservate nei volumi.

**Se lavori in Dev Containers**, usa le istruzioni nel `README.md` del singolo esercizio e scegli il terminale indicato: per Valutazione azienda puoi entrare nella cartella dell'esercizio ed eseguire `npm test` nel terminale integrato del container; per gli esercizi PHP e SQL esegui i comandi `docker compose` del loro README in un terminale **del computer**, fuori dal Dev Container. Il container dell'editor non include il comando `docker`.

### Scorciatoie npm per Docker Compose

Se hai anche Node.js e npm sul computer, dalla radice `school` puoi usare queste scorciatoie. Tutti i test e i servizi girano nei container; Node.js locale serve solo ad avviare lo script npm. Con il solo Docker, usa i comandi equivalenti nei README degli esercizi.

| Esercizio | Test | Avvio dei servizi |
| --- | --- | --- |
| Valutazione azienda | `npm run test:gestione-progetto:valutazione-azienda:docker` | — |
| API esempio libri | — | `npm run up:info:example` |
| API 01 | `npm run test:info:1:docker` | `npm run up:info:1` |
| API 02 | `npm run test:info:2:docker` | `npm run up:info:2` |
| API 02b | `npm run test:info:2b:docker` | `npm run up:info:2b` |
| API 03 | `npm run test:info:3:docker` | `npm run up:info:3` |
| API 04 | `npm run test:info:4:docker` | `npm run up:info:4` |
| Fondamenti SQL | — | `npm run up:info:sql` |
| DDL 01–12 | `npm run test:info:sql:ddl:01:docker` (fino a `12`) | `npm run up:info:sql:ddl:01` (fino a `12`) |

I comandi `up` attendono che i servizi siano pronti e li lasciano in esecuzione. Per richieste HTTP, query, arresto e reset dei dati, segui il README dell'esercizio. `npm run serve:info:2:docker` resta disponibile come scorciatoia precedente per avviare API 02.

## Comandi rapidi per le API

Apri un terminale nella cartella `school`. Con Node.js e PHP installati, `npm install` prepara il progetto; qui non ci sono pacchetti npm esterni da scaricare. I comandi seguenti sono scorciatoie per avviare PHP:

```bash
npm run doctor
npm run test:info:2
npm run test:info:2b
npm run serve:info:2
npm run serve:info:2:docker
```

`doctor` mostra quale PHP viene usato. `test:info:2` esegue i test dell'esercizio 02 senza database; `test:info:2b` usa SQLite in memoria e richiede `pdo_sqlite` nel PHP locale (con Docker è già disponibile). `serve:info:2` avvia l'API sulla porta 8000; ferma il server con `Ctrl+C`. `serve:info:2:docker` avvia PHP e MariaDB nei container e termina lasciandoli in esecuzione; per fermarli usa `docker compose down` dalla cartella dell'esercizio 02. La [prova completa dell'esercizio 02](info/API/02-create-student-api/README.md#con-docker) spiega come inviare il POST e verificare la riga. Usa `2b` per la nuova GET, `1`, `3` o `4` per gli altri esercizi; per l'esempio completo usa `npm run serve:info:example`. I test incompleti falliscono finché non vengono implementati i metodi richiesti.

## Debug locale dei test PHP in VS Code

Questa configurazione usa Node.js e PHP sul computer, come descritto nei [comandi rapidi](#comandi-rapidi-per-le-api). Per lavorare con solo Docker e avere l'analisi statica dell'esercizio TypeScript, segui la [guida all'editor](VS-CODE.md).

1. Apri **l'intera cartella `school`** in VS Code e installa l'estensione consigliata **PHP Debug** (`xdebug.php-debug`).
2. Apri il file PHP in cui vuoi fermarti e clicca a sinistra del numero di riga: appare un punto rosso, il *breakpoint*.
3. Nella sezione **Esegui e debug** scegli **Test API 02** (oppure 01, 03, 04) e premi **F5**. VS Code avvia il test e si ferma sul breakpoint. Usa i pannelli **Variabili** e **Stack chiamate** per vedere cosa sta succedendo; con **F10** passi alla riga successiva, con **F5** riprendi l'esecuzione.

Non serve aprire prima una sessione «ascolta» né usare comandi di debug nel terminale. La configurazione F5 avvia insieme il test e il listener di VS Code. Sul primo avvio con **Windows, XAMPP e PHP 8.0 a 64 bit**, se l'estensione PHP Xdebug manca, viene scaricata nella cartella locale `.runtime/xdebug/` del progetto: non servono privilegi di amministratore e non viene modificato `php.ini`. Serve una connessione Internet solo per questo primo download. L'estensione di VS Code e l'estensione PHP Xdebug sono due componenti diversi; entrambe servono per vedere i breakpoint nell'IDE.

Se XAMPP è in `C:\xampp`, il PHP viene trovato automaticamente. Se è altrove, imposta la variabile d'ambiente `PHP_BIN` al percorso di `php.exe` **prima di aprire VS Code**. La [guida ufficiale di PHP Debug](https://github.com/xdebug/vscode-php-debug) spiega i profili F5; la [pagina ufficiale di Xdebug](https://xdebug.org/download/historical) elenca le versioni per PHP.
