# Dal browser ai dati: API, Inspector e SQL

Questa guida permette di riprendere anche in autonomia l'osservazione di una richiesta HTTP vista a lezione. Per la parte iniziale bastano un browser, per esempio Chrome o Edge, e una connessione Internet: non devi installare un server o scrivere un'API. Gli esempi sono già forniti; il tuo compito è capire che cosa viene richiesto e che cosa viene restituito.

Per riprendere la dimostrazione della GET, leggi fino alla sezione DQL. Le sezioni successive collegano quanto osservato al percorso DML e sono un approfondimento.

## Chi fa che cosa

Un'**API** (*Application Programming Interface*) è un'interfaccia attraverso cui un programma mette funzionalità a disposizione di altri programmi. Qui osserviamo un'API web: un **client**, come il browser, invia una richiesta HTTP a un indirizzo e riceve una risposta dal **server**.

Il **frontend** è la parte dell'applicazione con cui interagisce l'utente. Il **backend** è la parte eseguita sul server: interpreta le richieste, applica le regole dell'applicazione e può accedere ai dati. Per esempio, quando apri l'elenco delle attività, il frontend può chiedere al backend di restituirlo.

Il backend può leggere un database SQL, un database NoSQL, un file o un altro servizio; può anche restituire dati già presenti in memoria o in cache. Il browser normalmente comunica con l'API, non invia direttamente istruzioni SQL al database.

## Osservare una GET con l'Inspector

Gli **strumenti per sviluppatori**, spesso chiamati *DevTools* o *Inspector*, contengono più pannelli. **Elements/Elementi** mostra la struttura della pagina; **Network/Rete** mostra le richieste e le risposte HTTP. Per osservare un'API ci interessa Network.

