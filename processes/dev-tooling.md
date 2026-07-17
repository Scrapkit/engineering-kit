# Processo — Tooling e scaffolding di sviluppo

> **Fonte:** Matt Zandstra, *PHP 8 Objects, Patterns, and Practice — Vol. 2* (7ª ed.): documentazione, version control, ambienti di sviluppo, coding standards, testing, Docker/Ansible, script CLI. È lo "scaffolding" del progetto — ciò che sta attorno al codice.
>
> ⚠️ File volutamente sintetico: da espandere alla lettura del libro. Le sezioni marcate ✅ sono già coperte da prassi di team esistenti.

## 1. Version control ✅

Tutto ciò che serve a ricostruire il progetto sta nel repo (regola condivisa con `pragmatic-principles.md`). Branch di lavoro piccoli e a vita breve; la storia racconta il *perché* (messaggi di commit come documentazione).

## 2. Ambienti di sviluppo ✅

Ambiente riproducibile via container (per noi: Laravel Sail/Docker) — "funziona sulla mia macchina" non è un ambiente. Il setup di un progetto da zero deve essere: clone + un comando documentato nel README.

## 3. Coding standards automatizzati

Lo standard di formattazione (PER-CS via Pint) e l'analisi statica si applicano **in CI**, non a memoria: le regole vivono in file versionati nel repo (`pint.json`, livello PHPStan/Larastan), identiche per tutti e per Claude Code. In review non si discute di stile applicabile da una macchina.

## 4. Documentazione

- README di progetto: setup, comandi, deviazioni dalle convenzioni (`laravel-architecture.md` §4).
- Doc-block dove il type system non arriva e sui contratti delle interfacce pubbliche (`software-design.md` §4) — non sulla sintassi ovvia.
- Le decisioni architetturali significative si registrano (formato leggero tipo ADR: contesto → decisione → conseguenze).

## 5. Testing come infrastruttura

La suite gira identica in locale e in CI, verde come prerequisito di merge. Disciplina d'uso in `testing-tdd.md`; strumenti Laravel in `laravel.md` §6.

## 6. Script e automazione

Operazioni ricorrenti di progetto (setup, seed, controlli, rilasci) → comandi Artisan o script versionati, con nome parlante e documentati nel README. Un comando CLI è codice a tutti gli effetti: tipizzato, testato dove ha logica.

## Checklist rapida (nuovo progetto / audit)

- [ ] Setup riproducibile: clone + comando unico
- [ ] Pint + analisi statica configurati nel repo e bloccanti in CI
- [ ] README con setup, comandi e deroghe alle convenzioni
- [ ] Suite di test bloccante per il merge
- [ ] Operazioni manuali ricorrenti trasformate in comandi versionati
