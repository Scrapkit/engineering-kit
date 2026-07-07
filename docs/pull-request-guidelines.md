# Pull Request Guidelines

## Authoring

- **Small PRs.** One logical change; aim for a diff a reviewer can hold in
  their head (~400 lines changed is a soft ceiling — split beyond that).
- Follow the [commit convention](../templates/commit-convention.md); the PR
  title follows the same format (`feat(users): …`) because it becomes the
  squash commit.
- Fill the PR template (installed to `.github/PULL_REQUEST_TEMPLATE.md`):
  what, why, how, checklist. Link the issue.
- CI must be green before requesting review: Pint, PHPStan, ESLint, Prettier,
  Pest/Vitest — all via the `Scrapkit/ci-pipeline` reusable workflows.
- Draft PRs for work-in-progress; ready-for-review means *you* already
  self-reviewed the diff.

## Reviewing

Review for, in order:

1. **Correctness** — does it do what the description claims; edge cases.
2. **Security** — see [security-guidelines.md](security-guidelines.md).
3. **Tests** — behavior covered; tests fail if the feature breaks.
4. **Design** — right altitude per
   [architecture-guidelines.md](architecture-guidelines.md); no premature
   abstraction, no missing one.
5. **Maintainability** — naming, clarity, docs updated.

Don't review style the tools already enforce. Comment with questions and
reasons, not commands ("what happens when X is null?" beats "add a null
check"). Approve when the code is better than what it replaces and safe —
not when it's perfect.

## Merging

- At least one approval; the author merges.
- Squash-merge is the default; the squash message follows the commit
  convention.
- A red pipeline is never merged over, and never "fixed" by weakening a test
  or silencing an analyser rule.
