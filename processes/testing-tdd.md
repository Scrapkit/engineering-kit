# Processo — Test-Driven Development

> **Fonte:** Kent Beck, *Test-Driven Development: By Example* (2003). Obiettivo dichiarato del TDD: "clean code that works". Si integra con la sezione testing di `laravel.md` (feature test, factories) e con `clean-code.md` (criterio FIRST).

## 1. Le due regole

1. Scrivi codice di produzione **solo** per far passare un test automatico che fallisce.
2. Elimina la duplicazione (tra test e implementazione, e nell'implementazione).

Da queste discende il ciclo:

## 2. Red → Green → Refactor

- **Red:** scrivi un piccolo test che non passa (magari nemmeno compila). Il test definisce cosa significa "corretto" *prima* dell'implementazione.
- **Green:** fallo passare nel modo più rapido possibile — sono ammessi "peccati" (valori hardcoded, copia-incolla). L'obiettivo è tornare in stato verde in fretta.
- **Refactor:** ora, con la rete verde, rimuovi la duplicazione e sistemi il design (catalogo in `refactoring.md`).

Il refactoring è **dentro** ogni ciclo, non una fase futura.

## 3. Tecniche di lavoro

- **Test list:** prima di iniziare, scrivi l'elenco dei test/casi che ti aspetti di dover coprire. Poi ne prendi uno alla volta. La lista è il tuo buffer mentale: ci aggiungi le idee che arrivano mentre lavori, senza deviare.
- **One step test:** scegli dalla lista il test che ti insegna qualcosa e che sei confidente di implementare — un passo alla volta verso l'obiettivo.
- **Fake it / Triangulate / Obvious implementation:** parti pure da un valore finto e generalizza col secondo esempio; se l'implementazione è ovvia, scrivila direttamente. La dimensione del passo si adatta alla fiducia: passi piccoli quando sei incerto, più larghi quando il terreno è solido.
- Se resti in rosso troppo a lungo, il passo era troppo grande: torna indietro e spezzalo.

## 4. Regole di team

- TDD è il **default per logica di dominio nuova** (action, service, value object, regole di calcolo). Non è obbligatorio per glue code, configurazione o spike esplorativi — ma il codice di uno spike che sopravvive si copre di test prima del merge.
- Su codice esistente non testato vale `legacy-code.md` (characterization test prima, TDD sul nuovo).
- Un bug segnalato si riproduce **prima** con un test rosso, poi si corregge: il test resta come regressione.
- Il ciclo si riflette nei commit: idealmente si committa in verde, spesso.

## Checklist rapida (review)

- [ ] Logica di dominio nuova: test scritti prima (o comunque presenti e significativi)
- [ ] Bugfix accompagnato dal test di regressione che lo riproduce
- [ ] Nessun test che replichi l'implementazione: i test descrivono comportamento
- [ ] Refactor fatto in verde, dentro il ciclo
