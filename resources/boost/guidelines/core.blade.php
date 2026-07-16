## Scrapkit Engineering Standards (scrapkit/engineering-kit)

This package is the single source of truth for Scrapkit engineering
standards: shared tool configs, process templates, and these AI guidelines.
The full standards live in `vendor/scrapkit/engineering-kit/docs/`
(`coding-guidelines.md`, `architecture-guidelines.md`,
`security-guidelines.md`, `pull-request-guidelines.md`, `ai-guidelines.md`)
— consult them when a decision is not covered here.

### Before modifying code

- Analyze the existing structure first: read sibling files and follow the
  patterns already in place (naming, directory layout, test style).
- Do not introduce new dependencies without an explicit reason; prefer what
  the project already uses.
- Prefer the simplest solution that solves the problem. No speculative
  abstractions.

### When generating code

- PHP: Pint (`laravel` preset) and PHPStan level 7 via the shared configs;
  explicit parameter and return types everywhere; thin controllers (validate
  via Form Request, delegate, respond); specific exception classes, never
  generic `\Exception`; log with context arrays, not string interpolation.
- React/TypeScript: strict TypeScript with the shared ESLint/Prettier
  configs; function components and hooks only; one component per file with an
  explicit `type Props`.
- Check for existing components, helpers, and utilities before writing new
  ones; avoid duplication.
- Add or update tests for every behavior change (Pest for PHP, Vitest for
  TypeScript). A change without a test needs a stated reason.
- Run the formatters before finishing, and update documentation when you
  change behavior it describes.

### Architecture decision rules

- Prefer the code that is simpler to delete or change later; boring and
  explicit beats clever.
- Duplication is cheaper than the wrong abstraction: extract at three
  occurrences with the same reason to change, not at two.
- Create a service class only when logic spans multiple models or external
  systems, is triggered from more than one entry point, or needs isolated
  testing — not "because controllers shouldn't have logic".
- Do not wrap Eloquent in repositories by default, do not create interfaces
  with a single implementation, and do not use events for linear flows.

### Review priorities

Check, in order of importance: 1. correctness, 2. security (input
validation, authorization, no injection vectors), 3. tests, 4. performance
(no N+1 queries, no unbounded loops over user data), 5. maintainability,
6. standards (Pint/ESLint/PHPStan clean).

### Hard rules

- Never commit secrets, tokens, or `.env` values.
- Never weaken or delete a failing test to make a build pass.
- Never disable static analysis rules inline without a comment explaining why.

### Keeping the kit in sync

After updating this package with Composer, re-sync the managed files:

@verbatim
<code-snippet name="Update engineering-kit managed files" lang="shell">
php artisan engineering-kit:update
</code-snippet>
@endverbatim
