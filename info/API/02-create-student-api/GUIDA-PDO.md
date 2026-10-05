# Guida a PDO: dal DTO alla riga nel database

Questa guida accompagna gli ultimi tre test di `tests/run.php`. Leggetela con `src/StudentRepository.php` aperto. L'obiettivo del metodo `create()` è ricevere uno studente già descritto da `CreateStudentRequest`, inserirlo nella tabella `students` e restituire l'ID della nuova riga.

## Che cos'è PDO?

**PDO** significa *PHP Data Objects*. È l'interfaccia che PHP mette a disposizione per parlare con diversi database tramite metodi come `prepare()` e `lastInsertId()`. L'oggetto `$pdo` rappresenta una connessione. Per parlare con MariaDB serve anche il driver `pdo_mysql`: la sola estensione PDO non basta. Nel nostro progetto `config/database.php` crea la connessione; il repository la riceve nel costruttore `__construct(private PDO $pdo)`.

Tenete distinti i tre soggetti:

| Soggetto | Nel progetto | Compito |
| --- | --- | --- |
| Client HTTP | `curl` | Invia un JSON all'API. |
| Programma PHP | `students.php`, DTO, repository | Controlla i dati e chiede al database di eseguire una `INSERT`. |
| MariaDB | database `school_ex02` | Conserva la riga e assegna un ID. |

Il JSON e la tabella usano nomi diversi. La richiesta contiene `firstName`, `lastName`, `age`, `studentCode`; le colonne SQL si chiamano `first_name`, `last_name`, `age`, `student_code`. Il DTO conserva i valori della richiesta; il repository li associa alle colonne. Il codice `"00123"` è una stringa in entrambe le parti: convertirlo nel numero `123` farebbe perdere gli zeri iniziali.

## Tre operazioni, tre test

**Passo 1 — `prepare()`: definire la query.** Il repository passa a PDO il testo della `INSERT`. Nel testo compaiono nomi di tabella e colonne fissi e quattro *segnaposto*, per esempio `:first_name`. Un segnaposto indica dove andrà un valore, ma la chiamata a `prepare()` da sola non inserisce ancora la riga. PDO restituisce un oggetto `PDOStatement`, cioè la query preparata. Il test `PDO step 1` controlla il testo passato a `prepare()`.

**Passo 2 — `execute()`: fornire i valori.** Sullo statement ottenuto si chiama `execute()` con un array associativo. Ogni chiave identifica un segnaposto e ogni valore viene dal DTO: per esempio, la chiave `first_name` o `:first_name` riceve `$request->firstName`. Nel testo SQL il segnaposto ha `:`; nell'array di `execute()` PHP accetta il nome con o senza `:`. È `execute()` a far eseguire la `INSERT`. Il test `PDO step 2` controlla nomi e valori, accettando entrambe le forme.

**Passo 3 — `lastInsertId()`: leggere il risultato.** La colonna `id` è `BIGINT UNSIGNED AUTO_INCREMENT`: MariaDB le assegna un valore quando inserisce la riga. Dopo `execute()`, la connessione PDO può leggere quell'ID con `lastInsertId()`. PDO restituisce una stringa (oppure `false` in caso di errore); anche `StudentRepository::create()` dichiara `: string`. Conservare l'ID come stringa evita di perdere precisione quando un valore `BIGINT UNSIGNED` supera il massimo intero rappresentabile da PHP. Il test `PDO step 3` verifica che il repository restituisca `"42"` come stringa.

```text
CreateStudentRequest
  → PDO::prepare(SQL con :segnaposto)
  → PDOStatement::execute(valori del DTO)
  → MariaDB inserisce la riga e genera id
  → PDO::lastInsertId()
  → create() restituisce una stringa
```

I segnaposto valgono per i **valori**, non per identificatori SQL come il nome della tabella. Tenere i valori fuori dal testo SQL evita che un contenuto inviato dal client venga interpretato come parte della query. Per questo non si costruisce la `INSERT` concatenando nomi o codici ricevuti dal JSON.

## Perché i test funzionano senza MariaDB?

In `tests/run.php`, `RecordingPdo` e `RecordingStatement` sono **doppi di test**: hanno i metodi che ci servono, ma non aprono una connessione. `RecordingPdo::prepare()` salva la SQL in `$pdo->sql` e restituisce uno statement; `RecordingStatement::execute()` salva l'array in `$pdo->statement->parameters`; `RecordingPdo::lastInsertId()` restituisce sempre la stringa `"42"`. I test confrontano questi dati registrati con quelli attesi. L'ID `42` è dunque inventato dal test, non letto da una tabella.

Questo permette di affrontare un pezzo alla volta. Partendo dal `throw` presente nel repository:

1. Per il primo passo, sostituite temporaneamente il `throw` con `return '0';`, aggiungete `prepare()` e rieseguite i test. `PDO step 1` deve diventare verde; gli altri due restano rossi. Il valore `'0'` è solo provvisorio.
2. Conservate lo statement restituito da `prepare()`, chiamate `execute()` con i quattro valori e rieseguite i test. Deve diventare verde anche `PDO step 2`.
3. Sostituite `return '0';` con il valore ottenuto da `lastInsertId()`. Deve diventare verde `PDO step 3`. Poiché il metodo può anche restituire `false`, valutate come segnalare un errore in quel caso senza restituirlo come ID.

Ogni test crea un nuovo `RecordingPdo`: non eredita le chiamate del test precedente. Se `create()` lancia ancora un'eccezione, il test si ferma prima delle verifiche. Per questo si usa il ritorno provvisorio mentre si costruisce il metodo.

I test dimostrano che il repository **chiede** a PDO di eseguire le operazioni giuste. La prova con XAMPP descritta nel [README](README.md#seconda-fase-preparare-xampp) verifica anche la connessione reale, la tabella e l'inserimento effettivo.

## Domande per controllare la comprensione

- Quale differenza c'è fra preparare la `INSERT` ed eseguirla?
- Perché la SQL contiene `:student_code`, mentre l'array di `execute()` contiene la chiave `student_code`?
- Perché il valore `"00123"` non deve diventare un intero?
- Perché `lastInsertId()` si legge dopo `execute()`?
- Se i tre test PDO passano ma il `POST` reale fallisce con `could not find driver`, quale componente manca?

Riferimenti: [introduzione ufficiale a PDO](https://www.php.net/manual/en/book.pdo.php), [PDO::prepare](https://www.php.net/manual/en/pdo.prepare.php), [PDOStatement::execute](https://www.php.net/manual/en/pdostatement.execute.php), [PDO::lastInsertId](https://www.php.net/manual/en/pdo.lastinsertid.php).
