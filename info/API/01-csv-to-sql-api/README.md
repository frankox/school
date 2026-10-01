# Esercizio 01 — Da CSV a INSERT SQL

## Obiettivo

Completare un convertitore che riceve dati CSV e produce una singola istruzione `INSERT` per MariaDB/MySQL.

L'API e i test sono già predisposti. Il lavoro va svolto esclusivamente nel metodo `convert` del file [`src/CsvToSqlConverter.php`](src/CsvToSqlConverter.php).

### Che cosa succede davvero

Il file CSV è una tabella scritta come testo: la prima riga contiene i nomi delle colonne, ogni riga successiva contiene i valori. Per esempio, `first_name,age` e `Mario,18` diventano una parte di `INSERT INTO ... (first_name, age) VALUES ('Mario', 18)`. Le virgole dentro un campo racchiuso tra virgolette CSV non dividono le colonne.

Questo esercizio **non apre un database**. Il programma restituisce solo una stringa SQL: non c'è una connessione a MariaDB e non vengono create tabelle o righe. `school` e `students` nella URL sono nomi da scrivere nella query, non prove che quel database o quella tabella esistano. Nel secondo esercizio la query verrà invece eseguita e il risultato apparirà davvero in XAMPP.

Il percorso dei file è: `examples/students.csv` → richiesta HTTP → `public/api/generate-insert.php` → `src/CsvToSqlConverter.php` → risposta SQL. `public/health.php` controlla solo che il server PHP risponda. `tests/run.php` chiama il convertitore direttamente, senza server web.

## Installare e verificare PHP

È necessario PHP 8.0 o successivo. Non servono Composer o librerie esterne.

### Windows con XAMPP

XAMPP include già PHP. Se XAMPP non è installato:

