# Commit Convention

We follow [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/).

```
<type>(<optional scope>): <description>

<optional body>

<optional footer>
```

## Types

| Type | Use for |
| --- | --- |
| `feat` | A new feature visible to users of the code |
| `fix` | A bug fix |
| `refactor` | Code change that neither fixes a bug nor adds a feature |
| `perf` | Performance improvement |
| `test` | Adding or correcting tests |
| `docs` | Documentation only |
| `style` | Formatting only (no logic change) |
| `build` | Build system or dependency changes |
| `ci` | CI configuration changes |
| `chore` | Maintenance that touches no `src` or `test` code |

## Rules

- Description in imperative mood, lowercase, no trailing period:
  `feat(auth): add passkey login`, not `Added passkey login.`
- One logical change per commit; keep commits small enough to review.
- Breaking changes: add `!` after the type/scope **and** a `BREAKING CHANGE:`
  footer explaining the migration path. Breaking commits require a major
  release of the package they touch.
- Reference issues in the footer: `Closes #123`.

## Examples

```
feat(users): add suspension expiry notification

fix(i18n): fall back to default locale when the cookie locale is unknown

refactor!: drop support for Laravel 10

BREAKING CHANGE: minimum supported version is now Laravel 11.
Update your composer constraint to ^11.0.
```
