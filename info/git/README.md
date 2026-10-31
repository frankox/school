# Git — guida essenziale

Puoi usare Git dal terminale oppure attraverso **GitHub Desktop**, un'applicazione con interfaccia grafica. I concetti sono gli stessi: se usi Desktop, trovi i passaggi nella [sezione dedicata](#usare-github-desktop).

## Che cos'è un VCS e a cosa serve

Un **VCS** (*Version Control System*, sistema di controllo di versione) registra nel tempo le modifiche ai file. Permette di confrontare versioni, recuperare contenuti precedenti e sapere chi ha registrato una modifica e perché, attraverso il messaggio che la accompagna.

Se modifichi un file SQL e introduci un errore, puoi confrontarlo con la versione precedente senza conservare copie chiamate `soluzione_finale.sql` e `soluzione_finale2.sql`. Nel lavoro di gruppo puoi integrare i contributi di più persone. Se le modifiche sono incompatibili, si verifica un **conflitto**, da risolvere manualmente.

Il **repository**, o *repo*, è l'archivio del progetto con la sua cronologia. Un **commit** registra una versione dei contenuti selezionati, con identificativo, autore e messaggio. **Salvare nell'editor non crea un commit.**

Riferimento: [Pro Git — Il controllo di versione](https://git-scm.com/book/en/v2/Getting-Started-About-Version-Control).

## Perché Git

**Git è un VCS distribuito, gratuito e open source.** Rispetto a un sistema centralizzato, come Subversion, un normale clone conserva localmente anche la cronologia, anziché dipendere dal server per consultarla.

- **Lavoro offline e velocità:** puoi consultare la cronologia, confrontare modifiche e creare commit localmente. Internet serve per scambiare aggiornamenti con un server.
- **Branch leggeri:** puoi lavorare su linee di sviluppo separate e poi riunirle con un *merge*.
- **Cronologia distribuita:** ogni clone completo ne conserva una copia, pur senza sostituire il backup del proprio lavoro.

Anche altri VCS distribuiti, come Mercurial, offrono vantaggi simili: non sono tutti esclusivi di Git.

**Git e GitHub sono diversi:** Git gestisce le versioni; GitHub è una piattaforma online che ospita repository Git e strumenti di collaborazione. Git funziona anche senza GitHub.

Riferimento: [Pro Git — Che cos'è Git](https://git-scm.com/book/en/v2/Getting-Started-What-is-Git%3F).

## Clonare il materiale

Per usare i comandi seguenti, Git deve essere disponibile nel terminale: controlla con `git --version`. Se manca, segui le [istruzioni ufficiali di installazione](https://git-scm.com/install/). Su Windows puoi usare Git Bash, fornito con Git. Con GitHub Desktop puoi svolgere le operazioni descritte nella sezione dedicata senza usare il terminale. Per eseguire gli esercizi segui le rispettive [istruzioni Docker](../../DOCKER.md).

Apri la [pagina di school su GitHub](https://github.com/frankox/school), premi **Code**, scegli **HTTPS** e copia l'URL. Poi apri un terminale nella cartella in cui vuoi conservare il progetto:

```bash
git clone https://github.com/frankox/school.git
cd school
git status
```

`clone` crea `school`, scarica file e cronologia e configura il collegamento al repository di origine, chiamato normalmente **`origin`**. `cd` entra nella cartella: è un comando del terminale, non di Git. Per clonare questo repository pubblico non serve un account GitHub.

La cartella nascosta **`.git`** conserva cronologia e impostazioni: non eliminarla. Esegui i successivi comandi Git dentro `school` o una sua sottocartella.

Riferimento: [manuale di `git clone`](https://git-scm.com/docs/git-clone).

### Clone oppure Download ZIP?

| | `git clone` | **Code → Download ZIP** |
| --- | --- | --- |
| Contenuto | File e cronologia Git. | File della versione selezionata, senza cronologia. |
| Cartella `.git` | Presente. | Assente. |
| Aggiornamenti | Li recuperi con Git. | Scarichi un nuovo ZIP e confronti o sostituisci i file manualmente. |
| Uso di Git | Puoi consultare la cronologia e creare commit. | La cartella estratta non è un repository Git. |

Lo ZIP basta per consultare una copia del materiale. **Clonare conviene per seguirne gli aggiornamenti e registrare il proprio lavoro.** Il clone non si aggiorna da solo. Eseguire `git init` sullo ZIP avvia una nuova cronologia: non recupera quella originale.

Riferimento: [GitHub — Scaricare, clonare e fare un fork](https://docs.github.com/en/repositories/working-with-files/using-files/downloading-files-from-github).

## Aggiornare il materiale

Dopo la prima clonazione, dalla cartella `school`:

```bash
git status
git pull
```

`pull` scarica gli aggiornamenti dal remoto e li integra nel branch corrente. 

Controlla le tue modifiche prima di aggiornare: Git può rifiutare l'operazione se rischia di sovrascriverle. Se si ferma, conserva il lavoro e chiedi supporto prima di procedere.

Riferimento: [manuale di `git pull`](https://git-scm.com/docs/git-pull).

## Registrare il proprio lavoro

Prima del primo commit, configura l'autore dentro il repository, sostituendo i dati di esempio:

```bash
git config user.name "Nome Cognome"
git config user.email "nome@example.com"
```

Questi dati valgono per questo repository e identificano l'autore: non sono credenziali GitHub.

Modifichi i file nella **cartella di lavoro**, selezioni i contenuti nella **staging area** con `add`, li registri nella cronologia locale con `commit`. Dopo aver modificato la tua soluzione SQL, per esempio:

```bash
git status
git diff
git add info/SQL/DDL/01-database-e-tabelle/solution.sql
git diff --staged
git commit -m "Crea il database e la tabella del catalogo"
git log --oneline
```

| Comando | Scopo |
| --- | --- |
| `git status` | Mostra branch corrente, file nuovi e modifiche selezionate o ancora da selezionare. |
| `git diff` | Mostra modifiche ai file tracciati rispetto alla staging area; non mostra i file nuovi non ancora aggiunti. |
| `git add percorso/file` | Seleziona il contenuto attuale del file. Se lo modifichi ancora, ripeti `add`. |
| `git diff --staged` | Mostra le modifiche selezionate per il commit. |
| `git commit -m "Messaggio"` | Registra i contenuti selezionati con un messaggio significativo. |
| `git log --oneline` | Mostra la cronologia del branch corrente in forma compatta. |

**Il commit resta locale: non invia nulla al docente o a GitHub.** Il file `.gitignore` indica quali file non ancora tracciati ignorare, per esempio file temporanei; non rimuove quelli già registrati.

Riferimenti: [tutorial ufficiale](https://git-scm.com/docs/gittutorial), [manuale di `git diff`](https://git-scm.com/docs/git-diff), [manuale di `git add`](https://git-scm.com/docs/git-add).

## Altri comandi principali

| Comando | Scopo |
| --- | --- |
| `git init` | Inizia un repository nella cartella corrente. Non serve dopo `clone`. |
| `git remote -v` | Mostra nomi e indirizzi dei repository remoti. |
| `git fetch` | Scarica commit e aggiorna i riferimenti remoti senza integrarli nel branch corrente. |
| `git push` | Invia i commit al branch remoto associato, con configurazione e permessi necessari. |
| `git branch` | Elenca i branch locali e indica quello corrente. |
| `git switch -c prova` | Crea il branch `prova` e passa a quella linea di lavoro. |
| `git switch main` | Passa al branch `main`, se presente. Il nome del branch principale può essere diverso. |
| `git merge prova` | Integra nel branch corrente i commit di `prova`; eventuali conflitti richiedono intervento manuale. |

Un **branch** è una linea di sviluppo: permette di provare modifiche senza cambiare subito il branch principale. Per pubblicare un nuovo branch usa `git push -u origin prova`: `-u` imposta l'associazione per i successivi `push` e `pull`.

**Clonare non concede il permesso di scrivere sul repository originale.** Per pubblicare su GitHub servono autenticazione e accesso in scrittura. Per recuperare il materiale del docente bastano `clone` la prima volta e `pull` per gli aggiornamenti.

Riferimenti: [manuale di `git push`](https://git-scm.com/docs/git-push), [manuale di `git switch`](https://git-scm.com/docs/git-switch), [tutorial — branch e collaborazione](https://git-scm.com/docs/gittutorial#_managing_branches).

## Usare GitHub Desktop

GitHub Desktop esegue operazioni Git attraverso pulsanti e menu: conserva la stessa cronologia e la stessa cartella `.git`. Non è l'editor in cui scrivere SQL: modifica e salva i file in VS Code, poi usa Desktop per controllare e registrare le modifiche.

### Clonare school

1. In GitHub Desktop apri **File → Clone repository…** e scegli la scheda **URL**.
2. Incolla `https://github.com/frankox/school.git`.
3. In **Local Path** scegli dove conservare la copia locale, usando una cartella di destinazione nuova o vuota.
4. Premi **Clone**. In **Current Repository** deve comparire `school`.

In alternativa, dalla pagina del repository su GitHub scegli **Code → Open with GitHub Desktop**, controlla il percorso e premi **Clone**. Questo è un clone completo, non un download ZIP: vale il confronto spiegato sopra.

Se hai già clonato dal terminale, usa **File → Add local repository…** e seleziona la cartella esistente, senza clonare nuovamente.

Riferimenti: [clonare da Desktop](https://docs.github.com/en/desktop/adding-and-cloning-repositories/cloning-and-forking-repositories-from-github-desktop?platform=windows), [aprire da GitHub](https://docs.github.com/en/desktop/adding-and-cloning-repositories/cloning-a-repository-from-github-to-github-desktop).

### Recuperare gli aggiornamenti

Seleziona `school` in **Current Repository** e il branch da aggiornare in **Current Branch**. Controlla eventuali modifiche nella scheda **Changes**, poi premi **Fetch origin**: scarica le novità, come `git fetch`, senza integrarle nei file di lavoro. Se ci sono aggiornamenti da applicare, premi **Pull origin**, corrispondente a `git pull`.

**Fetch e Pull non sono la stessa operazione.** Se Desktop segnala conflitti o modifiche da sistemare, conserva il tuo lavoro e chiedi supporto prima di procedere.

Riferimento: [sincronizzare il branch](https://docs.github.com/en/desktop/working-with-your-remote-repository-on-github-or-github-enterprise/syncing-your-branch-in-github-desktop).

### Controllare le modifiche e creare un commit

Per impostare l'autore solo per questo repository, apri **Repository → Repository settings… → Git Config**, scegli **Use a local Git config**, inserisci nome ed email e premi **Save**.

1. Modifica e salva in VS Code, per esempio, `info/SQL/DDL/01-database-e-tabelle/solution.sql`.
2. Torna in Desktop: **Changes** mostra i file cambiati, come `git status`. Seleziona un file per vedere il confronto, come `git diff`.
3. Lascia spuntati solo i file da includere nel commit. Le caselle selezionano i contenuti da registrare, svolgendo il ruolo della selezione con `git add`; non serve lanciare il comando separatamente.
4. In **Summary** scrivi un messaggio, per esempio `Crea il database e la tabella del catalogo`, poi premi **Commit to main** o **Commit to** seguito dal nome del branch corrente.

Apri **History** per vedere i commit e le relative modifiche, come con `git log`. **Il commit resta locale anche in Desktop.** Per inviarlo al remoto premi **Push origin**, equivalente a `git push`, solo su un repository su cui puoi scrivere. Per un branch nuovo il pulsante è **Publish branch**, corrispondente al primo `git push -u origin nome-branch`.

Riferimenti: [configurare l'autore](https://docs.github.com/en/desktop/configuring-and-customizing-github-desktop/configuring-git-for-github-desktop), [creare e controllare commit](https://docs.github.com/en/desktop/making-changes-in-a-branch/committing-and-reviewing-changes-to-your-project-in-github-desktop), [pubblicare un branch](https://docs.github.com/en/desktop/making-changes-in-a-branch/managing-branches-in-github-desktop#publishing-a-branch).

### Nuovi repository e branch

| Operazione Git | In GitHub Desktop |
| --- | --- |
| Iniziare un nuovo progetto con `git init` | **File → New repository…**, scegli nome e percorso, poi **Create repository**. Non serve per recuperare `school`. |
| Elencare i branch con `git branch` | Apri **Current Branch**. |
| Creare e aprire un branch con `git switch -c prova` | **Current Branch → New Branch**, scrivi `prova`, scegli il branch di partenza e premi **Create Branch**. |
| Passare a `main` con `git switch main` | Apri **Current Branch** e seleziona `main`, se presente. |
| Integrare `prova` con `git merge prova` | Passa al branch che deve ricevere le modifiche, poi **Current Branch → Choose a branch to merge into…**; scegli `prova` e conferma con **Merge prova into…**. |

Anche in Desktop i conflitti richiedono una risoluzione manuale. Per leggere il materiale aggiornato del docente sono sufficienti **Clone** la prima volta, poi **Fetch origin** e, quando disponibile, **Pull origin**.

Riferimenti: [creare un repository](https://docs.github.com/en/desktop/overview/creating-your-first-repository-using-github-desktop), [gestire i branch](https://docs.github.com/en/desktop/making-changes-in-a-branch/managing-branches-in-github-desktop), [unire branch](https://docs.github.com/en/desktop/working-with-your-remote-repository-on-github-or-github-enterprise/syncing-your-branch-in-github-desktop#merging-another-branch-into-your-project-branch).

## Controlla di aver capito

- Quale problema risolve un VCS rispetto a conservare copie numerate dei file?
- Perché puoi creare commit senza Internet?
- Che cosa manca nello ZIP rispetto a un clone?
- Che differenza c'è tra salvare, eseguire `add`, fare `commit` e fare `push`?
- Quale comando usi per ottenere il materiale la prima volta e quale per aggiornarlo?

[Torna a Informatica](../README.md).
