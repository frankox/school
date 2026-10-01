# Fondamenti SQL — interrogare e modificare dati

Questo percorso si svolge **senza PHP**. Si lavora direttamente in MariaDB per imparare a leggere e modificare dati, osservare una relazione 1:n e capire gli effetti delle query. Può essere svolto da solo oppure prima degli esercizi API.

## Preparare il database

Nel pannello XAMPP avviate **MySQL** e **Apache**; Apache serve qui per aprire phpMyAdmin. Andate su <http://localhost/phpmyadmin/>, dalla pagina iniziale scegliete **Importa**, selezionate `schema.sql` e premete **Esegui**. Comparirà `school_lab` con due tabelle.

`classes.id` è la chiave primaria di una classe. `students.class_id` è una chiave esterna: dice a quale classe appartiene lo studente. Una classe può avere molti studenti; ogni studente di questo esempio appartiene a una sola classe (**1:n**). Non ci sono ancora relazioni n:n.

Lo script contiene `CREATE DATABASE` (crea il contenitore), `USE` (sceglie il database), `CREATE TABLE` (definisce colonne e vincoli) e `INSERT` (aggiunge righe iniziali). Se lo importate di nuovo, le tabelle vengono ricreate e i dati inseriti da voi nel laboratorio si perdono.

## Leggere e modificare i dati

Selezionate `school_lab` in phpMyAdmin e aprite la scheda **SQL**. Eseguite ogni query separatamente e guardate la tabella dopo ogni modifica. Un primo esempio:

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

Per capire cosa fa una query, leggete sempre la tabella **prima e dopo**. Le query di modifica (`INSERT`, `UPDATE`, `DELETE`) hanno un effetto permanente finché non le annullate o ricreate il database.

## Pulizia

Quando avete finito, importate `cleanup.sql` dalla pagina iniziale di phpMyAdmin: esegue `DROP DATABASE IF EXISTS school_lab` e cancella definitivamente solo questo laboratorio. Potete ripartire importando `schema.sql`. Su Linux gli stessi file si eseguono dalla cartella del laboratorio con `mariadb -u root -p < schema.sql` e `mariadb -u root -p < cleanup.sql`.

## Documentazione

- [SELECT](https://mariadb.com/docs/server/reference/sql-statements/data-manipulation/selecting-data/select), [INSERT](https://mariadb.com/docs/server/reference/sql-statements/data-manipulation/inserting-loading-data/insert), [UPDATE](https://mariadb.com/docs/server/reference/sql-statements/data-manipulation/changing-deleting-data/update), [DELETE](https://mariadb.com/docs/server/reference/sql-statements/data-manipulation/changing-deleting-data/delete)
- [JOIN](https://mariadb.com/docs/server/reference/sql-statements/data-manipulation/selecting-data/joins-subqueries/joins/join-syntax) e [chiavi esterne](https://mariadb.com/docs/server/ha-and-performance/optimization-and-tuning/optimization-and-indexes/foreign-keys)
