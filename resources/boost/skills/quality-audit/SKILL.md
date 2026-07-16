---
name: quality-audit
description: Full-repository quality audit, written to docs/audits/ and comparable across runs
disable-model-invocation: true
---

Produce a full quality audit of this repository: a snapshot of code health at a
precise commit, repeatable and comparable against previous audits.

## When to use this skill

Only when explicitly asked to produce a repository quality audit — never
self-invoked (the audit writes a report to `docs/audits/`, hence
`disable-model-invocation: true`).

## Step 1 — Select the branch

Always audit the `staging` branch: it is the project's integration baseline.

If `staging` does not exist, use `main`. If `main` does not exist either, use
the repository's default branch and state that explicitly in the Executive
Summary.

Resolve each candidate locally first, then on the remote — in a CI checkout the
branch often exists only as `origin/<name>`. Take the first that resolves:

```bash
git rev-parse --verify --quiet staging || git rev-parse --verify --quiet origin/staging
git rev-parse --verify --quiet main    || git rev-parse --verify --quiet origin/main
git symbolic-ref --short --quiet refs/remotes/origin/HEAD || git branch --show-current
```

`--quiet` keeps a missing ref from printing an error; an empty output means the
candidate does not exist, so move to the next line. The last line covers the
repository without a remote, or with `origin/HEAD` unset.

## Step 2 — Check out the branch in a worktree

The analysis reads files, not refs: `find`, `grep`, and every finding that
cites a file and a line come from the working tree. Auditing the selected
branch therefore requires that branch on disk. Do not `git checkout` — it
would move the user's working tree and fails on a dirty one. Use a detached
worktree:

```bash
git worktree add --detach <tmpdir>/quality-audit <branch>
```

Run the whole audit from `<tmpdir>/quality-audit`, then remove it:

```bash
git worktree remove --force <tmpdir>/quality-audit
```

Write the audit file to `docs/audits/` in the **original** working tree, not in
the worktree — the worktree is deleted.

## Step 3 — Establish the baseline

Run these commands and use their real output. Never guess the date or a SHA.

```bash
date +%F                                   # audit date
git rev-parse <branch>                     # commit under audit
TZ=UTC git show -s --format=%cd --date=format-local:'%Y-%m-%d %H:%M UTC' <branch>   # commit date
ls docs/audits/quality-audit-*.md 2>/dev/null | sort | tail -1   # previous audit
```

Before starting the analysis, verify and report in the audit: the branch
analysed, the commit SHA analysed, and the commit date.

If a previous audit exists, read its `branch:` and `commit:` front-matter
fields. Its `commit:` is the baseline **only if its `branch:` matches the
branch selected in Step 1** — comparing across branches would report the whole
divergence between the two as change since the baseline. On a mismatch, or when
the previous audit predates the `branch:` field, treat the baseline as `none`,
run the full audit, and say so in the Executive Summary.

With a valid baseline, check whether any code changed since:

```bash
git diff --quiet <baseline-sha> <branch> -- . \
  ':(exclude)docs/audits' ':(exclude)vendor' ':(exclude)node_modules' \
  ':(exclude)storage' ':(exclude)bootstrap/cache' ':(exclude)public/build' \
  ':(exclude)dist' ':(exclude)coverage'
```

`docs/audits/` is excluded because the previous audit's own commit would
otherwise always register as a change.

**If that command exits 0 (no changes), stop the analysis.** Write only this
file and finish — do not analyse, do not re-score:

```markdown
---
branch: <branch>
commit: <current-sha>
baseline: <baseline-sha>
status: unchanged
---

# Quality Audit — [repo name]
Date: <audit-date>
Branch: <branch>
Commit: <current-sha>
Commit date: <commit-date> UTC
Previous audit: <previous-audit-date>

No code changed between `<baseline-sha>` and `<current-sha>` outside the
excluded paths. The findings and scores of
[quality-audit-YYYY-MM-DD.md](./quality-audit-YYYY-MM-DD.md) remain valid.
```

Otherwise continue with the full audit below.

## Step 4 — Scope

Exclude from analysis: `vendor/`, `node_modules/`, `storage/`,
`bootstrap/cache/`, `public/build/`, `dist/`, `coverage/`.

## Step 5 — Count, do not estimate

Every number in Quantitative Metrics comes from a command you actually ran
(`find`, `grep -c`, `cloc` if available), never from an impression of the
codebase. If a number cannot be obtained, write `n/a` rather than a guess.

## Binding rules

- Report nothing you cannot demonstrate by reading code in this repository.
  No assumptions about infrastructure, databases, external configuration, or
  functional requirements that are not present in the code.
