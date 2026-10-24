# DML — Data Manipulation Language

Questo percorso insegna a inserire, modificare ed eliminare dati in **MySQL 8.4.7**. Gli schemi sono già forniti: il lavoro consiste nello scrivere le operazioni sui dati, conservando struttura e vincoli.

## Programma degli esercizi

| Esercizio | Obiettivo | Stato |
| --- | --- | --- |
| [01 — Inserire righe](01-inserire-righe/README.md) | `INSERT INTO ... VALUES`, colonne esplicite, inserimenti singoli e multipli, valori predefiniti, `NULL` e identificatori automatici. | Disponibile |
| 02 — Inserire dati collegati | Popolare tabelle in relazione rispettando chiavi esterne e ordine degli inserimenti. | Pianificato |
| 03 — Copiare dati tra tabelle | Usare `INSERT ... SELECT` per importare un insieme di righe, dopo aver affrontato le query DQL di base. | Pianificato |
| 04 — Modificare righe selezionate | Usare `UPDATE ... SET ... WHERE`, verificando prima quali righe saranno coinvolte. | Pianificato |
| 05 — Aggiornare valori con espressioni | Calcolare variazioni di prezzi e quantità a partire dai valori esistenti, rispettando i vincoli. | Pianificato |
| 06 — Eliminare righe | Usare `DELETE ... WHERE`, distinguere cancellazione dei dati e della struttura, osservare gli effetti delle relazioni. | Pianificato |
| 07 — Gestire transazioni | Raggruppare operazioni con `START TRANSACTION`, `COMMIT` e `ROLLBACK`, verificando anche un annullamento. | Pianificato |
| 08 — Progetto riepilogativo: ordini di una libreria | Registrare un ordine, aggiornare le disponibilità e annullare operazioni incomplete in una transazione. | Pianificato |

Per ora è sviluppato **solo il primo esercizio**. Le altre righe definiscono la progressione didattica; le relative cartelle e consegne saranno aggiunte in seguito.

## Prima di iniziare

Consulta l’[introduzione SQL](../README.md) e il [percorso DDL](../DDL/README.md), in particolare tipi, vincoli e identificatori automatici. Per il primo esercizio le query di controllo sono già fornite; non serve aver completato il percorso DQL. Gli esercizi successivi su selezione e modifica richiederanno le basi di `SELECT` e `WHERE`.

Ogni esercizio disponibile contiene consegna, `schema.sql` iniziale, `solution.sql` da completare, test con descrizioni in inglese e un ambiente Docker indipendente. I test ricreano soltanto il database con suffisso `_test`, lasciando intatto quello delle prove manuali. Segui i comandi del README dell’esercizio; per i concetti comuni consulta la [guida Docker](../../../DOCKER.md) e la [guida VS Code](../../../VS-CODE.md).

Dalla radice `school`, se hai anche npm, puoi usare `npm run test:info:sql:dml:01:docker` per i test e `npm run up:info:sql:dml:01` per avviare il database. Con il solo Docker usa i comandi del primo esercizio.

[Torna all’introduzione SQL](../README.md).
