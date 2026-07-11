---
branch: main
commit: 3d71a2062cb0441fe3273b1fa84508ec2fbdea7d
baseline: none
status: audited
---

# Quality Audit — engineering-kit
Date: 2026-07-09
Branch: main
Commit: 3d71a2062cb0441fe3273b1fa84508ec2fbdea7d
Commit date: 2026-07-09 19:07 UTC
Previous audit: none

## Executive Summary
First audit of this repository (no baseline). `staging` does not exist, so `main`
was audited. The package is the Scrapkit standards kit: 5 small PHP classes
(install/update commands plus manifest), shared PHP/JS configs, guideline
documents, a Claude Code plugin with four skills, and CI/release workflows. Code
health is excellent: every source file is under 62 lines, the two artisan
commands are fully covered by 15 Pest tests including idempotency and `--force`
semantics, the release workflow verifies npm version alignment across both
manifests, and publishing uses OIDC trusted publishing with no stored token.
The one concrete debt found is `claude/prompts/quality-audit.md`: an
unreferenced near-duplicate of the plugin skill that has already diverged from
it. Standards Compliance was checked against this repository's own `docs/`, as
the skill prescribes for engineering-kit itself, and the code conforms.

## Engineering Health
Trend: n/a — first audit, no baseline to compare against.

Quality Gates
- ✓ No exposed secrets (npm publishing via OIDC; no tokens anywhere)
- ✓ No evident vulnerabilities
- ✓ No file of excessive complexity (largest source file: 61 lines)
- ✓ Tests present on critical logic (install/update commands, manifest integrity, npm exports)
- n/a Laravel conventions respected (engineering-kit itself, not a Laravel application)
- ✓ No critical TODO present

Final state: 🟢 Healthy

## Quality Score
```
Architecture      9/10
Code              9/10
Testing           8/10
Performance       9/10
Security          9/10
Maintainability   8/10
Overall: 8.7/10
```

## Findings Summary
- Critical problems: 0
- Medium problems: 1 (stale duplicate of the quality-audit prompt)
- Minor problems: 2 (actions pinned by mutable tag; `$signature`/`$description` untyped)
- TODOs found: 0
- Dead code: 1 file (`claude/prompts/quality-audit.md`)

## Quantitative Metrics
| Metric | Value | Source |
|---|---|---|
| Files (excl. .git) | 59 | `find . -type f` |
| PHP source files | 5 (213 lines) | `find src -name '*.php'`, `wc -l` |
| Classes / traits | 4 classes + 1 trait | file inspection |
| Artisan commands | 2 | `src/Commands/` |
| Controllers / models / migrations | 0 | package has none |
| Test files / test cases | 6 / 15 | `grep -c '^it(\|^arch('` |
| Plugin skills | 4 | `plugins/engineering-kit/skills/` |
| Guideline documents | 5 (334 lines) | `wc -l docs/*.md` |
| CI workflows | 5 | `.github/workflows/` |
| TODO/FIXME/HACK | 0 | `grep -rniE` |

## Critical Issues
None.

## Architecture
Clean and idiomatic for a Laravel package: `spatie/laravel-package-tools`
service provider, commands under `src/Commands/` with shared behavior in a
`Concerns/` trait, a single `Manifest` class as the source of truth for managed
files. The manifest sources the `.claude/commands/*` copies from the plugin
skills, deliberately keeping one copy of each prompt — see TD1 for the one
place where that principle is violated.

## Code Quality
**C1 — Command `$signature` / `$description` properties are untyped**
- Files: `src/Commands/InstallCommand.php:14-16`, `src/Commands/UpdateCommand.php:14-16`
- Category: Style — Classification: Suggestion — Impact: Low — Effort: <30 min
- Why: `docs/coding-guidelines.md` asks for explicit types everywhere; `public $signature` works (parent declares it) but `protected $signature = '...'` with the modern typed alternative (`protected string $signature`) would match the guideline the package itself ships.
- Fix: declare the properties `protected` with types, or leave as-is if matching the Laravel skeleton is preferred — note the tension in the guideline instead.

No duplication, no complexity: the largest class is 61 lines and every method
has explicit parameter and return types.

## Security
Nothing to report in the PHP code: the commands only write inside
`base_path()` with targets fixed by `Manifest::copies()` — no user input
reaches a path.

