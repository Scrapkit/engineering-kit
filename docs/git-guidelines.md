# Git Guidelines

How we use git day to day. Complements the
[pull request guidelines](pull-request-guidelines.md) and the
[commit convention](../templates/commit-convention.md).

## Branching model

- Trunk-based: `main` is the only long-lived branch and is always
  releasable. No `develop`, no environment branches.
- Work happens on short-lived branches cut from `main`; merge within days,
  not weeks. A branch big enough to drift for a sprint is a PR too big to
  review — split the work.
- Releases are tags on `main` (see below), never branches that live on.

## Branch naming

`type/kebab-description`, where the type mirrors the
[commit convention](../templates/commit-convention.md) types:

```
feat/invoice-pdf-export
fix/locale-cookie-fallback
docs/composer-vcs-route
release/v1.2.0
```

Keep the description short and specific: the branch name should tell a
teammate what is in flight without opening the PR.

## Commits

- Follow the [commit convention](../templates/commit-convention.md); the PR
  title uses the same format because it becomes the squash commit.
- Commit working states, small enough to review. Clean up `wip` chains with
  an interactive rebase *before* opening the PR — not after review starts,
  because rewriting reviewed commits discards the reviewer's anchors.

## Keeping a branch current

- Rebase your branch on `main` before requesting review; prefer rebase over
  merge commits so the PR shows only its own changes.
- Force-push only your own branch, only with `--force-with-lease`, and never
  once someone else has commits on it.
- Never rewrite `main` or a pushed tag. A bad release gets a new patch tag,
  not a re-tag.

## Tags & releases

- Releases are SemVer tags `vX.Y.Z` on `main`; one tag releases every
  package this repository ships. Bump rules and the release checklist live
  in the README's Versioning section.
