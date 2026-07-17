# Standard — Architettura Laravel oltre il CRUD

> **Fonte:** Brent Roose / Spatie, *Laravel Beyond CRUD*. Estende `laravel.md`: quelle convenzioni restano valide sempre; questo file si applica **quando il progetto cresce** oltre lo scaffolding di default. Nota: il libro precede PHP 8.1+, quindi dove suggeriva pacchetti (spatie/enum, DTO via pacchetto) oggi usiamo i costrutti nativi di `php-oop.md` (enum, readonly, promoted properties).

## 1. Quando si applica

Non dal giorno uno. La struttura app/ standard va benissimo finché il progetto è piccolo. Si passa all'organizzazione per dominio quando compaiono i sintomi: model "grassi" con decine di metodi di business, controller che orchestrano logica, stessa regola duplicata tra web, API e comandi.

## 2. Domini e applicazioni

Il codice si divide in due mondi:

- **Domain** — il linguaggio del business, raggruppato per concetto (non per tipo tecnico): `src/Domain/Invoices/` con dentro Actions, DataTransferObjects, Models, QueryBuilders, Collections, Events, Exceptions, States, Rules.
- **Application** — i punti d'ingresso che parlano con l'utente (HTTP web, API, admin, CLI): Controllers, Requests, Resources, ViewModels, Middleware. L'applicazione prende l'input, lo passa al dominio, presenta l'output. Un progetto può avere più applicazioni sullo stesso dominio.

Test del buon partizionamento: la struttura delle cartelle si legge col vocabolario del product owner, e una operazione di business si trova in pochi secondi.

## 3. I mattoni

- **DTO:** i dati entrano nel dominio come oggetti tipizzati, non come array associativi o `$request->all()`. In PHP 8.x: classi `readonly` con constructor promotion e named arguments. Il mapping request→DTO vive al confine (Form Request / factory applicativa), così il dominio non conosce HTTP.
- **Action:** un'operazione di business = una classe con un solo metodo pubblico (`execute`/`__invoke`), dipendenze nel costruttore, DTO in ingresso. Le action si compongono tra loro e sono l'API pubblica del dominio — riusabili identiche da controller, job e comandi Artisan.
- **Model snelli:** Eloquent resta per dati, relazioni, cast. La logica di business sta nelle action; le query complesse in **custom QueryBuilder** dedicati (non pile di scope nel model); le collection arricchite in **custom Collection**.
- **State pattern sui processi:** quando un model ha un ciclo di vita (bozza → inviata → pagata), ogni stato è una classe con le proprie transizioni consentite — non `if` sul campo `status` sparsi ovunque (è "Replace Conditional with Polymorphism" di `refactoring.md` applicato ai model). I valori degli stati: enum native.
- **ViewModel** per preparare i dati di view complesse, invece di logica in Blade o controller gonfi.

## 4. Guardrail

- Ogni mattone si adotta **quando risolve un dolore reale**, non in blocco per dogma: introdurre DTO+Action+State su un CRUD semplice è Speculative Generality (`refactoring.md`).
- Il dominio non importa mai nulla dal layer applicativo; il contrario sì.
- Deviazioni dalla struttura standard Laravel vanno documentate nel README del progetto — chi arriva deve orientarsi in fretta.

## Checklist rapida (review)

- [ ] Logica di business in action, non in controller/model
- [ ] Dati verso il dominio come DTO tipizzati, mai array grezzi
- [ ] Cicli di vita → State + enum, non `if` su `status`
- [ ] Query complesse in QueryBuilder dedicati
- [ ] Nessuna dipendenza Domain → Application
