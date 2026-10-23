# 01 — Database e tabelle

## Consegna

Una piccola libreria vuole sostituire il foglio di calcolo dei titoli disponibili con un catalogo condiviso. Prima di registrare i libri serve creare il contenitore e definire le colonne: la struttura deve essere comprensibile anche senza dati.

La libreria ha bisogno di conservare il titolo e il nome dell’autore di ogni libro, insieme a un numero con cui riconoscere la voce nel catalogo. Titoli e nomi devono poter mantenere la loro scrittura originale: possono contenere lettere accentate come in «Niccolò», caratteri di altre lingue come `本` o simboli come `📚`. Il database deve quindi poter salvare questi caratteri senza perderli o sostituirli. Per ora devi preparare soltanto la struttura del catalogo, senza inserire i libri.

### Requisiti tecnici

Crea il database `ddl_01` con caratteri `utf8mb4`, selezionalo e crea `books` con esattamente queste colonne: `id INT`, `title VARCHAR(150)`, `author VARCHAR(100)`.

## Prima di iniziare

[Introduzione a SQL](../../README.md).

## Concetti e sintassi MySQL

Il computer conserva anche il testo sotto forma di numeri. **Unicode** è uno standard che assegna un codice ai caratteri di moltissime lingue e a numerosi simboli: comprende, per esempio, le lettere accentate italiane come `è`, le lettere greche come `Ω`, i caratteri giapponesi come `本` e le emoji come `📚`. Una **codifica** stabilisce come rappresentare questi codici nei byte memorizzati dal computer; UTF-8 è una codifica di Unicode.

Per il catalogo della libreria è importante conservare correttamente titoli e nomi degli autori, anche quando contengono accenti o appartengono ad altre lingue: pensa a `Niccolò`, `García Márquez` o a un titolo nella sua scrittura originale. Un insieme di caratteri troppo limitato potrebbe impedire l’inserimento di alcuni testi o, se vengono effettuate conversioni con perdita di informazioni, sostituire caratteri con `?`. Scegliere una codifica adeguata fin dall’inizio permette al catalogo di accogliere anche libri e autori che aggiungeremo in futuro.

In MySQL, **`utf8mb4`** è il nome della codifica UTF-8 che supporta l’intero repertorio Unicode, usando fino a quattro byte per ciascun codice Unicode. `CREATE DATABASE nome_database CHARACTER SET utf8mb4;` la imposta come scelta predefinita per il database: le nuove tabelle e le loro colonne testuali la erediteranno, salvo indicazioni diverse. Per scambiare correttamente i testi, anche il client deve usare una codifica compatibile.

`USE nome_database;` seleziona il database sul quale lavorare. `CREATE TABLE` definisce le colonne tra parentesi, separate da virgole:

```sql
CREATE TABLE esempio (
    codice INT,
    descrizione VARCHAR(80)
);
```

`INT` conserva interi, `VARCHAR(n)` testo fino a `n` caratteri. `SHOW TABLES;` elenca le tabelle; `DESCRIBE esempio;` mostra le colonne. `IF NOT EXISTS` evita un errore se un oggetto esiste già, ma non corregge una struttura errata. Qui non servono ancora chiavi o vincoli.

## File forniti e lavoro da completare

- `solution.sql`: file da completare con le istruzioni DDL richieste.
- `tests/cases.sh`: verifiche già fornite, con descrizioni in inglese; gli inserimenti e le query sono strumenti di controllo, non DML o DQL da implementare.
- `compose.yaml`: database, client e test già disponibili nei container.
- `cleanup.sql`: pulizia dei database dell’esercizio, se usi un server condiviso.

**Input:** un server senza il database dell’esercizio e i requisiti della consegna. **Output:** lo schema creato o modificato da `solution.sql`. I nomi, i tipi, le dimensioni e i comportamenti richiesti fanno parte del contratto; i nomi dei vincoli possono essere scelti liberamente, salvo quelli esplicitamente indicati. Usa il motore predefinito InnoDB e non disabilitare controlli o vincoli. I test non sostituiscono il controllo manuale di ogni requisito e delle motivazioni.

