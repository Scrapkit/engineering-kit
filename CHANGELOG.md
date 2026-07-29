# Changelog

All notable changes to `scrapkit/engineering-kit` / `@scrapkit/engineering-kit`
are documented here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and the project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## v2.4.1 - 2026-07-29

### Fixed

- **Two of the four npm exports could not be loaded by any consumer.**
  `@scrapkit/engineering-kit/prettier` imported a default export that
  `prettier-plugin-tailwindcss` does not have — it is ESM-only and exposes only
  `options`, `parsers`, `printers` — so the module failed to link, and since the
  failure came from inside the shared config a consumer could not override it.
  The plugin is now named as a string, the form Prettier resolves itself.
  `@scrapkit/engineering-kit/vitest` shipped as TypeScript source: Vite
  externalizes dependencies instead of bundling them, so the file reached Node's
  ESM loader as-is and Node refuses to strip types under `node_modules`
  (`ERR_UNSUPPORTED_NODE_MODULES_TYPE_STRIPPING`). It is now shipped as
  JavaScript. **Both specifiers are unchanged, so consumers need no edit** — the
  documented `{ ...base }` and `mergeConfig(base, …)` snippets start working.

### Changed

- **`js-configs.yml` imports every npm export instead of syntax-checking two of
  them.** `node --check` only parses: it never links imports, which is how both
  bugs above shipped across two releases and stayed invisible to the suite. The
  job now installs the dependencies and `await import()`s whatever
  `package.json` promises in `exports`, so a config that cannot load fails here
  rather than in a consumer project.

## v2.4.0 - 2026-07-26

### Fixed

- **The prompts no longer degrade in silence where Composer never reached.**
  `code-review` and `feature-development` cited
  `vendor/scrapkit/engineering-kit/docs/…` as their checklist, but Claude Code
  materialises only `plugins/engineering-kit/` when the plugin is installed:
  in a repository that does not require the package those paths do not exist,
  so the prompts ran against invented criteria without saying so. Only
  `quality-audit` noticed, and reported `n/a`. Each skill now carries the
  guidelines it checks against in its own `references/` directory, so the
  checklist travels with the prompt on every route.

### Added

- **A `SessionStart` hook in the plugin**, delivering the org-wide rules to the
  repositories Composer cannot reach — previously they arrived only through the
  `@vendor/scrapkit/engineering-kit/claude/CLAUDE.md` import. It is deliberately
  narrow: it runs only when the repository's `origin` remote is under
  `Scrapkit/`, and stays silent when that vendor file exists, because the
  Composer import already states the rules there. A fallback, never a second
  parallel channel.
- **`composer sync-claude-assets`** (`scripts/sync-claude-assets.php`) —
  regenerates the copies that have to live inside the plugin subtree.
  `docs/` and `claude/` stay the canonical, human-facing sources; the script
  writes the skills' `references/` and `resources/boost/skills/`, prunes
  orphans, and the test suite fails on a copy that has drifted. Run it after
  editing a canonical file and commit what it writes.

### Changed

- The four skills cite `references/<document>.md` instead of a `vendor/` path,
  and `quality-audit` lost its "standards unreachable → `n/a`" branch: the
  documents ship with the skill, so the degraded path no longer exists. As a
  side effect the prompt and its standards are pinned by the same git ref, so
  an audit can no longer be scored against guidelines from a different release.
- `tests.yml` now also runs on changes under `docs/`, `claude/`, `plugins/` and
  `resources/`. A docs-only pull request did not run Pest and could ship a
  stale generated copy.
- `composer analyse` passes `--memory-limit=512M`, and PHPStan analyses
  `scripts/` alongside `src/`.

## v2.3.2 - 2026-07-25

### Changed

- `release.yml` now calls `Scrapkit/ci-pipeline/.github/workflows/github-release.yml@v1`
  instead of carrying its own copy of the note-extraction logic. Two copies of
  the same thing diverge at the first edit, and the other eight packages needed
  it too. Behavior is unchanged; the shared workflow additionally accepts the
  `## [1.2.3]` heading convention the rest of the packages use.

## v2.3.1 - 2026-07-25

### Fixed

- `release.yml` now opens the GitHub Release for the tag it publishes. It only
  pushed to npm, so `docs/package-guidelines.md` shipped a rule — every tag gets
  a Release — that this repository was itself keeping by hand, and had already
  missed once on v2.0.0. The notes are the tag's own `CHANGELOG.md` section, so
  the two cannot drift, and a tag whose version has no entry now fails the
  release instead of shipping undocumented.

