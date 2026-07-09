# AI Guidelines

How we use AI assistants (Claude Code and others) in development. The
Claude-specific operating rules live in [`claude/CLAUDE.md`](../claude/CLAUDE.md)
and are imported into every project; this document covers the human side.

## Ownership

- **You own what you ship.** AI-generated code is reviewed, understood, and
  tested by the developer who commits it, to the same standard as hand-written
  code. "The AI wrote it" is never an explanation in a review or an incident.
- Do not commit code you cannot explain line by line.

## What AI is for

Encouraged: exploration of unfamiliar code, drafting implementations against
existing patterns, writing tests, refactoring with tests green, generating
docs, code review as a *second* pair of eyes.

Requires extra scrutiny: security-sensitive code (auth, payments, crypto),
database migrations on production data, public API design, dependency
choices. AI drafts, a human decides.

## Data rules

- Never paste secrets, tokens, customer data, or production `.env` values
  into a prompt — local tools with repo access read files themselves; they
  don't need credentials inlined in chat.
- Treat prompts like commit messages: assume they may be retained.

## Review of AI-generated changes

Same PR process as any change, plus:

- Verify the change didn't invent APIs — check that called methods/packages
  actually exist at the pinned versions.
- Watch for plausible-but-wrong edge-case handling and for tests that assert
  the implementation rather than the behavior.
- Reject changes that introduce dependencies or patterns foreign to the
  project without justification (the imported CLAUDE.md forbids this; enforce
  it in review too).

## Keeping assistants aligned

- Every project imports the org-wide rules via the `CLAUDE.md` line added by
  `php artisan engineering-kit:install`. Don't remove the import; add
  project-specific instructions *below* it.
- Improvements to the org-wide rules go through a PR to this package, so all
  teams get them on their next `composer update`.
- Four reusable prompts ship with the kit — `code-review`,
  `feature-development`, `refactoring`, `quality-audit` — as slash commands in
  Laravel projects and as a Claude Code plugin everywhere else. Installation
  and the choice between the two routes are covered in the
  [README](../README.md#the-prompts-as-a-claude-code-plugin).
