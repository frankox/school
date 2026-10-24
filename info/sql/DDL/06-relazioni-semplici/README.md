# 06 — Relazioni semplici

## Consegna

Un ambulatorio deve assegnare ogni appuntamento a un medico. Ripetere nome e recapiti del medico in ogni appuntamento causerebbe aggiornamenti incoerenti. I medici vengono registrati una volta e gli appuntamenti li referenziano.

Ogni appuntamento deve avere un numero identificativo, una data con ora e il nome del paziente, e deve essere assegnato a un medico già registrato. Lo stesso medico può ricevere più appuntamenti; un medico appena inserito può invece non averne ancora nessuno. Non devono essere accettati appuntamenti senza medico o assegnati a un medico inesistente. Le informazioni anagrafiche dei medici restano nell’archivio già disponibile.

### Dati e comportamenti richiesti

L’archivio `doctors` è già fornito. Prepara `appointments` con `id` per il numero intero identificativo dell’appuntamento, `doctor_id` per il medico assegnato, `starts_at` per data e ora e `patient_name` per il nome del paziente. Tutti i campi sono obbligatori.

Scegli tipi e dimensioni compatibili con i dati e con l’archivio esistente. Il database deve garantire le regole di assegnazione descritte sopra. In `design.md` descrivi cardinalità e obbligatorietà e proponi un caso in cui un profilo riservato 1:1 sarebbe utile e uno in cui preferiresti una sola tabella.

## Prima di iniziare

[Modificare la struttura](../05-modificare-la-struttura/README.md). Gli esercizi sono indipendenti: non serve aver avviato i database precedenti.

## Concetti e sintassi MySQL

In una relazione **1:n**, una riga padre può essere collegata a molte righe figlie. La chiave esterna si trova sul lato n. Il valore deve corrispondere a una chiave della tabella referenziata; in questo percorso usiamo chiavi primarie. Tipi e, per gli interi, segno devono essere compatibili.

```sql
CREATE TABLE esempio_figli (
    id INT PRIMARY KEY,
    padre_id INT NOT NULL,
    CONSTRAINT esempio_padre_fk FOREIGN KEY (padre_id) REFERENCES esempio_padri(id)
);
```

Si crea prima il padre. `NOT NULL` rende obbligatorio il collegamento per il figlio; non impone che ogni padre abbia figli.

Una relazione **1:1** si può ottenere aggiungendo `UNIQUE` alla chiave esterna: ogni padre ha al massimo un figlio. Può essere utile per separare dati riservati con permessi distinti, dati opzionali voluminosi o informazioni con un ciclo di vita diverso. Se due gruppi di dati hanno sempre lo stesso ciclo di vita e gli stessi accessi, una sola tabella spesso è più semplice. `UNIQUE` non garantisce che ogni padre abbia necessariamente un figlio. Le relazioni molti-a-molti arriveranno più avanti.

## File forniti e lavoro da completare

- `schema.sql`: setup già fornito; non modificarlo per risolvere l’esercizio.
- `solution.sql`: file da completare con le istruzioni DDL richieste.
- `design.md`: spazio per le motivazioni richieste, da discutere anche quando i test passano.
- `tests/cases.sh`: verifiche già fornite, con descrizioni in inglese; gli inserimenti e le query sono strumenti di controllo, non DML o DQL da implementare.
- `compose.yaml`: database, client e test già disponibili nei container.
- `cleanup.sql`: pulizia dei database dell’esercizio, se usi un server condiviso.

**Input:** il setup e i requisiti della consegna. **Output:** lo schema creato o modificato da `solution.sql`. Rispetta i nomi e i comportamenti richiesti. La scelta dei tipi SQL e delle dimensioni spetta a te: deve essere adeguata ai dati descritti e compatibile con le strutture già fornite. Non è richiesta una dimensione identica a quella di un esempio di sintassi. I nomi dei vincoli sono liberi, salvo quelli esplicitamente indicati. Usa il motore predefinito InnoDB e non disabilitare controlli o vincoli. I test non sostituiscono il controllo manuale di ogni requisito e delle motivazioni.