1. Apri nel browser [questa attività dimostrativa](https://jsonplaceholder.typicode.com/todos/1).
2. Apri gli strumenti per sviluppatori dal menu del browser; su molti computer puoi usare **F12**. Seleziona **Network/Rete**.
3. Seleziona il filtro **All/Tutto**, poi ricarica la pagina con gli strumenti aperti. Le richieste precedenti all'apertura del pannello potrebbero non essere state registrate.
4. Seleziona la richiesta con indirizzo `https://jsonplaceholder.typicode.com/todos/1`. Il nome nella lista può essere soltanto `1`. Ignora eventuali richieste diverse, come quella per l'icona del sito.
5. In **Headers/Intestazioni** cerca **Request URL**, **Request Method** e **Status Code**. Nel caso normale vedrai il metodo `GET` e lo stato `200 OK`.
6. Apri **Response/Risposta** per leggere il corpo restituito; **Preview/Anteprima**, quando disponibile, ne facilita la lettura.

**Perché All e non soltanto Fetch/XHR?** Aprendo l'indirizzo dalla barra del browser, la richiesta può essere classificata come documento. Il filtro Fetch/XHR serve invece a isolare le richieste di quel tipo effettuate dal codice di una pagina. [Riferimento sui filtri e sui dettagli delle richieste](https://developer.chrome.com/docs/devtools/network/reference).

Riferimento: [Chrome DevTools — osservare le richieste nel pannello Network](https://developer.chrome.com/docs/devtools/network).

### Leggere richiesta e risposta

| Elemento | Che cosa indica nell'esempio |
| --- | --- |
| URL | L'indirizzo della risorsa richiesta: l'attività con identificativo `1`. |
| Metodo `GET` | Il client chiede di leggere una risorsa. Non sta chiedendo di crearla o modificarla. |
| Stato `200 OK` | Il server segnala che la richiesta è stata gestita con successo. |
| Corpo della risposta | I dati restituiti al client, in questo caso in formato JSON. |

La risposta dell'esempio è:

```json
{
  "userId": 1,
  "id": 1,
  "title": "delectus aut autem",
  "completed": false
}
```

**JSON** è un formato per scambiare dati. Qui le parentesi graffe racchiudono un oggetto composto da coppie nome-valore: `id` vale `1`, `title` contiene testo e `completed` vale `false`, cioè l'attività non risulta completata. Le parentesi quadre `[...]` indicano invece un array, cioè una sequenza di valori: per esempio un elenco di oggetti. JSON non è SQL e non dimostra l'uso di un database NoSQL. [Riferimento JSON](https://developer.mozilla.org/en-US/docs/Glossary/JSON).

La **richiesta** parte dal client; la **risposta** arriva dal server. Il corpo della risposta non è il codice del backend né la query che potrebbe aver eseguito.

Il servizio usato è [JSONPlaceholder](https://jsonplaceholder.typicode.com/), un'API dimostrativa con dati fittizi. Non serve un account. Se non è raggiungibile, puoi comunque studiare la risposta riportata sopra e applicare lo stesso metodo di lettura agli screenshot forniti dal docente.

## DQL: leggere dati attraverso un'API

Una API che legge un'attività potrebbe eseguire sul backend una query come questa, **se usasse un database SQL con una tabella `tasks` avente queste colonne**:

```sql
SELECT id, title, completed
FROM tasks
WHERE id = 1;
```

È un esempio ipotetico di **DQL**, perché legge dati. Non è la query di JSONPlaceholder e non devi eseguirla: serve a collegare la lettura dei dati via HTTP alla lettura con SQL. Un backend NoSQL potrebbe invece cercare un documento con lo stesso identificativo.

**Dallo screenshot possiamo dire che sono stati ricevuti determinati dati tramite una GET. Non possiamo concludere che il server abbia eseguito proprio una SELECT o che usi MySQL.** Per sapere come lavora il backend servono il suo codice, la sua documentazione o altri elementi di prova.

Vai al [percorso DQL](../SQL/DQL/README.md).

## DML: creare, modificare ed eliminare dati

Nelle API organizzate attorno a risorse, come libri o attività, si incontrano spesso queste associazioni:

| Azione | Possibile richiesta HTTP | Possibile operazione SQL | Operazione analoga in un database a documenti |
| --- | --- | --- | --- |
| Leggere un'attività | `GET /tasks/1` | `SELECT` — DQL | Cercare un documento. |
| Creare un'attività | `POST /tasks` | `INSERT` — DML | Inserire un documento. |
| Modificare parte di un'attività | `PATCH /tasks/1` | `UPDATE` — DML | Aggiornare alcuni campi. |
| Sostituire un'attività | `PUT /tasks/1` | `UPDATE` o inserimento, secondo il caso — DML | Sostituire o creare un documento. |
| Eliminare un'attività | `DELETE /tasks/1` | `DELETE` — DML | Eliminare un documento. |

**Sono collegamenti tra scopi, non traduzioni automatiche.** `POST` può anche avviare un'elaborazione; una richiesta può richiedere più operazioni sui dati. `PUT` indica la sostituzione della risorsa, `PATCH` una modifica parziale. Le operazioni effettive dipendono dall'API. *NoSQL* comprende sistemi diversi e non identifica un unico linguaggio alternativo a SQL. [Riferimento sui metodi HTTP](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Methods).

### Un esempio di POST da riconoscere in uno screenshot

Immagina di osservare questa richiesta, qui rappresentata in forma semplificata:

```http
POST /tasks
Content-Type: application/json

{"title": "Ripassare SQL", "completed": false}
```

Il client sta inviando i dati di una nuova attività nel **corpo della richiesta**, visibile normalmente in **Payload**. Un backend SQL potrebbe inserirli con `INSERT`; un backend a documenti potrebbe creare un documento. Una risposta [`201 Created`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/201) indica la creazione di una risorsa e può contenere il suo identificativo. Un esempio di SQL possibile, ipotizzando una tabella già predisposta, è:

```sql
INSERT INTO tasks (title, completed)
VALUES ('Ripassare SQL', FALSE);
```

È un esempio concettuale: non devi implementare un backend o conoscere JavaScript per comprenderlo. In JSONPlaceholder anche le richieste di creazione, modifica ed eliminazione sono **simulate e non persistono i cambiamenti**: una risposta di successo in quel servizio non prova una modifica permanente dei dati. [Esempi ufficiali del servizio](https://jsonplaceholder.typicode.com/#guide).

Vai al [percorso DML](../SQL/DML/README.md).

## E il DDL?

Il **DDL** prepara o modifica la struttura: per esempio `CREATE TABLE` definisce una tabella; il DML ne modifica le righe e il DQL le legge. Creare un'attività con `POST` non significa creare una tabella. La struttura viene normalmente predisposta separatamente dalle richieste con cui gli utenti lavorano sui dati: non c'è una corrispondenza generale fra un metodo HTTP e `CREATE TABLE`.

Riparti dall'[introduzione SQL](../SQL/README.md) e dal [primo esercizio DDL](../SQL/DDL/01-database-e-tabelle/README.md) per database e tabelle.

## Un backend SQL reale nel repository

L'[API dei libri già funzionante](00-example-books-api/README.md) collega realmente richieste HTTP e MariaDB. Nel [codice fornito](00-example-books-api/public/api/books.php) puoi vedere `GET` associato a `SELECT`, `POST` a `INSERT`, `PUT` a `UPDATE` e `DELETE` a `DELETE`. Qui l'associazione è verificabile leggendo il codice: non è dedotta dal solo JSON.

Questo è un approfondimento: per eseguirlo segui il suo README con Docker, comprese le istruzioni di arresto e reset. Non occorre avviarlo per capire gli screenshot di una GET.

## Ripasso: spiegare uno screenshot

Quando descrivi uno screenshot di Network:

1. Individua URL e metodo della richiesta, se visibili.
2. Distingui i dati inviati dal client da quelli ricevuti dal server.
3. Riporta almeno un nome e il relativo valore presenti nella risposta, spiegandone il significato nel contesto.
4. Collega lo scopo a DQL o DML, quando pertinente, distinguendo il dato osservabile da un'ipotesi sul backend.

Per esempio: «Il browser ha richiesto l'attività 1 con GET. La risposta JSON riporta `completed: false`, quindi l'attività non risulta completata. Se il backend usasse SQL, la lettura potrebbe essere ottenuta con SELECT; dall'immagine non posso sapere quale database usi».

[Torna alle API](README.md) · [Torna a Informatica](../README.md).
