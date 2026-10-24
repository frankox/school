# VS Code e dipendenze nei container

I test Docker funzionano anche quando VS Code segnala che non trova una libreria: l'editor aperto sul computer non vede i pacchetti installati nei volumi Docker. Per usare le stesse dipendenze anche nell'analisi statica, apri **l'intera cartella `school`** nel [Dev Container della radice](.devcontainer/devcontainer.json). VS Code mostrerà i file locali, compresi i README e le guide comuni, ma il servizio che analizza il codice lavorerà dentro il container.

## Esercizio TypeScript: Valutazione azienda

Segui i passaggi della [guida Docker, sezione «Usare VS Code nel container»](DOCKER.md#usare-vs-code-nel-container). La configurazione riusa il servizio `test` di Compose e installa nel volume `node_modules` le versioni fissate da `package-lock.json`. Nel file `gestione-progetto/valutazione-azienda/tests/companyEvaluation.test.ts`, il completamento e i tipi di `node:assert/strict` devono essere disponibili. Gli eventuali errori sulla funzione ancora da implementare sono parte dell'esercizio.

Non serve eseguire `npm install` o `npm ci` sul computer. Se cambia `gestione-progetto/valutazione-azienda/package-lock.json`, entra in quella cartella nel terminale del container, esegui `npm ci` e poi **TypeScript: Restart TS Server** in VS Code.

Il comando `docker compose down` dalla cartella dell'esercizio arresta i servizi del progetto; `down -v` elimina anche le dipendenze conservate nel volume.

## Esercizi PHP e SQL

Le API PHP attuali non hanno dipendenze Composer esterne. Le classi del progetto sono nei file dell'esercizio; `PDO`, `pdo_mysql` e `pdo_sqlite` sono forniti dall'immagine PHP degli esercizi. Per navigazione, completamento e controlli statici in VS Code puoi usare [PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client): include le definizioni delle funzioni e delle classi standard di PHP e non richiede PHP installato sul computer. Esegui comunque i test e il server con i comandi Docker del README dell'esercizio: l'analisi dell'editor non sostituisce l'esecuzione nel container.

Gli [esercizi DDL](info/sql/DDL/README.md) e il [primo esercizio DML](info/sql/DML/01-inserire-righe/README.md) usano MySQL 8.4.7 e il client `mysql` nel container; il laboratorio precedente Fondamenti SQL usa il client MariaDB. I file SQL sono visibili nel workspace globale `/school/info/sql/`: non servono librerie o dipendenze aggiuntive sul computer o nel Dev Container. Modifica `solution.sql` nell’editor ed esegui i comandi Docker dei rispettivi README da un terminale del computer. La validazione effettiva di sintassi e vincoli viene eseguita da MySQL nei test.
