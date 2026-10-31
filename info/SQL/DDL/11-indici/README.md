# 11 — Indici

## Consegna

Un negozio online consulta spesso gli ordini di un cliente ordinati per data e cerca singoli ordini dal riferimento pubblico. La tabella cresce e le ricerche richiedono strutture di accesso adatte, senza vietare acquisti ripetuti dello stesso cliente nello stesso giorno.

Il sistema deve facilitare due consultazioni: ritrovare gli ordini di un cliente in ordine cronologico e individuare un ordine dal suo riferimento pubblico, che non può essere condiviso da altri ordini. Un cliente può però effettuare più ordini nello stesso istante. Va conservato tutto lo storico esistente e va rimossa la struttura di ricerca basata soltanto sullo stato, poco utile per le consultazioni descritte. Gli strumenti per accelerare le ricerche devono riflettere queste necessità.

### Dati e comportamenti richiesti

La tabella `orders` è già fornita: `customer_id` indica il cliente, `created_at` la data e l’ora dell’ordine e `reference` il riferimento pubblico. È presente anche `idx_status`, una struttura di ricerca basata solo sullo stato, da rimuovere.

Progetta gli indici per le consultazioni descritte sopra, garantendo l’unicità del riferimento pubblico e permettendo più ordini dello stesso cliente nello stesso istante. Conserva righe e identificazione degli ordini. In `design.md` motiva l’ordine delle colonne scelto e spiega perché non aggiungere un indice a ogni colonna. Prova il comando `EXPLAIN` fornito senza aspettarti necessariamente l’uso dell’indice su soli due ordini.

## Prima di iniziare

[Riferimenti a chiavi composite](../10-riferimenti-a-chiavi-composite/README.md). Gli esercizi sono indipendenti: non serve aver avviato i database precedenti.

## Concetti e sintassi MySQL

Un indice accelera determinate ricerche ma occupa spazio e va aggiornato durante le scritture. Un indice ordinario non impone unicità; `UNIQUE` aggiunge anche una regola sui dati. Le chiavi primarie e univoche hanno già indici: non duplicarli senza ragione.

```sql
CREATE INDEX esempio_cliente_data ON esempio_ordini(cliente_id, creata_il);
CREATE UNIQUE INDEX esempio_riferimento ON esempio_ordini(riferimento);
DROP INDEX esempio_obsoleto ON esempio_ordini;
SHOW INDEX FROM esempio_ordini;
```

Per un indice composto l’ordine conta: `(cliente_id, creata_il)` serve normalmente alle ricerche sul cliente e sulla combinazione cliente-data, non equivale a `(creata_il, cliente_id)`. La scelta concreta dipende dalle query. `EXPLAIN SELECT ...` mostra il piano previsto: su tabelle minuscole il DBMS può preferire una scansione anche quando l’indice esiste; i test verificano struttura e vincoli, non tempi arbitrari.

## File forniti e lavoro da completare

- `schema.sql`: setup già fornito; non modificarlo per risolvere l’esercizio.
- `solution.sql`: file da completare con le istruzioni DDL richieste.
- `design.md`: spazio per le motivazioni richieste, da discutere anche quando i test passano.
- `tests/cases.sh`: verifiche già fornite, con descrizioni in inglese; gli inserimenti e le query sono strumenti di controllo, non DML o DQL da implementare.
- `compose.yaml`: database, client e test già disponibili nei container.
- `cleanup.sql`: pulizia dei database dell’esercizio, se usi un server condiviso.

**Input:** il setup e i requisiti della consegna. **Output:** lo schema creato o modificato da `solution.sql`. Rispetta i nomi e i comportamenti richiesti. La scelta dei tipi SQL e delle dimensioni spetta a te: deve essere adeguata ai dati descritti e compatibile con le strutture già fornite. Non è richiesta una dimensione identica a quella di un esempio di sintassi. I nomi dei vincoli sono liberi, salvo quelli esplicitamente indicati. Usa il motore predefinito InnoDB e non disabilitare controlli o vincoli. I test non sostituiscono il controllo manuale di ogni requisito e delle motivazioni.

