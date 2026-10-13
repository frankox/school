# Indicazioni per nuovi esercizi

Questo repository è un eserciziario per studenti. Ogni nuovo esercizio deve poter essere svolto con il solo Docker installato sul computer: linguaggio, dipendenze, test, eventuale server, client HTTP e database devono essere disponibili tramite container.

## Preparare l'esercizio

1. Mettere l'esercizio in una cartella dedicata sotto la materia e l'argomento corretti. Usare un nome comprensibile e coerente con gli esercizi vicini.
2. Scrivere una consegna autonoma nel `README.md` della cartella: obiettivo, file da modificare, input e output, criteri di riuscita e risultati attesi. Collegare la guida ai comandi pertinente, seguendo le regole nella sezione «Documentazione da aggiornare». Distinguere chiaramente le parti già fornite da quelle da completare dagli studenti.
3. Includere test che verifichino i comportamenti richiesti. Se lo scheletro è incompleto, documentare quali fallimenti iniziali sono previsti; non completare involontariamente la soluzione.
4. Aggiungere `compose.yaml` nella cartella dell'esercizio. Seguendo le convenzioni esistenti, usare `test` per la suite, `app` per un server, `db` per il database, `client` per richieste HTTP e `admin` per l'interfaccia del database, solo quando servono. Mettere i servizi eseguiti a richiesta nel profilo `tools` e l'interfaccia opzionale nel profilo `admin`. Dare al progetto Compose un nome univoco.
5. Montare il codice nel container, così le modifiche degli studenti sono lette subito. Installare le dipendenze nel container e fissarne le versioni con i file di lock del linguaggio. I comandi documentati non devono richiedere runtime, package manager, client SQL o curl sul computer.
6. Se serve un database, usare un nome e un volume dedicati. Includere `schema.sql`, dati iniziali se utili e istruzioni esplicite per vedere e modificare i dati. Documentare che lo schema iniziale viene caricato solo su un volume nuovo e che `docker compose down -v` cancella i dati. Aggiungere `cleanup.sql` se il percorso didattico usa anche un database locale.

## Documentazione da aggiornare

Prima di considerare finito il nuovo esercizio, controllare e aggiornare **tutti i documenti coinvolti**:

- Il `README.md` dell'esercizio deve spiegare la consegna e rimandare alle istruzioni di esecuzione pertinenti. Per gli esercizi di `gestione-progetto/`, collegare `gestione-progetto/TEST.md` senza ripetere in ogni README i comandi dei test; documentare nel README solo eventuali passaggi speciali di quell'esercizio. Negli altri percorsi, riportare dalla cartella corretta i comandi Docker per avvio, test, richieste e database, se presenti, con porte, credenziali didattiche, arresto e reset dei dati.
- L'indice della materia e quello dell'argomento devono collegare l'esercizio nel punto giusto del percorso, per esempio `gestione-progetto/README.md`, `info/README.md`, `info/API/README.md` o `info/sql/README.md`, quando pertinenti.
- Il `README.md` alla radice va aggiornato se descrive la struttura o i comandi rapidi toccati dal nuovo esercizio. Aggiornare anche guide collegate, script di avvio, configurazioni dell'editor e script SQL aggregati se il nuovo esercizio li rende incompleti o inesatti.
- `gestione-progetto/TEST.md` è l'unica guida ai comandi dei test di quell'area: mantenere Docker come prima opzione e l'alternativa Windows sotto. Aggiornarla quando cambia il modo comune di preparare l'ambiente o lanciare i test.
- `DOCKER.md` è una guida **generale**: non aggiungere elenchi, percorsi, nomi di database o comandi di un singolo esercizio. Modificarla solo quando cambia una convenzione Docker comune. I dettagli specifici restano nel `README.md` dell'esercizio o nella guida ai test dell'area.

## Verifica

- Validare il file con `docker compose config --quiet`.
- Eseguire i comandi Docker documentati quando il daemon è accessibile: test, avvio, richiesta o query e arresto. Verificare anche i casi senza database o con test inizialmente falliti.
- Controllare che link e percorsi dei README funzionino e che non siano rimaste istruzioni obsolete. Segnalare con precisione le verifiche non eseguibili nell'ambiente corrente.
- Conservare le modifiche preesistenti dell'utente negli altri esercizi; intervenire su di esse solo se necessario per il compito.
