# Fondamenti SQL — interrogare e modificare dati

Questo percorso si svolge **senza PHP**. Si lavora direttamente in MariaDB per imparare a leggere e modificare dati, osservare una relazione 1:n e capire gli effetti delle query. Può essere svolto da solo oppure prima degli esercizi API.

**Per le attività di questo laboratorio, XAMPP o un [SQL fiddle online](../fiddle/README.md) sono alternative equivalenti; puoi usare anche Docker.** L'equivalenza riguarda le query e i risultati attesi usando lo stesso schema e un DBMS compatibile. Cambiano la preparazione e la conservazione dei dati. Con Docker si usa **MariaDB 11.8**; online scegli MariaDB 11.8 come spiegato nella guida. Non è necessario installare XAMPP se scegli Docker o l'ambiente online.

## Database e dati forniti

Il file [`schema.sql`](schema.sql) prepara il database `school_lab`, le tabelle `classes` e `students`, due classi e tre studenti iniziali.

`classes.id` è la chiave primaria di una classe. `students.class_id` è una chiave esterna: dice a quale classe appartiene lo studente. Una classe può avere molti studenti; ogni studente di questo esempio appartiene a una sola classe (**1:n**). Non ci sono ancora relazioni n:n.

Lo script contiene `CREATE DATABASE` (crea il contenitore), `USE` (sceglie il database), `CREATE TABLE` (definisce colonne e vincoli) e `INSERT` (aggiunge righe iniziali). Se lo importate di nuovo, le tabelle vengono ricreate e i dati inseriti da voi nel laboratorio si perdono.

