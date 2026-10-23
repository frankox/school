# 05 — Modificare la struttura

## Consegna

Un negozio ha già un catalogo in produzione. Vuole aggiungere le scorte, ampliare i nomi e abbandonare un campo obsoleto senza perdere gli articoli esistenti. Ricreare tutte le tabelle cancellando i dati non è una migrazione accettabile.

Il nuovo catalogo deve accogliere nomi più lunghi e indicare il numero di pezzi disponibili, che non può essere negativo. Per i prodotti già registrati, la disponibilità iniziale sarà zero. Il negozio vuole anche chiamare più chiaramente il campo del prezzo e l’archivio del catalogo, eliminare il vecchio codice non più usato e rimuovere l’archivio temporaneo di importazione. I due prodotti esistenti devono mantenere numeri identificativi, nomi e prezzi.

### Requisiti tecnici

Modifica `products` fornita: amplia `name` a `VARCHAR(120) NOT NULL`, rinomina `cost` in `price` mantenendo `DECIMAL(8,2) NOT NULL`, aggiungi `stock INT NOT NULL DEFAULT 0` e un `CHECK (stock >= 0)`, elimina `legacy_code`. Rinomina la tabella in `catalog_products` ed elimina solo `import_buffer`. Conserva i due prodotti, i rispettivi id, nomi e prezzi. Usa operazioni DDL, senza ricreare o reinserire i prodotti.

## Prima di iniziare

[Identificare le righe](../04-identificare-le-righe/README.md). Gli esercizi sono indipendenti: non serve aver avviato i database precedenti.

## Concetti e sintassi MySQL

`ALTER TABLE` modifica una tabella esistente. In MySQL `MODIFY COLUMN` richiede di riscrivere la definizione completa, inclusi gli attributi da mantenere.

```sql
ALTER TABLE esempio ADD COLUMN quantita INT NOT NULL DEFAULT 0;
ALTER TABLE esempio MODIFY COLUMN nome VARCHAR(120) NOT NULL;
ALTER TABLE esempio RENAME COLUMN vecchio_nome TO nuovo_nome;
ALTER TABLE esempio DROP COLUMN obsoleto;
RENAME TABLE esempio TO nuovo_esempio;
DROP TABLE tabella_non_piu_usata;
```

Aggiungere un vincolo può fallire se i dati esistenti lo violano. Le modifiche DDL possono causare commit impliciti e non si annullano normalmente con `ROLLBACK`: prima si pianifica la migrazione e si conservano i dati.

## File forniti e lavoro da completare

- `schema.sql`: setup già fornito; non modificarlo per risolvere l’esercizio.
- `solution.sql`: file da completare con le istruzioni DDL richieste.
- `tests/cases.sh`: verifiche già fornite, con descrizioni in inglese; gli inserimenti e le query sono strumenti di controllo, non DML o DQL da implementare.
- `compose.yaml`: database, client e test già disponibili nei container.
- `cleanup.sql`: pulizia dei database dell’esercizio, se usi un server condiviso.

**Input:** il setup e i requisiti della consegna. **Output:** lo schema creato o modificato da `solution.sql`. I nomi, i tipi, le dimensioni e i comportamenti richiesti fanno parte del contratto; i nomi dei vincoli possono essere scelti liberamente, salvo quelli esplicitamente indicati. Usa il motore predefinito InnoDB e non disabilitare controlli o vincoli. I test non sostituiscono il controllo manuale di ogni requisito e delle motivazioni.

## Eseguire con Docker

Apri un terminale **del computer** nella cartella `info/sql/DDL/05-modificare-la-struttura`. Serve solo Docker con Compose; non occorrono MySQL, npm o altri strumenti locali. Se usi il Dev Container di `school`, i comandi Docker vanno eseguiti fuori dal suo terminale. Vedi la [guida Docker](../../../../DOCKER.md) e la [guida VS Code](../../../../VS-CODE.md).

```bash
docker compose up -d --wait
docker compose exec -e MYSQL_PWD=school_root_dev db mysql -u root ddl_05
```

Usiamo **MySQL 8.4.7**, database manuale `ddl_05` e utente didattico `root` con password `school_root_dev`. Non è pubblicata alcuna porta: il client è nel container. Al primo avvio `schema.sql` viene caricato automaticamente: fornisce lo stato iniziale, non la soluzione.

Nel client puoi eseguire:

```sql
SOURCE /work/solution.sql;
SHOW CREATE TABLE catalog_products; SELECT * FROM catalog_products;
```

`SOURCE` legge le modifiche dal file montato: non occorre ricostruire un’immagine. Termina le istruzioni SQL con `;` e scrivi `exit` per uscire. Puoi eseguire manualmente inserimenti e modifiche usando il client; i file `tests/cases.sh` contengono esempi già pronti, inclusi casi volutamente non validi. Se la soluzione crea oggetti già esistenti o applica una migrazione, riparti dallo stato iniziale prima di ricaricarla: non è richiesto che sia eseguibile due volte sullo stesso schema.

### Verifica automatica

```bash
docker compose run --rm test
```

Il comando prepara `ddl_05_test`, applica `schema.sql` e `solution.sql`, quindi verifica struttura e comportamenti. **Ogni esecuzione ricrea solo il database di test e ne cancella i dati precedenti**, lasciando intatto `ddl_05`. Non inserire `USE` o `CREATE DATABASE` nella soluzione: il database di destinazione è selezionato dal client. I test usano connessioni con modalità SQL rigorosa. Un errore di applicazione o un controllo fallito produce un codice di uscita diverso da zero.

**Stato iniziale previsto:** con `solution.sql` vuoto i test falliscono perché mancano le definizioni o le modifiche richieste. Non completare `schema.sql` o i test per farli passare: modifica la soluzione. L’obiettivo è ottenere tutti i controlli `PASS`.

### Arresto e ripristino

```bash
docker compose down
```

L’arresto conserva i dati nel volume dedicato al progetto `school-ddl-05`. Lo schema iniziale viene caricato **solo su un volume nuovo**. Per eliminare tutti i dati di questo esercizio, compresi quelli delle prove manuali, e ripartire:

```bash
docker compose --profile tools down -v
docker compose up -d --wait
```

`down -v` cancella definitivamente il volume; non elimina i file SQL. `cleanup.sql` serve invece a eliminare solo `ddl_05` e `ddl_05_test` se importati in un server condiviso o locale: non è un passaggio necessario con Docker.

### Ambiente online

Puoi usare un SQL fiddle con **MySQL 8.4** o una versione compatibile, almeno 8.0.16 per i `CHECK`. Carica prima `schema.sql`, poi la tua soluzione nello spazio destinato alla definizione dello schema. Usa il database assegnato dal servizio, senza crearne un altro. Le verifiche automatiche shell sono disponibili con Docker; online puoi riprodurre le istruzioni SQL dei test. Un servizio MariaDB, PostgreSQL o Oracle non è equivalente all’ambiente richiesto.

[Torna al percorso DDL](../README.md).
