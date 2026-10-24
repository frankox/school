# 01 — Inserire righe

## Consegna

Una libreria ha già un catalogo con un libro registrato. Sono arrivati quattro nuovi titoli: devi aggiungerli alla tabella `books`, conservando il libro esistente. La struttura è pronta e non deve essere modificata.

Completa **solo `solution.sql`** con due istruzioni `INSERT`, usando sempre l’elenco esplicito delle colonne:

1. Inserisci `LIB-001` con un’istruzione per una sola riga. Ometti `id` e `stock`: devono essere assegnati dal database.
2. Inserisci `LIB-002`, `LIB-003` e `LIB-004` con **un’unica istruzione per più righe**. Ometti `id`, usa `DEFAULT` per lo stock di `LIB-003`, `0` per quello di `LIB-004` e `NULL` esplicito per le date sconosciute.

### Dati da inserire e risultato atteso

| code | title | author | price | stock atteso | published_on |
| --- | --- | --- | --- | --- | --- |
| LIB-001 | Il barone rampante | Italo Calvino | 12.50 | 0 (colonna omessa) | 1957-06-15 |
| LIB-002 | L'isola di Arturo | Elsa Morante | 14.00 | 5 | `NULL` |
| LIB-003 | Storie 📚 | Niccolò Rossi | 9.90 | 0 (`DEFAULT`) | `NULL` |
| LIB-004 | Le città invisibili | Italo Calvino | 13.25 | 0 (esplicito) | 1972-11-03 |

Le date sono dati didattici da usare esattamente come indicato. Il catalogo finale deve contenere **cinque righe**, senza altre aggiunte. Il libro iniziale deve restare invariato: `id=1`, codice `LIB-000`, titolo `Il sentiero dei nidi di ragno`, autore `Italo Calvino`, prezzo `11.00`, stock `3`, data `1947-10-01`. I quattro nuovi identificatori devono essere generati da MySQL: non serve assegnarli né fare affidamento su una numerazione senza intervalli.

## Prima di iniziare

Consulta l’[introduzione SQL](../../README.md) e il [programma DML](../README.md). È utile conoscere [tipi di dato](../../DDL/02-tipi-di-dato/README.md), [vincoli](../../DDL/03-vincoli-sui-dati/README.md) e [identificatori automatici](../../DDL/04-identificare-le-righe/README.md). Le query di lettura per controllare il risultato sono fornite qui sotto.

## Struttura già fornita

| Colonna | Tipo e regole |
| --- | --- |
| `id` | `INT`, chiave primaria `AUTO_INCREMENT`. |
| `code` | `VARCHAR(20)`, obbligatorio e univoco. |
| `title` | `VARCHAR(150)`, obbligatorio. |
| `author` | `VARCHAR(100)`, obbligatorio. |
| `price` | `DECIMAL(8,2)`, obbligatorio e maggiore di zero. |
| `stock` | `INT`, obbligatorio, non negativo, valore predefinito `0`. |
| `published_on` | `DATE`, ammette `NULL`, valore predefinito `NULL`. |

Il database usa `utf8mb4` per conservare accenti e simboli. Salva anche `solution.sql` in UTF-8.

## Concetti e sintassi MySQL

`INSERT INTO` aggiunge righe a una tabella esistente. L’elenco delle colonne stabilisce a quale campo corrisponde ciascun valore: conta l’ordine scritto nell’istruzione. Esempio su un’altra tabella:

```sql
INSERT INTO stationery (code, label, quantity)
VALUES ('PEN-01', 'Penna blu', 8);
```

Per aggiungere più righe, separa le parentesi dei valori con una virgola e termina l’intera istruzione con `;`:

```sql
INSERT INTO stationery (code, label, quantity)
VALUES
    ('PEN-02', 'Penna rossa', 4),
    ('PEN-03', 'Penna verde', DEFAULT);
```

Ogni gruppo deve avere tanti valori quante sono le colonne elencate. Scrivere le colonne rende evidente la corrispondenza e permette di omettere quelle gestite dal database. Se ometti una colonna `AUTO_INCREMENT`, MySQL genera l’identificatore; se ometti una colonna con un valore predefinito, usa quel valore. Anche la parola chiave `DEFAULT`, al posto di un valore, richiede il valore predefinito della colonna. Non omettere campi obbligatori privi di un valore predefinito.

Testi e date vanno tra apici singoli; i numeri usano il punto decimale e si scrivono senza apici. Una data usa il formato `'AAAA-MM-GG'`. Per un apostrofo nel testo raddoppialo, per esempio `'L''astuccio'`.

`NULL`, senza apici, indica un valore assente o sconosciuto; `'NULL'` è invece una stringa. Zero è un valore noto: uno stock `0` indica che non ci sono copie, mentre una data `NULL` indica che la data non è conosciuta. Per cercare valori assenti usa `IS NULL`.

