# Esercizio 02 — Da JSON a risorsa Student

## Obiettivo

Realizzare un endpoint `POST /api/students.php` che riceve una risorsa JSON,
la converte in un DTO tipizzato e la salva con una query preparata PDO.

Il lavoro va svolto esclusivamente nei due metodi contrassegnati con `TODO`:

- `CreateStudentRequest::fromJson()`;
- `StudentRepository::create()`.

Non è richiesta una transazione: il salvataggio contiene un solo `INSERT`, che
con MariaDB/InnoDB è già atomico.

## Corpo della richiesta

```json
{
  "first_name": "Mario",
  "last_name": "Rossi",
  "age": 18,
  "student_code": "00123"
}
```

Regole:

1. il JSON deve contenere un oggetto;
2. sono obbligatori esattamente i quattro campi mostrati;
3. nomi e codice devono essere stringhe non vuote;
4. `age` deve essere un intero compreso tra 14 e 100;
5. `student_code` resta una stringa, conservando gli zeri iniziali;
6. valori e query devono essere separati mediante parametri PDO.

## Verifica

Non serve un database per i test: un doppio di PDO registra query e parametri.

```bash
php tests/run.php
```

## Prova con MariaDB

Creare il database e la tabella:

```bash
mariadb -u root -p < schema.sql
```

Esportare, se necessario, le credenziali e avviare il server:

```bash
export DB_DSN='mysql:host=127.0.0.1;dbname=school;charset=utf8mb4'
export DB_USER='root'
export DB_PASSWORD='password'
php -S localhost:8000 -t public
```

Inviare la richiesta:

```bash
curl -i -X POST \
  -H 'Content-Type: application/json' \
  --data-binary @examples/student.json \
  http://localhost:8000/api/students.php
```

La risposta corretta usa lo stato `201 Created` e contiene l'identificatore:

```json
{"id":1}
```
