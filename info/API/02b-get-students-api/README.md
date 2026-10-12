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

I test usano un database simulato; alcuni falliscono finché la GET non è completata. phpMyAdmin è su <http://127.0.0.1:8080/> (utente `school_user`, password `school_dev`). `docker compose down` ferma i container; `docker compose down -v` elimina i dati e permette di ricaricare lo schema al prossimo avvio. [Guida Docker comune](../../../DOCKER.md).



Questo esercizio si svolge dopo la `POST` dell'esercizio 02 e prima delle transazioni. Osservate la [GET dei libri](../00-example-books-api/public/api/books.php), poi costruite una GET per un database diverso. Qui avete già schema, dati, connessione PDO, controllo del metodo HTTP e gestione degli errori. La lettura del parametro, la query e la risposta sono da scrivere in `public/api/students.php`.

## Contratto dell'API

| Richiesta | Risposta |
| --- | --- |
| `GET /api/students.php` | `200` con tutti gli studenti, ordinati per `id` |
| `GET /api/students.php?min_age=18` | `200` con gli studenti la cui età è almeno 18 |
| `GET /api/students.php?min_age=100` | `200` con `{"students":[]}` |
| `GET` con `min_age` non intero tra 14 e 100 | `422` con `{"error":"..."}` |
| Qualsiasi altro metodo HTTP | `405` con `Allow: GET` |

La risposta di una GET riuscita ha questa forma:

```json
{
  "students": [
    {"id": 1, "first_name": "Giulia", "last_name": "Bianchi", "age": 16},
    {"id": 2, "first_name": "Marco", "last_name": "Rossi", "age": 17}
  ]
}
```

Sono mostrate solo le prime due righe per brevità. La tabella contiene anche `student_code`, ma **la risposta non deve mai includerlo**. Selezionate direttamente le sole colonne pubbliche: `id`, `first_name`, `last_name`, `age`. In questo modo la query documenta quali dati l'API può esporre.

`schema.sql` crea `school_ex02_get.students` e inserisce sei studenti, con età 16, 17, 18, 18, 19 e 21. Così il filtro restituisce risultati diversi dalla lista completa e permette di verificare la `WHERE`.

## Lavoro da fare

1. In `public/api/students.php`, leggere `min_age` dalla URL. Se manca, elencare tutti gli studenti. Se è presente, accettare solo un intero tra 14 e 100; per gli altri valori sollevare `InvalidArgumentException`, che lo scheletro trasforma già in `422`.
2. Preparare una `SELECT` con PDO. Usare `WHERE age >= :min_age` solo quando il filtro è presente, passando il valore a `execute()` come parametro. Ordinare per `id`. Non concatenare il valore ricevuto nella query.
3. Leggere le righe con `fetchAll()` e rispondere con `respond(200, ['students' => $rows])`. Sostituire il `LogicException` finale. Nessuna transazione è richiesta.

Il percorso è `curl → public/api/students.php → config/database.php → MariaDB → JSON`. La connessione è già pronta in `config/database.php`; la GET deve fare tutto il resto. Confrontate con la GET dell'esempio dei libri, ma qui scrivete voi anche la validazione del filtro e la scelta delle colonne.

## Test API senza MariaDB

Dalla cartella dell'esercizio:

```bash
php tests/run.php
```

Su Windows con XAMPP usare `& "C:\xampp\php\php.exe" .\tests\run.php` in PowerShell. I test avviano un server PHP locale, chiamano davvero l'endpoint HTTP e usano un database simulato con gli stessi sei studenti. Controllano lista, filtro, lista vuota, dati nascosti e stati di errore. All'inizio i test GET falliscono con `501`: è il lavoro ancora da svolgere. Il test del metodo non consentito è già verde. Serve il permesso di aprire una porta locale sul computer.

`tests/fake-database.php` è solo per i test: `DB_FACTORY_FILE` sostituisce la connessione a MariaDB mentre il server dei test è in esecuzione. Nelle prove normali non impostate questa variabile.

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

Per verificare la differenza tra tabella e risposta API, eseguite in phpMyAdmin:

```sql
SELECT id, first_name, last_name, age, student_code
FROM school_ex02_get.students
ORDER BY id;
```

Quando avete finito, importate `cleanup.sql` dalla pagina iniziale di phpMyAdmin. Elimina definitivamente solo `school_ex02_get`.
