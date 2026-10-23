# 12 — Progetto riepilogativo: centro di formazione

## Consegna

Un centro di formazione apre iscrizioni a corsi tenuti in aule diverse. La segreteria vuole evitare iscrizioni duplicate, riferimenti a corsi inesistenti e importi negativi. Vuole anche consultare rapidamente gli iscritti di un corso in ordine di data. Devi progettare lo schema partendo dai requisiti, non da una lista di comandi da copiare.

Per ogni aula servono un nome distinto e una capienza positiva. Per ogni corso occorrono un codice distinto, un titolo, un’aula esistente, le date di inizio e fine e una quota di partecipazione: la fine non può precedere l’inizio e la quota non può essere negativa; se non specificata, il corso è gratuito. Di ogni partecipante servono nome completo ed email personale non condivisa, mentre il telefono è facoltativo. Aule, corsi e partecipanti ricevono un numero interno automatico. Le iscrizioni collegano persone e corsi, registrano il giorno di adesione e non possono ripetersi per la stessa coppia. Non si devono eliminare o cambiare gli identificativi di elementi ancora referenziati; deve inoltre essere agevole consultare le iscrizioni di un corso per data. La capienza viene registrata, ma il controllo del numero totale degli iscritti richiederà strumenti ulteriori rispetto ai vincoli affrontati qui.

### Requisiti tecnici

Crea queste quattro tabelle, con tutti i campi obbligatori tranne `phone`:

- `rooms`: `id INT AUTO_INCREMENT PRIMARY KEY`, `name VARCHAR(50)` univoco, `capacity INT` positivo.
- `courses`: `id INT AUTO_INCREMENT PRIMARY KEY`, `code VARCHAR(20)` univoco, `title VARCHAR(100)`, `room_id INT` collegato a `rooms(id)`, `starts_on DATE`, `ends_on DATE` non precedente all’inizio, `fee DECIMAL(8,2)` non negativo e predefinito a 0.
- `participants`: `id INT AUTO_INCREMENT PRIMARY KEY`, `email VARCHAR(150)` univoca, `full_name VARCHAR(100)`, `phone VARCHAR(30)` facoltativo.
- `enrollments`: `course_id INT`, `participant_id INT`, `enrolled_on DATE`, chiave primaria sulla coppia `(course_id, participant_id)` e chiavi esterne alle due tabelle.

Blocca cancellazioni e aggiornamenti delle chiavi padre se sono referenziate. Aggiungi un indice non univoco su `enrollments(course_id, enrolled_on)`. Motiva in `design.md` le chiavi artificiali, la tabella associativa, la politica di cancellazione e il limite sul controllo della capienza. La valutazione delle motivazioni è manuale; i test verificano lo schema e i comportamenti, non il testo.

## Prima di iniziare

[Indici](../11-indici/README.md). Gli esercizi sono indipendenti: non serve aver avviato i database precedenti.

## Concetti e sintassi MySQL

Qui combini tipi, vincoli, relazioni e indici già incontrati. Parti dalle entità, individua gli identificatori, poi le cardinalità e gli attributi delle associazioni. Distingui regole verificabili sulla singola riga da regole che richiedono di contare altre righe: un semplice `CHECK` non può garantire, per esempio, che il numero degli iscritti non superi i posti disponibili. Non confondere il numero massimo dichiarato per un corso con il controllo automatico delle iscrizioni.

Scrivi in `design.md` le scelte e i limiti. Non servono nuovi comandi: usa le sintassi degli esercizi precedenti.

## File forniti e lavoro da completare

- `solution.sql`: file da completare con le istruzioni DDL richieste.
- `design.md`: spazio per le motivazioni richieste, da discutere anche quando i test passano.
- `tests/cases.sh`: verifiche già fornite, con descrizioni in inglese; gli inserimenti e le query sono strumenti di controllo, non DML o DQL da implementare.
- `compose.yaml`: database, client e test già disponibili nei container.
- `cleanup.sql`: pulizia dei database dell’esercizio, se usi un server condiviso.