## v2.3.0 - 2026-07-25

### Added

- **`docs/package-guidelines.md`** — how a `scrapkit/*` package is versioned and
  released. `architecture-guidelines.md` already said *whether* to create a
  package, and its third criterion asks that "someone owns its versioning and
  changelog"; nothing said what owning it looks like. This is that document:
  what counts as a breaking change, why `0.x` is a waiting room rather than a
  destination, tagging and release mechanics (the tag carries its own changelog,
  and is never left off `main`), Keep a Changelog expectations, deprecating
  before removing, publish tags and config keys as public API, and `^`
  constraints in consumers — `@v1` rather than `@main` for the reusable
  workflows. Each rule cites the release where ignoring it actually cost
  something.

### Changed

- `architecture-guidelines.md` links to the new document from the criterion that
  raises it, and `claude/CLAUDE.md` adds it to the docs Claude consults before
  versioning or releasing a package.

## v2.2.0 - 2026-07-25

### Changed

- **The reusable CI workflows are now pinned to `@v1`, not `@main`.**
  `Scrapkit/ci-pipeline` had no tags, so every caller tracked its default
  branch: a single push to `ci-pipeline` reached every consumer's CI at once,
  with no way to hold a known-good version. `ci-pipeline` v1.0.0 introduces the
  `v1` moving major tag, and this release moves the kit's own three workflows
  and `examples/laravel-react/.github/workflows/ci.yml` onto it.

  **Migration:** in your own workflows, replace `@main` with `@v1` on every
  `Scrapkit/ci-pipeline/.github/workflows/*.yml` reference. `v1` keeps moving
  with backward-compatible releases; a breaking change to a workflow's inputs
  will land on `v2`.
- `engineering-kit:install` now says to pin `@v1` in its CI step hint.

## v2.1.0 - 2026-07-25

### Added

- **RFC template and process.** `templates/rfc-template.md` is a one-page,
  ADR-style template (problem, proposal, alternatives considered, consequences,
  open questions) that `engineering-kit:install` writes to
  `docs/rfc/0000-template.md` in every consuming project; `update` keeps it in
  sync and leaves local edits alone, as with every other managed file.
  `docs/rfc-guidelines.md` is the process: the four triggers that make an RFC
  mandatory (new runtime dependency or external service, hard-to-reverse
  architectural change, new `scrapkit/*` package, breaking standards change),
  the `Draft → Review → Accepted/Rejected/Superseded` lifecycle, four-digit
  numbering, and acceptance by team consensus after a minimum three working
  day review window. `docs/pull-request-guidelines.md` and the
  `feature-development` prompt point at the threshold so it surfaces where the
  work actually starts.
- Twelve standards/process documents distilled from the team's reference
  library (Zandstra ×2, *Head First Design Patterns*, Stauffer, Fowler,
  *The Pragmatic Programmer*, *Clean Code*, Feathers, Ousterhout, *Laravel
  Beyond CRUD*, Beck, *Accelerate*), split between `standards/` (how code is
  written) and `processes/` (how the team works). Written in Italian; each
  file closes with a review checklist, and each folder README carries the
  source mapping and a caveat: the content is synthesized from public
  material, not the full books, and team-choice conventions are proposals
  until validated.
- Laravel Boost integration, via Boost's third-party package conventions:
  AI guidelines in `resources/boost/guidelines/core.blade.php` (a condensed
  `claude/CLAUDE.md` plus the key coding/architecture rules, deferring to the
  full docs) and the four prompts as Agent Skills under
  `resources/boost/skills/`. Consumers running `php artisan boost:install`
  (or `boost:update --discover`) get both automatically — nothing to
  configure. `plugins/engineering-kit/skills/` remains the canonical prompt
  source; a Pest test keeps the Boost copies byte-identical.

### Changed

- Skill frontmatter gained a `name:` key and the bodies a "When to use this
  skill" section — required and recommended, respectively, by Boost's Agent
  Skills format, and harmless on the plugin route (Claude Code ignores the
  extra frontmatter).

## v2.0.0 - 2026-07-20

### Changed

- **The Claude Code plugin is now the only route for the prompts.**
  `engineering-kit:install` no longer copies them into `.claude/commands/`;
  instead it enables the plugin project-wide by merging the scrapkit
  marketplace and `engineering-kit@scrapkit` into `.claude/settings.json`
  (non-destructively: only missing keys are added, an explicit opt-out and an
  unparseable file are left alone). `engineering-kit:update` does the same and
  removes the legacy `.claude/commands/` copies — automatically when they
  match the shipped version, with `--force` when they were edited locally.
  **Migration note:** the prompts become namespaced — `/quality-audit` is now
  `/engineering-kit:quality-audit`. The org-wide `claude/CLAUDE.md` import is
  unchanged and stays on the Composer route.
