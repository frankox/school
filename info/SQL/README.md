# SQL — introduzione al percorso

SQL (*Structured Query Language*) è il linguaggio usato per definire, interrogare e modificare i dati nei database relazionali. Con SQL descriviamo **quale risultato vogliamo ottenere**; il sistema che gestisce il database decide come eseguire l'operazione.

Questa guida presenta i concetti, i comandi principali e le convenzioni di scrittura. La sintassi dettagliata di ciascun comando sarà spiegata nel README dell'esercizio che lo introduce: per esempio, l'esercizio sulla creazione di database e tabelle spiegherà come scrivere `CREATE DATABASE` e `CREATE TABLE`.

**Il riferimento per la sintassi del percorso è MySQL.** SQL è il linguaggio; MySQL, PostgreSQL e Oracle Database sono diversi sistemi di gestione di database, detti **DBMS** (*Database Management System*). Condividono molti concetti e comandi, ma non tutta la sintassi è identica.


## Come sono organizzati i dati

Un **database** raccoglie dati organizzati. Nel modello relazionale, questi dati sono conservati in **tabelle**:

- Una **colonna** rappresenta una caratteristica, come il nome o l'età di uno studente, e ha un tipo di dato.
- Una **riga**, chiamata anche *record*, rappresenta un elemento, come un singolo studente.
- Una **chiave primaria** identifica in modo univoco ogni riga.
- Una **chiave esterna** collega i dati di una tabella a quelli di un'altra, per esempio uno studente alla sua classe.
- Un **vincolo** stabilisce una regola sui dati, come l'obbligo di compilare un campo o di non ripetere un valore.

Una tabella immaginaria chiamata `students` potrebbe contenere:

| id | first_name | age |
| --- | --- | --- |
| 1 | Anna | 17 |
| 2 | Luca | 18 |
| 3 | Sara | 18 |

Qui `id` identifica lo studente, `first_name` contiene testo e `age` un numero intero. I tipi di dato distinguono, per esempio, numeri interi, numeri decimali, testo e date. Nomi, tipi e vincoli costituiscono la **struttura** della tabella; le righe ne costituiscono il **contenuto**.

## A che cosa servono i comandi principali

Non occorre memorizzarli tutti ora: questa tabella è una mappa da consultare durante il percorso.

| Scopo | Comandi | Significato generale |
| --- | --- | --- |
| Definire la struttura | `CREATE`, `ALTER`, `DROP` | Creare, modificare o eliminare oggetti come database e tabelle. |
| Leggere i dati | `SELECT` | Ottenere un insieme di risultati senza modificare i dati letti. |
| Aggiungere dati | `INSERT` | Inserire nuove righe in una tabella. |
| Modificare dati | `UPDATE` | Cambiare i valori di righe esistenti. |
| Eliminare dati | `DELETE` | Rimuovere righe da una tabella. |
| Gestire transazioni | `START TRANSACTION`, `COMMIT`, `ROLLBACK` | Raggruppare operazioni e confermarle oppure annullarle, quando supportato. |
| Gestire permessi | `GRANT`, `REVOKE` | Concedere o revocare autorizzazioni agli utenti. |

Si incontrano spesso le sigle **DDL** (*Data Definition Language*, linguaggio di definizione dei dati) per i comandi che definiscono la struttura e **DML** (*Data Manipulation Language*, linguaggio di manipolazione dei dati) per quelli che manipolano i dati. La lettura con `SELECT` viene talvolta indicata separatamente come **DQL** (*Data Query Language*, linguaggio di interrogazione dei dati).

`DELETE` elimina righe; `DROP TABLE` elimina la tabella stessa, inclusi struttura e dati. Per `UPDATE` e `DELETE`, omettere una condizione di selezione può coinvolgere tutte le righe: prima di modificare dati bisogna capire quali righe saranno interessate.

Una **transazione** permette di trattare più operazioni come un'unica unità. Non bisogna però presumere di poter annullare qualsiasi comando: in MySQL diverse operazioni sulla struttura, come `CREATE TABLE`, provocano una conferma implicita della transazione. Il relativo esercizio approfondirà queste regole.

## Come leggere e scrivere SQL

Un'**istruzione** è un comando completo. Una **clausola** è una sua parte, introdotta da parole chiave come `FROM` o `WHERE`.

Questo esempio presuppone che la tabella `students` mostrata sopra esista già:

```sql
-- Legge i nomi degli studenti maggiorenni
SELECT first_name
FROM students
WHERE age >= 18
ORDER BY first_name;
```

`SELECT` indica la colonna da leggere, `FROM` la tabella, `WHERE` la condizione e `ORDER BY` l'ordinamento. Il risultato contiene Luca e Sara, in quest'ordine. Senza un ordinamento esplicito, non bisogna fare affidamento sull'ordine delle righe restituite.

Per leggere gli esempi del percorso:

