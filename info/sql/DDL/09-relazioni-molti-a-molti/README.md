# 09 — Relazioni molti-a-molti

## Consegna

Un centro di formazione iscrive più partecipanti a ogni corso e ogni partecipante può frequentare più corsi. Una lista di codici separati da virgole dentro una colonna impedirebbe al database di controllare correttamente i collegamenti.

Il centro dispone già degli elenchi di partecipanti e corsi. Ora deve registrare chi si iscrive a quale corso e in quale giorno, senza ammettere due iscrizioni della stessa persona allo stesso corso. Ogni iscrizione può ricevere successivamente un voto da zero a dieci, anche con decimali; prima della valutazione il voto resta assente. Non devono esistere iscrizioni a persone o corsi inesistenti, e non si devono cancellare partecipanti o corsi che hanno iscrizioni registrate.

### Requisiti tecnici

Sono fornite `participants` e `courses`. Crea `enrollments` con `participant_id INT`, `course_id INT`, `enrolled_on DATE NOT NULL`, `grade DECIMAL(4,2)` facoltativo e limitato tra 0 e 10 quando presente. La chiave primaria è `(participant_id, course_id)`; entrambe le chiavi esterne devono impedire la cancellazione di un padre con iscrizioni. In questo esempio un’iscrizione non può essere ripetuta allo stesso corso: eventuali edizioni future richiederebbero un modello diverso.

## Prima di iniziare

[Chiavi composite](../08-chiavi-composite/README.md). Gli esercizi sono indipendenti: non serve aver avviato i database precedenti.

## Concetti e sintassi MySQL

Una relazione **n:n** si rappresenta con una tabella associativa: due relazioni 1:n, una verso ogni entità. La coppia delle chiavi esterne può diventare la chiave primaria composta. Gli attributi che descrivono il collegamento, come data e risultato dell’iscrizione, appartengono alla tabella associativa.

```sql
CREATE TABLE esempio_associazioni (
    persona_id INT,
    attivita_id INT,
    registrata_il DATE NOT NULL,
    PRIMARY KEY (persona_id, attivita_id),
    FOREIGN KEY (persona_id) REFERENCES esempio_persone(id),
    FOREIGN KEY (attivita_id) REFERENCES esempio_attivita(id)
);
```

Un identificatore artificiale nella tabella associativa sarebbe possibile, ma da solo non impedirebbe di duplicare la stessa associazione: servirebbe comunque un vincolo univoco sulla coppia.

## File forniti e lavoro da completare

- `schema.sql`: setup già fornito; non modificarlo per risolvere l’esercizio.
- `solution.sql`: file da completare con le istruzioni DDL richieste.
- `tests/cases.sh`: verifiche già fornite, con descrizioni in inglese; gli inserimenti e le query sono strumenti di controllo, non DML o DQL da implementare.
- `compose.yaml`: database, client e test già disponibili nei container.
- `cleanup.sql`: pulizia dei database dell’esercizio, se usi un server condiviso.

**Input:** il setup e i requisiti della consegna. **Output:** lo schema creato o modificato da `solution.sql`. I nomi, i tipi, le dimensioni e i comportamenti richiesti fanno parte del contratto; i nomi dei vincoli possono essere scelti liberamente, salvo quelli esplicitamente indicati. Usa il motore predefinito InnoDB e non disabilitare controlli o vincoli. I test non sostituiscono il controllo manuale di ogni requisito e delle motivazioni.

## Eseguire con Docker

Apri un terminale **del computer** nella cartella `info/sql/DDL/09-relazioni-molti-a-molti`. Serve solo Docker con Compose; non occorrono MySQL, npm o altri strumenti locali. Se usi il Dev Container di `school`, i comandi Docker vanno eseguiti fuori dal suo terminale. Vedi la [guida Docker](../../../../DOCKER.md) e la [guida VS Code](../../../../VS-CODE.md).

```bash
docker compose up -d --wait
docker compose exec -e MYSQL_PWD=school_root_dev db mysql -u root ddl_09
```

Usiamo **MySQL 8.4.7**, database manuale `ddl_09` e utente didattico `root` con password `school_root_dev`. Non è pubblicata alcuna porta: il client è nel container. Al primo avvio `schema.sql` viene caricato automaticamente: fornisce lo stato iniziale, non la soluzione.

Nel client puoi eseguire:

```sql
SOURCE /work/solution.sql;
SHOW CREATE TABLE enrollments; SELECT * FROM enrollments;
```

`SOURCE` legge le modifiche dal file montato: non occorre ricostruire un’immagine. Termina le istruzioni SQL con `;` e scrivi `exit` per uscire. Puoi eseguire manualmente inserimenti e modifiche usando il client; i file `tests/cases.sh` contengono esempi già pronti, inclusi casi volutamente non validi. Se la soluzione crea oggetti già esistenti o applica una migrazione, riparti dallo stato iniziale prima di ricaricarla: non è richiesto che sia eseguibile due volte sullo stesso schema.

### Verifica automatica

```bash
docker compose run --rm test
```

Il comando prepara `ddl_09_test`, applica `schema.sql` e `solution.sql`, quindi verifica struttura e comportamenti. **Ogni esecuzione ricrea solo il database di test e ne cancella i dati precedenti**, lasciando intatto `ddl_09`. Non inserire `USE` o `CREATE DATABASE` nella soluzione: il database di destinazione è selezionato dal client. I test usano connessioni con modalità SQL rigorosa. Un errore di applicazione o un controllo fallito produce un codice di uscita diverso da zero.

**Stato iniziale previsto:** con `solution.sql` vuoto i test falliscono perché mancano le definizioni o le modifiche richieste. Non completare `schema.sql` o i test per farli passare: modifica la soluzione. L’obiettivo è ottenere tutti i controlli `PASS`.

### Arresto e ripristino

```bash
docker compose down
```

L’arresto conserva i dati nel volume dedicato al progetto `school-ddl-09`. Lo schema iniziale viene caricato **solo su un volume nuovo**. Per eliminare tutti i dati di questo esercizio, compresi quelli delle prove manuali, e ripartire:

```bash
docker compose --profile tools down -v
docker compose up -d --wait
```

`down -v` cancella definitivamente il volume; non elimina i file SQL. `cleanup.sql` serve invece a eliminare solo `ddl_09` e `ddl_09_test` se importati in un server condiviso o locale: non è un passaggio necessario con Docker.

### Ambiente online

Puoi usare un SQL fiddle con **MySQL 8.4** o una versione compatibile, almeno 8.0.16 per i `CHECK`. Carica prima `schema.sql`, poi la tua soluzione nello spazio destinato alla definizione dello schema. Usa il database assegnato dal servizio, senza crearne un altro. Le verifiche automatiche shell sono disponibili con Docker; online puoi riprodurre le istruzioni SQL dei test. Un servizio MariaDB, PostgreSQL o Oracle non è equivalente all’ambiente richiesto.

[Torna al percorso DDL](../README.md).
