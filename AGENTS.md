# Indicazioni per gli esercizi

Questo repository è un eserciziario per studenti. Ogni esercizio, nuovo o modificato, deve poter essere svolto con il solo Docker installato sul computer: linguaggio, dipendenze, test, eventuale server, client HTTP e database devono essere disponibili tramite container. VS Code deve poter aprire l'intera cartella `school` nel Dev Container della radice e analizzare il codice usando le dipendenze disponibili nei container.

## Preparare l'esercizio

1. Mettere l'esercizio in una cartella dedicata sotto la materia e l'argomento corretti. Usare un nome comprensibile e coerente con gli esercizi vicini.
2. Scrivere una consegna autonoma nel `README.md` della cartella: obiettivo, file da modificare, input e output, criteri di riuscita e risultati attesi. Collegare la guida ai comandi pertinente, seguendo le regole nella sezione «Documentazione da aggiornare». Distinguere chiaramente le parti già fornite da quelle da completare dagli studenti.
3. Includere test che verifichino i comportamenti richiesti. Se lo scheletro è incompleto, documentare quali fallimenti iniziali sono previsti; non completare involontariamente la soluzione.
    3.1 i tests devono avere descrizioni in inglese, se durante un controllo le descrizioni non sono in inglese, devono essere aggiornate.
4. Aggiungere `compose.yaml` nella cartella dell'esercizio. Seguendo le convenzioni esistenti, usare `test` per la suite, `app` per un server, `db` per il database, `client` per richieste HTTP e `admin` per l'interfaccia del database, solo quando servono. Mettere i servizi eseguiti a richiesta nel profilo `tools` e l'interfaccia opzionale nel profilo `admin`. Dare al progetto Compose un nome univoco.
5. Montare il codice nel container, così le modifiche degli studenti sono lette subito. Installare le dipendenze nel container e fissarne le versioni con i file di lock del linguaggio. I comandi documentati non devono richiedere runtime, package manager, client SQL o curl sul computer.
6. Se serve un database, usare un nome e un volume dedicati. Includere `schema.sql`, dati iniziali se utili e istruzioni esplicite per vedere e modificare i dati. Documentare che lo schema iniziale viene caricato solo su un volume nuovo e che `docker compose down -v` cancella i dati. Aggiungere `cleanup.sql` se il percorso didattico usa anche un database locale.

## Mantenere Docker e Dev Containers coerenti

Per **ogni nuovo esercizio e ogni modifica a un esercizio**, controllare se cambiano linguaggio, librerie, file di lock, comandi di test, servizi, porte, percorsi o volumi. Quando serve, aggiornare autonomamente tutti i file coinvolti:

- Il `compose.yaml` e gli eventuali Dockerfile dell'esercizio devono continuare a offrire runtime, dipendenze e comandi funzionanti senza installazioni sul computer. Conservare i nomi e i profili dei servizi previsti sopra. Se l'esercizio ha una propria `.devcontainer/`, aggiornare anche quella; crearla quando il Dev Container della radice non copre il flusso di lavoro dell'esercizio.
- Il Dev Container globale in `.devcontainer/devcontainer.json` e il suo Compose in `.devcontainer/compose.yaml` devono continuare ad aprire **l'intera cartella `school`**. Aggiornare riferimenti a servizi e Compose, montaggi, volumi, comandi di installazione, estensioni e impostazioni dell'editor quando cambiano gli esercizi. Le librerie e i tipi necessari all'analisi statica devono essere visibili nel percorso dell'esercizio dentro il Dev Container, usando le versioni dei file di lock. Non presumere che un volume `node_modules` o equivalente, usato dai test, sia visibile automaticamente all'editor: verificarne il montaggio nel workspace globale.
- Se un solo container non può fornire gli strumenti necessari a tutti gli esercizi, predisporre configurazioni Dev Containers selezionabili che aprano comunque la radice `school` e documentare quale usare. Non lasciare un esercizio con analisi statica dipendente da pacchetti installati sul computer.

Non modificare queste configurazioni quando la modifica riguarda soltanto la consegna o la logica dell'esercizio e l'ambiente esistente è ancora corretto; eseguire comunque il controllo di coerenza.

## Documentazione da aggiornare

Prima di considerare finito un nuovo esercizio o una modifica a un esercizio, controllare e aggiornare **tutti i documenti coinvolti**:

- Il `README.md` dell'esercizio deve spiegare la consegna e rimandare alle istruzioni di esecuzione pertinenti. Per gli esercizi di `gestione-progetto/`, collegare `gestione-progetto/TEST.md` senza ripetere in ogni README i comandi dei test; documentare nel README solo eventuali passaggi speciali di quell'esercizio. Negli altri percorsi, riportare dalla cartella corretta i comandi Docker per avvio, test, richieste e database, se presenti, con porte, credenziali didattiche, arresto e reset dei dati.
- L'indice della materia e quello dell'argomento devono collegare l'esercizio nel punto giusto del percorso, per esempio `gestione-progetto/README.md`, `info/README.md`, `info/API/README.md` o `info/sql/README.md`, quando pertinenti.
- Il `README.md` alla radice va aggiornato se descrive la struttura o i comandi rapidi toccati dall'esercizio nuovo o modificato. Aggiornare anche guide collegate, script di avvio, configurazioni dell'editor e script SQL aggregati se la modifica li rende incompleti o inesatti.
- `gestione-progetto/TEST.md` deve rimandare direttamente a `DOCKER.md` per i test con Docker e contenere sotto le istruzioni dettagliate per Windows senza Docker. Non duplicare i comandi Docker in questa guida; aggiornarla quando cambia l'alternativa Windows.
- `DOCKER.md` è una guida **generale**: non trasformarla in un indice da aggiornare a ogni nuovo esercizio. Può usare pochi esercizi esistenti come esempi per spiegare immagini, servizi e volumi; mantenere corretti quei riferimenti quando cambiano i file citati. Comandi e dati specifici delle consegne restano nei rispettivi `README.md` o nella guida ai test dell'area.
- Aggiornare le sezioni su VS Code in `DOCKER.md` e `VS-CODE.md` quando cambiano l'apertura della radice `school`, la preparazione delle dipendenze, i comandi nel terminale del Dev Container o le istruzioni per scegliere l'ambiente. Mantenere coerenti anche i rimandi nei README e in `gestione-progetto/TEST.md`.

## Verifica

- Validare con `docker compose config --quiet` il Compose dell'esercizio. Quando cambia il Dev Container globale, validare anche la combinazione dei file Compose indicati, nello stesso ordine, da `.devcontainer/devcontainer.json` e controllare che il montaggio della radice e dei volumi delle dipendenze punti ai percorsi previsti.
- Validare il JSON dei file `devcontainer.json` modificati. Quando Docker e VS Code sono accessibili, aprire `school` nel Dev Container e controllare che file globali, completamento e tipi dell'esercizio interessato siano disponibili senza dipendenze installate sul computer.
- Eseguire i comandi Docker documentati quando il daemon è accessibile: test, avvio, richiesta o query e arresto. Verificare anche i casi senza database o con test inizialmente falliti.
- Controllare che link e percorsi dei README funzionino e che non siano rimaste istruzioni obsolete. Segnalare con precisione le verifiche non eseguibili nell'ambiente corrente.
- Conservare le modifiche preesistenti dell'utente negli altri esercizi; intervenire su di esse solo se necessario per il compito.

## Manutenzione package.json
- manutenere il package.json globale per avere un npm run command di ogni esercizio, sia per i test che per il serving (dove il serving è possibile)
