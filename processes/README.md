# Processes — come si lavora

Processi derivati dai libri di riferimento del team. Insieme a [`standards/`](../standards/)
(come si scrive il codice) seguono la stessa convenzione di `claude-code-standards`, così
restano compatibili con un'eventuale pubblicazione in `.claude/rules/` dei progetti consumer.

Ogni file chiude con una **checklist rapida** usabile direttamente in code review o come
regola per Claude Code.

## Mappatura fonte → file

| Libro | File | Contenuto |
|---|---|---|
| Beck — *TDD by Example* | [`testing-tdd.md`](testing-tdd.md) | Due regole, red-green-refactor, test list, regole di adozione del team |
| Forsgren/Humble/Kim — *Accelerate* | [`delivery-metrics.md`](delivery-metrics.md) | Quattro metriche DORA, lettura velocità+stabilità, capability collegate al resto del kit |
| Zandstra — *PHP 8 Objects, Patterns, and Practice* Vol. 2 | [`dev-tooling.md`](dev-tooling.md) | Scaffolding: version control, ambienti, standards in CI, documentazione, script (bozza da espandere alla lettura) |

## Note di integrazione

- Le convenzioni marcate come scelte di team sono proposte: da validare con il
  coordinatore tecnico prima di renderle vincolanti.
- I riferimenti incrociati tra file (es. `laravel.md`, `refactoring.md`) usano il solo
  nome file: i documenti di questa cartella e di `standards/` formano un namespace unico.

## Avvertenza sulle fonti

Il contenuto è una sintesi rielaborata basata su ricerca web (sommari, indici, cataloghi
ufficiali come refactoring.com, note di lettura pubbliche), **non sul testo integrale dei
libri**. Prima di promuovere una regola a standard vincolante, verificarla sul capitolo
corrispondente.