**S1 — Third-party actions referenced by mutable tags**
- Files: `.github/workflows/release.yml:24-25`, `js-configs.yml`
- Category: Best Practice — Classification: Suggestion — Impact: Low — Effort: <30 min
- Why: `actions/checkout@v4` and `actions/setup-node@v4` are mutable refs; the release job holds `id-token: write`, so a compromised upstream action could mint a publishing token.
- Fix: pin to commit SHAs and let Dependabot bump them.

Positive: the release workflow verifies npm ≥ 11.5.1 before trusted publishing
and checks the tag against **both** `package.json` and the plugin manifest,
preventing the silently-stranded-plugin failure mode its comment describes.

## Performance
n/a in practice: the package performs a handful of small file copies at
install time; no queries, no runtime hot path.

## Testing
Coverage of the critical logic is strong: install creation/skip/idempotency,
update create/keep/force/restore-import, manifest-source existence, JSON
validity of shipped configs, npm exports pointing at real files, and an arch
test banning debug functions. Gap worth closing:

**T1 — `ensureClaudeImport` edge not covered: import present but not at the top**
- File: `tests/InstallCommandTest.php` (absence), behavior in `src/Commands/Concerns/ManagesKitFiles.php:33-38`
- Category: Best Practice — Classification: Suggestion — Impact: Low — Effort: <30 min
- Why: the trait treats a file *containing* the import anywhere as up-to-date; no test pins that behavior, so a refactor to "must be first line" (or the opposite) would pass the suite silently.
- Fix: add a test with the import mid-file asserting the file is left untouched.

## Technical Debt
**TD1 — `claude/prompts/quality-audit.md` is a stale, unreferenced duplicate of the plugin skill**
- File: `claude/prompts/quality-audit.md:1-253`
- Category: Maintainability — Classification: Verified Finding — Impact: Medium — Effort: <30 min
- Why: nothing in the repository references `claude/prompts/` (verified by grep); `Manifest::copies()` and the plugin both use `plugins/engineering-kit/skills/quality-audit/SKILL.md`, and the two files have already diverged (the copy lacks the `disable-model-invocation` frontmatter and the newer "standards unreachable" paragraph). This contradicts the manifest's own "one copy to maintain" comment.
- Fix: delete `claude/prompts/quality-audit.md` (and the now-empty `claude/prompts/`).

## Standards Compliance
Checked against this repository's own `docs/` (`coding-guidelines.md`,
`architecture-guidelines.md`, `security-guidelines.md`), as the audit
prescription requires for engineering-kit itself.

- Explicit parameter/return types: compliant (see C1 for the one soft spot).
- No `dd`/`dump`/`ray`: compliant, enforced by `tests/ArchTest.php`.
- Artisan command naming `domain:action`: compliant (`engineering-kit:install`, `engineering-kit:update`).
- No secrets in code: compliant.
- Pint + PHPStan wired in CI via the shared ci-pipeline workflows: compliant.

## Positive Findings
1. Single source of truth (`Manifest::copies()`) drives install, update, and the manifest-integrity test — adding a managed file is a one-line change tested for free.
2. Release workflow cross-checks the git tag against both npm and plugin manifests, with the failure mode documented in place.
3. npm trusted publishing (OIDC) — no long-lived token to leak.
4. `ShippedConfigsTest` prevents publishing a package whose exports point at missing files.
5. Update command is conservative by default (keeps local changes, lists them, requires `--force`), and tests pin all three paths.
6. Non-obvious decisions are documented where they live (npm version check rationale, why Node 24, why both manifests).
7. Test suite resets the Testbench skeleton in `beforeEach`/`afterEach`, keeping tests order-independent.
8. Guidelines are concise (334 lines across 5 docs) and split by concern, with cross-references instead of repetition.

## Opinionated Suggestions
- `ensureClaudeImport` prepends the import when missing but tolerates it anywhere in the file; consider normalizing to "first line" so consuming projects stay uniform.
- The four `.claude/commands/*.md` copies duplicate what the plugin already delivers to plugin users; a future major could drop the copies and require the plugin, removing a whole sync surface.

## Priority Roadmap
### 1. Quick Wins (Effort <30 min / 1-4 h, Impact medium-high)
1. Delete the stale `claude/prompts/quality-audit.md` (TD1).
2. Pin release-workflow actions to commit SHAs (S1) — small effort, protects the npm publishing credential.

### 2. High Impact (High impact, regardless of effort)
None open beyond the quick wins — no high-impact defect was found.

### 3. Long-term Refactoring (Effort 1-2 d / >2 d)
1. Evaluate collapsing the `.claude/commands` copy mechanism into plugin-only distribution (see Opinionated Suggestions) in the next major.
