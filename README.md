# Scrapkit Engineering Kit

The **single source of truth** for Scrapkit engineering standards: coding
standards, architecture guidelines, AI-usage guidelines, shared tool
configurations, and process templates. One versioned package instead of
copies drifting apart across repositories.

It ships as two packages from the same repository and version tag:

| Package | Registry | Provides |
| --- | --- | --- |
| `scrapkit/engineering-kit` | Packagist (Composer) | Docs, PHP configs (PHPStan, Pint), Claude Code guidelines & plugin enablement, Laravel Boost guidelines & skills, PR/issue templates, `artisan engineering-kit:*` commands |
| `@scrapkit/engineering-kit` | npm | ESLint, Prettier, tsconfig, Vitest base configs (consumed by extension, nothing copied) |

CI is intentionally **not** part of this package: reusable GitHub Actions
workflows live in [`Scrapkit/ci-pipeline`](https://github.com/Scrapkit/ci-pipeline)
and are referenced from consumer projects (see
[`examples/laravel-react/.github/workflows/ci.yml`](examples/laravel-react/.github/workflows/ci.yml)).

## What's inside

```
docs/          coding, architecture, AI, pull-request, security guidelines
standards/     how code is written — per-book standards from the team's reference library (in Italian)
processes/     how the team works — TDD, DORA delivery metrics, dev tooling (in Italian)
configs/       php/ (phpstan.neon, pint.json)  javascript/ (eslint, prettier, tsconfig)  testing/ (pest, vitest)
templates/     PR template, issue template, RFC template, commit convention
claude/        org-wide CLAUDE.md, imported into each project's CLAUDE.md
plugins/       the Claude Code plugin: reusable prompts (code review, feature dev, refactoring, quality audit)
resources/     boost/ — guidelines and skills auto-discovered by Laravel Boost
examples/      a fully wired Laravel + React + TypeScript consumer project
src/           the artisan install/update commands
```

## Installation (Laravel + React + TypeScript project)

The Composer package is not on Packagist ([#4](https://github.com/Scrapkit/engineering-kit/issues/4)),
so each project tells Composer to read it straight from GitHub first:

```bash
composer config repositories.engineering-kit vcs https://github.com/Scrapkit/engineering-kit
```

Then install both sides:

```bash
composer require scrapkit/engineering-kit --dev
php artisan engineering-kit:install

npm install --save-dev @scrapkit/engineering-kit
```

Composer resolves versions from this repository's tags. In CI, configure a
GitHub token (`composer config github-oauth.github.com <token>`) to stay clear
of API rate limits. If the package lands on Packagist later, the `repositories`
entry becomes unnecessary but keeps working — nothing to migrate.

`engineering-kit:install` creates (never overwrites) the PHP-side files:

- `pint.json` — copy of the shared config (Pint cannot include external files)
- `phpstan.neon` — includes the shared baseline, keeps `paths` local
- `CLAUDE.md` — with the `@vendor/scrapkit/engineering-kit/claude/CLAUDE.md`
  import line (org rules follow the installed version automatically)
- `.claude/settings.json` — enables the Claude Code plugin from the scrapkit
  marketplace for everyone who clones the project (merged into an existing
  file; only missing keys are added, an explicit opt-out is respected)
- `.github/PULL_REQUEST_TEMPLATE.md`, `.github/ISSUE_TEMPLATE/default.md`
- `docs/rfc/0000-template.md` — the RFC template; copy it to
  `docs/rfc/NNNN-short-title.md` for a decision that needs one
  ([docs/rfc-guidelines.md](docs/rfc-guidelines.md))

## The prompts as a Claude Code plugin

The four prompts ship **only** as a Claude Code plugin, installed over git
rather than Composer, so one route reaches every repository — PHP or not — and
no un-namespaced copy can drift. They are namespaced accordingly:
`/engineering-kit:quality-audit`, not `/quality-audit`.

**In a project that runs `engineering-kit:install`** there is nothing to do:
the command writes the plugin enablement into `.claude/settings.json` (see the
list above), and everyone who clones the project gets the prompts with no
setup:

```json
{
  "extraKnownMarketplaces": {
    "scrapkit": { "source": { "source": "github", "repo": "scrapkit/engineering-kit" } }
  },
  "enabledPlugins": { "engineering-kit@scrapkit": true }
}
```

**For yourself, across every other repository.** Add the marketplace once,
install, and enable it in your own `~/.claude/settings.json`:

```bash
/plugin marketplace add scrapkit/engineering-kit
/plugin install engineering-kit@scrapkit
```

Refresh with `/plugin marketplace update` after a new release.

The plugin's git ref and the package's Composer version move independently.
That is fine: `quality-audit` audits a project against
`vendor/scrapkit/engineering-kit/docs/` at whatever version `composer.lock`
pins; with no package installed it reports Standards Compliance as `n/a`
rather than inventing one.

Releases before 2.0 also copied the prompts into `.claude/commands/` as
un-namespaced commands. `engineering-kit:update` removes those copies — with
`--force` when they were edited locally.

### Which route to use

The plugin and Laravel Boost deliver the same prompt content: the canonical
files live under `plugins/engineering-kit/skills/`, and the Boost copies under
`resources/boost/skills/` are kept byte-identical by a test. The routes are not
interchangeable, though:

- **A Laravel project that uses Laravel Boost** can take the prompts from
  Boost's discovery (see [Using with Laravel Boost](#using-with-laravel-boost)).
  They arrive with the guidelines they cite, so a single Composer version pins
  both and they move together on `composer update`.
- **Every other repository** should use the plugin. It is the only route that
  reaches a repository Composer does not — PHP or otherwise.

Enabling both in one project is supported but discouraged: every prompt shows up
twice, once as a Boost skill and once as `/engineering-kit:quality-audit`, and —
as above — the two pins move independently, so an audit can be scored against
guidelines from a different release.

The JavaScript side is consumed **by extension** — nothing is copied. Wire it
up with three small files (full versions in [`examples/laravel-react/`](examples/laravel-react/)):

```js
// eslint.config.js
import scrapkit from '@scrapkit/engineering-kit/eslint';
export default [...scrapkit /*, project overrides */];
```

```js
// prettier.config.js
import base from '@scrapkit/engineering-kit/prettier';
export default { ...base, tailwindStylesheet: 'resources/css/app.css' };
```

```jsonc
// tsconfig.json
{ "extends": "@scrapkit/engineering-kit/tsconfig.base.json", "include": ["resources/js/**/*"] }
```

## Using with Laravel Boost

Projects that use [Laravel Boost](https://laravel.com/docs/boost) need no
extra configuration. The kit ships Boost's third-party package conventions:
AI guidelines in `resources/boost/guidelines/core.blade.php` (a condensed
version of the org rules, deferring to the full docs in
`vendor/scrapkit/engineering-kit/docs/`) and the four prompts as Agent Skills
in `resources/boost/skills/`. With both packages installed,
`php artisan boost:install` — or `boost:update --discover` on an existing
Boost setup — detects the kit, inlines the guidelines into the generated
agent files (`CLAUDE.md`, `AGENTS.md`, …), and offers the skills.

Boost's copy may coexist with the `@vendor/scrapkit/engineering-kit/claude/CLAUDE.md`
import added by `engineering-kit:install`: both derive from the same Composer
version of this package, so the content never conflicts — the rules are merely
stated twice. For the prompts, pick one route
(see [Which route to use](#which-route-to-use)).

No consumer-side wiring is needed — the [example project](examples/laravel-react/)
demonstrates none because there is none: the whole flow is
`composer require laravel/boost --dev` followed by `php artisan boost:install`.

## Overriding the standards

Every config is a **base layer**; the project extends it and local values win:

| Tool | Override mechanism |
| --- | --- |
| ESLint | entries after `...scrapkit` in the flat-config array |
| Prettier | keys after `...base` in the spread |
| TypeScript | local `compilerOptions` beat the extended base |
| Vitest | `mergeConfig(base, defineConfig({ … }))` |
| PHPStan | local `parameters` beat the shared include |
| Pest arch | add expectations next to `scrapkit_arch_preset()` |
| Pint | edit the copied `pint.json` (it is the override point) |
| CLAUDE.md | project rules below the import line take precedence |

Overrides should be the exception: if every project overrides the same rule,
change it *here* instead.

## Updating

```bash
composer update scrapkit/engineering-kit
npm update @scrapkit/engineering-kit
php artisan engineering-kit:update        # sync managed files, keeps local edits
php artisan engineering-kit:update --force # overwrite locally modified managed files

claude plugin marketplace update scrapkit # prompts installed as the Claude Code plugin

php artisan boost:update                  # guidelines/skills delivered through Laravel Boost
```

The version each project uses is tracked by its `composer.lock` /
`package-lock.json`.

## Versioning

Semantic Versioning; one git tag `vX.Y.Z` releases both packages
(Packagist follows the tag; npm publish runs in [`release.yml`](.github/workflows/release.yml)).

- **MAJOR** — breaking: new rules that fail existing pipelines by default,
  renamed/removed configs or exports, dropped platform versions.
- **MINOR** — new non-breaking rules, new docs/prompts/templates, new configs.
- **PATCH** — fixes to docs or configs with no behavioral tightening.

Release checklist: update `CHANGELOG.md`; bump the version in **both**
`package.json` and `plugins/engineering-kit/.claude-plugin/plugin.json`; tag.
The plugin bump is not redundant with the tag: Claude Code delivers a plugin
update only when that manifest field changes, so a release that skips it never
reaches anyone who installed the plugin. `release.yml` refuses to publish a
tag that disagrees with either file.

## Contributing

Standards change via PR to this repository — never by patching a consumer
project. A change that would fail existing pipelines is a **major** release
and needs a migration note in the changelog. Docs changes follow the same
review process as code ([docs/pull-request-guidelines.md](docs/pull-request-guidelines.md)).

Run the package's own checks:

```bash
composer test      # Pest
composer analyse   # PHPStan
composer format    # Pint
```

## Documentation

- [Coding guidelines](docs/coding-guidelines.md) — PHP/Laravel and React/TS standards
- [Standards](standards/README.md) and [Processes](processes/README.md) — per-book
  standards and processes distilled from the team's reference library (in Italian),
  each with a source mapping and a review checklist
- [Architecture guidelines](docs/architecture-guidelines.md) — decision criteria, not just rules
- [RFC guidelines](docs/rfc-guidelines.md) — when a decision needs an RFC, and how it gets accepted
- [AI guidelines](docs/ai-guidelines.md) — how we use AI assistants
- [Pull request guidelines](docs/pull-request-guidelines.md)
- [Security guidelines](docs/security-guidelines.md)
- [Commit convention](templates/commit-convention.md)
