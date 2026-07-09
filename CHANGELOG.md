# Changelog

All notable changes to `scrapkit/engineering-kit` / `@scrapkit/engineering-kit`
are documented here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and the project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

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
