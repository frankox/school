# 10 — Riferimenti a chiavi composite

## Consegna

Un sistema logistico identifica uno scaffale con il magazzino e il codice locale. Lo scaffale A può esistere in più magazzini. Un pacco deve essere assegnato a una coppia realmente esistente, non a due valori singolarmente validi ma appartenenti a scaffali diversi.

Per ogni pacco bisogna conservare un numero identificativo, un’etichetta descrittiva e la posizione completa: magazzino e codice dello scaffale. Tutte queste informazioni sono obbligatorie. Più pacchi possono occupare lo stesso scaffale, ma la posizione assegnata deve esistere nell’elenco già disponibile. Finché uno scaffale contiene pacchi, non deve essere possibile eliminarlo o cambiarne i dati identificativi.

### Dati e comportamenti richiesti

L’archivio `bins` è già fornito: ogni scaffale è identificato dal magazzino e dal proprio codice locale. Prepara `parcels` con `id` per il numero intero identificativo del pacco, `warehouse_id` per il magazzino, `bin_code` per il codice testuale dello scaffale e `label` per l’etichetta descrittiva. Tutti i campi sono obbligatori.

Scegli tipi e dimensioni compatibili con l’archivio esistente. Il database deve verificare la posizione completa e applicare le regole descritte sopra: per esempio, la posizione `(1, 'B01')` deve essere rifiutata anche se il magazzino 1 e il codice B01 esistono separatamente.

## Prima di iniziare

[Relazioni molti-a-molti](../09-relazioni-molti-a-molti/README.md). Gli esercizi sono indipendenti: non serve aver avviato i database precedenti.

## Concetti e sintassi MySQL

Una chiave esterna composta controlla una combinazione completa:

```sql
FOREIGN KEY (sede_id, posizione)
    REFERENCES esempio_posizioni(sede_id, posizione)
```

Numero e ordine delle colonne devono corrispondere alla chiave referenziata; i tipi devono essere compatibili. Due chiavi esterne separate non esprimono la stessa regola. In MySQL, se una colonna della chiave esterna composta è `NULL`, il riferimento può non essere verificato: rendi tutte le colonne `NOT NULL` quando il collegamento è obbligatorio. Per evitare dipendenze da comportamenti non standard, referenzia una chiave primaria o univoca completa.

## File forniti e lavoro da completare

- `schema.sql`: setup già fornito; non modificarlo per risolvere l’esercizio.
- `solution.sql`: file da completare con le istruzioni DDL richieste.
- `tests/cases.sh`: verifiche già fornite, con descrizioni in inglese; gli inserimenti e le query sono strumenti di controllo, non DML o DQL da implementare.
- `compose.yaml`: database, client e test già disponibili nei container.
- `cleanup.sql`: pulizia dei database dell’esercizio, se usi un server condiviso.

**Input:** il setup e i requisiti della consegna. **Output:** lo schema creato o modificato da `solution.sql`. Rispetta i nomi e i comportamenti richiesti. La scelta dei tipi SQL e delle dimensioni spetta a te: deve essere adeguata ai dati descritti e compatibile con le strutture già fornite. Non è richiesta una dimensione identica a quella di un esempio di sintassi. I nomi dei vincoli sono liberi, salvo quelli esplicitamente indicati. Usa il motore predefinito InnoDB e non disabilitare controlli o vincoli. I test non sostituiscono il controllo manuale di ogni requisito e delle motivazioni.

## Eseguire con Docker

Apri un terminale **del computer** nella cartella `info/sql/DDL/10-riferimenti-a-chiavi-composite`. Serve solo Docker con Compose; non occorrono MySQL, npm o altri strumenti locali. Se usi il Dev Container di `school`, i comandi Docker vanno eseguiti fuori dal suo terminale. Vedi la [guida Docker](../../../../DOCKER.md) e la [guida VS Code](../../../../VS-CODE.md).

```bash
docker compose up -d --wait
docker compose exec -e MYSQL_PWD=school_root_dev db mysql -u root ddl_10
```

Usiamo **MySQL 8.4.7**, database manuale `ddl_10` e utente didattico `root` con password `school_root_dev`. Non è pubblicata alcuna porta: il client è nel container. Al primo avvio `schema.sql` viene caricato automaticamente: fornisce lo stato iniziale, non la soluzione.

Nel client puoi eseguire:

```sql
SOURCE /work/solution.sql;
SHOW CREATE TABLE parcels; SELECT * FROM bins; SELECT * FROM parcels;
```

`SOURCE` legge le modifiche dal file montato: non occorre ricostruire un’immagine. Termina le istruzioni SQL con `;` e scrivi `exit` per uscire. Puoi eseguire manualmente inserimenti e modifiche usando il client; i file `tests/cases.sh` contengono esempi già pronti, inclusi casi volutamente non validi. Se la soluzione crea oggetti già esistenti o applica una migrazione, riparti dallo stato iniziale prima di ricaricarla: non è richiesto che sia eseguibile due volte sullo stesso schema.

### Verifica automatica

```bash
docker compose run --rm test
```

Il comando prepara `ddl_10_test`, applica `schema.sql` e `solution.sql`, quindi verifica struttura e comportamenti. **Ogni esecuzione ricrea solo il database di test e ne cancella i dati precedenti**, lasciando intatto `ddl_10`. Non inserire `USE` o `CREATE DATABASE` nella soluzione: il database di destinazione è selezionato dal client. I test usano connessioni con modalità SQL rigorosa. Un errore di applicazione o un controllo fallito produce un codice di uscita diverso da zero.

**Stato iniziale previsto:** con `solution.sql` vuoto i test falliscono perché mancano le definizioni o le modifiche richieste. Non completare `schema.sql` o i test per farli passare: modifica la soluzione. L’obiettivo è ottenere tutti i controlli `PASS`.

### Arresto e ripristino

```bash
docker compose down
```

L’arresto conserva i dati nel volume dedicato al progetto `school-ddl-10`. Lo schema iniziale viene caricato **solo su un volume nuovo**. Per eliminare tutti i dati di questo esercizio, compresi quelli delle prove manuali, e ripartire:

```bash
docker compose --profile tools down -v
docker compose up -d --wait
```

`down -v` cancella definitivamente il volume; non elimina i file SQL. `cleanup.sql` serve invece a eliminare solo `ddl_10` e `ddl_10_test` se importati in un server condiviso o locale: non è un passaggio necessario con Docker.

### Ambiente online

Puoi usare un SQL fiddle con **MySQL 8.4** o una versione compatibile, almeno 8.0.16 per i `CHECK`. Carica prima `schema.sql`, poi la tua soluzione nello spazio destinato alla definizione dello schema. Usa il database assegnato dal servizio, senza crearne un altro. Le verifiche automatiche shell sono disponibili con Docker; online puoi riprodurre le istruzioni SQL dei test. Un servizio MariaDB, PostgreSQL o Oracle non è equivalente all’ambiente richiesto.

[Torna al percorso DDL](../README.md).
