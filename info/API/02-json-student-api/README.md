# Esercizio 02 — Da JSON a studente nel database

## Cosa cambia rispetto al primo esercizio

Nel primo esercizio l'API **produce il testo di una query SQL**: anche se la risposta contiene `INSERT`, nessuna riga viene salvata. In questo secondo esercizio l'API **si collega a MariaDB e inserisce davvero uno studente** nella tabella `students` del vostro XAMPP. Dopo una chiamata riuscita potete vederlo in phpMyAdmin con `SELECT * FROM students;`.

Prima di cominciare, potete avviare e leggere l'[esempio completo dell'API dei libri](../00-example-books-api/README.md). Mostra un percorso funzionante da richiesta HTTP a database; qui dovrete costruire voi i due passaggi centrali.

## Obiettivo e lavoro richiesto

Realizzare `POST /api/students.php`. Il client invia questo JSON:

```json
{
  "first_name": "Mario",
  "last_name": "Rossi",
  "age": 18,
  "student_code": "00123"
}
```

L'endpoint deve trasformare e controllare i dati in `CreateStudentRequest::fromJson()`, poi `StudentRepository::create()` deve fare una sola `INSERT` con PDO. Il codice dell'endpoint e della connessione è già predisposto. Non serve una transazione esplicita per questa unica query; la userete nell'esercizio 03 per più `INSERT` che devono riuscire insieme.

Regole da rispettare:

1. Il JSON deve contenere un **oggetto**, non un elenco o un valore singolo, con **esattamente** `first_name`, `last_name`, `age`, `student_code`.
2. Nomi e codice devono essere stringhe non vuote; `age` deve essere un intero tra 14 e 100.
3. `student_code` resta una stringa: `"00123"` non deve diventare il numero `123`.
4. I valori ricevuti devono essere passati a una **query preparata PDO**, usando parametri come `:first_name`; non vanno concatenati nel testo SQL.

## Il percorso dei dati, file per file

```text
examples/student.json
  → curl invia POST con corpo JSON
  → public/api/students.php legge php://input
  → src/CreateStudentRequest.php interpreta e valida il JSON
  → config/database.php apre una connessione PDO a MariaDB
  → src/StudentRepository.php esegue INSERT nella tabella students
  → MariaDB assegna un id; l'API risponde {"id": ...}
```

Un **client** è il programma che invia la richiesta (qui `curl`); il **server PHP** la riceve. `POST` è il metodo HTTP usato per creare una risorsa. Il **corpo** è il contenuto inviato con la richiesta; `Content-Type: application/json` ne dichiara il formato. La **risposta** contiene uno stato HTTP e un JSON. `201 Created` significa che il record è stato creato.

Un **DTO** (`CreateStudentRequest`) è un oggetto PHP che raccoglie dati già interpretati e controllati. Nel JSON i nomi sono `first_name` e `student_code`; nelle proprietà PHP sono `firstName` e `studentCode`: serve una corrispondenza esplicita. `JsonToClassConverter` è un esperimento presente nella cartella `utils`; se lo usate, controllate con attenzione che sappia gestire questa differenza, i campi mancanti e i tipi. Una stampa con `echo` durante l'elaborazione romperebbe la risposta JSON dell'API.

Il **repository** raccoglie il codice che accede al database. `PDO` è la libreria PHP usata per la connessione. `prepare()` prepara l'SQL con segnaposto; `execute()` associa i valori; `lastInsertId()` legge l'ID che MariaDB ha appena assegnato. Una query preparata non può usare un parametro al posto del nome di una tabella: qui il nome `students` è fisso nel codice.

## Prima fase: test senza MariaDB

Aprire PowerShell nella cartella `02-json-student-api` e controllare:

Per farlo da Esplora file, aprire questa cartella, scrivere `powershell` nella barra dell'indirizzo e premere Invio. In un terminale già aperto, `cd percorso-della-cartella` cambia cartella.

```powershell
Test-Path .\tests\run.php
& "C:\xampp\php\php.exe" --version
& "C:\xampp\php\php.exe" .\tests\run.php
```

`Test-Path` risponde `True` se il terminale è nella cartella corretta. `--version` mostra la versione di PHP: serve almeno PHP 8.0. L'ultimo comando esegue i test. All'inizio alcuni fallimenti sono previsti: indicano i comportamenti ancora da implementare. I test usano un **doppio di PDO**: una classe che registra le chiamate senza toccare il database. Questo permette di correggere interpretazione del JSON e query prima di configurare XAMPP.

