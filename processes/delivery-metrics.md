# Processo — Metriche di delivery (DORA)

> **Fonte:** Forsgren, Humble, Kim, *Accelerate* (2018) — sintesi di anni di ricerca State of DevOps. Uso previsto nel kit: alimentare la Engineering Health dashboard degli audit con numeri confrontabili nel tempo.

## 1. Le quattro metriche

| Metrica | Cosa misura | Dimensione |
|---|---|---|
| **Deployment Frequency** | Quanto spesso rilasciamo in produzione | Velocità |
| **Lead Time for Changes** | Tempo da commit a produzione | Velocità |
| **Change Failure Rate** | % di deploy che causano un problema in produzione (hotfix, rollback) | Stabilità |
| **Time to Restore** | Quanto ci mettiamo a ripristinare il servizio dopo un incidente | Stabilità |

Il risultato centrale della ricerca: **velocità e stabilità non sono in trade-off** — i team migliori sono più veloci *e* più stabili. Quindi le quattro si leggono sempre **insieme**: ottimizzarne una sola in isolamento (es. deploy più frequenti con failure rate che sale) è un peggioramento, non un miglioramento.

## 2. Regole d'uso per il team

- Le metriche misurano il **sistema di lavoro**, mai le persone. Vietato usarle per confronti individuali o come target punitivi (una metrica usata come obiettivo smette di misurare — legge di Goodhart).
- Si parte semplice: rilevazione anche manuale (data deploy, data commit, incidenti dal log) va bene; l'automazione via CI arriva dopo.
- Ogni trimestre: trend delle quattro metriche a fianco della Engineering Health dashboard. La domanda in retrospettiva non è "il numero è buono?" ma "cosa nel processo lo sta muovendo?".

## 3. Le capability che muovono le metriche

La ricerca collega le prestazioni a pratiche precise — quasi tutte già presenti nel kit, il che li rende rinforzi reciproci: version control di tutto (`pragmatic-principles.md`), test automatici affidabili (`testing-tdd.md`), integrazione continua e lavoro in piccoli batch (`refactoring.md`, passi piccoli), architettura debolmente accoppiata che permette deploy indipendenti (`software-design.md`, `laravel-architecture.md`), deployment automatizzato (`dev-tooling.md`). Migliorare le metriche = investire su queste pratiche, non "spingere di più".

## Checklist rapida (retro trimestrale)

- [ ] Quattro metriche rilevate e a trend, lette in coppia velocità+stabilità
- [ ] Nessun uso individuale/punitivo
- [ ] Per ogni metrica in peggioramento: capability collegata su cui agire