## Eseguire con Docker

Apri un terminale **del computer** nella cartella `info/sql/DDL/01-database-e-tabelle`. Serve solo Docker con Compose; non occorrono MySQL, npm o altri strumenti locali. Se usi il Dev Container di `school`, i comandi Docker vanno eseguiti fuori dal suo terminale. Vedi la [guida Docker](../../../../DOCKER.md) e la [guida VS Code](../../../../VS-CODE.md).

```bash
docker compose up -d --wait
docker compose exec -e MYSQL_PWD=school_root_dev db mysql -u root
```

Usiamo **MySQL 8.4.7**, database manuale `ddl_01` e utente didattico `root` con password `school_root_dev`. Non è pubblicata alcuna porta: il client è nel container. Il database non esiste ancora: lo creerai nella soluzione.

Nel client puoi eseguire:

```sql
SOURCE /work/solution.sql;
SHOW TABLES; DESCRIBE books; SELECT * FROM books;
```

`SOURCE` legge le modifiche dal file montato: non occorre ricostruire un’immagine. Termina le istruzioni SQL con `;` e scrivi `exit` per uscire. Puoi eseguire manualmente inserimenti e modifiche usando il client; i file `tests/cases.sh` contengono esempi già pronti, inclusi casi volutamente non validi. Se la soluzione crea oggetti già esistenti o applica una migrazione, riparti dallo stato iniziale prima di ricaricarla: non è richiesto che sia eseguibile due volte sullo stesso schema.

### Verifica automatica

```bash
docker compose run --rm test
```

Il comando prepara `ddl_01_test`, esegue `solution.sql`, quindi verifica struttura e comportamenti. **Ogni esecuzione ricrea solo il database di test e ne cancella i dati precedenti**, lasciando intatto `ddl_01`. Per provare anche la creazione del database, il runner sostituisce nel file il nome `ddl_01` con `ddl_01_test` prima di eseguirlo. Usa esattamente `ddl_01` nella soluzione. I test usano connessioni con modalità SQL rigorosa. Un errore di applicazione o un controllo fallito produce un codice di uscita diverso da zero.

**Stato iniziale previsto:** con `solution.sql` vuoto i test falliscono perché mancano database e tabella. Non modificare i test per farli passare: completa la soluzione. L’obiettivo è ottenere tutti i controlli `PASS`.

### Arresto e ripristino

```bash
docker compose down
```

L’arresto conserva i dati nel volume dedicato al progetto `school-ddl-01`. Il server inizialmente non contiene il database dell’esercizio, che creerai nella soluzione. Per eliminare tutti i dati di questo esercizio, compresi quelli delle prove manuali, e ripartire:

```bash
docker compose --profile tools down -v
docker compose up -d --wait
```

`down -v` cancella definitivamente il volume; non elimina i file SQL. `cleanup.sql` serve invece a eliminare solo `ddl_01` e `ddl_01_test` se importati in un server condiviso o locale: non è un passaggio necessario con Docker.

### Ambiente online

Puoi usare un SQL fiddle con **MySQL 8.4** o una versione compatibile, almeno 8.0.16 per i `CHECK`. Carica la tua soluzione nello spazio destinato alla definizione dello schema: non è previsto un setup iniziale. Se il servizio vieta CREATE DATABASE o USE, prova soltanto CREATE TABLE nel database fornito: la parte di creazione del database va verificata con Docker. Le verifiche automatiche shell sono disponibili con Docker; online puoi riprodurre le istruzioni SQL dei test. Un servizio MariaDB, PostgreSQL o Oracle non è equivalente all’ambiente richiesto.

[Torna al percorso DDL](../README.md).
