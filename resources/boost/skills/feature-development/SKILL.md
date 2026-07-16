---
name: feature-development
description: Develop a feature following the Scrapkit engineering standards
---

Implement the following feature: $ARGUMENTS

## When to use this skill

When implementing a new feature or a behavior change in a Scrapkit project —
from the first exploration to the final formatter run.

Process:

1. **Understand before coding.** Explore the code paths the feature touches;
   identify existing services, components, and utilities to reuse. Respect
   the architecture criteria in
   `vendor/scrapkit/engineering-kit/docs/architecture-guidelines.md`
   (when to create a service, when not to add a pattern).
2. **Plan.** List the files to change and the tests that will prove the
   feature works. Confirm the plan if anything is ambiguous. When the feature
   crosses the RFC threshold in
   `vendor/scrapkit/engineering-kit/docs/rfc-guidelines.md` (new runtime
   dependency, hard-to-reverse architectural change, new package), the RFC is
   written and accepted before the implementation starts.
3. **Test-first where practical.** Write the failing test (Pest or Vitest),
   then the implementation, then make it pass.
4. **Stay in scope.** No drive-by refactors, no new dependencies without a
   stated reason.
5. **Finish clean.** Run formatters (Pint, Prettier), static analysis
   (PHPStan, ESLint), and the affected tests. Update docs the change affects.