I vincoli restano attivi: codici duplicati, prezzi non positivi, quantità negative e campi obbligatori impostati a `NULL` provocano errori. Non usare `INSERT IGNORE`, `REPLACE` o aggiornamenti per aggirarli. Sono richiesti soltanto i due `INSERT`; non aggiungere `USE`, DDL, `UPDATE`, `DELETE` o modifiche ai controlli. Il riferimento è il [manuale MySQL su INSERT](https://dev.mysql.com/doc/refman/8.4/en/insert.html).

## File forniti e lavoro da completare

- `schema.sql`: tabella e libro iniziale già forniti; non modificarlo.
- `solution.sql`: scheletro da completare con gli inserimenti richiesti.
- `tests/cases.sh` e `../_support/run.sh`: controlli e runner già forniti, con descrizioni in inglese.
- `compose.yaml`: server, client MySQL e test nei container.
- `cleanup.sql`: pulizia per chi usa anche un server locale condiviso.

**Input:** la tabella iniziale con una sola riga e i quattro libri della consegna. **Output:** il catalogo con le cinque righe attese. I test verificano dati, valori assenti, stock e identificatori, compresa la conservazione della riga iniziale. Il rispetto dei due `INSERT`, delle colonne omesse e dell’uso di `DEFAULT` va verificato anche leggendo il file con il docente: lo stesso risultato può essere ottenuto con istruzioni diverse.

## Eseguire con Docker

Apri un terminale **del computer** nella cartella `info/sql/DML/01-inserire-righe`. Serve solo Docker con Compose; non occorrono MySQL o npm locali. Se modifichi il file nel Dev Container della radice `school`, esegui i comandi Docker fuori dal suo terminale. Vedi la [guida Docker](../../../../DOCKER.md) e la [guida VS Code](../../../../VS-CODE.md).

```bash
docker compose up -d --wait
docker compose exec -e MYSQL_PWD=school_root_dev db mysql --default-character-set=utf8mb4 -u root dml_01
```

Usiamo **MySQL 8.4.7**, database manuale `dml_01`, utente didattico `root` e password `school_root_dev`. Non sono pubblicate porte sul computer: il client è incluso nel container. Al primo avvio `schema.sql` prepara tabella e libro iniziale.

Nel client puoi vedere la struttura, caricare il tuo file e controllare i dati:

```sql
DESCRIBE books;
SELECT * FROM books ORDER BY code;
SOURCE /work/solution.sql;
SELECT code, title, author, price, stock, published_on FROM books ORDER BY code;
SELECT code FROM books WHERE published_on IS NULL ORDER BY code;
```

L’ultima query deve restituire `LIB-002` e `LIB-003`. Puoi provare altri `INSERT` direttamente nel client per aggiungere dati: per la verifica finale torna però allo stato iniziale. `SOURCE` legge il file montato, quindi le modifiche sono subito disponibili. Scrivi `exit` per uscire.

La soluzione va eseguita **una sola volta sullo stato iniziale**: caricarla di nuovo provoca un errore sui codici univoci. Per riprovare manualmente, usa il reset descritto sotto; per i test automatici il ripristino è già gestito dal runner.

### Verifica automatica

```bash
docker compose run --rm test
```

Ogni esecuzione ricrea **solo `dml_01_test`**, carica `schema.sql`, esegue `solution.sql` e controlla i dati in modalità SQL rigorosa. I dati di test precedenti vengono cancellati; `dml_01` resta intatto. Un errore SQL o un controllo fallito produce un codice di uscita diverso da zero.

**Stato iniziale previsto:** con lo scheletro fornito passa soltanto il controllo sul libro esistente. Gli altri **sei controlli falliscono** perché mancano gli inserimenti. Completa la soluzione fino a ottenere tutti e sette i controlli `PASS`, senza modificare i test.

### Arresto e ripristino

```bash
docker compose down
```

L’arresto conserva i dati nel volume dedicato `school-dml-01_db_data`. **Lo schema iniziale viene caricato soltanto su un volume nuovo**: modificare `schema.sql` o riavviare i container non lo ricarica.

Per cancellare tutti i dati dell’esercizio, incluse le prove manuali, e ricominciare:

```bash
docker compose --profile tools down -v
docker compose up -d --wait
```

`down -v` elimina definitivamente il volume e i database contenuti, senza cancellare i file della cartella.

### Server locale condiviso (facoltativo)

Il percorso principale richiede solo Docker. Se il laboratorio usa anche un server MySQL locale, crea `dml_01` con `CHARACTER SET utf8mb4`, selezionalo, carica `schema.sql` e poi `solution.sql`. Il file `cleanup.sql` elimina sia `dml_01` sia `dml_01_test`, compresi tutti i dati. È incluso anche nella pulizia aggregata [di Informatica](../../../cleanup-all.sql).

[Torna al programma DML](../README.md).
