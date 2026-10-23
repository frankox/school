# Usare gli esercizi con Docker

Serve Docker con il comando `docker compose`. Per ottenere i file del repository si può anche scaricare uno ZIP, senza installare Git. Al primo utilizzo Docker scarica le immagini e, se necessario, le dipendenze **dentro i container**. Sul computer non occorrono i linguaggi di programmazione, i relativi gestori di pacchetti, un database o un client HTTP aggiuntivi.

Aprire un terminale **nella cartella dell'esercizio**, dove si trova `compose.yaml`. I comandi seguenti funzionano in PowerShell e nelle shell Linux/macOS. Il `README.md` di ogni esercizio indica quali servizi sono presenti, quali comandi usare e quali risultati aspettarsi. Su Linux, se l'utente non ha accesso al daemon Docker, anteporre `sudo` a `docker`.

## Che cosa sono immagini e container

Un'**immagine** è il modello con un programma e il suo ambiente; un **container** è un'istanza creata da quell'immagine. È simile alla differenza tra una classe e un oggetto, anche se un container è un processo isolato, non un oggetto del programma. Fermare un container non elimina l'immagine.

| Elemento | A che cosa serve | Esempio in questo repository |
| --- | --- | --- |
| Immagine | Fornisce runtime e strumenti | `node:20-bookworm-slim` per i test TypeScript; `mariadb:11.8` per il database. |
| Dockerfile | Costruisce un'immagine personalizzata | [Quello delle API PHP](info/API/docker/php/Dockerfile) aggiunge i driver `pdo_mysql` e `pdo_sqlite` a PHP. |
| Container | Esegue un servizio o un comando | `app` serve un'API; `test` esegue una suite e termina. |
| Volume | Conserva dati oltre la vita del container | `db_data` conserva le righe MariaDB anche dopo `docker compose down`. |
| Montaggio di cartella | Rende visibili nel container i file del computer | `.:/work` fa leggere subito al container le modifiche al codice. |
| Docker Compose | Descrive e collega i servizi in `compose.yaml` | Avvia PHP, MariaDB e gli strumenti necessari con i comandi qui sotto. |

I container comunicano sulla rete creata da Compose usando i **nomi dei servizi**. Negli esercizi, montaggi e volumi rendono disponibili il codice e i dati persistenti. Su Linux i container usano il kernel dell'host; su Windows Docker Desktop esegue i container Linux in un ambiente Linux gestito da WSL 2 o da una VM.

### Due esempi del repository

Nell'[API degli studenti](info/API/02-create-student-api/compose.yaml), una richiesta percorre questa strada:

```text
client HTTP → app (PHP) → db (MariaDB) → volume db_data
```

`app` usa l'immagine PHP costruita con il Dockerfile; `db` usa un'immagine MariaDB già pronta. Il nome `db` nella connessione PHP indica il servizio sulla rete Compose, non il computer dello studente. La porta `127.0.0.1:8000:8000` rende invece l'API raggiungibile dal browser del computer sulla porta 8000. `admin` offre un'interfaccia web opzionale per il database. Il servizio `test` è separato: può controllare il codice senza lasciare acceso il server.

Nel progetto di [Valutazione azienda](gestione-progetto/valutazione-azienda/compose.yaml) c'è solo `test`: l'immagine Node.js esegue installazione delle dipendenze, compilazione TypeScript e test. Il codice viene montato dalla cartella dell'esercizio; `node_modules` e i file compilati restano in volumi. Per questo progetto non servono un'API o un database.

Quando un servizio deve aspettare il database, `depends_on` da solo stabilisce l'ordine di avvio. Gli esercizi API con MariaDB aggiungono un controllo di salute e fanno attendere `app` finché `db` è pronto.

## Usare VS Code nel container

Eseguire i test con Docker non basta per l'analisi statica: VS Code aperto sul computer non vede i pacchetti conservati in un volume Docker. Il [Dev Container di `school`](.devcontainer/devcontainer.json) apre **l'intero repository**, compresi i README e le guide comuni, e rende disponibili all'editor le dipendenze TypeScript di Valutazione azienda. Per il PHP installa Intelephense nel container, senza richiedere PHP sul computer.

1. Avvia Docker e verifica che il tuo account possa usarlo. In VS Code installa l'estensione [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers). Non occorre installare Node.js o npm sul computer.
2. In VS Code scegli **File → Apri cartella** e apri **`school`**, la radice del repository che contiene `.devcontainer/devcontainer.json`. Nel pannello Explorer vedrai anche `README.md`, `DOCKER.md`, `info/` e `gestione-progetto/`.
3. Premi **Ctrl+Shift+P** (oppure **F1**), cerca **Dev Containers: Reopen in Container** e premi Invio. Attendi che VS Code prepari il container: al primo avvio scarica l'immagine, le dipendenze npm e i componenti remoti necessari.
4. Controlla che in basso a sinistra VS Code indichi il Dev Container. Apri `gestione-progetto/valutazione-azienda/tests/companyEvaluation.test.ts` e scegli **TypeScript: Select TypeScript Version → Use Workspace Version** dalla tavolozza dei comandi. Ora completamento e analisi statica usano i tipi disponibili nel container.

