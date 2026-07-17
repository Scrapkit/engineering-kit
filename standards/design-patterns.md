# Standard — Principi di design e pattern

> **Fonti:** Freeman & Robson, *Head First Design Patterns* (2ª ed.); Matt Zandstra, *PHP 8 Objects, Patterns, and Practice — Vol. 1* (parte pattern).

## 1. Principi (vengono prima dei pattern)

I pattern sono conseguenze di questi principi, non viceversa. In review si valuta l'aderenza ai principi, non il numero di pattern usati.

1. **Incapsula ciò che varia.** Identifica gli aspetti dell'applicazione che cambiano e separali da ciò che resta stabile. È il principio da cui derivano quasi tutti i pattern.
2. **Programma verso un'interfaccia, non un'implementazione.** Il tipo dichiarato è un supertipo (interfaccia o astrazione); l'oggetto concreto arriva a runtime.
3. **Preferisci la composizione all'ereditarietà.** Comportamenti componibili a runtime battono gerarchie rigide decise a compile time.
4. **Punta a design debolmente accoppiati** tra oggetti che interagiscono: ognuno conosce degli altri solo l'interfaccia.
5. **Open-Closed:** aperto all'estensione, chiuso alla modifica. Aggiungere un caso nuovo non deve richiedere di toccare codice esistente e testato.
6. **Dependency Inversion:** dipendere da astrazioni, mai da classi concrete di alto valore di business.
7. **Least Knowledge (Legge di Demetra):** parla solo con i tuoi "vicini diretti"; le catene `$a->b()->c()->d()` sono uno smell (vedi `refactoring.md`, "Message Chains").
8. **Hollywood Principle:** "non chiamarci, ti chiamiamo noi" — i moduli di alto livello orchestrano quelli di basso livello (è il modo in cui il framework chiama il nostro codice).
9. **Single Responsibility:** una classe, un motivo per cambiare.

## 2. Catalogo operativo — quando usare cosa

| Pattern | Intento | Dove lo usiamo (contesto Laravel/PHP) |
|---|---|---|
| **Strategy** | Algoritmi intercambiabili incapsulati dietro un'interfaccia | Calcoli/regole che variano per cliente o configurazione; driver custom |
| **Observer** | Notifica uno-a-molti al cambio di stato | Events + Listeners del framework; non reinventarlo a mano |
| **Decorator** | Aggiungere responsabilità a runtime senza toccare la classe | Wrapping di servizi (cache/logging attorno a un repository), middleware come concetto |
| **Factory Method / Abstract Factory** | Delegare la scelta della classe concreta | Manager/driver pattern, `bind()` nel container, factories di test |
| **Command** | Incapsulare una richiesta come oggetto | Job in coda, comandi Artisan, azioni invocabili |
| **Adapter** | Rendere compatibile un'interfaccia esterna con quella attesa | Integrazioni con SDK/API terze dietro un contract nostro |
| **Facade** | Interfaccia semplice su un sottosistema complesso | Le Facade del framework; per il nostro dominio preferire dependency injection |
| **Template Method** | Scheletro d'algoritmo nel padre, passi variabili nei figli | Base class di import/report con hook; usare con misura (è ereditarietà) |
| **Composite** | Trattare oggetti singoli e aggregati in modo uniforme | Strutture ad albero (categorie, menu, regole annidate) |
| **State** | Comportamento che cambia con lo stato interno | Macchine a stati su modelli (ordini, pratiche) al posto di `if` su campi status |
| **Proxy** | Controllare l'accesso a un oggetto | Lazy loading, controllo permessi attorno a servizi costosi |
| **Iterator** | Attraversare una collezione senza esporne la struttura | Già fornito da Collections/generatori; non esporre array interni |
| **Singleton** | Istanza unica | ⚠️ **Non implementarlo a mano.** Se serve un'istanza condivisa: `singleton()` del container. Lo stato globale nasconde le dipendenze e uccide la testabilità |

Zandstra estende il catalogo classico con **pattern enterprise e di database** (es. Front Controller, Domain Model, Data Mapper, Unit of Work): utili come vocabolario per capire *cosa fa già il framework* (Eloquent è un Active Record; il Router+Kernel un Front Controller), non da reimplementare.

## 3. Anti-regole

- **Non forzare i pattern.** Un pattern si "scopre" quando il problema lo richiede; applicarlo in anticipo è Speculative Generality.
- Il nome del pattern va nel nome della classe solo se aiuta la ricerca (`InvoiceNumberStrategy` sì, `UserManagerFactorySingleton` no).
- Se due sviluppatori non riescono a spiegare il pattern in una frase, in quel punto è complessità, non design.

## Checklist rapida (review)

- [ ] La parte che varia è isolata dietro un'interfaccia?
- [ ] Nuovo caso di business = nuova classe, non nuovo `if`?
- [ ] Nessun Singleton/stato statico fatto a mano?
- [ ] Il pattern scelto è spiegabile in una frase?
