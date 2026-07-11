# Scrapkit Engineering Guidelines

The office conventions: what we expect from every Scrapkit codebase and from
whoever works on it, humans and AI assistants alike. Projects receive this
directory inside `vendor/scrapkit/engineering-kit/docs/`, and Claude reads
the distilled rules through the `claude/CLAUDE.md` import — one source, both
audiences.

## The guidelines

| Document | Covers |
| --- | --- |
| [coding-guidelines.md](coding-guidelines.md) | PHP/Laravel and React/TS standards the tools can't enforce |
| [architecture-guidelines.md](architecture-guidelines.md) | Decision criteria: when to add a pattern, when not to |
| [testing-guidelines.md](testing-guidelines.md) | What gets a test and how, with Pest and Vitest |
| [git-guidelines.md](git-guidelines.md) | Branching model, branch naming, rebase rules, tags |
| [pull-request-guidelines.md](pull-request-guidelines.md) | Authoring, reviewing, and merging PRs |
| [security-guidelines.md](security-guidelines.md) | The minimum security bar; failing it blocks the PR |
| [ai-guidelines.md](ai-guidelines.md) | How we use AI assistants |

The [commit convention](../templates/commit-convention.md) sits in
`templates/` with the other process templates and is linked from the git and
PR guidelines.

## Suggested reading order

For a new engineer: [coding](coding-guidelines.md) →
[architecture](architecture-guidelines.md) → [git](git-guidelines.md) and
[pull-request](pull-request-guidelines.md) before your first PR; then
[testing](testing-guidelines.md), [security](security-guidelines.md), and
[ai](ai-guidelines.md) as you start shipping.

## Audits

`audits/` holds the dated reports produced by the `quality-audit` prompt;
the latest committed report is the baseline the next run compares against.