- Le **parole chiave** sono scritte in maiuscolo per distinguerle dai nomi. MySQL riconosce sia `SELECT` sia `select`.
- I **nomi** di tabelle e colonne sono identificatori, come `students` e `first_name`. Mantieni la grafia indicata nella consegna: la sensibilità alle maiuscole dei nomi può dipendere dal DBMS e dalla configurazione.
- Il **punto e virgola** `;` termina un'istruzione negli script e nel client SQL.
- Spazi e a capo rendono il codice leggibile; una nuova riga non conclude da sola l'istruzione. L'ordine delle clausole segue invece regole precise.
- Le **virgole** separano elementi di un elenco, per esempio `SELECT first_name, age`.
- I **valori testuali** si scrivono tra apici singoli, come `'Anna'`; i numeri si scrivono senza apici, come `18`. Un apostrofo dentro un testo si può raddoppiare: `'D''Angelo'`.
- `NULL` rappresenta un valore assente o sconosciuto: non equivale a zero o a una stringa vuota. Si verifica con `IS NULL` o `IS NOT NULL`, non con `= NULL`.
- I **commenti** spiegano il codice senza essere eseguiti: `-- commento` su una riga oppure `/* commento */` su più righe. Dopo `--` usa uno spazio, come richiesto da MySQL.

Negli schemi di sintassi delle consegne, nomi come `nome_tabella` saranno segnaposto da sostituire. Gli esempi completi useranno invece nomi e valori concreti.

## Lo stesso SQL può avere dialetti diversi

Le varianti del linguaggio offerte dai DBMS vengono spesso chiamate **dialetti SQL**. Bastano due esempi per capire perché una query trovata online potrebbe richiedere un adattamento.

### Limitare il numero di risultati

Per leggere al massimo i primi cinque studenti ordinati per un identificatore univoco:

```sql
-- MySQL e PostgreSQL
SELECT id, first_name FROM students ORDER BY id LIMIT 5;

-- Oracle Database, dalla versione 12c
SELECT id, first_name FROM students ORDER BY id FETCH FIRST 5 ROWS ONLY;
```

L'obiettivo è lo stesso, ma cambia la clausola finale. Sono forme documentate nei manuali di [MySQL](https://dev.mysql.com/doc/refman/8.4/en/select.html), [PostgreSQL](https://www.postgresql.org/docs/current/queries-limit.html) e [Oracle Database](https://docs.oracle.com/en/database/oracle/oracle-database/21/sqlrf/SELECT.html).

### Delimitare i nomi di tabelle e colonne

Quando è necessario delimitare un identificatore, MySQL usa normalmente gli accenti gravi (*backtick*), mentre PostgreSQL usa i doppi apici:

```sql
-- MySQL
SELECT `first_name` FROM `students`;

-- PostgreSQL
SELECT "first_name" FROM "students";
```

Questi delimitatori riguardano i **nomi**, mentre gli apici singoli delimitano i **valori testuali**. I dettagli sono nei manuali di [MySQL](https://dev.mysql.com/doc/refman/8.4/en/identifiers.html) e [PostgreSQL](https://www.postgresql.org/docs/current/sql-syntax-lexical.html). Con nomi semplici come quelli dell'esempio, i delimitatori si possono omettere.

Anche tipi di dato, funzioni e generazione automatica degli identificatori possono cambiare. Prima di riutilizzare un esempio, controlla per quale DBMS e versione è scritto. Negli esercizi seguiremo la sintassi MySQL spiegata nella singola consegna.

## Come proseguire

Gli esercizi sono organizzati per tipologia di linguaggio:

- [DDL — Data Definition Language](DDL/README.md): 12 esercizi progressivi su database, tabelle, tipi, vincoli, relazioni, chiavi composite e indici, fino a un progetto riepilogativo.
- [DML — Data Manipulation Language](DML/README.md): programma di otto esercizi su inserimento, modifica, eliminazione e transazioni; è disponibile il [primo esercizio su INSERT](DML/01-inserire-righe/README.md).
- [DQL — Data Query Language](DQL/README.md): sezione predisposta per i futuri esercizi di lettura e interrogazione dei dati.

Inizia dal percorso DDL: ogni cartella descrive un caso reale, i concetti necessari, la sintassi MySQL, l’attività e i risultati attesi. Gli ambienti sono indipendenti e i test forniscono già le query necessarie a verificare le definizioni.

SQL si può eseguire attraverso un client collegato a un database, anche avviato con Docker, oppure in un ambiente online di tipo *SQL fiddle*. In quest'ultimo caso occorre selezionare MySQL e una versione compatibile con la consegna; non tutti gli ambienti online consentono le stesse operazioni, per esempio la creazione di un database. Ogni esercizio che richiede un database avrà il proprio `compose.yaml` e le istruzioni per svolgerlo con il solo Docker. Per i concetti comuni sull'ambiente consulta la [guida Docker](../../DOCKER.md).

### Materiale già disponibile

- [Fondamenti SQL — interrogare e modificare dati](fondamenti-SQL/README.md): laboratorio introduttivo già esistente su lettura, modifica e relazione 1:n. Usa attualmente **MariaDB**, un DBMS distinto da MySQL e compatibile con molte delle sue istruzioni; il suo README descrive l'ambiente effettivamente fornito. La compatibilità non implica che i due sistemi siano identici.

[Torna all'indice di Informatica](../README.md).
