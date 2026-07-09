# Scrapkit Engineering Kit

The **single source of truth** for Scrapkit engineering standards: coding
standards, architecture guidelines, AI-usage guidelines, shared tool
configurations, and process templates. One versioned package instead of
copies drifting apart across repositories.

It ships as two packages from the same repository and version tag:

| Package | Registry | Provides |
| --- | --- | --- |
| `scrapkit/engineering-kit` | Packagist (Composer) | Docs, PHP configs (PHPStan, Pint), Claude Code guidelines & prompts, PR/issue templates, `artisan engineering-kit:*` commands |
| `@scrapkit/engineering-kit` | npm | ESLint, Prettier, tsconfig, Vitest base configs (consumed by extension, nothing copied) |

CI is intentionally **not** part of this package: reusable GitHub Actions
workflows live in [`Scrapkit/ci-pipeline`](https://github.com/Scrapkit/ci-pipeline)
and are referenced from consumer projects (see
[`examples/laravel-react/.github/workflows/ci.yml`](examples/laravel-react/.github/workflows/ci.yml)).

## What's inside

```
docs/          coding, architecture, AI, pull-request, security guidelines
configs/       php/ (phpstan.neon, pint.json)  javascript/ (eslint, prettier, tsconfig)  testing/ (pest, vitest)
templates/     PR template, issue template, commit convention
claude/        org-wide CLAUDE.md, imported into each project's CLAUDE.md
plugins/       the Claude Code plugin: reusable prompts (code review, feature dev, refactoring, quality audit)
examples/      a fully wired Laravel + React + TypeScript consumer project
src/           the artisan install/update commands
```

## Installation (Laravel + React + TypeScript project)

```bash
composer require scrapkit/engineering-kit --dev
php artisan engineering-kit:install

npm install --save-dev @scrapkit/engineering-kit
```

`engineering-kit:install` creates (never overwrites) the PHP-side files:

- `pint.json` — copy of the shared config (Pint cannot include external files)
- `phpstan.neon` — includes the shared baseline, keeps `paths` local
- `CLAUDE.md` — with the `@vendor/scrapkit/engineering-kit/claude/CLAUDE.md`
  import line (org rules follow the installed version automatically)
- `.github/PULL_REQUEST_TEMPLATE.md`, `.github/ISSUE_TEMPLATE/default.md`
- `.claude/commands/{code-review,feature-development,refactoring,quality-audit}.md`

## The prompts as a Claude Code plugin

The four prompts also ship as a Claude Code plugin, installed over git rather
than Composer. Use it to get them in repositories this package is not installed
in — including repositories that are not PHP at all.

The prompts are namespaced when they come from the plugin:
`/engineering-kit:quality-audit`, not `/quality-audit`.

**For yourself, across every repository.** Add the marketplace once, install,
and enable it in your own `~/.claude/settings.json`:

```bash
/plugin marketplace add scrapkit/engineering-kit
/plugin install engineering-kit@scrapkit
```

**For a whole team.** Commit this to the project's `.claude/settings.json`, and
everyone who clones it gets the prompts with no setup:

```json
{
  "extraKnownMarketplaces": {
    "scrapkit": { "source": { "source": "github", "repo": "scrapkit/engineering-kit" } }
  },
  "enabledPlugins": { "engineering-kit@scrapkit": true }
}
```

Refresh with `/plugin marketplace update` after a new release.

The plugin and `engineering-kit:install` read the same files, under
`plugins/engineering-kit/skills/`. A Laravel project that does both will see
each prompt twice — once as `/quality-audit`, once as
`/engineering-kit:quality-audit`. Pick one route per project.

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

Release checklist: update `CHANGELOG.md`, bump `package.json` version, tag.

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
- [Architecture guidelines](docs/architecture-guidelines.md) — decision criteria, not just rules
- [AI guidelines](docs/ai-guidelines.md) — how we use AI assistants
- [Pull request guidelines](docs/pull-request-guidelines.md)
- [Security guidelines](docs/security-guidelines.md)
- [Commit convention](templates/commit-convention.md)
