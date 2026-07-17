# Standard — Principi pragmatici

> **Fonte:** Thomas & Hunt, *The Pragmatic Programmer — 20th Anniversary Edition* (2019, 100 tip; elenco ufficiale completo: pragprog.com/tips). Consigli indipendenti da linguaggio e framework: qui teniamo quelli che diventano regole operative per il team.

## 1. Atteggiamento professionale

- **Cura il tuo mestiere e pensa a ciò che fai:** niente pilota automatico; ogni scelta tecnica deve essere difendibile se qualcuno chiede "perché così?".
- **Opzioni, non scuse.** Davanti a un blocco non si dice "non si può fare": si presenta cosa si può fare, con trade-off.
- **Niente finestre rotte.** Design scadenti, decisioni sbagliate e codice povero si sistemano appena visti (o si trackano esplicitamente): il degrado tollerato accelera il degrado. È il complemento della "regola del campeggio" in `refactoring.md`.
- **Software "good enough":** la qualità è un requisito da discutere con gli stakeholder, non un assoluto. Si punta al livello di qualità concordato, non alla perfezione infinita né al rilascio sciatto.
- **Investi nel knowledge portfolio:** imparare con regolarità (linguaggi, strumenti, dominio) è parte del lavoro, non un extra.

## 2. Principi di design

- **DRY — Don't Repeat Yourself.** Ogni *conoscenza* deve avere una rappresentazione unica e autorevole nel sistema. Attenzione: DRY riguarda la conoscenza, non il testo — due pezzi di codice simili che esprimono regole di business *diverse* non sono una violazione; duplicare una regola in codice + validazione + documentazione sì.
- **ETC — Easier To Change.** Il metro di ogni decisione di design: "questa scelta rende il sistema più facile o più difficile da cambiare?". Buon design = facile da cambiare.
- **Ortogonalità:** componenti indipendenti, senza effetti a distanza. Cambiare una cosa non deve romperne un'altra scollegata.
- **Nessuna decisione è definitiva.** Non sposarsi con vendor/librerie: isolare le dipendenze esterne dietro interfacce nostre (vedi Adapter in `design-patterns.md`) per mantenere reversibilità.
- **Tracer bullets:** per feature incerte, costruire prima uno scheletro end-to-end minimo ma funzionante (route → logica → persistenza → UI) e poi ingrassarlo. I **prototipi**, al contrario, servono a imparare e **si buttano via**: mai promuovere un prototipo a codice di produzione.

## 3. Pratiche di codifica

- **Passi piccoli, sempre** — in coppia con la disciplina di `refactoring.md`.
- **Refactor early, refactor often.**
- **Crash early:** un programma che fallisce subito e rumorosamente fa meno danni di uno che arranca in uno stato corrotto. Assert e precondizioni per le assunzioni ("Don't assume — prove it"); mai inghiottire eccezioni.
- **Debugging senza panico:** il bug quasi certamente è nel *nostro* codice, non nel framework o nel linguaggio ("`select` isn't broken"). Riprodurre prima, ipotizzare poi. Il **rubber ducking** (spiegare il problema ad alta voce, o al collega junior) è una tecnica legittima, non una perdita di tempo.
- **Automatizza:** tutto ciò che si fa più di due volte a mano (setup, build, controlli, rilasci) diventa script/CI. Codice che scrive codice (generatori, scaffolding) è benvenuto se il risultato resta leggibile.
- **Tutto sotto version control:** codice, configurazione, script, documentazione — se serve per ricostruire il progetto, sta nel repo.
- **I requisiti si imparano in un feedback loop:** non esistono specifiche complete a priori; rilasci piccoli e conversazione continua con chi usa il software. Il nostro compito è aiutare le persone a capire cosa vogliono.

## Checklist rapida (review)

- [ ] La conoscenza modificata vive in un punto solo? (DRY)
- [ ] La modifica rende il sistema più facile da cambiare? (ETC)
- [ ] Dipendenze esterne dietro un'interfaccia nostra?
- [ ] Fallimenti rumorosi e precoci, nessuna eccezione inghiottita?
- [ ] Operazione manuale ripetuta ≥2 volte → automatizzata?
- [ ] Nessuna "finestra rotta" nuova lasciata indietro
