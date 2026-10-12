# Esercizio 04 — Ordine composto e gestione dello stock

## Con Docker (solo Docker richiesto)

Aprire un terminale in questa cartella. Lo schema crea clienti e prodotti di esempio al primo avvio.

```bash
docker compose run --build --rm test
docker compose up -d --build --wait
docker compose run --rm client -i -X POST -H "Content-Type: application/json" --data-binary "@examples/order.json" http://app:8000/api/orders.php
docker compose exec db mariadb -u school_user -pschool_dev shop_ex04 -e "SELECT * FROM orders; SELECT * FROM order_items; SELECT * FROM products;"
docker compose --profile admin up -d --wait
```

phpMyAdmin è su <http://127.0.0.1:8080/> (utente `school_user`, password `school_dev`). `docker compose down` ferma i container; `docker compose down -v` elimina i dati e ripristina quelli iniziali al prossimo avvio. [Guida Docker comune](../../../DOCKER.md).



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

Attenzione: `schema.sql` ricrea le quattro tabelle nel database dedicato `shop_ex04` e inserisce
alcuni dati dimostrativi.

```bash
mariadb -u root -p < schema.sql
```

```bash
export DB_DSN='mysql:host=127.0.0.1;dbname=shop_ex04;charset=utf8mb4'
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

Per cancellare i dati di prova, eseguire `mariadb -u root -p < cleanup.sql` oppure importare `cleanup.sql` in phpMyAdmin. Lo script elimina definitivamente soltanto `shop_ex04`.
