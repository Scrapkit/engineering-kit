# Scrapkit Engineering Standards — Claude Code Guidelines

Org-wide rules for AI-assisted development. These are imported into every
project's `CLAUDE.md` via `@vendor/scrapkit/engineering-kit/claude/CLAUDE.md`.
Project-specific rules live in the project's own `CLAUDE.md` and take
precedence over this file.

The full standards live in `vendor/scrapkit/engineering-kit/docs/` — consult
`coding-guidelines.md`, `architecture-guidelines.md`, and
`testing-guidelines.md` when a decision is not covered here.

## Before modifying code

- Analyze the existing structure first: read sibling files and follow the
  patterns already in place (naming, directory layout, test style).
- Do not introduce new dependencies without an explicit reason; prefer what
  the project already uses.
- Prefer the simplest solution that solves the problem. No speculative
  abstractions.

## When generating code

- Follow the shared standards: Pint (laravel preset) for PHP, the shared
  ESLint/Prettier configs for TypeScript. Run the formatters before finishing.
- Keep consistency with the surrounding project over personal preference.
- Check for existing components, helpers, and utilities before writing new
  ones; avoid duplication.
- Add or update tests for every behavior change (Pest for PHP, Vitest for
  TypeScript). A change without a test needs a stated reason.
- Update documentation when you change behavior it describes.

## When refactoring

- Identify the impact first: find every caller and usage before changing a
  signature or moving code.
- Do not change public APIs (published package classes, HTTP contracts,
  events) without necessity; when unavoidable, keep backward compatibility or
  flag the break explicitly so it lands in a major release.
- Refactor in small steps with tests green at each step.

## When reviewing code

Check, in order of importance:

1. **Correctness** — does it do what it claims? Are edge cases handled?
2. **Security** — input validation, authorization, no secrets in code,
   no raw SQL/HTML injection vectors (see `docs/security-guidelines.md`).
3. **Tests** — new behavior covered, existing tests still meaningful.
4. **Performance** — no N+1 queries, no unbounded loops over user data.
5. **Maintainability** — clear naming, single responsibility, no dead code.
6. **Standards** — Pint/ESLint/PHPStan clean, conventions respected.

## Hard rules

- Never commit secrets, tokens, or `.env` values.
- Never weaken or delete a failing test to make a build pass.
- Never disable static analysis rules inline without a comment explaining why.
