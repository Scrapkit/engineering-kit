# Architecture Guidelines

Decision criteria, not rules. When two guidelines conflict, prefer the one
that keeps the code **simpler to delete or change later**.

## Principles we actually apply

- **SOLID, pragmatically.** Single responsibility and dependency inversion
  carry most of the weight: a class should have one reason to change, and
  domain logic should depend on abstractions only where a second
  implementation realistically exists (or tests need one). Don't create an
  interface with a single implementation "for flexibility".
- **Clean code = readable code.** Descriptive names, small functions, no
  dead code, no commented-out code. A junior developer should follow the
  flow without a guide.
- **Separation of concerns.** HTTP in controllers, business rules in
  services/models, persistence in Eloquent, presentation in React.
  A change to one layer should rarely force changes in another.

## DRY vs premature abstraction

Duplication is cheaper than the wrong abstraction.

- Two occurrences: usually leave them. Note the duplication in the PR.
- Three occurrences **with the same reason to change**: extract.
- Never extract code that is only *coincidentally* similar — if the two call
  sites can evolve differently, an abstraction couples them wrongly.
- When an abstraction accumulates flags/parameters to serve divergent
  callers, inline it back and split.

## Managing complexity

- Prefer boring, explicit code over clever code. Optimize for the reader.
- Depth over breadth: a module should hide complexity behind a small
  interface, not leak it through many shallow classes.
- Introduce a pattern only when the pain it solves is already present, not
  when you predict it might appear.

## When to create a service class

Create a service when **any** of these hold:

- The logic spans multiple models or external systems (payment + invoice +
  mail).
- The same operation is triggered from more than one entry point
  (controller + command + job).
- The logic needs isolated testing with mocked collaborators.

Do **not** create a service for a one-line Eloquent call, a single-use
transformation, or "because controllers shouldn't have logic" — a Form
Request plus a model method is often enough.

## When to create a package

Extract to a `scrapkit/*` package when **all** of these hold:

1. At least two projects need the same behavior *today* (not hypothetically).
2. The behavior has a clear boundary (its own config, its own tests, no
   reach into app internals).
3. Someone owns its versioning and changelog.

Otherwise keep it in the app; extraction is easy later, un-extraction is not.

## When to avoid a pattern

- **Repository over Eloquent**: skip unless you truly swap storage backends
  or share a large query surface. Eloquent is already the abstraction.
- **Interfaces everywhere**: skip when there's one implementation and no
  test double is needed.
- **Events for linear flows**: if A always causes B and nothing else listens,
  call B directly; events are for genuine fan-out or decoupled modules.
- **DTOs for every array**: use them at module/API boundaries; inside a
  method chain, a typed array shape in PHPDoc can be enough.
- **Microservices / new infrastructure**: not until the monolith measurably
  can't serve the need.
