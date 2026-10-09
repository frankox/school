# Valutazione di un'azienda con il reddito medio prospettico

## Situazione aziendale

Un'impresa dispone dei rendiconti degli ultimi cinque esercizi, ma non conosce ancora il risultato dell'anno corrente. Per ogni esercizio sono disponibili il **reddito lordo prima delle imposte** e le **imposte**. Il committente chiede una stima del reddito netto dell'anno corrente, dei redditi netti dei prossimi `n` anni e del valore dell'impresa secondo il metodo semplificato della capitalizzazione del reddito medio prospettico.

Scrivi in **TypeScript** la funzione `valueCompany` nel file [`src/companyValuation.ts`](src/companyValuation.ts). I tipi, la firma e i [test](tests/companyValuation.test.ts) sono già pronti. Lavora sulla funzione, senza cambiare i risultati attesi nei test. Il testo della consegna è in italiano; nomi, commenti e messaggi del codice sono in inglese.

## Dati in ingresso e risultato

La funzione riceve:

- `statements`: **esattamente cinque** oggetti `{ grossIncome, taxes }`, ordinati dal più vecchio al più recente;
- `projectionYears`: il numero intero positivo `n` di anni da stimare;
- `capitalizationRate`: un numero positivo in forma decimale, per esempio `0.10` per il 10%.

Tutti gli importi sono espressi nella stessa unità monetaria, per esempio euro. Il risultato deve contenere `historicalNetIncomes` (i cinque redditi storici al netto delle imposte), `currentYearIncome`, `projectedIncomes` (un elemento per ciascuno dei `n` anni), `averageProjectedIncome` e `companyValue`.

L'**anno corrente** è il primo anno senza rendiconto. È anche il **primo** dei `n` anni prospettici: con `n = 3` devi stimare l'anno corrente e i due successivi. Non aggiungere l'anno corrente una seconda volta alla media.

## Modello da applicare

1. Per ogni bilancio calcola il reddito netto: `grossIncome - taxes`.
2. Associa ai cinque redditi netti gli anni `x = 1, 2, 3, 4, 5`. Trova la retta `y = m × x + q` che meglio approssima **tutti e cinque** i punti con il metodo dei minimi quadrati. È il principio della funzione [TENDENZA di Excel](https://support.microsoft.com/it-it/excel/functions/trend-function) per una sola variabile, con intercetta libera.
3. Usa la retta per prevedere il reddito netto agli anni `x = 6, 7, …, 5 + n`. La previsione per `x = 6` è `currentYearIncome`.
4. Calcola la media aritmetica di queste `n` previsioni.
5. Stima il valore aziendale: `companyValue = averageProjectedIncome / capitalizationRate`.

Per calcolare la retta, indica con `ȳ` la media dei cinque redditi netti. Qui la media degli anni `x` è sempre `x̄ = 3`. Puoi usare:

```text
m = Σ[(x - 3) × (y - ȳ)] / Σ[(x - 3)²]
q = ȳ - m × 3
previsione(x) = m × x + q
```

La sommatoria `Σ` comprende i cinque anni storici. Il denominatore vale `10`, ma è utile capire da dove viene. Non usare soltanto la differenza fra il primo e l'ultimo reddito: i tre valori intermedi devono influenzare la previsione.

### Esempio da controllare a mano

| Anno storico | Reddito lordo (€) | Imposte (€) | Reddito netto (€) |
| --- | ---: | ---: | ---: |
| 1 | 125 000 | 25 000 | 100 000 |
| 2 | 190 000 | 40 000 | 150 000 |
| 3 | 150 000 | 30 000 | 120 000 |
| 4 | 220 000 | 40 000 | 180 000 |
| 5 | 250 000 | 50 000 | 200 000 |

La retta ottenuta ha pendenza `23 000` e intercetta `81 000`. Con `n = 3` le previsioni sono `219 000`, `242 000` e `265 000` euro. Il reddito medio prospettico è `242 000` euro; con un tasso del 10% il valore stimato è `2 420 000` euro.

## Regole sugli input

Se un input non rispetta queste regole, lancia un `RangeError`:

- i bilanci devono essere esattamente cinque;
- redditi lordi e imposte devono essere numeri finiti, non negativi; le imposte non possono superare il reddito lordo dello stesso anno;
- `projectionYears` deve essere un intero positivo;
- `capitalizationRate` deve essere finito e maggiore di zero.

Non arrotondare i calcoli intermedi: i test confrontano i risultati numerici con una piccola tolleranza. Questa è una **stima didattica**: la tendenza lineare può dare previsioni economicamente poco plausibili se proiettata troppo lontano. Il tasso è un'ipotesi fornita dal committente; la funzione non deve ricavarlo dai bilanci.

## Come lavorare

Serve Node.js **20 o successivo** con npm. Apri un terminale nella cartella `gestione-progetto/valutazione-azienda`, quella che contiene `package.json`, poi esegui:

```bash
npm install
npm test
```

`npm install` installa TypeScript e gli strumenti necessari per i test. `npm test` compila i file `.ts` e poi esegue i test. All'inizio i test falliscono perché la funzione contiene soltanto un segnaposto: è previsto. Quando modifichi il codice, riesegui `npm test`. Il lavoro è completo quando tutti i test passano.

Procedi nell'ordine suggerito dai test: controlla gli input, calcola i cinque redditi netti, ricava la retta, genera le previsioni, calcola media e valore aziendale. Un test sulla serie irregolare verifica che tu usi tutti e cinque i bilanci; quelli sulla serie costante e decrescente aiutano a controllare la formula della tendenza. Se un test fallisce, il suo nome indica quale comportamento manca.

Puoi consultare la [documentazione Microsoft di TENDENZA](https://support.microsoft.com/it-it/excel/functions/trend-function) per confrontare il metodo statistico. La funzione TypeScript va comunque scritta da te: non serve Excel per eseguire l'esercizio.