Scegliete **uno** dei tre ambienti seguenti, poi svolgete la stessa sezione [Leggere e modificare i dati](#leggere-e-modificare-i-dati).

## Con XAMPP

### Preparare il database

Nel pannello XAMPP avviate **MySQL** e **Apache**; Apache serve qui per aprire phpMyAdmin. Andate su [phpMyAdmin locale](http://localhost/phpmyadmin/), dalla pagina iniziale scegliete **Importa**, selezionate `schema.sql` e premete **Esegui**. Comparirà `school_lab` con due tabelle.

### Scrivere query e leggere i risultati

1. Selezionate **school_lab** nel menu a sinistra di phpMyAdmin.
2. Aprite la scheda **SQL**, scrivete una query e premete **Esegui**. Per leggere i dati iniziali potete usare:

   ```sql
   SELECT * FROM classes;
   SELECT * FROM students;
   ```

3. I risultati compaiono sotto l'editor SQL: devono esserci **due classi e tre studenti**. La scheda **Sfoglia** di una tabella permette di vederne le righe anche senza scrivere una query.
4. Dopo una `INSERT`, `UPDATE` o `DELETE`, eseguite di nuovo una `SELECT` per osservare il risultato. Scrivete le query nella scheda SQL, non nella barra degli indirizzi del browser.

### Pulizia con XAMPP

Quando avete finito, importate [`cleanup.sql`](cleanup.sql) dalla pagina iniziale di phpMyAdmin: esegue `DROP DATABASE IF EXISTS school_lab` e cancella definitivamente solo questo laboratorio. Potete ripartire importando `schema.sql`. Per arrestare i servizi locali usate i pulsanti **Stop** di MySQL e Apache nel pannello XAMPP.

## Con Docker (solo Docker richiesto)

Aprire un terminale **del computer** nella cartella `info/SQL/fondamenti-sql`, quella che contiene `compose.yaml`. Se usate il Dev Container di `school`, questi comandi vanno eseguiti fuori dal suo terminale. Non servono MariaDB o un client SQL installati sul computer. Vedi la [guida Docker](../../../DOCKER.md) e la [guida VS Code](../../../VS-CODE.md).

### Avviare il database e phpMyAdmin

```bash
docker compose up -d --wait
```

Il servizio `db` esegue **MariaDB 11.8**; `admin` offre phpMyAdmin. Lo schema e le tre righe iniziali vengono caricati **solo quando il volume del database è nuovo**. Se il volume esiste già, ritrovate le modifiche delle sessioni precedenti. Il progetto Compose si chiama `school-fondamenti-sql` e usa il proprio volume `db_data`.

### Opzione 1: query dal terminale

Entrate nel client MariaDB già disponibile nel container:

```bash
docker compose exec db mariadb -u school_user -pschool_dev school_lab
```

Le credenziali didattiche sono utente **`school_user`**, password **`school_dev`**, database **`school_lab`**. Quando compare il prompt `MariaDB [school_lab]>`, scrivete SQL, per esempio:

```sql
SHOW TABLES;
DESCRIBE students;
SELECT * FROM classes;
SELECT * FROM students;
```

`SHOW TABLES` elenca le tabelle, `DESCRIBE` mostra le colonne e `SELECT` restituisce le righe. I risultati compaiono direttamente nel terminale. Terminate ogni istruzione con **`;`**: se il client mostra `->`, sta aspettando che completiate l'istruzione. Per uscire scrivete `exit`.

Una volta entrati nel client potete eseguire tutte le query del laboratorio, comprese `INSERT`, `UPDATE`, `DELETE` e `JOIN`. Dopo una modifica, usate una `SELECT` per leggere lo stato aggiornato. **I comandi `docker compose` si scrivono nel terminale del computer; le query SQL si scrivono nel client MariaDB.**

Per una singola query senza entrare nel client interattivo potete anche usare:

```bash
docker compose exec db mariadb -u school_user -pschool_dev school_lab -e "SELECT * FROM students;"
```

### Opzione 2: query dal browser

Con i servizi Docker avviati, aprite [phpMyAdmin Docker](http://127.0.0.1:8080/). Il Compose configura l'accesso a `db` con utente `school_user` e password `school_dev`.

Selezionate **school_lab**, aprite la scheda **SQL**, scrivete la query e premete **Esegui**: le istruzioni e i risultati sono gli stessi della sezione XAMPP. Con **Sfoglia** vedete direttamente le righe di una tabella. Non occorre importare manualmente `schema.sql` su un volume nuovo: Docker lo ha già caricato. phpMyAdmin usa la porta **8080** del computer; il database non pubblica una porta SQL verso il computer.

### Arresto e ripristino con Docker

```bash
docker compose down
```

Questo ferma i container e conserva le modifiche nel volume. Per cancellare tutti i dati di questo laboratorio e ripartire dallo schema e dalle righe iniziali:

```bash
docker compose down -v
docker compose up -d --wait
```

**`down -v` cancella definitivamente il volume e i suoi dati.** I file SQL del repository restano sul computer. `cleanup.sql` è utile per un database locale o condiviso; con Docker il reset completo indicato sopra è sufficiente.

## Online con un SQL fiddle

Seguite la [guida breve a SQL fiddle](../fiddle/README.md), che usa il servizio db<>fiddle. Per questo laboratorio scegliete **MariaDB 11.8**.

Copiate da `schema.sql` il contenuto **a partire da `DROP TABLE IF EXISTS students;` fino alla fine**, omettendo `CREATE DATABASE` e `USE`: online si lavora nel database assegnato dal servizio. Dopo lo schema, aggiungete `SELECT * FROM students;` ed eseguite lo script. Dovete ottenere i tre studenti iniziali.

Le query degli esercizi sono le stesse di XAMPP e Docker. **Ogni esecuzione online ricomincia da un database nuovo**: per osservare un aggiornamento o una cancellazione di una persona appena inserita, mantenete nello script anche la relativa `INSERT`, prima delle operazioni successive. Il vincolo di chiave esterna deve rifiutare `class_id = 99` anche online. Non occorre eseguire `cleanup.sql`; salvate sul computer il vostro SQL.

## Leggere e modificare i dati

Usate l'editor SQL di phpMyAdmin, il client MariaDB nel container oppure l'editor del [SQL fiddle](../fiddle/README.md), secondo l'ambiente scelto. In XAMPP e Docker eseguite le query una alla volta; online conservate nello script la sequenza necessaria a ricreare lo stato desiderato. Guardate i risultati prima e dopo ogni modifica. Un primo esempio:

```sql
SELECT first_name, last_name, age
FROM students
ORDER BY last_name;
```

`SELECT` sceglie le colonne da leggere, `FROM` la tabella, `ORDER BY` l'ordinamento. Questa query non modifica dati.

1. Scrivete una `SELECT` che mostri tutti gli studenti con `age = 18`. Provate ad aggiungere `ORDER BY last_name`. Dovete ottenere due righe.
2. Scrivete una `INSERT` per aggiungere una quarta persona alla classe `5B INF` (il suo `class_id` è `2`). Lasciate che il database assegni `id` automaticamente. Poi controllate con `SELECT * FROM students;`.
3. Con `UPDATE ... SET ... WHERE id = ...`, cambiate l'età solo della persona appena aggiunta. Prima trovate il suo `id` con una `SELECT`. Ripetete la `SELECT` dopo l'aggiornamento. **Senza `WHERE` cambiereste tutte le righe.**
4. Con `DELETE FROM students WHERE id = ...`, cancellate solo la persona di prova. Verificate che restino i tre studenti iniziali. **Senza `WHERE` cancellereste tutte le righe.**
5. Provate ad aggiungere uno studente con `class_id = 99`. MariaDB dovrebbe rifiutarlo: non esiste una classe con quell'ID. Questo è il lavoro del vincolo di chiave esterna.

Quando queste query sono chiare, provate la relazione 1:n:

```sql
SELECT s.first_name, s.last_name, c.name AS class_name
FROM students AS s
JOIN classes AS c ON s.class_id = c.id
ORDER BY c.name, s.last_name;
```

`JOIN` unisce righe di due tabelle; `ON` specifica quali ID devono corrispondere. `AS s` e `AS c` sono abbreviazioni dei nomi delle tabelle. Dovreste vedere tre studenti con il nome della loro classe. Come sfida facoltativa, contate gli studenti per classe con `COUNT(*)` e `GROUP BY c.name`.

Per capire cosa fa una query, leggete sempre la tabella **prima e dopo**. In XAMPP e Docker le modifiche restano nel database fino a ulteriori modifiche o a un ripristino; nel servizio online valgono all'interno della singola esecuzione dello script, come spiegato nella [guida a SQL fiddle](../fiddle/README.md).

## Documentazione

- [SELECT](https://mariadb.com/docs/server/reference/sql-statements/data-manipulation/selecting-data/select), [INSERT](https://mariadb.com/docs/server/reference/sql-statements/data-manipulation/inserting-loading-data/insert), [UPDATE](https://mariadb.com/docs/server/reference/sql-statements/data-manipulation/changing-deleting-data/update), [DELETE](https://mariadb.com/docs/server/reference/sql-statements/data-manipulation/changing-deleting-data/delete)
- [JOIN](https://mariadb.com/docs/server/reference/sql-statements/data-manipulation/selecting-data/joins-subqueries/joins/join-syntax) e [chiavi esterne](https://mariadb.com/docs/server/ha-and-performance/optimization-and-tuning/optimization-and-indexes/foreign-keys)

[Torna all'introduzione SQL](../README.md).