1. scaricarlo dal [sito ufficiale di Apache Friends](https://www.apachefriends.org/download.html);
2. eseguire l'installazione lasciando selezionati almeno Apache, MySQL e PHP;
3. mantenere il percorso proposto `C:\xampp`, se non ci sono esigenze diverse.

Aprire **PowerShell nella cartella principale dell'esercizio**, cioè quella che
contiene le cartelle `src`, `tests` e `public`. Per controllare di essere nella
cartella corretta:

In Esplora file si può aprire la cartella `01-csv-to-sql-api`, scrivere `powershell` nella barra dell'indirizzo e premere Invio. In alternativa, da un terminale già aperto, `cd percorso-della-cartella` cambia cartella.

```powershell
Test-Path .\tests\run.php
```

Il comando deve stampare `True`.

`Test-Path` controlla se il file indicato esiste; `.\` significa «a partire dalla cartella attuale». Nei comandi PowerShell il simbolo `&` avvia il programma indicato fra virgolette. `--version` chiede a PHP quale versione si sta usando.

Controllare quindi la versione di PHP inclusa in XAMPP:

```powershell
& "C:\xampp\php\php.exe" --version
```

PHP può essere usato direttamente con il percorso completo, senza modificare Windows:

```powershell
& "C:\xampp\php\php.exe" .\tests\run.php
& "C:\xampp\php\php.exe" -S localhost:8000 -t .\public
```

Per poter scrivere semplicemente `php`, aggiungere `C:\xampp\php` alla variabile di ambiente `Path`:

1. cercare **Modifica le variabili di ambiente per l'account** dal menu Start;
2. selezionare `Path`, quindi **Modifica** e **Nuovo**;
3. inserire `C:\xampp\php` e confermare tutte le finestre;
4. chiudere e riaprire PowerShell;
5. verificare con `php --version`.

### Linux

Su Arch Linux e derivate:

```bash
sudo pacman -S php
```

Su Ubuntu, Debian e derivate:

```bash
sudo apt update
sudo apt install php-cli
```

Al termine verificare l'installazione:

```bash
php --version
```

`sudo` esegue il comando con i permessi necessari per installare software; `pacman -S` e `apt install` installano PHP dai rispettivi gestori di pacchetti. Su XAMPP questi comandi Linux non servono.

## Avvio rapido

Su Windows, il modo più semplice è eseguire il launcher dalla cartella
principale dell'esercizio:

```powershell
.\run-tests.bat
```

Il launcher usa `php` dal `Path`, se disponibile, altrimenti cerca
automaticamente `C:\xampp\php\php.exe`. Funziona anche se viene richiamato da
un'altra cartella.

`Path` è l'elenco delle cartelle in cui Windows cerca un programma quando si scrive solo il suo nome. `run-tests.bat` avvia `tests/run.php`; anche il comando `php tests/run.php` fa soltanto questo, senza server web e senza database.

In alternativa, eseguire direttamente PHP dalla cartella principale
dell'esercizio:

```powershell
php .\tests\run.php
```

Se `php` non viene riconosciuto, su Windows usare il percorso completo:

```powershell
& "C:\xampp\php\php.exe" .\tests\run.php
```

Se compare `Could not open input file`, il problema non è PHP: PowerShell si
trova nella cartella sbagliata. Tornare nella cartella che contiene `src`,
`tests` e `public`, quindi rieseguire il comando. Se invece PowerShell è già
dentro la cartella `tests`, il comando corretto è:

```powershell
& "C:\xampp\php\php.exe" .\run.php
```

Su Linux:

```bash
php tests/run.php
```

All'inizio i test falliscono perché il convertitore non è ancora implementato. L'esercizio è terminato quando tutti i test passano.

I test sono piccoli esempi di comportamento. Leggetene uno per volta: osservate il CSV in ingresso, la query attesa e il caso particolare che verifica. Implementate prima la riga semplice, poi più righe, poi valori vuoti, numeri, apici e casi errati. Se un test fallisce, la parte `Expected` è il risultato richiesto e `Actual` è quello prodotto dal vostro codice. Il file temporaneo aperto da `php://temp` serve a leggere il testo CSV con le funzioni PHP; **non** è un file nel database.

## Regole di conversione

Il comportamento completo è descritto anche dai test già forniti.

1. Il CSV usa la virgola come separatore e la prima riga contiene i nomi delle colonne.
2. Database, tabella e colonne sono identificatori validi solo se rispettano la forma `[A-Za-z_][A-Za-z0-9_]*` (è una regex, se non sapete cos'è cercate in internet oppure chiedete. Per capire a cosa si riferisce potete usare regex101).
3. Le colonne non possono essere vuote o duplicate.
4. Ogni riga deve contenere esattamente un valore per ogni colonna.
5. Le righe completamente vuote vengono ignorate.
6. Un campo vuoto diventa `NULL`.
7. Interi e numeri decimali, anche negativi, non hanno apici SQL. Gli zeri iniziali vengono conservati trattando il valore come stringa.
8. Tutti gli altri valori diventano stringhe SQL racchiuse tra apici singoli.
9. Un apostrofo contenuto in una stringa viene raddoppiato: `D'Amico` diventa `'D''Amico'`.
10. I campi CSV racchiusi tra virgolette possono contenere virgole.
11. Il risultato contiene una sola `INSERT` con più gruppi di valori.

Il formato richiesto è:

```sql
INSERT INTO `database`.`table` (`column1`, `column2`) VALUES
('value', 10),
('another value', NULL);
```

## Che cos'è un'API

Un'API (Application Programming Interface) permette a due programmi di comunicare seguendo regole definite. In questo esercizio un client invia un file CSV al server tramite HTTP; il server elabora i dati e restituisce la query SQL generata.

L'API mette a disposizione due indirizzi, chiamati **endpoint**:

- `GET /health.php` controlla che il server sia attivo;
- `POST /api/generate-insert.php` riceve il CSV e restituisce la query `INSERT`.

L'endpoint principale accetta soltanto richieste `POST`. Aprirlo direttamente dalla barra degli indirizzi del browser non è sufficiente, perché il browser invierebbe una richiesta `GET` senza il file CSV.

## Provare l'API

I comandi seguenti devono essere eseguiti dalla cartella dell'esercizio `01-csv-to-sql-api`.

### 1. Avviare il server

Aprire un primo terminale e avviare il server integrato di PHP:

```bash
php -S localhost:8000 -t public
```

Su Windows, se PHP è installato tramite XAMPP, usare:

```powershell
C:\xampp\php\php.exe -S localhost:8000 -t public
```

Lasciare aperto questo terminale mentre si prova l'API. Il server è raggiungibile all'indirizzo `http://localhost:8000`.

Nel comando di avvio, `-S` avvia il server di sviluppo, `localhost:8000` dice su quale indirizzo e porta ascoltare, `-t public` indica la cartella accessibile ai client. I file `src` e `tests` restano fuori dalla cartella pubblica.

Per controllare che sia attivo, aprire nel browser <http://localhost:8000/health.php> oppure eseguire:

```bash
curl http://localhost:8000/health.php
```

La risposta attesa è:

```json
{"status":"ok"}
```

### 2. Inviare il file CSV

Aprire un secondo terminale, sempre nella cartella dell'esercizio, e inviare il file di esempio:

```bash
curl -i \
  -X POST \
  -H "Content-Type: text/csv" \
  --data-binary @examples/students.csv \
  "http://localhost:8000/api/generate-insert.php?database=school&table=students"
```

In PowerShell usare `curl.exe` per evitare che `curl` venga interpretato come un altro comando:

```powershell
curl.exe -i -X POST -H "Content-Type: text/csv" --data-binary "@examples/students.csv" "http://localhost:8000/api/generate-insert.php?database=school&table=students"
```

### Come leggere il comando `curl`

`curl` è un programma da terminale che permette di inviare richieste HTTP. Le parti del comando hanno questo significato:

- `-i` mostra anche lo stato e le intestazioni della risposta HTTP;
- `-X POST` specifica che il metodo della richiesta è `POST`;
- `-H "Content-Type: text/csv"` comunica al server che il contenuto inviato è in formato CSV;
- `--data-binary @examples/students.csv` legge il file indicato dopo `@` e ne invia il contenuto nel corpo della richiesta;
- `/api/generate-insert.php` è l'endpoint che elabora il CSV;
- `database=school&table=students` sono i parametri che indicano il database e la tabella da usare nella query SQL.

Il percorso completo dei dati è quindi:

```text
examples/students.csv
        ↓
curl legge e invia il file con una richiesta POST
        ↓
public/api/generate-insert.php riceve la richiesta
        ↓
CsvToSqlConverter converte i dati
        ↓
il server restituisce la query INSERT
```

Prima di completare il convertitore, l'endpoint risponde con lo stato `501 Not Implemented`. Dopo aver fatto passare i test, restituisce la query SQL con stato `200 OK`.

Con XAMPP è anche possibile esporre la cartella `public` tramite Apache e usare gli stessi file `health.php` e `api/generate-insert.php`.

Il server integrato di PHP occupa la porta `8000` finché il primo terminale resta aperto; `Ctrl+C` lo ferma. Il numero della porta identifica il programma che riceve la richiesta HTTP. In questo esercizio non serve avviare MySQL dal pannello XAMPP. Se vedete `Address already in use`, un altro programma sta già usando la porta: fermatelo oppure scegliete `8001` sia nel comando di avvio sia nelle URL delle prove.

## Limiti intenzionali

Questo primo esercizio genera una query ma non la esegue. Il collegamento a MariaDB, le query preparate e le transazioni saranno affrontati negli esercizi successivi.

La conversione manuale dei valori in stringhe SQL serve qui per capire il formato di `INSERT`. Quando i dati arrivano da utenti e vanno salvati nel database, nell'esercizio 02 userete i parametri di PDO per tenere separati SQL e valori. La query generata qui è un risultato didattico: non eseguitela automaticamente su un database con dati importanti.

## Per approfondire

- [Formato CSV: RFC 4180](https://www.rfc-editor.org/rfc/rfc4180)
- [fgetcsv in PHP](https://www.php.net/manual/en/function.fgetcsv.php) e [stream `php://temp`](https://www.php.net/manual/en/wrappers.php.php)
- [Server integrato PHP](https://www.php.net/manual/en/features.commandline.webserver.php)
- [HTTP POST](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Methods/POST) e [manuale curl](https://curl.se/docs/manpage.html)
- [INSERT in MariaDB](https://mariadb.com/docs/server/reference/sql-statements/data-manipulation/inserting-loading-data)
