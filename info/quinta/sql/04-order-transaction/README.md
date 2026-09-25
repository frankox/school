# Esercizio 04 — Ordine composto e gestione dello stock

## Obiettivo

Completare `OrderService::create()` per salvare una risorsa composta da un
ordine e dalle sue righe, aggiornando anche la disponibilità dei prodotti.

Qui la transazione è necessaria: una richiesta produce più modifiche che
devono riuscire tutte insieme.

```text
creazione ordine
├── INSERT orders
└── per ogni prodotto
    ├── UPDATE products (riduzione stock)
    └── INSERT order_items
```

Se manca lo stock o una query fallisce, nessuna modifica deve restare nel
database.

## Input

```json
{
  "customer_id": 7,
  "items": [
    {"product_id": 10, "quantity": 2},
    {"product_id": 20, "quantity": 1}
  ]
}
```

Il parsing e la validazione sono già implementati in `CreateOrderRequest`.
Il lavoro va svolto esclusivamente nel metodo `OrderService::create()`.

Requisiti:

1. aprire una transazione;
2. inserire l'ordine e recuperarne l'ID;
3. preparare una sola volta la query dello stock e quella delle righe;
4. ridurre lo stock solo quando è sufficiente;
5. controllare che l'`UPDATE` abbia modificato esattamente una riga;
6. effettuare `commit()` solo dopo tutte le operazioni;
7. effettuare `rollBack()` e rilanciare l'eccezione in caso di errore.

## Verifica

```bash
php tests/run.php
```

## Prova con MariaDB

Attenzione: `schema.sql` ricrea le quattro tabelle dell'esercizio e inserisce
alcuni dati dimostrativi.

```bash
mariadb -u root -p < schema.sql
```

```bash
export DB_DSN='mysql:host=127.0.0.1;dbname=shop;charset=utf8mb4'
export DB_USER='root'
export DB_PASSWORD='password'
php -S localhost:8000 -t public
```

```bash
curl -i -X POST \
  -H 'Content-Type: application/json' \
  --data-binary @examples/order.json \
  http://localhost:8000/api/orders.php
```
