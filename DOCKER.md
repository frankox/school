# Usare gli esercizi con Docker

È sufficiente Docker con il comando `docker compose` (Docker Desktop lo include). Scaricate il repository come ZIP se non avete Git. Alla prima esecuzione Docker scarica le immagini; per TypeScript `npm ci` scarica i pacchetti **dentro** il container. Sul computer non servono PHP, Node.js, npm, MariaDB, XAMPP o curl.

Aprire un terminale **nella cartella dell'esercizio**, dove si trova `compose.yaml`. I comandi sono uguali in PowerShell e nelle shell Linux/macOS. Se su Linux l'utente non ha accesso a Docker, eseguire gli stessi comandi con `sudo` davanti a `docker`.

| Esercizio | Cartella | Servizi |
| --- | --- | --- |
| Fondamenti SQL | `info/sql/fondamenti-sql` | MariaDB, phpMyAdmin |
| API libri | `info/API/00-example-books-api` | PHP, MariaDB, client HTTP, phpMyAdmin |
| API CSV | `info/API/01-csv-to-sql-api` | PHP, client HTTP |
| API 02 | `info/API/02-create-student-api` | PHP, MariaDB, client HTTP, phpMyAdmin |
| API 02b | `info/API/02b-get-students-api` | PHP, MariaDB, client HTTP, phpMyAdmin |
| API 03 | `info/API/03-json-batch-transaction` | PHP, MariaDB, client HTTP, phpMyAdmin |
| API 04 | `info/API/04-order-transaction` | PHP, MariaDB, client HTTP, phpMyAdmin |
| Valutazione azienda | `gestione-progetto/valutazione-azienda` | Node.js, TypeScript, test |

## Test e avvio

Nelle cartelle delle API con esercizi da completare (01, 02, 02b, 03, 04):

```bash
docker compose run --build --rm test
docker compose up -d --build --wait
docker compose run --rm client -i http://app:8000/health.php
```

Il primo comando esegue i test e mostra anche gli eventuali fallimenti previsti dalla consegna. L'esempio dei libri (00) è già completo e non ha una suite di test: per quello partire dal secondo comando. Il secondo avvia PHP e, dove serve, MariaDB. Il terzo fa una richiesta HTTP dal client nel container. Per altre richieste, usare gli esempi nel README dell'esercizio. L'API è accessibile anche dal browser su <http://127.0.0.1:8000/>. Se un altro esercizio sta già usando la porta 8000, fermarlo prima con `docker compose down` dalla sua cartella. Lo stesso vale per phpMyAdmin sulla porta 8080.

Per Fondamenti SQL:

```bash
docker compose up -d --wait
docker compose exec db mariadb -u school_user -pschool_dev school_lab
```

Nella shell MariaDB si possono scrivere query SQL terminandole con `;`; `exit` la chiude. Per vedere le tabelle nel browser aprire <http://127.0.0.1:8080/>. Credenziali: utente `school_user`, password `school_dev`.

Per un'API con database, dalla **sua** cartella:

```bash
docker compose exec db mariadb -u school_user -pschool_dev NOME_DATABASE
docker compose --profile admin up -d --wait
```

Sostituire `NOME_DATABASE` con il nome indicato nel README: `school_example`, `school_ex02`, `school_ex02_get`, `school_ex03` o `shop_ex04`. Il secondo comando rende disponibile phpMyAdmin su <http://127.0.0.1:8080/> con le stesse credenziali. La porta del database resta interna alla rete Docker.

Per Valutazione azienda:

```bash
docker compose run --rm test
```

## Dati e arresto

`docker compose down` ferma i container dell'esercizio. I database restano nel volume e lo schema viene importato automaticamente **solo al primo avvio**. Per ricominciare con i dati iniziali, dalla cartella dell'esercizio usare:

```bash
docker compose down -v
docker compose up -d --build --wait
```

`down -v` cancella i dati del database di quell'esercizio. Per Valutazione azienda elimina invece la cache delle dipendenze Node.js; il codice rimane nella cartella del progetto. I progetti Compose hanno nomi distinti, quindi i volumi dei diversi esercizi restano separati.
