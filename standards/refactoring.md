# Standard — Refactoring e code smells

> **Fonte:** Martin Fowler (con Kent Beck), *Refactoring: Improving the Design of Existing Code* (2ª ed., 2018). Catalogo completo: refactoring.com/catalog

## 1. Principi di processo

- **Definizione operativa:** refactoring = modifica della struttura interna **senza cambiare il comportamento osservabile**, in una sequenza di passi piccoli e verificabili.
- **Prerequisito: test.** Non si refattorizza codice senza una suite di test auto-verificante che copra il comportamento toccato. Se manca, il primo passo è scriverla (characterization test).
- **Regola dei due cappelli:** o stai aggiungendo funzionalità, o stai refattorizzando. Mai entrambe nello stesso momento — e idealmente **non nello stesso commit**: commit di refactoring separati da commit di comportamento.
- **Refactoring preparatorio:** se la struttura attuale rende difficile la feature, prima rendi facile il cambiamento, poi fai il cambiamento facile.
- **Regola del campeggio:** lascia il codice un po' più pulito di come l'hai trovato — refactoring opportunistico, piccolo e continuo, dentro il lavoro normale. Non serve chiedere permesso per il refactoring di routine; le ristrutturazioni grandi invece si pianificano nel team.
- **Passi piccoli:** ogni passo lascia il codice funzionante (test verdi). Se qualcosa si rompe, il colpevole è nell'ultimo micro-passo.
- Guida di fondo: il codice deve essere comprensibile da un altro essere umano prima che efficiente per la macchina.

## 2. Catalogo dei code smells (2ª ed.) → rimedio tipico

| Smell | Sintomo | Refactoring tipico |
|---|---|---|
| Mysterious Name | Il nome non comunica scopo/uso | Rename Variable/Field, Change Function Declaration |
| Duplicated Code | Stessa struttura in più punti | Extract Function, Pull Up Method |
| Long Function | Funzione lunga, difficile da capire | Extract Function, Decompose Conditional |
| Long Parameter List | Troppi parametri | Introduce Parameter Object, Preserve Whole Object |
| Global Data | Stato globale difficile da tracciare | Encapsulate Variable |
| Mutable Data | Mutazioni con effetti a sorpresa | Change Reference to Value, Remove Setting Method |
| Divergent Change | Un modulo cambia per motivi diversi | Split Phase, Extract Class |
| Shotgun Surgery | Un cambiamento tocca molti file | Move Function/Field, Inline Class |
| Feature Envy | Metodo più interessato ai dati altrui | Move Function |
| Data Clumps | Stessi 3-4 dati sempre insieme | Introduce Parameter Object, Extract Class |
| Primitive Obsession | Primitive al posto di tipi di dominio | Replace Primitive with Object (value object / enum) |
| Repeated Switches | Stesso switch/`match` in più punti | Replace Conditional with Polymorphism |
| Loops | Loop dove una pipeline è più chiara | Replace Loop with Pipeline (→ Collections) |
| Lazy Element | Elemento che non giustifica la sua esistenza | Inline Function/Class, Collapse Hierarchy |
| Speculative Generality | Astrazioni "per il futuro" mai usate | Remove Dead Code, Inline, Collapse |
| Temporary Field | Campo valorizzato solo a volte | Extract Class, Introduce Special Case |
| Message Chains | `$a->b()->c()->d()` | Hide Delegate |
| Middle Man | Classe che delega quasi tutto | Remove Middle Man |
| Insider Trading | Moduli che si scambiano troppi dettagli | Move Function/Field, Hide Delegate |
| Large Class | Troppe responsabilità, troppi campi | Extract Class, Extract Superclass |
| Alternative Classes with Different Interfaces | Classi simili con interfacce diverse | Change Function Declaration, Extract Superclass |
| Data Class | Solo getter/setter, zero comportamento | Move Function (porta la logica dai client ai dati) |
| Refused Bequest | La sottoclasse rifiuta l'eredità del padre | Replace Subclass with Delegate |
| Comments (deodorante) | Commento che spiega codice poco chiaro | Extract Function + Rename finché il commento è superfluo |

## 3. Refactoring da conoscere a memoria

Il "kit di base" quotidiano: **Extract Function, Inline Function, Extract Variable, Rename, Change Function Declaration, Introduce Parameter Object, Encapsulate Variable, Split Phase**. Per i condizionali: **Guard Clauses** al posto di if annidati e **Replace Conditional with Polymorphism** per gli switch sul tipo. Per l'ereditarietà problematica: **Replace Subclass/Superclass with Delegate** (è la forma concreta di "composizione > ereditarietà").

## 4. Regole di team

- PR di refactoring puro = descrizione che dichiara "nessun cambio di comportamento" + test invariati verdi.
- Uno smell segnalato in review si cita **per nome** (vocabolario condiviso di questo file) con il refactoring proposto.
- Non mescolare riformattazione massiva (Pint/CS fixer) e refactoring strutturale nello stesso diff.

## Checklist rapida (review)

- [ ] Test verdi prima e dopo, comportamento invariato
- [ ] Commit di refactoring separati da quelli di feature
- [ ] Smell citati per nome, rimedio dal catalogo
- [ ] Passi piccoli (diff leggibile)
