# Security Guidelines

Minimum bar for every project. Anything here failing in review blocks the PR.

## Secrets & configuration

- No secrets in code, commits, or prompts — ever. Configuration through
  `.env` (never committed) and `config/*.php`; access via `config()`, not
  `env()` outside config files.
- Rotate any secret that leaks, immediately, even if the commit is amended.

## Input & output

- Every request input is validated (Form Request / `Validator`) with the
  strictest practical rules — allowlists over denylists.
- SQL through Eloquent/Query Builder bindings; raw SQL with interpolated
  user input is forbidden.
- Output escaping: Blade `{{ }}` / React JSX by default. `{!! !!}` and
  `dangerouslySetInnerHTML` require sanitized input and a review comment
  justifying them.
- File uploads: validate MIME + size, store outside the public root with
  generated names.

## Authentication & authorization

- Authorize every route and action explicitly — policy, gate, or middleware.
  Model binding does not imply the user may touch the model
  (no IDOR: always scope queries to the authenticated user/tenant).
- Mass-assignment protection on all models (`$fillable`); never
  `$request->all()` into `create()`/`update()`.
- Session/token handling stays framework-standard (Fortify/Sanctum); don't
  hand-roll auth.

## Dependencies & CI

- Keep dependencies updated (Dependabot enabled on every repo); react to
  advisories on the day they land.
- `composer audit` / `npm audit` failures in CI block the merge.
- Lockfiles are always committed.

## Data

- Personal data: collect the minimum, log the minimum (never log passwords,
  tokens, or full request bodies containing them), encrypt sensitive columns
  (`encrypted` cast).
- Errors shown to users are generic; details go to logs. `APP_DEBUG=false`
  outside local.

## When in doubt

Security questions outrank shipping speed: raise it in the PR or with the
team instead of guessing.
