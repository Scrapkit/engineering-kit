# Standard — Leggibilità del codice (Clean Code)

> **Fonte:** Robert C. Martin, *Clean Code* (2008). Criterio di fondo: il codice è pulito se un altro membro del team lo capisce e lo estende senza chiedere spiegazioni all'autore.
>
> ⚠️ *Nota critica:* alcune prescrizioni del libro (funzioni ultra-corte, zero commenti) sono oggi dibattute. Le regole qui sotto sono la versione che adottiamo come team; in caso di conflitto con `refactoring.md` o con le convenzioni Laravel, vincono quelle.

## 1. Naming

- I nomi rivelano l'intento: devono rispondere a *perché esiste, cosa fa, come si usa*. Se un nome ha bisogno di un commento per essere capito, il nome è sbagliato.
- Nomi pronunciabili e ricercabili; niente abbreviazioni criptiche (`$d`, `$tmp2`) né contesto ridondante (`$userUserName`).
- Funzioni/metodi = verbi (`calculateTotal`, `suspend`); classi = sostantivi; boolean leggibili come predicati (`isExpired`, `hasAccess`).
- Un concetto, una parola: non alternare `fetch`/`get`/`retrieve` per la stessa operazione nel codebase.

## 2. Funzioni e metodi

- **Piccole e con un solo compito.** Se per descrivere il metodo servono "e"/"poi", va spezzato (→ Extract Function). Un solo livello di astrazione per funzione: o orchestri, o fai il lavoro, non entrambi.
- **Pochi argomenti:** 0–2 come norma; da 3 in su, quasi sempre è un oggetto che chiede di esistere (→ Introduce Parameter Object, DTO, value object).
- **Niente flag boolean come argomenti:** un flag significa che la funzione fa due cose — spezzarla in due metodi con nomi chiari.
- **Niente side effect nascosti:** il metodo fa ciò che dice il nome, nient'altro. Command-Query Separation: o risponde a una domanda, o cambia stato — non entrambi (stesso principio di "Separate Query from Modifier" in `refactoring.md`).
- Evitare condizionali negativi doppi; preferire early return (guard clauses).

## 3. Commenti

- Il primo tentativo è sempre *esprimersi nel codice* (nomi migliori, Extract Function), non commentare codice confuso.
- Commenti legittimi: **perché** una scelta è stata fatta, avvertimenti sulle conseguenze, riferimenti a ticket/decisioni, doc-block richiesti dal tooling.
- Commenti vietati: parafrasi dell'ovvio, log di modifiche (c'è Git), **codice commentato** — si cancella e basta, il version control lo ricorda.

## 4. Formattazione e struttura

- La formattazione meccanica non si discute in review: la decide **Pint/PER-CS** in CI.
- Ciò che il formatter non copre: densità verticale — variabili dichiarate vicino all'uso, funzioni dipendenti vicine tra loro, il file si legge dall'alto verso il basso come un articolo (dal generale al dettaglio).

## 5. Errori e confini

- Eccezioni al posto di return code; **mai restituire né passare `null`** come contratto implicito — usare tipi nullable espliciti solo dove il dominio lo prevede, altrimenti eccezioni, Null Object o `Optional`-like (vedi "Introduce Special Case" in `refactoring.md`).
- **Confini incapsulati:** API e librerie terze non si spargono nel codebase; si avvolgono dietro un'interfaccia nostra (stessa regola di `pragmatic-principles.md` sulla reversibilità).

## 6. Test

- I test sono codice di prima classe: stessa cura di naming e struttura della produzione.
- Criterio **FIRST**: Fast, Independent, Repeatable, Self-validating, Timely.
- Un concetto per test, struttura Arrange-Act-Assert; il nome del test descrive il comportamento (`test_suspended_user_cannot_login`), non l'implementazione.

## Checklist rapida (review)

- [ ] Ogni nome si spiega da solo (zero commenti-stampella)
- [ ] Metodi con un compito, ≤2 argomenti, niente flag boolean
- [ ] Nessun `null` implicito nei contratti, nessun codice commentato
- [ ] Librerie terze dietro interfacce nostre
- [ ] Test FIRST, un concetto per test, nomi che descrivono il comportamento
