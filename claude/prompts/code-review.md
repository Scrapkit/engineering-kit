---
description: Review the current changes against the Scrapkit engineering standards
---

Review the pending changes (working tree diff, or the PR branch if given as
argument: $ARGUMENTS) against the Scrapkit standards.

Process:

1. Read the diff and the surrounding code of every touched file.
2. Evaluate in this order: correctness, security, tests, performance,
   maintainability, standards compliance (Pint/ESLint/PHPStan clean).
   Use `vendor/scrapkit/engineering-kit/docs/pull-request-guidelines.md` and
   `security-guidelines.md` as the checklist.
3. Report findings ranked by severity. For each finding give: file:line, what
   is wrong, a concrete failure scenario, and a suggested fix.
4. Do not report style nits the formatters would catch — run the formatters
   instead.
5. If nothing is wrong, say so plainly; do not invent findings.
