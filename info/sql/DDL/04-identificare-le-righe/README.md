# 04 — Identificare le righe

## Consegna

Una biblioteca deve distinguere utenti omonimi e impedire che lo stesso indirizzo email sia registrato due volte. L’identificatore interno deve restare stabile anche se cambia l’email del lettore.

Per ogni lettore bisogna registrare nome completo ed email, entrambi obbligatori, e generare automaticamente un numero interno distinto. Due persone possono avere lo stesso nome, ma non possono condividere lo stesso indirizzo email. Il codice della tessera, lungo otto caratteri, può essere assegnato in un secondo momento: quando è presente deve appartenere a un solo lettore. Il numero interno deve continuare a identificare la stessa persona anche dopo una modifica dei recapiti.

### Requisiti tecnici

Crea `readers` con `id INT AUTO_INCREMENT PRIMARY KEY`, `full_name VARCHAR(100) NOT NULL`, `email VARCHAR(150) NOT NULL UNIQUE`, `card_code CHAR(8) UNIQUE` facoltativo. In `design.md` spiega perché due omonimi sono ammessi e perché l’email non viene usata come chiave primaria.

## Prima di iniziare

[Vincoli sui dati](../03-vincoli-sui-dati/README.md). Gli esercizi sono indipendenti: non serve aver avviato i database precedenti.

## Concetti e sintassi MySQL

`PRIMARY KEY` identifica ogni riga: è unica e non ammette `NULL`. Una tabella ha una sola chiave primaria, anche quando composta da più colonne. `UNIQUE` impone altre regole di unicità; in MySQL una colonna univoca ma facoltativa può contenere più `NULL`. `AUTO_INCREMENT` genera un identificatore numerico quando viene omesso:

```sql
CREATE TABLE esempio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codice VARCHAR(30) NOT NULL UNIQUE
);
```

Una chiave naturale proviene dal dominio, come un codice ufficiale; una chiave artificiale viene introdotta dal sistema. I numeri automatici possono avere buchi: non sono un contatore affidabile del numero di righe.

## File forniti e lavoro da completare

- `solution.sql`: file da completare con le istruzioni DDL richieste.
- `design.md`: spazio per le motivazioni richieste, da discutere anche quando i test passano.
- `tests/cases.sh`: verifiche già fornite, con descrizioni in inglese; gli inserimenti e le query sono strumenti di controllo, non DML o DQL da implementare.
- `compose.yaml`: database, client e test già disponibili nei container.
- `cleanup.sql`: pulizia dei database dell’esercizio, se usi un server condiviso.

**Input:** un database vuoto e i requisiti della consegna. **Output:** lo schema creato o modificato da `solution.sql`. I nomi, i tipi, le dimensioni e i comportamenti richiesti fanno parte del contratto; i nomi dei vincoli possono essere scelti liberamente, salvo quelli esplicitamente indicati. Usa il motore predefinito InnoDB e non disabilitare controlli o vincoli. I test non sostituiscono il controllo manuale di ogni requisito e delle motivazioni.

## Eseguire con Docker

Apri un terminale **del computer** nella cartella `info/sql/DDL/04-identificare-le-righe`. Serve solo Docker con Compose; non occorrono MySQL, npm o altri strumenti locali. Se usi il Dev Container di `school`, i comandi Docker vanno eseguiti fuori dal suo terminale. Vedi la [guida Docker](../../../../DOCKER.md) e la [guida VS Code](../../../../VS-CODE.md).

```bash
docker compose up -d --wait
docker compose exec -e MYSQL_PWD=school_root_dev db mysql -u root ddl_04
```

Usiamo **MySQL 8.4.7**, database manuale `ddl_04` e utente didattico `root` con password `school_root_dev`. Non è pubblicata alcuna porta: il client è nel container. Al primo avvio viene preparato un database vuoto: tutte le tabelle devono essere definite in `solution.sql`.

Nel client puoi eseguire:

```sql
SOURCE /work/solution.sql;
SHOW CREATE TABLE readers; SELECT * FROM readers;
```

`SOURCE` legge le modifiche dal file montato: non occorre ricostruire un’immagine. Termina le istruzioni SQL con `;` e scrivi `exit` per uscire. Puoi eseguire manualmente inserimenti e modifiche usando il client; i file `tests/cases.sh` contengono esempi già pronti, inclusi casi volutamente non validi. Se la soluzione crea oggetti già esistenti o applica una migrazione, riparti dallo stato iniziale prima di ricaricarla: non è richiesto che sia eseguibile due volte sullo stesso schema.

### Verifica automatica

```bash
docker compose run --rm test
```

Il comando prepara `ddl_04_test`, esegue `solution.sql`, quindi verifica struttura e comportamenti. **Ogni esecuzione ricrea solo il database di test e ne cancella i dati precedenti**, lasciando intatto `ddl_04`. Non inserire `USE` o `CREATE DATABASE` nella soluzione: il database di destinazione è selezionato dal client. I test usano connessioni con modalità SQL rigorosa. Un errore di applicazione o un controllo fallito produce un codice di uscita diverso da zero.

**Stato iniziale previsto:** con `solution.sql` vuoto i test falliscono perché mancano le definizioni o le modifiche richieste. Non modificare i test per farli passare: completa la soluzione. L’obiettivo è ottenere tutti i controlli `PASS` e spiegare le scelte richieste in `design.md`.

### Arresto e ripristino

```bash
docker compose down
```

L’arresto conserva i dati nel volume dedicato al progetto `school-ddl-04`. Il database vuoto viene preparato **solo su un volume nuovo**. Per eliminare tutti i dati di questo esercizio, compresi quelli delle prove manuali, e ripartire:

```bash
docker compose --profile tools down -v
docker compose up -d --wait
```

`down -v` cancella definitivamente il volume; non elimina i file SQL. `cleanup.sql` serve invece a eliminare solo `ddl_04` e `ddl_04_test` se importati in un server condiviso o locale: non è un passaggio necessario con Docker.

### Ambiente online

Puoi usare un SQL fiddle con **MySQL 8.4** o una versione compatibile, almeno 8.0.16 per i `CHECK`. Carica la tua soluzione nello spazio destinato alla definizione dello schema: non è previsto un setup iniziale. Usa il database assegnato dal servizio, senza crearne un altro. Le verifiche automatiche shell sono disponibili con Docker; online puoi riprodurre le istruzioni SQL dei test. Un servizio MariaDB, PostgreSQL o Oracle non è equivalente all’ambiente richiesto.

[Torna al percorso DDL](../README.md).