- The release checklist in the README now includes the plugin manifest
  (`plugins/engineering-kit/.claude-plugin/plugin.json`), and `release.yml`
  enforces it: a tag whose version disagrees with either manifest is refused
  before publish. Claude Code delivers plugin updates only on a manifest
  version change, so a release that skipped the bump would never reach plugin
  installs. (#5)
- The README's Updating section covers the plugin route
  (`claude plugin marketplace update scrapkit`), and `docs/ai-guidelines.md`
  points to the prompts and the plugin.
- Installation now documents the VCS `repositories` entry: the Composer package
  is consumed straight from GitHub, since it is not on Packagist (#4 — deferred
  by choice, the repo being public makes the VCS route work for everyone).

### Removed

- `claude/prompts/quality-audit.md` — a stale duplicate of
  `plugins/engineering-kit/skills/quality-audit/SKILL.md` left behind by the
  v1.1.0 move of the prompt sources. Nothing referenced it, and it had already
  diverged from the canonical skill (audit finding TD1). The 2026-07-09 audit
  report that flagged it is now committed under `docs/audits/`.

## v1.1.1 - 2026-07-09

### Fixed

- `npm publish` in the release workflow. It had never succeeded: the step that
  upgraded npm to satisfy trusted publishing (`npm install -g npm@latest`) made
  npm prune its own dependencies mid-upgrade, and `--provenance` then died on a
  missing `sigstore`. Node 24 bundles npm 11.16.0, past the 11.5.1 trusted
  publishing needs, so the upgrade is gone and the runtime supplies it. See #4.

No change to the guidelines, configs, prompts or plugin. `v1.1.0` carries the
same content but was never published to npm, because its tag predates this fix.

## v1.1.0 - 2026-07-09

### Added

- `quality-audit` Claude Code prompt, installed as `.claude/commands/quality-audit.md`.
  Writes a dated report to `docs/audits/`, recording the audited commit in
  front-matter so the next run can skip a codebase that has not changed.
- `templates/quality-audit-workflow.yml` — opt-in monthly schedule that opens a
  pull request with the report. Not copied by `engineering-kit:install`.
- Claude Code plugin. The repository now also serves a marketplace
  (`.claude-plugin/marketplace.json`) publishing the `engineering-kit` plugin,
  so the four prompts can be installed over git — with `/plugin marketplace add
  scrapkit/engineering-kit` — in projects where Composer does not reach. Prompts
  arriving this way are namespaced: `/engineering-kit:quality-audit`.

### Changed

- The prompt sources moved from `claude/prompts/*.md` to
  `plugins/engineering-kit/skills/*/SKILL.md`. Both the plugin and
  `engineering-kit:install` read them from there, so there is a single copy to
  maintain. The files `engineering-kit:install` writes into a project are
  unchanged: still `.claude/commands/{code-review,feature-development,refactoring,quality-audit}.md`.
- `quality-audit` no longer runs unless you invoke it. Claude Code treats
  `.claude/commands/*.md` and a plugin's `SKILL.md` as the same thing, so until
  now Claude could start an audit on its own — one that adds a git worktree and
  writes a report — along either route. `disable-model-invocation: true` in the
  prompt closes both. The other three prompts stay model-invocable: they only
  advise.
- `quality-audit` reports Standards Compliance as `n/a` when neither
  `vendor/scrapkit/engineering-kit/docs/` nor `docs/` is present, instead of
  scoring the repository against guidelines it cannot read. This is the case in
  a repository reached by the plugin but not by Composer.

## v1.0.0 - 2026-07-08

Initial release.

- Guidelines: coding, architecture, AI, pull-request, security.
- Shared configs: PHPStan baseline (level 7), Pint (laravel preset), ESLint
  flat config, Prettier, tsconfig base, Vitest base, Pest arch preset.
- Org-wide Claude Code rules (`claude/CLAUDE.md`) and reusable prompts
  (code review, feature development, refactoring).
- PR/issue templates and commit convention.
- `php artisan engineering-kit:install` / `engineering-kit:update`.
- Laravel + React + TypeScript integration example, wired to the
  `Scrapkit/ci-pipeline` reusable workflows.