In PowerShell, `&` avvia il programma indicato tra virgolette; `.\` indica un percorso a partire dalla cartella attuale. Se avete già aggiunto PHP al `Path` di Windows, potete usare `php .\tests\run.php` al posto del percorso completo.

Su Linux usare `php --version` e `php tests/run.php`.

Per orientarsi nella repository: aprire `tests/run.php`, cercare un test alla volta, capire input e risultato atteso, poi intervenire nei metodi indicati. Ad esempio, controllare prima JSON valido e campi obbligatori, poi le eccezioni per input errati, infine la `INSERT`.

## Seconda fase: preparare XAMPP

1. Nel **XAMPP Control Panel** avviare **MySQL**. XAMPP include MariaDB sotto questo nome. Avviare anche **Apache** se si vuole usare phpMyAdmin; per servire l'API useremo il server integrato di PHP.
2. Aprire <http://localhost/phpmyadmin/> e, dalla pagina iniziale, scegliere **Importa**. Selezionare il file `schema.sql` di questa cartella e premere **Esegui**.
3. Verificare che compaiano il database `school_ex02` e la tabella `students`. `schema.sql` esegue `CREATE DATABASE`, `USE` e `CREATE TABLE`: crea la struttura ma non aggiunge studenti.
4. Controllare il driver PHP per MariaDB:

```powershell
& "C:\xampp\php\php.exe" -m | Select-String "pdo_mysql"
```

`-m` elenca i moduli PHP; `Select-String` cerca `pdo_mysql`. Se manca, va abilitato nel `php.ini` di XAMPP. Il modulo PDO da solo non basta per parlare con MariaDB.

`config/database.php` usa per impostazione predefinita `127.0.0.1:3306`, database `school_ex02`, utente `root`, password vuota. `127.0.0.1` indica questo computer; `3306` è la porta abituale di MariaDB. Se XAMPP usa una password o una porta diversa, impostarle **nello stesso terminale da cui si avvierà PHP**:

```powershell
$env:DB_USER = 'root'
$env:DB_PASSWORD = 'la-tua-password'
$env:DB_DSN = 'mysql:host=127.0.0.1;port=3306;dbname=school_ex02;charset=utf8mb4'
```

`DB_DSN` descrive la destinazione della connessione; `DB_USER` e `DB_PASSWORD` sono le credenziali. Modificare la porta nell'esempio se necessario. Se il vostro XAMPP usa la configurazione predefinita, non serve impostare nessuna variabile.

## Terza fase: avviare e chiamare l'API

Nel primo terminale, dalla cartella dell'esercizio:

```powershell
& "C:\xampp\php\php.exe" -S localhost:8000 -t .\public
```

`-S` avvia il server web integrato; `localhost:8000` è l'indirizzo dell'API; `-t .\public` indica la cartella pubblica. Lasciare aperto il terminale. `Ctrl+C` ferma il server. **PHP sulla porta 8000 e MariaDB sulla porta 3306 sono due processi diversi:** PHP riceve la richiesta HTTP e poi apre una connessione a MariaDB.

Nel secondo terminale:

```powershell
curl.exe -i http://localhost:8000/health.php
curl.exe -i -X POST -H "Content-Type: application/json" --data-binary "@examples/student.json" http://localhost:8000/api/students.php
```

`curl.exe` invia richieste HTTP; `-i` mostra lo stato, `-X POST` sceglie il metodo, `-H` indica il formato, `--data-binary @examples/student.json` legge il file e lo invia nel corpo. `health.php` risponde `{"status":"ok"}` se il server PHP funziona, ma **non** controlla il database. Quando l'esercizio è completo, il `POST` risponde `201 Created` con un ID, per esempio `{"id":1}`. Ogni `POST` valido aggiunge davvero una riga.

Per vedere il risultato, in phpMyAdmin aprire `school_ex02` → `students` → **Mostra**, oppure usare la scheda SQL:

```sql
SELECT id, first_name, last_name, age, student_code
FROM students
ORDER BY id;
```

Su Linux, dalla stessa cartella, si può importare con `mariadb -u root -p < schema.sql`, avviare con `php -S localhost:8000 -t public` e chiamare l'API con `curl` al posto di `curl.exe`.

## Errori frequenti

| Segnale | Controllo utile |
| --- | --- |
| `Could not open input file` | Il terminale non è nella cartella dell'esercizio. |
| `404` su `health.php` | Verificare `-t .\public` e la cartella corrente. |
| `could not find driver` | Verificare `pdo_mysql` con il comando `-m`. |
| `Connection refused` | Verificare che MySQL sia avviato nel pannello XAMPP e che la porta sia giusta. |
| `Unknown database` | Importare `schema.sql` e verificare `school_ex02` in phpMyAdmin. |
| `500` o JSON inatteso | Guardare il terminale del server PHP; controllare anche stampe `echo` non richieste e file PHP da caricare. |
| `duplicate entry` | `student_code` è `UNIQUE`: usare un codice diverso per una seconda prova. |

## Pulizia finale

`cleanup.sql` contiene `DROP DATABASE IF EXISTS school_ex02`. Importarlo dalla pagina iniziale di phpMyAdmin, oppure su Linux eseguire `mariadb -u root -p < cleanup.sql`. **Cancella definitivamente tutte le righe di questo esercizio.** Per rifare la prova importare di nuovo `schema.sql`. Gli altri esercizi usano database diversi.

## Documentazione da consultare

- [Traccia ministeriale di Informatica 2023](https://www.istruzione.it/esame_di_stato/202223/Istituti%20tecnici/Ordinaria/A038_ORD23.pdf): mostra progettazione, SQL e applicazione web nello stesso compito.
- [HTTP POST](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Methods/POST), [manuale curl](https://curl.se/docs/manpage.html)
- [Server integrato PHP](https://www.php.net/manual/en/features.commandline.webserver.php), [decodifica JSON](https://www.php.net/manual/en/function.json-decode.php)
- [PDO MySQL](https://www.php.net/manual/en/ref.pdo-mysql.php), [PDO::prepare](https://www.php.net/manual/en/pdo.prepare.php), [PDOStatement::execute](https://www.php.net/manual/en/pdostatement.execute.php)
- [CREATE TABLE](https://mariadb.com/docs/server/server-usage/tables/create-table), [INSERT](https://mariadb.com/docs/server/reference/sql-statements/data-manipulation/inserting-loading-data)
- [FAQ XAMPP per Windows](https://www.apachefriends.org/faq_windows.html)
