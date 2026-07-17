# Standards — come si scrive il codice

Standard derivati dai libri di riferimento del team. Insieme a [`processes/`](../processes/)
(come si lavora) seguono la stessa convenzione di `claude-code-standards`, così restano
compatibili con un'eventuale pubblicazione in `.claude/rules/` dei progetti consumer.

Ogni file chiude con una **checklist rapida** usabile direttamente in code review o come
regola per Claude Code.

## Mappatura fonte → file

| Libro | File | Contenuto |
|---|---|---|
| Zandstra — *PHP 8 Objects, Patterns, and Practice* Vol. 1 (7ª ed.) | [`php-oop.md`](php-oop.md) | Tipizzazione PHP 8.x (enum, readonly, typed constants), progettazione delle classi, costrutti da limitare, eccezioni |
| Freeman & Robson — *Head First Design Patterns* (2ª ed.) + Zandstra (parte pattern) | [`design-patterns.md`](design-patterns.md) | 9 principi di design, catalogo pattern → uso in contesto Laravel, anti-regole |
| Stauffer — *Laravel: Up & Running* (3ª ed.) | [`laravel.md`](laravel.md) | Convention over configuration, HTTP layer, Eloquent, view, code/eventi, config e testing |
| Fowler & Beck — *Refactoring* (2ª ed.) | [`refactoring.md`](refactoring.md) | Processo (due cappelli, passi piccoli, test), catalogo 24 smells → rimedi, regole di team |
| Thomas & Hunt — *The Pragmatic Programmer* (20th Anniversary) | [`pragmatic-principles.md`](pragmatic-principles.md) | Atteggiamento professionale, DRY/ETC/ortogonalità, tracer bullets, crash early, automazione |
| R. C. Martin — *Clean Code* | [`clean-code.md`](clean-code.md) | Naming, funzioni, commenti, errori/confini, test FIRST — con nota critica sulle prescrizioni dibattute |
| Feathers — *Working Effectively with Legacy Code* | [`legacy-code.md`](legacy-code.md) | Legacy = codice senza test; characterization test, seam, sprout/wrap, incrementi senza riscritture |
| Ousterhout — *A Philosophy of Software Design* | [`software-design.md`](software-design.md) | Complessità e sintomi, moduli profondi, information hiding, tattico vs strategico; arbitrato con Clean Code |
| Roose/Spatie — *Laravel Beyond CRUD* | [`laravel-architecture.md`](laravel-architecture.md) | Domain vs Application, DTO, Action, State, QueryBuilder custom — per progetti oltre lo scaffolding |

## Note di integrazione

- Le convenzioni marcate come scelte di team (es. `final` di default, soglie) sono
  proposte: da validare con il coordinatore tecnico prima di renderle vincolanti.
- I riferimenti incrociati tra file (es. `refactoring.md`, `clean-code.md`) usano il solo
  nome file: i documenti di questa cartella e di `processes/` formano un namespace unico.

## Avvertenza sulle fonti

Il contenuto è una sintesi rielaborata basata su ricerca web (sommari, indici, cataloghi
ufficiali come refactoring.com, note di lettura pubbliche), **non sul testo integrale dei
libri**. Prima di promuovere una regola a standard vincolante, verificarla sul capitolo
corrispondente.
