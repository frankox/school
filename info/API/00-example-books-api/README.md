# Esempio completo — API dei libri

Questo esempio funziona già: si può copiare come guida per creare un'API su un proprio database XAMPP. Non è la soluzione dell'esercizio 02: qui la risorsa è `book`, lì è `student` e alcune parti vanno costruite dagli studenti.

## Cosa fa

| Richiesta | Risultato | SQL usato |
| --- | --- | --- |
| `GET /api/books.php` | Elenca tutti i libri | `SELECT` |
| `GET /api/books.php?id=1` | Legge un libro | `SELECT ... WHERE` |
| `POST /api/books.php` | Crea un libro | `INSERT` |
| `PUT /api/books.php?id=1` | Modifica un libro | `UPDATE` |
| `DELETE /api/books.php?id=1` | Cancella un libro | `DELETE` |

Il percorso di una richiesta è:

```text
curl / browser → server PHP (porta 8000) → public/api/books.php
             → config/database.php → MariaDB (porta 3306) → school_example.books
             ← risposta JSON
```

`public` contiene i file raggiungibili via HTTP. `config/database.php` crea la connessione PDO. `schema.sql` crea database e tabella; importarlo non inserisce libri. `cleanup.sql` cancella tutto il database dell'esempio. `examples/book.json` è un corpo di richiesta pronto da inviare.

## Preparazione su Windows con XAMPP

1. Aprire **XAMPP Control Panel** e avviare **MySQL**. Avviare anche **Apache** solo se si usa phpMyAdmin: l'API verrà servita dal server integrato di PHP sulla porta 8000.
2. In PowerShell entrare nella cartella `00-example-books-api`, quella che contiene `schema.sql`. `Test-Path .\schema.sql` deve mostrare `True`.
3. Aprire <http://localhost/phpmyadmin/>, andare alla pagina iniziale, scegliere **Importa**, selezionare `schema.sql` e premere **Esegui**. Si vedranno `school_example` e la tabella `books`.
4. Controllare che PHP abbia il driver MySQL:

```powershell
& "C:\xampp\php\php.exe" -m | Select-String "pdo_mysql"
```

Se non compare `pdo_mysql`, abilitarlo nel `php.ini` di XAMPP e riaprire il terminale. In una installazione standard è disponibile.

5. Avviare l'API dalla stessa cartella:

```powershell
& "C:\xampp\php\php.exe" -S localhost:8000 -t .\public
```

`-S` avvia il server web di sviluppo; `localhost:8000` è l'indirizzo a cui chiamare l'API; `-t .\public` fa sì che solo i file in `public` siano pubblici. Lasciare aperto questo terminale. Per fermarlo: `Ctrl+C`.

La configurazione predefinita usa MariaDB su `127.0.0.1:3306`, utente `root`, password vuota. Se si usa una password o una porta diversa, impostare le variabili **nello stesso terminale, prima di avviare PHP**:

```powershell
$env:DB_USER = 'root'
$env:DB_PASSWORD = 'la-tua-password'
$env:DB_DSN = 'mysql:host=127.0.0.1;port=3306;dbname=school_example;charset=utf8mb4'
```

La sigla `DSN` descrive tipo di database, indirizzo, porta, nome e codifica. Le variabili durano per la sessione PowerShell. Se si lascia la password vuota, non occorre impostarle.

## Prova passo per passo

Aprire un secondo terminale nella stessa cartella. `curl.exe` invia richieste HTTP; `-i` mostra anche lo stato della risposta.

```powershell
curl.exe -i http://localhost:8000/health.php
curl.exe -i http://localhost:8000/api/books.php
curl.exe -i -X POST -H "Content-Type: application/json" --data-binary "@examples/book.json" http://localhost:8000/api/books.php
curl.exe -i http://localhost:8000/api/books.php
curl.exe -i http://localhost:8000/api/books.php?id=1
```

`health.php` risponde `{"status":"ok"}` e controlla solo il server PHP. La prima lista risponde `{"books":[]}`. Il `POST` deve rispondere `201 Created` con `{"id":1}`: solo a questo punto il libro è stato scritto nel database. Nella nuova lista il libro compare; si può verificarlo anche nella tabella `books` di phpMyAdmin.

Il numero `1` è solo un esempio: dopo più inserimenti usare l'`id` restituito dal proprio `POST`.

Per provare la modifica, usate `examples/book-updated.json`, che contiene un titolo diverso:

```powershell
curl.exe -i -X PUT -H "Content-Type: application/json" --data-binary "@examples/book-updated.json" "http://localhost:8000/api/books.php?id=1"
curl.exe -i -X DELETE "http://localhost:8000/api/books.php?id=1"
```

`-X` sceglie il metodo HTTP; `-H` dichiara il formato del corpo; `--data-binary @...` legge il file e lo invia senza alterarlo. `?id=1` è un parametro nella URL. Dopo `DELETE`, la lista torna vuota. Le chiamate `POST`, `PUT` e `DELETE` cambiano davvero il database.

Su Linux si può usare `php -S localhost:8000 -t public`; per importare lo schema: `mariadb -u root -p < schema.sql`. Nei comandi HTTP si usa `curl` al posto di `curl.exe`.

## Come adattarlo al proprio progetto

1. Disegnare una tabella piccola: colonne, tipi, chiave primaria. Scrivere uno `schema.sql` con un **nome di database nuovo**, per non cancellare dati di altri esercizi.
2. Cambiare `dbname` in `config/database.php` e l'SQL in `public/api/books.php`. I nomi delle colonne nei placeholder (`:title`, ecc.) devono corrispondere alle chiavi passate a `execute()`.
3. Cambiare la validazione in `readBook()` e il JSON d'esempio. Provare prima `GET`, poi `POST`, poi le altre operazioni.
4. Controllare in phpMyAdmin che il database cambi davvero. `health.php` da solo non verifica la connessione al database.

`prepare()` crea una query con posti riservati ai valori; `execute()` passa i valori separatamente. Non inserire direttamente nel testo SQL dati arrivati dall'utente. Il codice risponde `422` per dati non validi, `404` quando un ID manca e `500` quando la connessione o la query fallisce.

## Ripulire

Quando i dati di prova non servono più, importare `cleanup.sql` dalla pagina iniziale di phpMyAdmin, oppure eseguire `mariadb -u root -p < cleanup.sql` su Linux. **Questo elimina definitivamente `school_example` e tutti i suoi libri.** Per ricominciare, importare di nuovo `schema.sql`.

## Documentazione

- [Server web integrato in PHP](https://www.php.net/manual/en/features.commandline.webserver.php)
- [PDO e driver MySQL](https://www.php.net/manual/en/book.pdo.php), [query preparate](https://www.php.net/manual/en/pdo.prepare.php)
- [JSON in PHP](https://www.php.net/manual/en/function.json-decode.php)
- [Metodi HTTP](https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods) e [manuale curl](https://curl.se/docs/manpage.html)
- [CREATE TABLE in MariaDB](https://mariadb.com/docs/server/server-usage/tables/create-table)