- Classify every finding as **Verified Finding** (demonstrable from the code),
  **Suggestion** (possible improvement, not a defect), or **Unconfirmed** (a
  signal, not a confirmation). When unsure, use Unconfirmed — never omit, never
  overstate.
- Each problem appears exactly once, in its most appropriate section. Other
  sections may refer to it by name.
- Do not modify source code. The audit file is the only write.

## Scores

Score each axis 1-10:

```
9-10  excellent, no significant problems
7-8   good, some improvements advised
5-6   adequate, relevant problems present
3-4   low quality, refactoring needed
1-2   critical
```

`Overall` is the arithmetic mean of the six axes, unweighted, to one decimal.
Nothing else: a weighting invented per run would make two audits incomparable.

Scores are an indicative reading of a single run, not a measurement: do not
compute deltas against the previous audit and do not narrate a score as having
"improved" or "worsened". The comparable signals across audits are the Quality
Gates and the Quantitative Metrics — both anchored to facts. Derive the
Engineering Health trend from those, and name the specific code change that
justifies it.

## Analyse

1. **Architecture** — folder structure, patterns, consistency with Laravel conventions
2. **Code quality** — readability, duplication, complexity, naming
3. **Security** — input validation, injection, permissions/auth, exposed secrets
4. **Performance** — N+1 queries, missing indexes, eager loading (read migrations
   and models; do not speculate about the running database)
5. **Testing** — coverage, missing tests on critical logic
6. **Technical debt** — TODOs, dead code, outdated dependencies
7. **Standards compliance** — against `vendor/scrapkit/engineering-kit/docs/`
   (`coding-guidelines.md`, `architecture-guidelines.md`, `security-guidelines.md`),
   or against `docs/` when auditing engineering-kit itself. When neither exists
   the standards are unreachable — the audit reached this repository without the
   package. Write `n/a` in the Standards Compliance section, say so in the
   Executive Summary, and stop there: an invented standard would produce
   findings that no document in the repository supports.

For every finding give: file and line; Category (Bug / Security / Performance /
Maintainability / Style / Best Practice / Opinion); Classification; Impact
(High / Medium / Low); Effort (<30 min / 1-4 h / 1-2 d / >2 d); why it is a
problem; a short suggested fix — do not rewrite the code.

Within each section, order findings by Impact, then Effort, then file.

## Output

Write to `docs/audits/quality-audit-YYYY-MM-DD.md` using the date from
`date +%F`. Create `docs/audits/` if absent.

```markdown
---
branch: <branch>
commit: <current-sha>
baseline: <baseline-sha or none>
status: audited
---

# Quality Audit — [repo name]
Date: <audit-date>
Branch: <branch>
Commit: <current-sha>
Commit date: <commit-date> UTC
Previous audit: <previous-audit-date> or "none"

## Executive Summary
(5-10 lines. State here, when applicable: that the repository's default branch
was audited because neither `staging` nor `main` exists, naming it; that the
previous audit was discarded as baseline because it ran on another branch)

## Engineering Health
Trend: ↑ Improved / → Unchanged / ↓ Worsened
(justified by gates and metrics, citing the code change responsible)

Quality Gates
✓/✗ No exposed secrets
✓/✗ No evident vulnerabilities
✓/✗ No file of excessive complexity
✓/✗ Tests present on critical logic
✓/✗ Laravel conventions respected (n/a when the repository is not a Laravel
    application — e.g. engineering-kit itself)
✓/✗ No critical TODO present

Final state: 🟢 Healthy / 🟡 Attention / 🔴 Critical

## Quality Score
Architecture      X/10
Code              X/10
Testing           X/10
Performance       X/10
Security          X/10
Maintainability   X/10
Overall: X.X/10

## Findings Summary
Critical problems, medium problems, minor problems, TODOs found, dead code.
Counted from the findings below.

## Quantitative Metrics
Files analysed, classes, controllers, services, models, migrations, tests,
lines of code, % of files with tests. Each from a command.

## Critical Issues
(table with the fields above, ordered by Impact → Effort → file)

## Architecture
## Code Quality
## Security
## Performance
## Testing
## Technical Debt
## Standards Compliance

## Positive Findings
(max 10 — what is done well, and why)

## Opinionated Suggestions
Architectural or stylistic improvements reflecting preference, not objective
defects. Does not affect the Quality Score.

## Priority Roadmap
### 1. Quick Wins (Effort <30 min / 1-4 h, Impact medium-high)
### 2. High Impact (High impact, regardless of effort)
### 3. Long-term Refactoring (Effort 1-2 d / >2 d)
```
