# Esercizi API

Tutti gli esercizi hanno un `compose.yaml`: dalla cartella del singolo esercizio potete eseguire PHP, test e, quando serve, MariaDB usando solo Docker. Comandi e spiegazioni sono nella [guida Docker](../../DOCKER.md) e nel README di ciascun esercizio.

Questi esercizi usano PHP e richieste HTTP. L'ordine suggerito è:

1. [Da CSV a INSERT SQL](01-csv-to-sql-api/README.md): produce SQL, senza scrivere nel database.
2. [API dei libri completa](00-example-books-api/README.md): esempio funzionante da osservare e adattare.
3. [Da JSON a risorsa Student](02-create-student-api/README.md): prima `INSERT` reale via API.
4. [GET degli studenti da costruire](02b-get-students-api/README.md): `SELECT`, `WHERE` e risposta come array di `StudentDto`.
5. [Importazione batch con transazione](03-json-batch-transaction/README.md).
6. [Ordine composto e gestione dello stock](04-order-transaction/README.md).

Per esercitarsi sulle query prima di collegare PHP a MariaDB, seguire [Fondamenti SQL](../sql/fondamenti-sql/README.md). Si può svolgere anche mentre si completa il convertitore CSV.

Ogni progetto che usa MariaDB ha un database proprio (`school_example`, `school_ex02`, `school_ex02_get`, `school_ex03`, `shop_ex04`) e contiene `cleanup.sql` per cancellarlo quando non serve più. L'esercizio 01 genera solo testo SQL e non crea database.

Alla fine del percorso, [`cleanup-all.sql`](../cleanup-all.sql) cancella tutti i database didattici, compreso `school_lab` di Fondamenti SQL. Importarlo dalla pagina iniziale di phpMyAdmin solo dopo aver verificato che non servano più i dati.