## Eseguire con Docker

Apri un terminale **del computer** nella cartella `info/sql/DDL/06-relazioni-semplici`. Serve solo Docker con Compose; non occorrono MySQL, npm o altri strumenti locali. Se usi il Dev Container di `school`, i comandi Docker vanno eseguiti fuori dal suo terminale. Vedi la [guida Docker](../../../../DOCKER.md) e la [guida VS Code](../../../../VS-CODE.md).

```bash
docker compose up -d --wait
docker compose exec -e MYSQL_PWD=school_root_dev db mysql -u root ddl_06
```

Usiamo **MySQL 8.4.7**, database manuale `ddl_06` e utente didattico `root` con password `school_root_dev`. Non è pubblicata alcuna porta: il client è nel container. Al primo avvio `schema.sql` viene caricato automaticamente: fornisce lo stato iniziale, non la soluzione.

Nel client puoi eseguire:

```sql
SOURCE /work/solution.sql;
SHOW CREATE TABLE appointments; SELECT * FROM doctors; SELECT * FROM appointments;
```

`SOURCE` legge le modifiche dal file montato: non occorre ricostruire un’immagine. Termina le istruzioni SQL con `;` e scrivi `exit` per uscire. Puoi eseguire manualmente inserimenti e modifiche usando il client; i file `tests/cases.sh` contengono esempi già pronti, inclusi casi volutamente non validi. Se la soluzione crea oggetti già esistenti o applica una migrazione, riparti dallo stato iniziale prima di ricaricarla: non è richiesto che sia eseguibile due volte sullo stesso schema.

### Verifica automatica

```bash
docker compose run --rm test
```

Il comando prepara `ddl_06_test`, applica `schema.sql` e `solution.sql`, quindi verifica struttura e comportamenti. **Ogni esecuzione ricrea solo il database di test e ne cancella i dati precedenti**, lasciando intatto `ddl_06`. Non inserire `USE` o `CREATE DATABASE` nella soluzione: il database di destinazione è selezionato dal client. I test usano connessioni con modalità SQL rigorosa. Un errore di applicazione o un controllo fallito produce un codice di uscita diverso da zero.

**Stato iniziale previsto:** con `solution.sql` vuoto i test falliscono perché mancano le definizioni o le modifiche richieste. Non completare `schema.sql` o i test per farli passare: modifica la soluzione. L’obiettivo è ottenere tutti i controlli `PASS` e spiegare le scelte richieste in `design.md`.

### Arresto e ripristino

```bash
docker compose down
```

L’arresto conserva i dati nel volume dedicato al progetto `school-ddl-06`. Lo schema iniziale viene caricato **solo su un volume nuovo**. Per eliminare tutti i dati di questo esercizio, compresi quelli delle prove manuali, e ripartire:

```bash
docker compose --profile tools down -v
docker compose up -d --wait
```

`down -v` cancella definitivamente il volume; non elimina i file SQL. `cleanup.sql` serve invece a eliminare solo `ddl_06` e `ddl_06_test` se importati in un server condiviso o locale: non è un passaggio necessario con Docker.

### Ambiente online

Puoi usare un SQL fiddle con **MySQL 8.4** o una versione compatibile, almeno 8.0.16 per i `CHECK`. Carica prima `schema.sql`, poi la tua soluzione nello spazio destinato alla definizione dello schema. Usa il database assegnato dal servizio, senza crearne un altro. Le verifiche automatiche shell sono disponibili con Docker; online puoi riprodurre le istruzioni SQL dei test. Un servizio MariaDB, PostgreSQL o Oracle non è equivalente all’ambiente richiesto.

[Torna al percorso DDL](../README.md).
