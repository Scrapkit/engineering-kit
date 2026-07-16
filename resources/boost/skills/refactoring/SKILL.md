---
name: refactoring
description: Refactor code safely following the Scrapkit engineering standards
---

Refactor the following: $ARGUMENTS

## When to use this skill

When restructuring existing code without changing its behavior — renames,
moves, extractions, or signature changes in a Scrapkit project.

Process:

1. **Map the impact first.** Find every caller and usage of the code being
   refactored (including tests, config references, and queued/serialized
   usages). List public APIs involved.
2. **Protect behavior.** Ensure tests cover the current behavior before
   changing it; add characterization tests if coverage is missing.
3. **Preserve contracts.** Do not change public APIs (package classes, HTTP
   responses, events, DB schema) unless the task explicitly requires it. When
   a break is unavoidable, keep a backward-compatible path or state clearly
   that the change is breaking (major release).
4. **Small steps.** Refactor incrementally, keeping the test suite green at
   each step; commit-sized moves, not big-bang rewrites.
5. **Verify.** Run the full affected test suite, PHPStan/ESLint, and the
   formatters before declaring the refactor done.
