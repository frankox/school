# Esercizio 02b — Una GET per gli studenti, partendo quasi da zero

## Con Docker (solo Docker richiesto)

Aprire un terminale in questa cartella. `schema.sql` crea e popola il database al primo avvio.

```bash
docker compose run --build --rm test
docker compose up -d --build --wait
docker compose run --rm client -i http://app:8000/api/students.php
docker compose run --rm client -i "http://app:8000/api/students.php?min_age=18"
docker compose exec db mariadb -u school_user -pschool_dev school_ex02_get -e "SELECT id, first_name, last_name, age, student_code FROM students ORDER BY id;"
docker compose --profile admin up -d --wait
```

I test usano un database SQLite temporaneo in memoria e verificano stato HTTP e risposta JSON, senza controllare la forma della query SQL. Alcuni falliscono finché la GET non è completata. phpMyAdmin è su <http://127.0.0.1:8080/> (utente `school_user`, password `school_dev`). `docker compose down` ferma i container; `docker compose down -v` elimina i dati e permette di ricaricare lo schema al prossimo avvio. [Guida Docker comune](../../../DOCKER.md).



Questo esercizio si svolge dopo la `POST` dell'esercizio 02 e prima delle transazioni. Osservate la [GET dei libri](../00-example-books-api/public/api/books.php), poi costruite una GET per un database diverso. Qui avete già schema, dati, connessione PDO, controllo del metodo HTTP e gestione degli errori. La lettura del parametro e la risposta sono da completare in `public/api/students.php`; la lettura dei dati è in `src/StudentRepository.php`.

## Contratto dell'API

| Richiesta | Risposta |
| --- | --- |
| `GET /api/students.php` | `200` con tutti gli studenti, ordinati per `id` |
| `GET /api/students.php?min_age=18` | `200` con gli studenti la cui età è almeno 18 |
| `GET /api/students.php?min_age=100` | `200` con `[]` |
| `GET` con `min_age` non intero tra 14 e 100 | `422` con `{"error":"..."}` |
| Qualsiasi altro metodo HTTP | `405` con `Allow: GET` |
| Errore interno o del database | `500` con `{"error":"<messaggio dell'eccezione>"}` |

La risposta di una GET riuscita è un array JSON di elementi con i campi di `StudentDto`:

```json
[
  {"firstName": "Giulia", "lastName": "Bianchi", "age": 16, "studentCode": "ST-001"},
  {"firstName": "Marco", "lastName": "Rossi", "age": 17, "studentCode": "ST-002"}
]
```

Sono mostrati solo i primi due elementi per brevità. La risposta deve contenere esattamente `firstName`, `lastName`, `age` e `studentCode` per ogni studente, nello stesso ordine delle righe ordinate per `id`. `id` serve per l'ordinamento nel database ma non è un campo di `StudentDto`.

`schema.sql` crea `school_ex02_get.students` e inserisce sei studenti, con età 16, 17, 18, 18, 19 e 21. Così il filtro restituisce risultati diversi dalla lista completa e permette di verificare la `WHERE`.

## Lavoro da fare

1. In `public/api/students.php`, leggere `min_age` dalla URL. Se manca, elencare tutti gli studenti. Se è presente, accettare solo un intero tra 14 e 100; per gli altri valori sollevare `InvalidArgumentException`, che lo scheletro trasforma già in `422`.
2. In `src/StudentRepository.php`, preparare una `SELECT` con PDO. Usare `WHERE age >= :min_age` solo quando il filtro è presente, passando il valore come parametro. Ordinare per `id`. Non concatenare il valore ricevuto nella query.
3. Leggere le righe con `fetchAll()`, trasformarle in elementi `StudentDto` e rispondere con `respond(200, $students)`. La risposta è l'array stesso, senza un oggetto `students` esterno. Nessuna transazione è richiesta.

