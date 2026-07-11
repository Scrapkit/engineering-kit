# Changelog

All notable changes to `scrapkit/engineering-kit` / `@scrapkit/engineering-kit`
are documented here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and the project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Changed

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
