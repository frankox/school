# 02 — Tipi di dato

## Consegna

Un negozio di strumenti musicali vende prodotti con un codice fisso, un prezzo, una descrizione e una data di arrivo. Memorizzare tutto come testo rende difficili calcoli e controlli: ogni informazione richiede un tipo adatto.

Per ogni prodotto il negozio vuole conservare un numero interno, un codice commerciale di otto caratteri, il nome e una descrizione estesa. Deve inoltre conoscere il prezzo esatto al centesimo, il numero di pezzi in magazzino, il giorno da cui il prodotto sarà disponibile e il momento in cui è stato registrato. Un indicatore segnala se il prodotto è attivo nel catalogo. I codici possono iniziare con zero: quel primo zero va conservato, perché fa parte del codice e non di una quantità.

### Dati e comportamenti richiesti

Prepara la tabella `products` con questi campi, scegliendo tipi e dimensioni adatti al significato delle informazioni:

- `id`: numero intero interno.
- `sku`: codice commerciale testuale di otto caratteri, con eventuali zeri iniziali.
- `name`: nome del prodotto.
- `description`: descrizione estesa, che può contenere più paragrafi.
- `price`: prezzo espresso in euro, da conservare esattamente fino ai centesimi.
- `stock`: numero intero di pezzi in magazzino.
- `available_on`: giorno di disponibilità, senza orario.
- `created_at`: data e ora di registrazione.
- `active`: indicatore vero o falso della presenza nel catalogo attivo.

Per ora non aggiungere vincoli. In `design.md` motiva i tipi e le dimensioni scelti, spiegando in particolare perché un prezzo non è testo né un numero approssimato e perché il codice è testo anche se contiene solo cifre.

## Prima di iniziare

[Database e tabelle](../01-database-e-tabelle/README.md). Gli esercizi sono indipendenti: non serve aver avviato i database precedenti.

## Concetti e sintassi MySQL

I tipi esprimono il significato dei dati. `INT` conserva interi; `DECIMAL(p,s)` numeri esatti con `p` cifre totali e `s` decimali, adatti agli importi. `FLOAT` e `DOUBLE` sono approssimati: non sono la prima scelta per prezzi. `CHAR(n)` è adatto a codici di lunghezza fissa; `VARCHAR(n)` a testi di lunghezza variabile; `TEXT` a descrizioni lunghe. `DATE` conserva una data, `DATETIME` una data con ora. In MySQL `BOOLEAN` è un alias di `TINYINT(1)`: da solo non impedisce valori diversi da 0 e 1.

```sql
CREATE TABLE esempio (
    quantita INT,
    importo DECIMAL(8,2),
    codice CHAR(6),
    consegna DATE,
    registrato_il DATETIME,
    disponibile BOOLEAN
);
```

Nelle prove fornite le date usano `YYYY-MM-DD`, le date con ora `YYYY-MM-DD HH:MM:SS`, i decimali il punto. La modalità SQL rigorosa usata dal laboratorio rifiuta valori fuori intervallo.

## File forniti e lavoro da completare

- `solution.sql`: file da completare con le istruzioni DDL richieste.
- `design.md`: spazio per le motivazioni richieste, da discutere anche quando i test passano.
- `tests/cases.sh`: verifiche già fornite, con descrizioni in inglese; gli inserimenti e le query sono strumenti di controllo, non DML o DQL da implementare.
- `compose.yaml`: database, client e test già disponibili nei container.
- `cleanup.sql`: pulizia dei database dell’esercizio, se usi un server condiviso.

**Input:** un database vuoto e i requisiti della consegna. **Output:** lo schema creato o modificato da `solution.sql`. Rispetta i nomi e i comportamenti richiesti. La scelta dei tipi SQL e delle dimensioni spetta a te: deve essere adeguata ai dati descritti e compatibile con le strutture già fornite. Non è richiesta una dimensione identica a quella di un esempio di sintassi. I nomi dei vincoli sono liberi, salvo quelli esplicitamente indicati. Usa il motore predefinito InnoDB e non disabilitare controlli o vincoli. I test non sostituiscono il controllo manuale di ogni requisito e delle motivazioni.

## Eseguire con Docker

Apri un terminale **del computer** nella cartella `info/sql/DDL/02-tipi-di-dato`. Serve solo Docker con Compose; non occorrono MySQL, npm o altri strumenti locali. Se usi il Dev Container di `school`, i comandi Docker vanno eseguiti fuori dal suo terminale. Vedi la [guida Docker](../../../../DOCKER.md) e la [guida VS Code](../../../../VS-CODE.md).

```bash
docker compose up -d --wait
docker compose exec -e MYSQL_PWD=school_root_dev db mysql -u root ddl_02
```

Usiamo **MySQL 8.4.7**, database manuale `ddl_02` e utente didattico `root` con password `school_root_dev`. Non è pubblicata alcuna porta: il client è nel container. Al primo avvio viene preparato un database vuoto: tutte le tabelle devono essere definite in `solution.sql`.

Nel client puoi eseguire:

```sql
SOURCE /work/solution.sql;
DESCRIBE products; SELECT * FROM products;
```

`SOURCE` legge le modifiche dal file montato: non occorre ricostruire un’immagine. Termina le istruzioni SQL con `;` e scrivi `exit` per uscire. Puoi eseguire manualmente inserimenti e modifiche usando il client; i file `tests/cases.sh` contengono esempi già pronti, inclusi casi volutamente non validi. Se la soluzione crea oggetti già esistenti o applica una migrazione, riparti dallo stato iniziale prima di ricaricarla: non è richiesto che sia eseguibile due volte sullo stesso schema.

### Verifica automatica

```bash
docker compose run --rm test
```

Il comando prepara `ddl_02_test`, esegue `solution.sql`, quindi verifica struttura e comportamenti. **Ogni esecuzione ricrea solo il database di test e ne cancella i dati precedenti**, lasciando intatto `ddl_02`. Non inserire `USE` o `CREATE DATABASE` nella soluzione: il database di destinazione è selezionato dal client. I test usano connessioni con modalità SQL rigorosa. Un errore di applicazione o un controllo fallito produce un codice di uscita diverso da zero.

**Stato iniziale previsto:** con `solution.sql` vuoto i test falliscono perché mancano le definizioni o le modifiche richieste. Non modificare i test per farli passare: completa la soluzione. L’obiettivo è ottenere tutti i controlli `PASS` e spiegare le scelte richieste in `design.md`.

### Arresto e ripristino

```bash
docker compose down
```

L’arresto conserva i dati nel volume dedicato al progetto `school-ddl-02`. Il database vuoto viene preparato **solo su un volume nuovo**. Per eliminare tutti i dati di questo esercizio, compresi quelli delle prove manuali, e ripartire:

```bash
docker compose --profile tools down -v
docker compose up -d --wait
```

`down -v` cancella definitivamente il volume; non elimina i file SQL. `cleanup.sql` serve invece a eliminare solo `ddl_02` e `ddl_02_test` se importati in un server condiviso o locale: non è un passaggio necessario con Docker.

### Ambiente online

Puoi usare un SQL fiddle con **MySQL 8.4** o una versione compatibile, almeno 8.0.16 per i `CHECK`. Carica la tua soluzione nello spazio destinato alla definizione dello schema: non è previsto un setup iniziale. Usa il database assegnato dal servizio, senza crearne un altro. Le verifiche automatiche shell sono disponibili con Docker; online puoi riprodurre le istruzioni SQL dei test. Un servizio MariaDB, PostgreSQL o Oracle non è equivalente all’ambiente richiesto.

[Torna al percorso DDL](../README.md).
