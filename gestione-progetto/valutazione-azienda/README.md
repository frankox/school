# Valutazione di un'azienda con il reddito medio prospettico

## Situazione aziendale

Un'impresa dispone dei rendiconti di almeno cinque esercizi consecutivi, ma non conosce ancora il risultato dell'anno corrente. Per ogni esercizio sono disponibili l'**utile dell'azienda prima delle imposte** e le **imposte**. Il committente chiede una stima dell'utile netto dell'anno corrente, degli utili netti dei prossimi `n` anni e del valore dell'impresa secondo il metodo semplificato della capitalizzazione del reddito medio prospettico. In questa consegna, «reddito» indica sempre l'utile dell'azienda, non lo stipendio di un dipendente o il reddito personale di un socio.

Scrivi in **TypeScript** la funzione `getCompanyValuation` nel file [`src/companyEvaluation.ts`](src/companyEvaluation.ts). I tipi, la firma e i [test](tests/companyEvaluation.test.ts) sono già pronti. Lavora sulla funzione, senza cambiare i risultati attesi nei test. Il testo della consegna è in italiano; nomi, commenti e messaggi del codice sono in inglese.

## Dati in ingresso e risultato

La funzione riceve:

- `statements`: **almeno cinque** oggetti `{ grossIncome, taxes }`, ordinati dal più vecchio al più recente. In questo esercizio `grossIncome` è l'**utile aziendale prima delle imposte**: l'azienda ha già sottratto dai ricavi i costi, tra cui gli stipendi. Non è il fatturato. `taxes` sono le imposte dell'azienda sullo stesso utile. Il **reddito netto** (o **utile netto**) è ciò che resta all'azienda dopo le imposte: `grossIncome - taxes`. Può essere trattenuto nell'azienda o distribuito ai soci. Per esempio, con `grossIncome = 125 000` euro e `taxes = 25 000` euro, l'utile netto è `100 000` euro;
- `projectionYears`: il numero intero positivo `n` di anni da stimare;
- `capitalizationRate`: il **tasso di capitalizzazione**, un numero positivo in forma decimale (`0.10` significa 10%). **Non serve per prevedere gli utili né per calcolare `averageProjectedIncome`**: per quelli bastano la regressione e la media. Serve solo per stimare `companyValue`. L'utile medio, per esempio `242 000` euro *all'anno*, dice quanto si prevede che l'azienda guadagni ogni anno, ma non quanto valga l'azienda. Il tasso stabilisce quale rapporto ipotizzare tra quell'utile annuo e il valore: con `0.10`, un valore di `100` euro corrisponde a `10` euro di utile netto annuo. Quindi `companyValue = averageProjectedIncome / capitalizationRate = 242 000 / 0.10 = 2 420 000` euro. Con lo stesso utile e un tasso di `0.20` (20%), il valore sarebbe `1 210 000` euro. Il committente fornisce il tasso perché la scelta di questo rapporto dipende dalle ipotesi di valutazione, non si ricava dai soli utili passati.

Tutti gli importi sono espressi nella stessa unità monetaria, per esempio euro. Il risultato deve contenere `historicalNetIncomes` (un reddito netto per ogni bilancio storico), `currentYearIncome`, `projectedIncomes` (un elemento per ciascuno dei `n` anni), `averageProjectedIncome` e `companyValue`.

L'**anno corrente** è il primo anno senza rendiconto. È anche il **primo** dei `n` anni prospettici: con `n = 3` devi stimare l'anno corrente e i due successivi. Non aggiungere l'anno corrente una seconda volta alla media.

## Modello da applicare

1. Per ogni bilancio calcola il reddito netto: `grossIncome - taxes`.
2. Se i bilanci sono `k`, associa ai redditi netti gli anni `x = 1, 2, …, k`. Trova la retta `y = m × x + q` che meglio approssima **tutti** i punti con il metodo dei minimi quadrati. È il principio della funzione [TENDENZA di Excel](https://support.microsoft.com/it-it/excel/functions/trend-function) per una sola variabile, con intercetta libera.
3. Usa la retta per prevedere il reddito netto agli anni `x = k + 1, k + 2, …, k + n`. La previsione per `x = k + 1` è `currentYearIncome`.
4. Calcola la media aritmetica di queste `n` previsioni.
5. Stima il valore aziendale: `companyValue = averageProjectedIncome / capitalizationRate`.

Nella retta `y = m × x + q`:

- `x` è il numero progressivo dell'anno: `1` per il bilancio più vecchio, `k` per l'ultimo bilancio disponibile e `k + 1` per l'anno corrente;
- `y` è il reddito netto associato a quell'anno: per gli anni storici si calcola come `grossIncome - taxes`, mentre per gli anni futuri è il valore previsto dalla retta;
- `m` è la **pendenza**: indica di quanto cambia il reddito netto previsto quando `x` aumenta di un anno. Può essere positiva, negativa o zero;
- `q` è l'**intercetta**: il valore che la retta avrebbe per `x = 0`. Serve a posizionare la retta, anche se l'anno `0` non è un bilancio da valutare.

La retta approssima i redditi storici: non deve necessariamente passare per ciascuno dei punti osservati. Una volta trovati `m` e `q`, inserisci nella formula il numero dell'anno da prevedere. Per esempio, con cinque bilanci la previsione dell'anno corrente usa `x = 6`, quindi `y = m × 6 + q`.

Per calcolare la retta, indica con `ȳ` la media dei `k` redditi netti. La media degli anni `x` è `x̄ = (k + 1) / 2`. Puoi usare:

```text
m = Σ[(x - x̄) × (y - ȳ)] / Σ[(x - x̄)²]
q = ȳ - m × x̄
previsione(x) = m × x + q
```

La sommatoria `Σ` comprende tutti i `k` anni storici. Con cinque bilanci il denominatore vale `10`; con più bilanci cambia. Non usare soltanto la differenza fra il primo e l'ultimo reddito: tutti i valori intermedi devono influenzare la previsione.

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

- i bilanci devono essere almeno cinque;
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

Procedi nell'ordine suggerito dai test: controlla gli input, calcola i redditi netti di tutti i bilanci, ricava la retta, genera le previsioni, calcola media e valore aziendale. Un test sulla serie irregolare verifica che tu usi tutti i bilanci; quelli sulla serie costante e decrescente aiutano a controllare la formula della tendenza. Se un test fallisce, il suo nome indica quale comportamento manca.

Puoi consultare la [documentazione Microsoft di TENDENZA](https://support.microsoft.com/it-it/excel/functions/trend-function) per confrontare il metodo statistico. La funzione TypeScript va comunque scritta da te: non serve Excel per eseguire l'esercizio.

### Se l'editor segnala errori sugli import `node:`

Se `npm run build` riesce ma l'editor indica che `node:test` o `node:assert/strict` non esistono, controlla che il file di test appartenga al progetto definito da questo `tsconfig.json` e che la cartella `node_modules/@types/node` sia presente. In VS Code, con il test aperto, usa **TypeScript: Go to Project Configuration**: deve aprire il `tsconfig.json` di questo esercizio. Poi scegli **TypeScript: Select TypeScript Version → Use Workspace Version** e riavvia il server TypeScript dall'elenco comandi. L'editor può usare una versione di TypeScript diversa da quella che esegue `npm run build`; la [guida di VS Code](https://code.visualstudio.com/docs/typescript/typescript-transpiling#_using-newer-typescript-versions) spiega come selezionarla.
