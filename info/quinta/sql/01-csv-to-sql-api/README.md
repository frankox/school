# Esercizio 01 — Da CSV a INSERT SQL

## Obiettivo

Completare un convertitore che riceve dati CSV e produce una singola istruzione `INSERT` per MariaDB/MySQL.

L'API e i test sono già predisposti. Il lavoro va svolto esclusivamente nel metodo `convert` del file [`src/CsvToSqlConverter.php`](src/CsvToSqlConverter.php).

## Installare e verificare PHP

È necessario PHP 8.1 o successivo. Non servono Composer o librerie esterne.

### Windows con XAMPP

XAMPP include già PHP. Se XAMPP non è installato:

1. scaricarlo dal [sito ufficiale di Apache Friends](https://www.apachefriends.org/download.html);
2. eseguire l'installazione lasciando selezionati almeno Apache, MySQL e PHP;
3. mantenere il percorso proposto `C:\xampp`, se non ci sono esigenze diverse.

Aprire PowerShell nella cartella dell'esercizio e controllare la versione con:

```powershell
C:\xampp\php\php.exe --version
```

PHP può essere usato direttamente con il percorso completo, senza modificare Windows:

```powershell
C:\xampp\php\php.exe tests\run.php
C:\xampp\php\php.exe -S localhost:8000 -t public
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

## Avvio rapido

Eseguire i test dalla cartella dell'esercizio:

```bash
php tests/run.php
```

All'inizio i test falliscono perché il convertitore non è ancora implementato. L'esercizio è terminato quando tutti i test passano.

## Regole di conversione

Il comportamento completo è descritto anche dai test già forniti.

1. Il CSV usa la virgola come separatore e la prima riga contiene i nomi delle colonne.
2. Database, tabella e colonne sono identificatori validi solo se rispettano la forma `[A-Za-z_][A-Za-z0-9_]*`.
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

## Provare l'API

Avviare il server integrato di PHP dalla cartella dell'esercizio:

```bash
php -S localhost:8000 -t public
```

Controllare che sia attivo:

```bash
curl http://localhost:8000/health.php
```

Inviare il CSV di esempio nel corpo di una richiesta `POST`:

```bash
curl -i \
  -X POST \
  -H "Content-Type: text/csv" \
  --data-binary @examples/students.csv \
  "http://localhost:8000/api/generate-insert.php?database=school&table=students"
```

Prima di completare il convertitore l'endpoint risponde con lo stato `501 Not Implemented`. Dopo aver fatto passare i test restituisce la query SQL con stato `200 OK`.

Con XAMPP è possibile esporre la cartella `public` tramite Apache e usare gli stessi file `health.php` e `api/generate-insert.php`.

## Limiti intenzionali

Questo primo esercizio genera una query ma non la esegue. Il collegamento a MariaDB, le query preparate e le transazioni saranno affrontati negli esercizi successivi.
