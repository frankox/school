# SQL fiddle — guida breve

Un **SQL fiddle** è un ambiente online in cui prepari alcune tabelle, inserisci dati di prova ed esegui SQL dal browser. Per gli esercizi che lo prevedono è un'alternativa a XAMPP o Docker: permette di studiare le stesse query senza installare un database sul computer. L'equivalenza riguarda l'attività didattica usando il DBMS e lo schema richiesti dalla consegna.

Questa guida usa [db<>fiddle](https://dbfiddle.uk/), che non richiede un account. Esistono altri servizi con interfacce diverse: i passaggi seguenti si riferiscono a questo sito.

## Scegliere il database

1. Apri [db<>fiddle](https://dbfiddle.uk/).
2. Scegli **MariaDB 11.8** per [Fondamenti SQL](../fondamenti-sql/README.md), oppure **MySQL 8.4** per i percorsi [DDL](../DDL/README.md), [DML](../DML/README.md) e [DQL](../DQL/README.md).
3. Si apre un esempio. Premi **clear** per svuotare l'editor e controlla che nei menu in alto siano rimasti il DBMS e la versione corretti.

La scelta conta: MariaDB e MySQL condividono molta sintassi, mentre PostgreSQL e SQLite usano regole diverse. Segui sempre la versione richiesta dal README dell'esercizio.

## Preparare schema e query

Il servizio assegna già un database. Se una consegna usa un proprio `CREATE DATABASE` o `USE`, lascia fuori queste istruzioni e lavora sulle tabelle nel database disponibile. La creazione del database stesso, quando richiesta come obiettivo, va verificata con l'ambiente Docker della consegna.

Nel riquadro SQL scrivi prima le istruzioni di preparazione e poi la query. Puoi usare un unico riquadro per tutto:

```sql
CREATE TABLE esempio (
    id INT,
    nome VARCHAR(40)
);

INSERT INTO esempio (id, nome) VALUES (1, 'Anna'), (2, 'Luca');

SELECT id, nome
FROM esempio
ORDER BY id;
```

Premi **run**. Il risultato della `SELECT` compare sotto il riquadro: due righe, una per Anna e una per Luca. Se c'è un errore, leggi il messaggio associato al blocco SQL, correggi e riprova. Puoi anche dividere preparazione e query usando i comandi **add** tra i riquadri: vengono eseguiti dall'alto verso il basso nello stesso database.

Per un esercizio del repository, usa gli eventuali file `schema.sql` come preparazione e aggiungi dopo il tuo `solution.sql` o le tue query, nell'ordine indicato dalla consegna. Se l'esercizio non fornisce uno schema, parti dalla tua soluzione. Non eseguire l'esempio con Anna e Luca insieme alle tabelle di un esercizio: è solo una prova introduttiva.

## Osservare una modifica

Ogni pressione di **run** esegue nuovamente tutto lo script in un **database nuovo**. Il servizio non conserva una sessione modificabile come il database locale di XAMPP o il volume Docker. Per vedere l'effetto di un aggiornamento, mantieni nello script la preparazione e aggiungi, dopo la prima `SELECT`:

```sql
UPDATE esempio
SET nome = 'Sara'
WHERE id = 2;

SELECT id, nome
FROM esempio
ORDER BY id;
```

Premi **run**: il primo risultato mostra Anna e Luca; quello successivo mostra Anna e Sara. Se togli `CREATE TABLE` e `INSERT`, la nuova esecuzione non ritrova la tabella preparata prima.

Lo stesso vale per `DELETE` e per un inserimento seguito da altre modifiche: includi tutta la sequenza necessaria e inserisci `SELECT` nei punti in cui vuoi osservare i dati.

## Salvare il lavoro

Dopo un'esecuzione, copia l'indirizzo del browser per riaprire o condividere quello script e i relativi risultati. Conserva comunque il codice sul computer in un file `.sql`, per esempio nel file richiesto dalla consegna. Il pulsante **clear** svuota l'editor; prima di usarlo salva ciò che vuoi mantenere.

La [documentazione del servizio](https://dbfiddle.uk/llms.txt) conferma che i blocchi SQL vengono eseguiti in ordine su un database nuovo e che ogni esecuzione produce un collegamento al risultato.

[Torna all'introduzione SQL](../README.md).
