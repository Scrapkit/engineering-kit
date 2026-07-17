# Standard — Convenzioni Laravel

> **Fonte:** Matt Stauffer, *Laravel: Up & Running* (3ª ed., copre fino a Laravel 11). Riferimento sempre valido: documentazione ufficiale.

## 1. Filosofia: convention over configuration

Il valore di Laravel è velocità + leggibilità. Si ottengono **seguendo le convenzioni del framework**, non combattendole:

- Rispettare le naming convention: model singolare (`User`), tabella plurale (`users`), foreign key `user_id`, pivot in ordine alfabetico. Ogni deviazione va configurata esplicitamente e motivata.
- Usare gli strumenti first-party prima di scrivere codice custom o aggiungere pacchetti terzi (auth scaffolding, code, notifiche, Scout, ecc.).
- Struttura di default del progetto finché non c'è una ragione forte e condivisa per cambiarla.

## 2. HTTP layer

- **Controller sottili:** il controller traduce HTTP ↔ dominio. Niente logica di business, niente query complesse: quelle vivono in model/action/service.
- **Validazione nei Form Request**, non inline nel controller. Il Form Request è anche il posto per `authorize()` puntuale.
- **Route model binding** al posto di `find($id)` manuale.
- Route con **nome** (`->name()`) e riferite sempre per nome, mai per URL hardcoded.
- **Middleware** per concern trasversali (auth, throttling, locale), non `if` ripetuti nei controller.
- API: **API Resources** per serializzare, mai `toArray()` del model direttamente verso l'esterno.

## 3. Eloquent e database

- **Ogni modifica allo schema passa da una migration.** Sempre reversibile quando possibile.
- **Factory + Seeder** per ogni model: sono il prerequisito dei test e degli ambienti demo.
- **Eager loading esplicito** (`with()`) dove si itera su relazioni: il problema N+1 si previene in code review, non in produzione.
- Query riusabili → **local scope** o metodi dedicati; niente query builder duplicato in giro per i controller.
- **Mass assignment:** `$fillable` esplicito; mai `$guarded = []` su model esposti a input utente.
- Accessor/Cast per la formattazione dei dati, così la logica non finisce nelle view.

## 4. View e frontend

- **Blade senza logica di business:** le view ricevono dati già pronti. Ammessi solo condizionali/loop di presentazione.
- Componenti Blade per markup ripetuto; niente copia-incolla di partial.

## 5. Effetti collaterali e lavoro asincrono

- Side effect non essenziali alla risposta (email, notifiche, sync esterni) → **Events + Listeners** o **Job in coda**, non inline nel flusso della richiesta.
- I Job sono piccoli, idempotenti quando possibile, e dichiarano `tries`/`backoff` consapevolmente.

## 6. Configurazione e testing

- **`env()` solo dentro i file `config/`**; il codice applicativo legge esclusivamente `config()`.
- **Feature test come default** (testano il comportamento attraverso lo stack HTTP), unit test per logica pura isolata. Ogni feature nuova arriva con i suoi test; Dusk per i flussi browser critici.
- `RefreshDatabase` + factories: i test non dipendono mai da dati pre-esistenti.

## Checklist rapida (review)

- [ ] Naming convention del framework rispettate (o deroga documentata)
- [ ] Controller sottile + Form Request per la validazione
- [ ] Nessun N+1 evidente (eager loading dove serve)
- [ ] Migration + factory aggiornate insieme al model
- [ ] Side effect in event/job, non inline
- [ ] `env()` fuori da `config/` = ❌
- [ ] Feature test presenti per il comportamento nuovo