Il percorso è `curl → public/api/students.php → config/database.php → MariaDB → JSON`. La connessione è già pronta in `config/database.php`; la GET deve fare tutto il resto. Confrontate con la GET dell'esempio dei libri, ma qui scrivete voi anche la validazione del filtro e la conversione delle righe in DTO.

## Test API senza MariaDB

Dalla cartella dell'esercizio:

```bash
php tests/run.php
```

Su Windows con XAMPP usare `& "C:\xampp\php\php.exe" .\tests\run.php` in PowerShell dopo aver abilitato `pdo_sqlite` nel PHP locale; con Docker il driver è già nell'immagine. I test avviano un server PHP locale, chiamano davvero l'endpoint HTTP e usano SQLite in memoria con gli stessi sei studenti. Controllano il contenuto dell'array di DTO, il filtro, la lista vuota e gli stati di errore. Nello scheletro iniziale i test GET falliscono con `501`: è il lavoro ancora da svolgere. Il test del metodo non consentito è già verde. Serve il permesso di aprire una porta locale sul computer.

Su Omarchy/Arch, per usare il PHP del computer invece di Docker, installare `php-sqlite` con `sudo pacman -Syu php-sqlite` e abilitare `extension=pdo_sqlite` in `/etc/php/php.ini`. Per servire l'API locale con MariaDB, abilitare anche `extension=pdo_mysql`; il modulo è incluso nel pacchetto `php`.

Se compare `Impossibile avviare il server PHP`, il processo PHP dei test non è riuscito a creare il server figlio. Il messaggio successivo indica l'errore di sistema; controllate che l'esecuzione di processi non sia bloccata e che la cartella temporanea sia scrivibile. Se compare `Il server PHP non si è avviato`, viene mostrato anche il log del server. Un altro server sulla porta `8000` non interferisce con i test: scelgono una porta libera automaticamente.

`tests/test-database.php` è solo per i test: `DB_FACTORY_FILE` sostituisce la connessione a MariaDB con un database SQLite nuovo per ogni richiesta. Nelle prove normali non impostate questa variabile.

## Prova con MariaDB

Avviate MySQL nel pannello XAMPP. Dalla pagina iniziale di phpMyAdmin importate `schema.sql`: crea il database e inserisce i sei studenti. Il database `school_ex02_get` è separato da quello dell'esercizio 02. Se importate di nuovo lo schema senza cancellare i dati, i codici univoci impediscono un secondo inserimento; per ricominciare importate prima `cleanup.sql`.

Nella cartella dell'esercizio, avviate il server PHP:

```powershell
& "C:\xampp\php\php.exe" -S localhost:8000 -t .\public
```

In un altro terminale:

```powershell
curl.exe -i http://localhost:8000/health.php
curl.exe -i http://localhost:8000/api/students.php
curl.exe -i "http://localhost:8000/api/students.php?min_age=18"
```

`health.php` controlla solo il server PHP, non il database. Il driver `pdo_mysql` deve essere attivo. La connessione predefinita usa `127.0.0.1:3306`, utente `root`, password vuota. Per un'altra configurazione, impostate `DB_DSN`, `DB_USER` e `DB_PASSWORD` nello stesso terminale prima di avviare PHP, come nell'[esercizio 02](../02-create-student-api/README.md#seconda-fase-preparare-xampp), sostituendo il nome del database con `school_ex02_get`.

Su Linux, dopo aver importato lo schema con `mariadb -u root -p < schema.sql`, avviate `php -S 127.0.0.1:8000 -t public` e usate `curl` al posto di `curl.exe`.

Per confrontare le righe del database con gli elementi restituiti dall'API, eseguite in phpMyAdmin:

```sql
SELECT id, first_name, last_name, age, student_code
FROM school_ex02_get.students
ORDER BY id;
```

Quando avete finito, importate `cleanup.sql` dalla pagina iniziale di phpMyAdmin. Elimina definitivamente solo `school_ex02_get`.
