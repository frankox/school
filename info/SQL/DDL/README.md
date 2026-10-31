# DDL — Data Definition Language

Il DDL è il linguaggio di definizione dei dati: descrive la struttura del database e le regole che i dati devono rispettare. Il percorso usa **MySQL 8.4.7** e parte da casi realistici per arrivare alla progettazione di uno schema completo.

Leggi prima l’[introduzione SQL](../README.md). Ogni esercizio ha un ambiente indipendente: non occorre tenere avviati i database precedenti. Le consegne descrivono dati e comportamenti attesi: la scelta dei tipi SQL, delle dimensioni e dei vincoli spetta allo studente. Gli esempi di sintassi illustrano gli strumenti, senza prescrivere la struttura da copiare. Le scelte devono restare compatibili con gli eventuali schemi già forniti; nel progetto finale si devono combinare e motivare.

| Esercizio | Obiettivo |
| --- | --- |
| [01 — Database e tabelle](01-database-e-tabelle/README.md) | Creare il contenitore e una prima struttura. |
| [02 — Tipi di dato](02-tipi-di-dato/README.md) | Scegliere tipi per testo, importi, quantità e date. |
| [03 — Vincoli sui dati](03-vincoli-sui-dati/README.md) | Imporre obbligatorietà, valori predefiniti e regole. |
| [04 — Identificare le righe](04-identificare-le-righe/README.md) | Usare chiavi primarie, unicità e identificatori automatici. |
| [05 — Modificare la struttura](05-modificare-la-struttura/README.md) | Evolvere uno schema conservando i dati. |
| [06 — Relazioni semplici](06-relazioni-semplici/README.md) | Definire relazioni 1:n e valutare quando una 1:1 ha senso. |
| [07 — Azioni referenziali](07-azioni-referenziali/README.md) | Scegliere le conseguenze di cancellazioni e aggiornamenti. |
| [08 — Chiavi composite](08-chiavi-composite/README.md) | Identificare una riga attraverso una combinazione di colonne. |
| [09 — Relazioni molti-a-molti](09-relazioni-molti-a-molti/README.md) | Rappresentare associazioni n:n e i loro attributi. |
| [10 — Riferimenti a chiavi composite](10-riferimenti-a-chiavi-composite/README.md) | Controllare riferimenti a combinazioni complete. |
| [11 — Indici](11-indici/README.md) | Progettare indici in funzione delle ricerche. |
| [12 — Progetto riepilogativo: centro di formazione](12-progetto-riepilogativo/README.md) | Tradurre requisiti di un centro di formazione in uno schema. |

Ogni cartella contiene consegna, `solution.sql` da completare, test e `compose.yaml`. `schema.sql` è presente solo quando servono tabelle o dati iniziali già forniti; questi setup non contengono la soluzione. Gli inserimenti e le query dei test sono già forniti: non serve saper scrivere DML o DQL prima di iniziare. Negli esercizi che richiedono motivazioni progettuali è presente anche `design.md`, da completare e discutere con il docente.

Con solo Docker segui i comandi nel README di ogni esercizio. I database non espongono porte sul computer; non serve un client SQL locale. I test ricreano il database dedicato con suffisso `_test`, senza cancellare quello delle prove manuali. Le credenziali sono esclusivamente didattiche. Per volumi, container e uso di VS Code vedi la [guida Docker](../../../DOCKER.md).

Dalla radice `school`, se hai anche npm, sono disponibili le scorciatoie `npm run test:info:sql:ddl:01:docker` e `npm run up:info:sql:ddl:01` (sostituisci `01` con il numero fino a `12`). Il primo comando esegue i test nei container, il secondo avvia il database; per client, arresto e reset usa i comandi del singolo README.

I riferimenti per le particolarità MySQL sono la documentazione su [creazione delle tabelle](https://dev.mysql.com/doc/refman/8.4/en/create-table.html), [CHECK](https://dev.mysql.com/doc/refman/8.4/en/create-table-check-constraints.html), [chiavi esterne](https://dev.mysql.com/doc/refman/8.4/en/create-table-foreign-keys.html) e [indici composti](https://dev.mysql.com/doc/refman/8.4/en/multiple-column-indexes.html).

[Torna all’introduzione SQL](../README.md).
