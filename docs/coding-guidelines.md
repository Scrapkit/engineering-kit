# Coding Guidelines

Concrete standards for day-to-day code. For *when to apply which pattern*,
see [architecture-guidelines.md](architecture-guidelines.md). Everything a
formatter or analyser can enforce is enforced by the shared configs in
`configs/` — this document covers what the tools can't check.

## PHP / Laravel

### Baseline

- PSR-12 via **Laravel Pint** (`laravel` preset, shared `pint.json`).
- **PHPStan/Larastan level 7** via the shared `configs/php/phpstan.neon`.
- Explicit parameter and return types everywhere:
  `function isAccessible(User $user, ?string $path = null): bool`.
- PHP 8 constructor property promotion; no empty `__construct()`.
- Prefer PHPDoc blocks over inline comments; use array-shape types in PHPDoc.

### Naming

- Classes: `PascalCase` nouns (`InvoiceGenerator`), Enums with `TitleCase`
  cases (`Monthly`, `BestLake`).
- Methods and variables: `camelCase`, descriptive over short —
  `isRegisteredForDiscounts`, not `discount()`.
- Eloquent: singular model (`User`), plural table (`users`); follow Laravel
  conventions so the framework does the work.
- Artisan commands: `domain:action` (`engineering-kit:install`).

### Structure & dependency injection

- Controllers stay thin: validate (Form Request), delegate, respond. No
  business logic, no queries beyond trivial lookups.
- Inject dependencies through constructors; never `new` a collaborator that
  has behavior, and avoid facades inside domain services (fine in
  controllers, commands, and views).
- **Service layer**: extract a service class when logic spans multiple models
  or is called from more than one entry point (see architecture guidelines
  for the full criteria).
- **Repository pattern only when needed**: Eloquent already is a repository.
  Add one only when you genuinely swap storage or the query surface is large
  and duplicated. Do not wrap Eloquent by default.

### Validation, exceptions, logging

- All request input goes through Form Requests or `Validator` — never trust
  `$request->input()` raw.
- Throw specific exception classes (`SubscriptionExpiredException`), not
  generic `\Exception`; let the handler map them to responses. Never swallow
  an exception without logging and a comment explaining why.
- Log with context arrays (`Log::warning('payment failed', ['order' => $id])`),
  not string interpolation. No `dd`/`dump`/`ray` in committed code (enforced
  by the shared Pest arch preset).

### Security essentials

See [security-guidelines.md](security-guidelines.md). Non-negotiables:
authorize every route (policy/gate), mass-assignment protection on models,
no raw SQL with user input, no secrets in code.

## React / TypeScript

### Baseline

- TypeScript **strict** (shared `tsconfig.base.json`); typing is mandatory —
  no `any` unless interoperating with untyped code, and then contained.
- Shared ESLint + Prettier configs; imports auto-ordered, type-only imports
  via `import type`.

### Components

- One component per file; file and component share the name
  (`UserCard.tsx` exports `UserCard`).
- Function components + hooks only; no classes.
- Props typed with an explicit `type Props = { … }`; destructure in the
  signature.
- Prefer composition over configuration: small components combined in the
  page, not one component with ten boolean props.
- Reuse `resources/js/components/ui/*` primitives before writing new ones.

### Folder structure (Inertia apps)

```
resources/js/
├── components/        # shared components (ui/ = design-system primitives)
├── hooks/             # shared hooks (useXxx.ts)
├── layouts/           # page layouts
├── lib/               # framework-agnostic utilities
├── pages/             # Inertia pages (mirror route structure)
└── types/             # shared type declarations
```

### State & data

- Server state belongs to Inertia props — don't mirror it into local state.
- Local UI state: `useState`/`useReducer` in the component that owns it;
  lift only when actually shared.
- Extract a custom hook when the same stateful logic appears twice, or when
  a component mixes heavy logic with markup.

### Hooks rules

- Follow `eslint-plugin-react-hooks` (enforced): complete dependency arrays,
  no conditional hooks.
- Effects are a last resort — derive during render when possible; effects
  are for synchronizing with things outside React.

### Errors & CSS

- Handle failure states explicitly: every async UI needs loading, error, and
  empty states. Use error boundaries for render-time failures.
- Styling with Tailwind utilities; combine classes via `cn()`/`clsx`
  (Prettier sorts them). No inline `style` objects for static styles; no new
  CSS files unless the utility approach genuinely can't express it.
