# Prova tecnico-pratica 2 — Test + Git + Markdown

**Durata indicativa:** 4 ore.

Questa è la seconda delle due prove pratiche. Leggi integralmente il presente documento prima di iniziare.

---

## 1. Materiale consegnato

Ti è stato consegnato un archivio `hangar-drones-prova2.zip` contenente la versione di riferimento del progetto: il bugfix della Prova 1 è già applicato e la feature di dismissione è già implementata. La cartella `tests/` è vuota.

```
hangar-drones/
├── bin/
│   └── hangar.php         # Script CLI che include la feature retire
├── src/
│   ├── Drone.php          # Entità drone: docked, in_flight, maintenance, retired
│   └── Hangar.php         # Hangar con capacità finita + pool ritirati
├── tests/                 # Vuota: contenuto di questa prova
├── composer.json
├── phpunit.xml
├── phpcs.xml              # PSR-12
└── README.md
```

Il materiale **non** dipende dalla tua produzione della Prova 1: inizi da una base condivisa con tutti i candidati.

---

## 2. Requisiti di ambiente

- PHP 8.1 o superiore (verifica con `php -v`).
- Composer installato e raggiungibile da shell (`composer --version`).
- Git installato.
- Tutto il lavoro deve essere svolto **da shell**. Il contenuto dei commit, i messaggi e la topologia dei branch devono essere visibili con `git log --graph --oneline --all`.

Il primo passo operativo è:

```bash
composer install
composer test   # deve girare senza test presenti, esito "no tests executed"
php bin/hangar.php   # deve produrre output coerente
```

Se uno di questi passaggi fallisce, risolvi il problema di ambiente prima di iniziare a scrivere i test.

---

## 3. Cosa ti viene richiesto

Questa prova comprende **due attività**, entrambe obbligatorie:

1. **Scrivere una suite di test PHPUnit** esaustiva per `Drone` e `Hangar`, coprendo sia il comportamento preesistente sia la funzionalità di dismissione (§4).
2. **Gestire il lavoro con Git**, secondo le pratiche prudenziali descritte al §5, su **repository remoto**.

---

## 4. Suite di test

### 4.1 Ambito obbligatorio

Devi scrivere test PHPUnit in `tests/` che coprano ragionevolmente il codice delle classi `Drone` e `Hangar`.


### 4.2 Vincoli tecnici

- I test devono essere scritti in PHPUnit e vivere sotto il namespace `Tests\` (PSR-4 già configurato in `composer.json`, mappato a `tests/`).
- Un file di test per classe: almeno `tests/DroneTest.php` e `tests/HangarTest.php`. Puoi aggiungere file ulteriori se preferisci organizzare scenari specifici (es. `tests/HangarRetireTest.php`).
- I test devono essere **deterministici**: nessuna dipendenza da filesystem, rete, `sleep`, orologio di sistema o variabili d'ambiente.
- I test devono essere **isolati**: ogni metodo di test deve essere indipendente dall'ordine di esecuzione.
- I test devono essere **leggibili**: nomi auto-esplicativi (in inglese), struttura Arrange-Act-Assert chiara, una singola idea verificata per metodo di test.
- I test devono girare con `composer test` e terminare verdi.
- Tutto il codice dei test deve superare `composer lint` (PSR-12).

### 4.3 Quantità attesa

Non c'è un numero minimo rigido, ma una suite ragionevole e realizzabile nel tempo concesso si colloca nell'intervallo **indicativo** di 15–40 metodi di test. Meno di 10 è certamente sotto-dimensionato; oltre 60 probabilmente contiene ridondanze.


### 4.4 Stile

Tutto il codice aggiunto o modificato deve superare `composer lint` (PSR-12). L'esecuzione di `composer lint` deve terminare senza errori.

---

## 5. Gestione con Git

### 5.1 Repository remoto

Il lavoro deve risiedere su un **repository remoto**: crea un repository su GitHub.

**Questa prova usa un repository a sé**, indipendente da quello della Prova 1.

### 5.2 Inizializzazione e primo commit

1. Entra nella directory `hangar-drones/` del materiale consegnato.
2. Inizializza il repository e verifica che il branch di default si chiami `main` (altrimenti rinominalo con `git branch -M main`).
3. Aggiungi un file `.gitignore` adeguato (almeno: `vendor/`, `.phpunit.result.cache`, `.phpcs-cache`, eventuali file di IDE). **Non** escludere `composer.lock`: va versionato.
4. Esegui un **commit iniziale** che contenga esclusivamente il materiale consegnato, senza alcuna tua modifica.
5. Fai push di `main`.

### 5.3 Branch della suite di test

Tutto il lavoro di scrittura dei test deve avvenire su un branch dedicato, a partire da `main`. Nome richiesto: `feature/test-suite`.

### 5.4 Commit atomici e messaggi

- Sono richiesti **vari commit** sul branch. Un singolo commit monolitico con tutta la suite è considerato errore di metodo.
- I messaggi devono essere chiari e in inglese.
- Dopo ogni commit significativo la suite deve restare verde (`composer test`). Evita di committare uno stato con test rossi, salvo che nel messaggio lo dichiari esplicitamente.


### 5.5 Cose da non fare

- Non alterare `src/` o `bin/`: questa prova verifica i test, non il codice di produzione. Se trovi un difetto nel codice di produzione, **annotalo in `NOTES.md`** senza correggerlo nel codice (ma puoi comunque scrivere un test che ne documenti il comportamento atteso).

---

## 6. Deliverable della Prova 2

Alla consegna deve essere presente tutto quanto segue:


1. Tutto il codice presente su repository e soltanto quello. Non includere `vendor/`, `.git/` o altro materiale non nel repository.
2. Un file `git.log` che contiene **tutto** l'output di `git log -g --all --pretty=fuller` (attenzione: potresti dover scorrere l'output con la barra spaziatrice).
3. Un breve file `NOTES.md` (massimo una pagina) in cui riassumi, a parole tue:
   - Le scelte implementative dei test, cosa hai deciso di coprire e cosa no e perché.
   - Eventuali assunzioni fatte laddove il testo fosse ambiguo.

Verificare inoltre che:
1. `composer install` seguito da `php bin/hangar.php` deve terminare senza errori e mostrare un output sensato che include la nuova funzionalità.
2. `composer lint` deve terminare **senza errori**.

**La consegna consisterà di un unico file zip, denominato `prova2_COGNOME_NOME.zip`**

---

## 7. Note pratiche

- **Non è ammesso l'uso di assistenti AI generativi.** È ammessa la consultazione della documentazione ufficiale di PHP, PHPUnit, Composer, PHP_CodeSniffer e Git.
- Non è richiesto l'uso di `xdebug`, di mock, né di librerie di test oltre a PHPUnit.
- Se il tempo a disposizione stringe, meglio consegnare una suite più piccola ma solida e ben organizzata che una grande ma fragile.
