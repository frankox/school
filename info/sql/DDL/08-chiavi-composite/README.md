# 08 — Chiavi composite

## Consegna

Un cinema numera i posti a partire da 1 in ogni sala. Il posto 12 può esistere in due sale diverse: il numero da solo non identifica il posto. Anche la posizione fisica, fila e numero nella fila, deve essere unica all’interno della sala.

Per ogni posto bisogna conservare il codice della sala, il numero del posto, la lettera della fila e la posizione nella fila. Nella stessa sala non possono esserci due posti con lo stesso numero né due posti nella stessa posizione fisica. In sale diverse, invece, numeri e posizioni possono ripetersi. Il numero del posto e quello della posizione nella fila devono essere maggiori di zero, e tutte le informazioni devono essere presenti.

### Dati e comportamenti richiesti

Prepara la tabella `seats` usando `room_code` per il codice testuale della sala, `seat_number` per il numero intero del posto, `row_label` per la lettera della fila e `position_in_row` per il numero intero della posizione nella fila.

Scegli tipi e dimensioni adeguati e traduci in regole del database l’identificazione del posto e l’unicità della posizione fisica descritte sopra. Tutti i campi sono obbligatori e i due numeri devono essere positivi. Non creare ancora la tabella delle sale: qui studiamo l’identificazione composta, non le relazioni.

## Prima di iniziare

[Azioni referenziali](../07-azioni-referenziali/README.md). Gli esercizi sono indipendenti: non serve aver avviato i database precedenti.

## Concetti e sintassi MySQL

Una chiave composta usa più colonne insieme:

```sql
PRIMARY KEY (contenitore_id, numero),
CONSTRAINT posizione_unica UNIQUE (contenitore_id, coordinata_a, coordinata_b)
```

L’unicità riguarda la combinazione, non ogni singola colonna. Tutte le colonne della chiave primaria sono implicitamente `NOT NULL`. Un ulteriore `UNIQUE` esprime una regola diversa dalla chiave primaria. Quando il vincolo deve valere anche per combinazioni con dati mancanti, bisogna valutare `NOT NULL`: un indice univoco MySQL ammette più valori `NULL`.

## File forniti e lavoro da completare

- `solution.sql`: file da completare con le istruzioni DDL richieste.
- `tests/cases.sh`: verifiche già fornite, con descrizioni in inglese; gli inserimenti e le query sono strumenti di controllo, non DML o DQL da implementare.
- `compose.yaml`: database, client e test già disponibili nei container.
- `cleanup.sql`: pulizia dei database dell’esercizio, se usi un server condiviso.

**Input:** un database vuoto e i requisiti della consegna. **Output:** lo schema creato o modificato da `solution.sql`. Rispetta i nomi e i comportamenti richiesti. La scelta dei tipi SQL e delle dimensioni spetta a te: deve essere adeguata ai dati descritti e compatibile con le strutture già fornite. Non è richiesta una dimensione identica a quella di un esempio di sintassi. I nomi dei vincoli sono liberi, salvo quelli esplicitamente indicati. Usa il motore predefinito InnoDB e non disabilitare controlli o vincoli. I test non sostituiscono il controllo manuale di ogni requisito e delle motivazioni.

## Eseguire con Docker

Apri un terminale **del computer** nella cartella `info/sql/DDL/08-chiavi-composite`. Serve solo Docker con Compose; non occorrono MySQL, npm o altri strumenti locali. Se usi il Dev Container di `school`, i comandi Docker vanno eseguiti fuori dal suo terminale. Vedi la [guida Docker](../../../../DOCKER.md) e la [guida VS Code](../../../../VS-CODE.md).

```bash
docker compose up -d --wait
docker compose exec -e MYSQL_PWD=school_root_dev db mysql -u root ddl_08
```

Usiamo **MySQL 8.4.7**, database manuale `ddl_08` e utente didattico `root` con password `school_root_dev`. Non è pubblicata alcuna porta: il client è nel container. Al primo avvio viene preparato un database vuoto: tutte le tabelle devono essere definite in `solution.sql`.

Nel client puoi eseguire:

```sql
SOURCE /work/solution.sql;
SHOW CREATE TABLE seats; SELECT * FROM seats;
```

`SOURCE` legge le modifiche dal file montato: non occorre ricostruire un’immagine. Termina le istruzioni SQL con `;` e scrivi `exit` per uscire. Puoi eseguire manualmente inserimenti e modifiche usando il client; i file `tests/cases.sh` contengono esempi già pronti, inclusi casi volutamente non validi. Se la soluzione crea oggetti già esistenti o applica una migrazione, riparti dallo stato iniziale prima di ricaricarla: non è richiesto che sia eseguibile due volte sullo stesso schema.

### Verifica automatica

```bash
docker compose run --rm test
```

Il comando prepara `ddl_08_test`, esegue `solution.sql`, quindi verifica struttura e comportamenti. **Ogni esecuzione ricrea solo il database di test e ne cancella i dati precedenti**, lasciando intatto `ddl_08`. Non inserire `USE` o `CREATE DATABASE` nella soluzione: il database di destinazione è selezionato dal client. I test usano connessioni con modalità SQL rigorosa. Un errore di applicazione o un controllo fallito produce un codice di uscita diverso da zero.

**Stato iniziale previsto:** con `solution.sql` vuoto i test falliscono perché mancano le definizioni o le modifiche richieste. Non modificare i test per farli passare: completa la soluzione. L’obiettivo è ottenere tutti i controlli `PASS`.

### Arresto e ripristino

```bash
docker compose down
```

L’arresto conserva i dati nel volume dedicato al progetto `school-ddl-08`. Il database vuoto viene preparato **solo su un volume nuovo**. Per eliminare tutti i dati di questo esercizio, compresi quelli delle prove manuali, e ripartire:

```bash
docker compose --profile tools down -v
docker compose up -d --wait
```

`down -v` cancella definitivamente il volume; non elimina i file SQL. `cleanup.sql` serve invece a eliminare solo `ddl_08` e `ddl_08_test` se importati in un server condiviso o locale: non è un passaggio necessario con Docker.

### Ambiente online

Puoi usare un SQL fiddle con **MySQL 8.4** o una versione compatibile, almeno 8.0.16 per i `CHECK`. Carica la tua soluzione nello spazio destinato alla definizione dello schema: non è previsto un setup iniziale. Usa il database assegnato dal servizio, senza crearne un altro. Le verifiche automatiche shell sono disponibili con Docker; online puoi riprodurre le istruzioni SQL dei test. Un servizio MariaDB, PostgreSQL o Oracle non è equivalente all’ambiente richiesto.

[Torna al percorso DDL](../README.md).
