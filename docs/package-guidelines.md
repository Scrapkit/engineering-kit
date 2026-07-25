# Package Guidelines

How a `scrapkit/*` package is versioned and released. For *whether* to create
one at all, see [when to create a package](architecture-guidelines.md#when-to-create-a-package)
— this document is the third criterion spelled out.

Every package follows [Semantic Versioning 2.0.0](https://semver.org/spec/v2.0.0.html)
and [Keep a Changelog](https://keepachangelog.com/en/1.1.0/). The version is a
promise to consumers, not a counter.

## What counts as a breaking change

A **MAJOR** is required for any of these, even when the diff looks small:

- A public method signature changes, or a public class, trait, contract, event
  or config key is removed or renamed.
- A default behavior changes — same call, different result.
- A `vendor:publish` tag is renamed (see below).
- The minimum PHP or Laravel version rises in a way that drops a supported version.
- A migration becomes mandatory to keep the package working.

A **MINOR** adds backward-compatible surface: new methods, new config keys with
defaults that preserve current behavior, new commands. A **PATCH** fixes a bug
or refactors internals without touching the public surface.

When in doubt, ask what a consumer's CI would do on `composer update`. If it can
break, it's a MAJOR.

## `0.x` is a waiting room, not a destination

Below `1.0.0` anything may change, and that is fine while the API is still
moving and nothing depends on it. The moment a project consumes the package,
one of two things must be true: either you freeze at `1.0.0`, or you accept that
`^0.x` pins consumers to a single minor and every feature release is a manual
upgrade for them.

Promote deliberately, after reviewing the public surface you are about to
freeze — exposed classes, config keys, contracts, publish tags. Do not promote
by drift, and do not declare `1.0.0` on an API nobody has read: a version that
has to break immediately afterwards is worse than an honest `0.x`.

> Eight packages sat at `0.x` while `laravel-starter-kit` already consumed them.

## Tags and releases

- Tags are always `vX.Y.Z`. The `v` prefix is not optional — Composer accepts
  both forms, but a repository with one odd tag out is a repository where nobody
  can guess the next one.
- Write the CHANGELOG entry **first**, commit it, push, and only then tag
  `origin/main`. The tag must contain its own changelog; `git describe --tags
  origin/main` should print a bare version, not `v1.2.0-3-gabc1234`.
- Never tag a commit that is not reachable from `main`. After a rebase, confirm
  it: `git merge-base --is-ancestor <tag> origin/main`.
- Every tag gets a GitHub Release. The release notes carry the migration
  instructions for a breaking change, so they are visible without opening the repo.

> A rebase left `laravel-permission-hierarchy` v0.2.0 on an orphaned commit. The
> content happened to be identical, but a consumer's `composer.lock` was pinned
> to a SHA unreachable from `main`.

## CHANGELOG

One entry per released version, with its date, under the Keep a Changelog
sections (Added / Changed / Deprecated / Removed / Fixed / Security). A released
version with no entry is a defect, not an omission.

Breaking changes carry migration instructions **inline** — the old value and the
new one, and the command to run. A changelog that says "renamed X" without
saying what to do about it has not documented the break.

> Three packages were released with a three-line stub changelog and no entries.

Do not wire spatie's `update-changelog.yml` workflow. It prepends the release
body into `CHANGELOG.md` when a release is published, which duplicates the entry
you wrote by hand and stamps it with the release date instead of the real one.

## Deprecate before removing

Anything removed in a MAJOR must have been deprecated in a MINOR first: marked
`@deprecated` with the replacement named, and listed under `Deprecated` in the
changelog. A removal with no prior warning is a breaking change consumers had no
way to prepare for, even when the MAJOR is correct.

## Publish tags, config keys and migrations are public API

`spatie/laravel-package-tools` derives publish tags from `shortName()`, which is
the package name with a leading `laravel-` stripped. `->name('laravel-foo-bar')`
publishes under `foo-bar-config`, `foo-bar-translations`, and so on. Any manual
`publishes()` call must use the same short name, or the package ends up with two
naming schemes.

Renaming a publish tag is a breaking change **that fails silently**: Artisan does
not error on an unknown tag, it simply publishes nothing. A consumer's deploy
step keeps exiting zero while quietly shipping nothing.

> `laravel-localization-i18n` v0.1.1 renamed two publish tags and shipped as a
> PATCH — the exact bump `^0.1` upgrades into automatically. Re-released as 0.2.0.

## Consumers pin with `^`

- Use `^1.0`. Never `dev-main`, never `*`. A branch constraint gives up every
  guarantee this document exists to provide, and `*` silently accepts the next
  major.
- After any tag is moved or deleted, consumers need `composer clear-cache`
  before `composer update` — a populated cache will not notice a rewritten tag.
- Reusable GitHub Actions workflows follow the same rule with the Actions
  convention: pin the moving major tag, `@v1`, never `@main`. Pinning a branch
  means every push to the shared pipeline reaches every consumer's CI at once.

> `file-processing-kit` was consumed as `dev-main`; `Scrapkit/ci-pipeline` was
> referenced as `@main` from every repository that used it.
