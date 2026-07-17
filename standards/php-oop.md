# Standard PHP — Object-Oriented Design

> **Fonte principale:** Matt Zandstra, *PHP 8 Objects, Patterns, and Practice — Vol. 1* (7ª ed., aggiornata a PHP 8.3).
> Da integrare con PSR-12 / PER Coding Style per la formattazione.

## 1. Tipizzazione

- `declare(strict_types=1)` in ogni file PHP.
- Dichiarare **sempre** i tipi di parametri, ritorno e proprietà. I doc-block non sostituiscono i tipi nativi: si usano solo dove il type system non arriva (es. generics di collection: `@return Collection<int, User>`).
- Preferire tipi espliciti e ristretti: nullable (`?Foo`) e union (`Foo|Bar`) solo quando il dominio lo richiede davvero; `mixed` è un code smell.
- **Typed class constants** (PHP 8.3+): tipizzare le costanti di classe/interfaccia.
- **Enum** (pure o backed) al posto di gruppi di costanti stringa/int per insiemi chiusi di valori (stati, ruoli, tipi). Le enum possono avere metodi: la logica legata al valore vive lì.
- **`readonly` properties** per value object e DTO; **`readonly class`** quando l'intero oggetto è immutabile. L'immutabilità di default riduce i bug da stato condiviso.
- **Constructor property promotion** come stile di default per costruttori che assegnano soltanto.

## 2. Progettazione delle classi

- **Encapsulation prima di tutto:** proprietà `private` di default, `protected` solo se una sottoclasse ne ha un bisogno dimostrato, mai `public` su stato mutabile. Esporre comportamento, non dati.
- **Alta coesione, basso accoppiamento:** una classe ha una sola responsabilità e un solo motivo per cambiare. Se descrivi la classe con una "e", probabilmente va divisa.
- **Composizione > ereditarietà:** estendere una classe crea l'accoppiamento più forte possibile. Prima di scrivere `extends`, chiedersi se un collaboratore iniettato risolve il problema.
- **Programmare verso interfacce, non implementazioni:** i type hint su parametri e proprietà puntano ad astrazioni (interfacce/contracts), le classi concrete si scelgono in fase di wiring (service container).
- **Polimorfismo al posto dei condizionali sul tipo:** un `if`/`switch`/`match` che ramifica sul "tipo" di qualcosa e si ripete in più punti va sostituito con classi polimorfiche (vedi anche `refactoring.md`, smell "Repeated Switches").
- Ereditarietà solo per vere relazioni *is-a* con sostituibilità piena (Liskov): la sottoclasse non deve indebolire il contratto del padre né rifiutarne il comportamento.

## 3. Costrutti da usare con parsimonia

- **Magic methods** (`__get`, `__set`, `__call`): opachi per IDE, static analysis e colleghi. Ammessi solo in codice infrastrutturale (il framework li usa; il nostro codice applicativo no, salvo casi motivati in review).
- **Reflection:** strumento per librerie e tooling, non per la logica applicativa.
- **Static state / Singleton fatti a mano:** vietati nel codice applicativo; le dipendenze condivise passano dal container (vedi `design-patterns.md`).

## 4. Errori ed eccezioni

- Segnalare i fallimenti con **eccezioni di dominio tipizzate** (una piccola gerarchia per bounded context), mai con return code, `false` o `null` ambigui.
- Catturare un'eccezione solo dove si può gestirla in modo sensato; altrimenti lasciarla salire.

## Checklist rapida (review)

- [ ] `strict_types` + tipi ovunque, niente `mixed` non motivato
- [ ] Stato `private`/`readonly`, niente setter gratuiti
- [ ] Insiemi chiusi di valori → `enum`
- [ ] `extends` giustificato? (altrimenti composizione)
- [ ] Type hint su interfacce, wiring nel container
- [ ] Nessun `switch` sul tipo duplicato
- [ ] Fallimenti → eccezioni di dominio