**Input:** un database vuoto e i requisiti della consegna. **Output:** lo schema creato o modificato da `solution.sql`. I nomi, i tipi, le dimensioni e i comportamenti richiesti fanno parte del contratto; i nomi dei vincoli possono essere scelti liberamente, salvo quelli esplicitamente indicati. Usa il motore predefinito InnoDB e non disabilitare controlli o vincoli. I test non sostituiscono il controllo manuale di ogni requisito e delle motivazioni.

## Eseguire con Docker

Apri un terminale **del computer** nella cartella `info/sql/DDL/12-progetto-riepilogativo`. Serve solo Docker con Compose; non occorrono MySQL, npm o altri strumenti locali. Se usi il Dev Container di `school`, i comandi Docker vanno eseguiti fuori dal suo terminale. Vedi la [guida Docker](../../../../DOCKER.md) e la [guida VS Code](../../../../VS-CODE.md).

```bash
docker compose up -d --wait
docker compose exec -e MYSQL_PWD=school_root_dev db mysql -u root ddl_12
```

Usiamo **MySQL 8.4.7**, database manuale `ddl_12` e utente didattico `root` con password `school_root_dev`. Non è pubblicata alcuna porta: il client è nel container. Al primo avvio viene preparato un database vuoto: tutte le tabelle devono essere definite in `solution.sql`.

Nel client puoi eseguire:

```sql
SOURCE /work/solution.sql;
SHOW TABLES; SHOW CREATE TABLE enrollments; SELECT * FROM courses;
```

`SOURCE` legge le modifiche dal file montato: non occorre ricostruire un’immagine. Termina le istruzioni SQL con `;` e scrivi `exit` per uscire. Puoi eseguire manualmente inserimenti e modifiche usando il client; i file `tests/cases.sh` contengono esempi già pronti, inclusi casi volutamente non validi. Se la soluzione crea oggetti già esistenti o applica una migrazione, riparti dallo stato iniziale prima di ricaricarla: non è richiesto che sia eseguibile due volte sullo stesso schema.

### Verifica automatica

```bash
docker compose run --rm test
```

Il comando prepara `ddl_12_test`, esegue `solution.sql`, quindi verifica struttura e comportamenti. **Ogni esecuzione ricrea solo il database di test e ne cancella i dati precedenti**, lasciando intatto `ddl_12`. Non inserire `USE` o `CREATE DATABASE` nella soluzione: il database di destinazione è selezionato dal client. I test usano connessioni con modalità SQL rigorosa. Un errore di applicazione o un controllo fallito produce un codice di uscita diverso da zero.

**Stato iniziale previsto:** con `solution.sql` vuoto i test falliscono perché mancano le definizioni o le modifiche richieste. Non modificare i test per farli passare: completa la soluzione. L’obiettivo è ottenere tutti i controlli `PASS` e spiegare le scelte richieste in `design.md`.

### Arresto e ripristino

```bash
docker compose down
```

L’arresto conserva i dati nel volume dedicato al progetto `school-ddl-12`. Il database vuoto viene preparato **solo su un volume nuovo**. Per eliminare tutti i dati di questo esercizio, compresi quelli delle prove manuali, e ripartire:

```bash
docker compose --profile tools down -v
docker compose up -d --wait
```

`down -v` cancella definitivamente il volume; non elimina i file SQL. `cleanup.sql` serve invece a eliminare solo `ddl_12` e `ddl_12_test` se importati in un server condiviso o locale: non è un passaggio necessario con Docker.

### Ambiente online

Puoi usare un SQL fiddle con **MySQL 8.4** o una versione compatibile, almeno 8.0.16 per i `CHECK`. Carica la tua soluzione nello spazio destinato alla definizione dello schema: non è previsto un setup iniziale. Usa il database assegnato dal servizio, senza crearne un altro. Le verifiche automatiche shell sono disponibili con Docker; online puoi riprodurre le istruzioni SQL dei test. Un servizio MariaDB, PostgreSQL o Oracle non è equivalente all’ambiente richiesto.

[Torna al percorso DDL](../README.md).
