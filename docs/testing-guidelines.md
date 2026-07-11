# Testing Guidelines

What to test and how. The shared configs in `configs/testing/` provide the
bases (Pest arch preset, Vitest base config); this document covers what the
tools can't check.

## What gets a test

- Every behavior change ships with a test — Pest for PHP, Vitest for
  TypeScript. A change without a test needs a stated reason (the same hard
  rule Claude follows via `claude/CLAUDE.md`).
- Test behavior through public entry points, not implementation: assert what
  a caller observes (response, state, side effect), not which methods were
  called along the way.
- Bug fixes start with a failing test that reproduces the bug.

## PHP / Pest

- **Feature tests first**: hit the route, command, or job and assert the
  observable result. Reach for Unit tests when a class carries enough logic
  to be worth isolating (services, actions, value objects).
- Descriptions state behavior, not method names:
  `it('suspends the user after three failed attempts')`.
- Model factories over hand-built state; `RefreshDatabase` over manual
  cleanup.
- Include the shared arch preset in `tests/ArchTest.php` —
  `scrapkit_arch_preset()` from `configs/testing/pest.php` (no debug
  functions, Pest `php()` and `security()` presets).

## TypeScript / Vitest

- Extend the shared base with `mergeConfig` (see
  `configs/testing/vitest.config.ts`); jsdom and globals come from the base.
- Test components through Testing Library: query by role, label, or text —
  what the user perceives — not by CSS selectors or component internals.
- Mock at the boundary (network, browser APIs, time); don't mock your own
  modules unless the real one is genuinely unusable in a test.

## Test quality

- A test must fail when the behavior it covers breaks. If you can't make it
  fail, it verifies nothing.
- One behavior per test; shared setup goes in factories and helpers, not
  copy-paste.
- Never weaken or delete a failing test to make a build pass — fix the code,
  or fix the test's wrong assumption and say so in the PR.
- Deterministic: fake time (`Carbon::setTestNow`, `vi.useFakeTimers`), no
  dependence on network, ordering, or leftover state.

## Coverage & CI

- Tests run in CI via the `Scrapkit/ci-pipeline` reusable workflows; a red
  pipeline blocks the merge (see
  [pull-request-guidelines.md](pull-request-guidelines.md)).
- No coverage percentage target: cover the critical paths and the edge cases
  that would hurt in production, and use judgement for the rest. A high
  number earned by asserting nothing is worse than an honest gap.
