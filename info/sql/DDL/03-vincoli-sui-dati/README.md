# 03 — Vincoli sui dati

## Consegna

Un servizio di noleggio biciclette riceve prenotazioni incomplete e durate negative. Il database deve impedire dati impossibili anche quando arrivano da applicazioni diverse. Il telefono resta facoltativo, mentre nome, durata e stato sono obbligatori.

Ogni prenotazione deve riportare il nome del cliente, una durata da una a ventiquattro ore e uno stato: in attesa, confermata o annullata. Il recapito telefonico può mancare. Se chi registra la prenotazione non indica durata e stato, il sistema deve assumere un’ora e lo stato in attesa. Deve invece rifiutare durate fuori intervallo, stati non previsti e l’assenza delle informazioni obbligatorie.

### Requisiti tecnici

Crea `rentals`: `customer_name VARCHAR(100) NOT NULL`, `phone VARCHAR(30)` facoltativo, `hours INT NOT NULL DEFAULT 1` compreso tra 1 e 24, `status VARCHAR(12) NOT NULL DEFAULT 'pending'` limitato a `pending`, `confirmed`, `cancelled`. Assegna nomi comprensibili ai due `CHECK`.

## Prima di iniziare

[Tipi di dato](../02-tipi-di-dato/README.md). Gli esercizi sono indipendenti: non serve aver avviato i database precedenti.

## Concetti e sintassi MySQL

`NOT NULL` impedisce un valore assente; non vieta da solo il testo vuoto. `DEFAULT` fornisce un valore quando una colonna viene omessa, non sostituisce un `NULL` esplicito. `CHECK` verifica una condizione su ogni riga. Un controllo che restituisce valore sconosciuto per effetto di `NULL` non basta a rifiutare la riga: abbinalo a `NOT NULL` quando necessario.

```sql
CREATE TABLE esempio (
    nome VARCHAR(80) NOT NULL,
    quantita INT NOT NULL DEFAULT 1,
    CONSTRAINT quantita_positiva CHECK (quantita > 0)
);
```

Un `CHECK` può confrontare colonne della stessa riga e usare `IN (...)` per limitare un insieme di valori. In MySQL i `CHECK` sono effettivamente applicati dalla versione 8.0.16; qui usiamo MySQL 8.4.

## File forniti e lavoro da completare

- `solution.sql`: file da completare con le istruzioni DDL richieste.
- `tests/cases.sh`: verifiche già fornite, con descrizioni in inglese; gli inserimenti e le query sono strumenti di controllo, non DML o DQL da implementare.
- `compose.yaml`: database, client e test già disponibili nei container.
- `cleanup.sql`: pulizia dei database dell’esercizio, se usi un server condiviso.

**Input:** un database vuoto e i requisiti della consegna. **Output:** lo schema creato o modificato da `solution.sql`. I nomi, i tipi, le dimensioni e i comportamenti richiesti fanno parte del contratto; i nomi dei vincoli possono essere scelti liberamente, salvo quelli esplicitamente indicati. Usa il motore predefinito InnoDB e non disabilitare controlli o vincoli. I test non sostituiscono il controllo manuale di ogni requisito e delle motivazioni.

## Eseguire con Docker

Apri un terminale **del computer** nella cartella `info/sql/DDL/03-vincoli-sui-dati`. Serve solo Docker con Compose; non occorrono MySQL, npm o altri strumenti locali. Se usi il Dev Container di `school`, i comandi Docker vanno eseguiti fuori dal suo terminale. Vedi la [guida Docker](../../../../DOCKER.md) e la [guida VS Code](../../../../VS-CODE.md).

```bash
docker compose up -d --wait
docker compose exec -e MYSQL_PWD=school_root_dev db mysql -u root ddl_03
```

Usiamo **MySQL 8.4.7**, database manuale `ddl_03` e utente didattico `root` con password `school_root_dev`. Non è pubblicata alcuna porta: il client è nel container. Al primo avvio viene preparato un database vuoto: tutte le tabelle devono essere definite in `solution.sql`.

Nel client puoi eseguire:

```sql
SOURCE /work/solution.sql;
SHOW CREATE TABLE rentals; SELECT * FROM rentals;
```

`SOURCE` legge le modifiche dal file montato: non occorre ricostruire un’immagine. Termina le istruzioni SQL con `;` e scrivi `exit` per uscire. Puoi eseguire manualmente inserimenti e modifiche usando il client; i file `tests/cases.sh` contengono esempi già pronti, inclusi casi volutamente non validi. Se la soluzione crea oggetti già esistenti o applica una migrazione, riparti dallo stato iniziale prima di ricaricarla: non è richiesto che sia eseguibile due volte sullo stesso schema.

### Verifica automatica

```bash
docker compose run --rm test
```

Il comando prepara `ddl_03_test`, esegue `solution.sql`, quindi verifica struttura e comportamenti. **Ogni esecuzione ricrea solo il database di test e ne cancella i dati precedenti**, lasciando intatto `ddl_03`. Non inserire `USE` o `CREATE DATABASE` nella soluzione: il database di destinazione è selezionato dal client. I test usano connessioni con modalità SQL rigorosa. Un errore di applicazione o un controllo fallito produce un codice di uscita diverso da zero.

**Stato iniziale previsto:** con `solution.sql` vuoto i test falliscono perché mancano le definizioni o le modifiche richieste. Non modificare i test per farli passare: completa la soluzione. L’obiettivo è ottenere tutti i controlli `PASS`.

### Arresto e ripristino

```bash
docker compose down
```

L’arresto conserva i dati nel volume dedicato al progetto `school-ddl-03`. Il database vuoto viene preparato **solo su un volume nuovo**. Per eliminare tutti i dati di questo esercizio, compresi quelli delle prove manuali, e ripartire:

```bash
docker compose --profile tools down -v
docker compose up -d --wait
```

`down -v` cancella definitivamente il volume; non elimina i file SQL. `cleanup.sql` serve invece a eliminare solo `ddl_03` e `ddl_03_test` se importati in un server condiviso o locale: non è un passaggio necessario con Docker.

### Ambiente online

Puoi usare un SQL fiddle con **MySQL 8.4** o una versione compatibile, almeno 8.0.16 per i `CHECK`. Carica la tua soluzione nello spazio destinato alla definizione dello schema: non è previsto un setup iniziale. Usa il database assegnato dal servizio, senza crearne un altro. Le verifiche automatiche shell sono disponibili con Docker; online puoi riprodurre le istruzioni SQL dei test. Un servizio MariaDB, PostgreSQL o Oracle non è equivalente all’ambiente richiesto.

[Torna al percorso DDL](../README.md).