I file che modifichi restano sul computer e sono letti subito dal container. Per provare Valutazione azienda nel terminale **interno al Dev Container**, entra in `gestione-progetto/valutazione-azienda` ed esegui `npm test`. Per gli altri esercizi segui i rispettivi README ed esegui i comandi `docker compose` da un terminale **del computer**, fuori dal Dev Container: il container dell'editor non include il comando `docker`. Per tornare all'editor locale, scegli **Dev Containers: Reopen Folder Locally**. Gli avvisi sulle librerie possono ricomparire perché il volume `node_modules` non è visibile all'editor locale.

VS Code deve poter accedere a Docker con i permessi dell'account dello studente: anteporre `sudo` ai comandi del terminale non abilita l'estensione Dev Containers. Sui computer del laboratorio questo accesso va predisposto per gli account degli studenti. Per dettagli su TypeScript e sugli esercizi PHP, vedi la [guida all'editor](VS-CODE.md).

## Eseguire i test

Aprire il `compose.yaml` dell'esercizio. Se sotto `services:` compare una voce chiamata `test:`, eseguire dalla stessa cartella:

```bash
docker compose run --build --rm test
```

Se appare `permission denied while trying to connect to the docker API` su Linux, la sessione non può accedere al socket Docker. Ripetere il comando con i privilegi necessari:

```bash
sudo docker compose run --build --rm test
```

La password richiesta è quella dell'account sul computer. Usare `sudo` anche davanti agli altri comandi `docker compose` dello stesso esercizio.

Il nome `test` nel comando è quello del servizio nel `compose.yaml`; il comando funziona anche quando il servizio è nel profilo `tools`. Crea un container temporaneo, esegue la suite e ne mostra l'esito. Dopo una modifica al codice, ripetere lo stesso comando. Alcuni esercizi partono con test falliti perché contengono parti da completare. Se l'esercizio consiste solo in una funzione verificata dai test, questo comando è tutto ciò che serve: non occorre avviare un server. Se il servizio `test` manca, seguire la verifica indicata nel README dell'esercizio.

## Avviare un'applicazione

Se il progetto include servizi da lasciare in esecuzione:

```bash
docker compose up -d --build --wait
```

`up` avvia servizi che restano in esecuzione; `run` crea invece un container per un compito singolo, mentre `exec` esegue un comando in un servizio già avviato. `--wait` attende i controlli di salute definiti nel Compose. Indirizzo, porta e percorso dell'applicazione sono nel `README.md` dell'esercizio. Se la porta è già occupata da un altro esercizio, fermare prima quest'ultimo dalla sua cartella con `docker compose down`.

Per controllare i servizi attivi e leggere i messaggi di un'applicazione, quando il servizio si chiama `app`:

```bash
docker compose ps
docker compose logs -f app
```

`Ctrl+C` interrompe la visualizzazione dei log senza fermare il servizio.

Se il progetto include il servizio `client`, inviare richieste HTTP dal container usando gli esempi esatti nel suo README. La forma generale è:

```bash
docker compose run --rm client -i "http://app:8000/PERCORSO"
```

`PERCORSO` è un segnaposto: sostituirlo con quello indicato nella consegna. Per POST e PUT aggiungere metodo, intestazioni e file di input come mostrato nel README. Il servizio `client` evita di installare curl sul computer.

## Consultare un database

Se il progetto include `db`, il README dell'esercizio riporta nome del database e credenziali. Per aprire il client SQL **dentro** il container:

```bash
docker compose exec db mariadb -u UTENTE -p NOME_DATABASE
```

Questo esempio usa il client MariaDB. Per un servizio MySQL, come negli esercizi DDL, il client si chiama `mysql`:

```bash
docker compose exec db mysql -u UTENTE -p NOME_DATABASE
```

Scegliere il client indicato nel README dell’esercizio e sostituire `UTENTE` e `NOME_DATABASE` con i valori documentati; `-p` chiede la password. Terminare ogni query SQL con `;` e digitare `exit` per uscire. Se il progetto include il servizio opzionale `admin`, avviare l'interfaccia nel browser con:

```bash
docker compose --profile admin up -d --wait
```

L'indirizzo e le credenziali dell'interfaccia sono nel README dell'esercizio. Il database può restare accessibile soltanto dalla rete Docker: non serve pubblicarne la porta sul computer.

## Fermare e ripartire

Dalla cartella dell'esercizio:

```bash
docker compose down
```

Questo ferma i container e conserva i volumi. Quando sono presenti dati persistenti, lo schema iniziale viene di norma caricato solo al primo avvio. Per eliminare **anche i dati e le cache nei volumi**:

```bash
docker compose --profile tools --profile admin down -v
```

Il comando elimina i volumi di quel progetto Compose; leggere nel README dell'esercizio quali dati andranno persi. I file del codice montati dalla cartella del progetto restano sul computer. Dopo il reset, ripetere il comando di test o di avvio appropriato per l'esercizio.

Questa guida riprende e adatta i concetti della [spiegazione Docker condivisa](https://chatgpt.com/share/6ace427e-f994-83ed-a07b-393cc67bd4b6) agli esercizi del repository.