## Eseguire con Docker

Apri un terminale **del computer** nella cartella `info/SQL/DDL/11-indici`. Serve solo Docker con Compose; non occorrono MySQL, npm o altri strumenti locali. Se usi il Dev Container di `school`, i comandi Docker vanno eseguiti fuori dal suo terminale. Vedi la [guida Docker](../../../../DOCKER.md) e la [guida VS Code](../../../../VS-CODE.md).

```bash
docker compose up -d --wait
docker compose exec -e MYSQL_PWD=school_root_dev db mysql -u root ddl_11
```

Usiamo **MySQL 8.4.7**, database manuale `ddl_11` e utente didattico `root` con password `school_root_dev`. Non è pubblicata alcuna porta: il client è nel container. Al primo avvio `schema.sql` viene caricato automaticamente: fornisce lo stato iniziale, non la soluzione.

Nel client puoi eseguire:

```sql
SOURCE /work/solution.sql;
SHOW INDEX FROM orders; EXPLAIN SELECT * FROM orders WHERE customer_id=10 ORDER BY created_at;
```

`SOURCE` legge le modifiche dal file montato: non occorre ricostruire un’immagine. Termina le istruzioni SQL con `;` e scrivi `exit` per uscire. Puoi eseguire manualmente inserimenti e modifiche usando il client; i file `tests/cases.sh` contengono esempi già pronti, inclusi casi volutamente non validi. Se la soluzione crea oggetti già esistenti o applica una migrazione, riparti dallo stato iniziale prima di ricaricarla: non è richiesto che sia eseguibile due volte sullo stesso schema.

### Verifica automatica

```bash
docker compose run --rm test
```

Il comando prepara `ddl_11_test`, applica `schema.sql` e `solution.sql`, quindi verifica struttura e comportamenti. **Ogni esecuzione ricrea solo il database di test e ne cancella i dati precedenti**, lasciando intatto `ddl_11`. Non inserire `USE` o `CREATE DATABASE` nella soluzione: il database di destinazione è selezionato dal client. I test usano connessioni con modalità SQL rigorosa. Un errore di applicazione o un controllo fallito produce un codice di uscita diverso da zero.

**Stato iniziale previsto:** con `solution.sql` vuoto i test falliscono perché mancano le definizioni o le modifiche richieste. Non completare `schema.sql` o i test per farli passare: modifica la soluzione. L’obiettivo è ottenere tutti i controlli `PASS` e spiegare le scelte richieste in `design.md`.

### Arresto e ripristino

```bash
docker compose down
```

L’arresto conserva i dati nel volume dedicato al progetto `school-ddl-11`. Lo schema iniziale viene caricato **solo su un volume nuovo**. Per eliminare tutti i dati di questo esercizio, compresi quelli delle prove manuali, e ripartire:

```bash
docker compose --profile tools down -v
docker compose up -d --wait
```

`down -v` cancella definitivamente il volume; non elimina i file SQL. `cleanup.sql` serve invece a eliminare solo `ddl_11` e `ddl_11_test` se importati in un server condiviso o locale: non è un passaggio necessario con Docker.

### Ambiente online

Puoi usare un SQL fiddle con **MySQL 8.4** o una versione compatibile, almeno 8.0.16 per i `CHECK`. Carica prima `schema.sql`, poi la tua soluzione nello spazio destinato alla definizione dello schema. Usa il database assegnato dal servizio, senza crearne un altro. Le verifiche automatiche shell sono disponibili con Docker; online puoi riprodurre le istruzioni SQL dei test. Un servizio MariaDB, PostgreSQL o Oracle non è equivalente all’ambiente richiesto.

[Torna al percorso DDL](../README.md).
