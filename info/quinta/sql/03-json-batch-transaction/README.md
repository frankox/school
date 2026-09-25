# Esercizio 03 — Importazione batch con transazione

## Obiettivo

Completare `StudentBatchImporter::import()` per ricevere un array JSON di
studenti e inserirli come un'unica operazione atomica.

Questo esercizio introduce le transazioni:

- tutti gli studenti vengono inseriti e si esegue `commit()`;
- se anche un solo inserimento fallisce, si esegue `rollBack()`;
- la query viene preparata una sola volta e riutilizzata.

## Input

```json
[
  {
    "first_name": "Mario",
    "last_name": "Rossi",
    "age": 18,
    "student_code": "00123"
  },
  {
    "first_name": "Anna",
    "last_name": "Verdi",
    "age": 19,
    "student_code": "00124"
  }
]
```

Il JSON deve essere un array non vuoto. Ogni elemento segue le regole del DTO
`StudentData`, già implementato. Tutte le righe devono essere validate prima
di aprire la transazione.

## Verifica

```bash
php tests/run.php
```

I test usano un doppio di PDO e verificano anche l'ordine delle operazioni.

## Prova con MariaDB

La tabella è la stessa dell'esercizio 02. Dopo averla creata con `schema.sql`:

```bash
export DB_DSN='mysql:host=127.0.0.1;dbname=school;charset=utf8mb4'
export DB_USER='root'
export DB_PASSWORD='password'
php -S localhost:8000 -t public
```

```bash
curl -i -X POST \
  -H 'Content-Type: application/json' \
  --data-binary @examples/students.json \
  http://localhost:8000/api/students-batch.php
```

Risposta prevista:

```json
{"inserted":2}
```
