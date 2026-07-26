# RFC Guidelines

An RFC is a one-page document in `docs/rfc/` that settles a decision **before**
the code makes it expensive to change. It is not a design doc for every
feature: most changes are decided in the PR that implements them.

`engineering-kit:install` writes the template to `docs/rfc/0000-template.md`.

## When an RFC is required

Any one of these is enough:

1. **A new runtime dependency or external service** — a package that ships to
   production, a third-party API, a new piece of infrastructure.
2. **An architectural change that is hard to reverse** — a new layer, a change
   of storage, a new boundary between modules, a change to a public contract
   (HTTP API, published events).
3. **A new `scrapkit/*` package** — check it against the three criteria in
   [architecture-guidelines.md](architecture-guidelines.md#when-to-create-a-package)
   first; the RFC records the answer.
4. **A breaking change to the shared standards** — anything that fails an
   existing pipeline by default, i.e. a major release of engineering-kit.

## When it is not

Ordinary features, refactors, bugfixes, non-breaking rules and dev-only
dependencies: the *Why* section of the PR is the right place. When in doubt,
open the PR — a reviewer can still ask for an RFC, and that costs less than
writing one nobody needed.

## Lifecycle

| Status | Meaning |
| --- | --- |
| `Draft` | Being written; nobody is expected to read it yet. |
| `Review` | PR open, the team is reading it. |
| `Accepted` | Decided. Implementation can start. |
| `Rejected` | Decided against. The reasoning stays in the repository. |
| `Superseded by NNNN` | A later RFC replaced this decision. |

## Writing one

1. Copy `docs/rfc/0000-template.md` to `docs/rfc/NNNN-short-title.md`, where
   `NNNN` is the next free four-digit number (`0000` is the template and is
   never an RFC).
2. Fill it while the status is `Draft`. One page. An empty *Alternatives
   considered* section means the decision was assumed, not made.
3. Set the status to `Review` and open a PR containing **only** the RFC, titled
   per the [commit convention](../templates/commit-convention.md):
   `docs(rfc): add RFC 0007 — queue-backed exports`.

## Accepting one

An RFC is accepted when **both** hold:

- it has been open for at least **three working days**, so the decision is not
  made before everyone has had a chance to read it; and
- **every engineer on the team has approved the PR**. Silence is not consent.

An unresolved objection keeps the RFC in `Review` until it is addressed or the
author withdraws the proposal. To land it: set the final status, fill
`decided`, merge.

Rejected RFCs are merged too — the next person with the same idea should find
out why it was turned down instead of rediscovering it.

## After acceptance

- Implementation PRs reference it: `Implements RFC 0007`.
- An accepted RFC is not rewritten when reality disagrees with it. Write a new
  RFC and set the old one to `Superseded by NNNN`. Typos and clarifications are
  the only edits after `Accepted`.

## In this repository

engineering-kit does not run its own installer, so the template lives at
[`templates/rfc-template.md`](../templates/rfc-template.md) with no copy under
`docs/rfc/` to drift from it. The directory is created by the first RFC written
here.
