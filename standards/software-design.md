# Standard — Gestione della complessità

> **Fonte:** John Ousterhout, *A Philosophy of Software Design*. È il contrappunto dichiarato ad alcune prescrizioni di `clean-code.md`: dove i due confliggono, questo file spiega il criterio con cui scegliere.

## 1. Il nemico è la complessità

La complessità è tutto ciò che rende il sistema difficile da capire e modificare. Si manifesta in tre sintomi, utili come vocabolario di review:

- **Change amplification:** una modifica semplice richiede tocchi in molti punti.
- **Cognitive load:** per fare una modifica devi sapere troppe cose.
- **Unknown unknowns:** non è nemmeno ovvio *cosa* devi sapere o toccare (il sintomo peggiore).

Le cause sono due: **dipendenze** (da gestire, non eliminabili) e **oscurità** (informazione importante non evidente). La complessità è incrementale: si accumula per piccole concessioni — per questo "è solo una piccola porcheria" non è mai un argomento valido.

## 2. Moduli profondi

Il modulo migliore offre **molta funzionalità dietro un'interfaccia semplice** (modulo *profondo*). Un modulo *superficiale* — interfaccia complessa quasi quanto l'implementazione — aggiunge costi senza nascondere nulla. Conseguenze pratiche:

- Il valore di una classe si misura sul rapporto funzionalità/interfaccia, **non sulla brevità**. Tante micro-classi superficiali ("classitis") aumentano la complessità di sistema anche se ogni pezzo è piccolo.
- **Information hiding:** ogni modulo incapsula una decisione di progetto; quella conoscenza non deve trapelare dall'interfaccia. Red flag: *information leakage* (la stessa decisione nota a più moduli), *temporal decomposition* (struttura che rispecchia l'ordine di esecuzione invece della conoscenza).
- **Layer diversi, astrazioni diverse:** metodi pass-through e interfacce fotocopiate tra layer sono red flag — il layer che non trasforma l'astrazione non paga il suo costo.
- **Pull complexity downwards:** meglio che la complessità la assorba l'implementazione del modulo piuttosto che scaricarla sugli utilizzatori (es. via parametri di configurazione: pochi, con default sensati).
- **Define errors out of existence:** quando possibile, progetta l'API perché il caso d'errore non esista (es. operazione idempotente) invece di moltiplicare le eccezioni da gestire.
- **Design it twice:** per le interfacce importanti, butta giù due alternative prima di scegliere — la seconda è quasi sempre migliore.

## 3. Tattico vs strategico

- Programmazione **tattica**: "basta che funzioni", chiude il task oggi e sposta il costo sul futuro.
- Programmazione **strategica**: investe una quota costante dell'effort (indicativamente il 10–20%) nel migliorare il design mentre si lavora.

Come team adottiamo l'approccio strategico: è la versione "di design" della regola del campeggio (`refactoring.md`) e del divieto di finestre rotte (`pragmatic-principles.md`).

## 4. Arbitrato con Clean Code

Regola di conflitto: **"quanto è piccola?" perde contro "quanto nasconde?"**. Spezzare una funzione/classe è giusto quando separa responsabilità e riduce ciò che il chiamante deve sapere; è sbagliato quando produce frammenti superficiali che il lettore deve ricomporre mentalmente. In review il criterio da applicare è la profondità, non la conta delle righe.

Su un punto Ousterhout corregge Martin anche sui commenti: i commenti che documentano il *contratto* di un'interfaccia (cosa garantisce, cosa assume) sono parte del design, non un fallimento del naming. Restano vietati quelli in `clean-code.md` §3 (parafrasi, codice commentato).

## Checklist rapida (review)

- [ ] La modifica riduce o aumenta ciò che i chiamanti devono sapere?
- [ ] Nessuna classe/metodo pass-through senza trasformazione di astrazione
- [ ] Decisioni di progetto note a un solo modulo (niente leakage)
- [ ] Configurabilità minima, default sensati
- [ ] Interfaccia importante → valutate almeno due alternative
