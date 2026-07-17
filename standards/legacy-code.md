# Standard — Codice legacy

> **Fonte:** Michael Feathers, *Working Effectively with Legacy Code* (2004). Complemento operativo di `refactoring.md`: Fowler presuppone i test, Feathers spiega come arrivarci quando non ci sono.

## 1. Definizione operativa

**Legacy code = codice senza test.** Non importa quanto sia vecchio o chi l'abbia scritto: senza test ogni modifica è a rischio, con i test è controllabile. Da questa definizione discendono tutte le regole sotto.

## 2. L'algoritmo per modificare codice legacy

1. Identifica i punti da modificare.
2. Trova dove scrivere i test (interception point).
3. Rompi le dipendenze minime necessarie per testare.
4. Scrivi i test.
5. Modifica e refattorizza.

Mai il contrario: prima la rete di protezione, poi il cambiamento. E mai la "grande riscrittura": si procede in modo incrementale, un punto testato alla volta.

## 3. Characterization test

Quando non sai cosa il codice *dovrebbe* fare, scrivi test che catturano cosa **fa davvero adesso**: chiami il metodo, asserisci un valore che sai essere sbagliato, leggi il valore reale dal fallimento e lo fissi nel test. Non verificano correttezza: bloccano il comportamento corrente prima di toccarlo. Se specifica ufficiale e comportamento osservato divergono, in prima battuta vince il comportamento osservato — è quello da cui dipendono gli utenti (la divergenza va poi segnalata come "Da verificare").

## 4. Seam: dove rompere le dipendenze

Un *seam* è un punto in cui puoi cambiare il comportamento del programma senza modificare il codice in quel punto. In PHP/Laravel i seam naturali sono: costruttori e method injection (sostituisci il collaboratore con un fake), il service container (`bind` di un'implementazione di test), interfacce esistenti, eventi. Tecniche di rottura dipendenze da usare per prime: **Extract Interface**, **Parameterize Constructor**, **Extract Method**, **Sprout/Wrap** (sotto). Regola pratica: scegli il seam più vicino alla modifica, con il raggio d'impatto più piccolo.

È accettabile **sacrificare un po' di incapsulamento per la testabilità** (es. promuovere una dipendenza a parametro di costruttore): un compromesso dichiarato vale più di codice "pulito" e intestabile.

## 5. Sprout e Wrap: aggiungere feature senza peggiorare

Quando devi aggiungere comportamento a un metodo mostruoso non ancora testato:

- **Sprout Method/Class:** scrivi la logica nuova in un metodo/classe *nuovi e testati* (in TDD), e dal codice vecchio fai solo la chiamata. Il legacy non migliora, ma il nuovo nasce pulito.
- **Wrap Method:** rinomina il metodo esistente, crea un metodo col nome originale che chiama il vecchio + il comportamento nuovo.

Vietato invece "aggiungo solo due righe qui dentro": è così che i metodi mostruosi crescono.

## 6. Strumenti di comprensione

Per capire l'impatto di una modifica prima di farla: **effect sketch** (schizzo carta-e-penna di cosa influenza cosa) e **pinch point** (il punto stretto dove pochi test coprono molti effetti — il posto più economico dove testare). **Scratch refactoring:** refattorizza liberamente solo per capire il codice, poi butta via tutto e riparti con i test.

## Checklist rapida (review)

- [ ] Modifica a codice non testato → prima characterization test
- [ ] Feature nuova su codice mostruoso → Sprout/Wrap, mai inline
- [ ] Dipendenze rotte al seam più vicino, via constructor/container
- [ ] Nessuna riscrittura big-bang: incrementi testati
- [ ] Divergenze specifica/comportamento segnalate, non "corrette" in silenzio
