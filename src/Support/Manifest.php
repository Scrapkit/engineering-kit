<?php

namespace Scrapkit\EngineeringKit\Support;

final class Manifest
{
    /**
     * Import line added to the consuming project's CLAUDE.md. Claude Code
     * resolves @path imports, so the org-wide rules always reflect the
     * installed package version without copying content.
     */
    public const CLAUDE_IMPORT = '@vendor/scrapkit/engineering-kit/claude/CLAUDE.md';

    /**
     * Marketplace and plugin enabled in the consuming project's
     * .claude/settings.json — the plugin is the only route for the prompts,
     * so the package enables it for the whole team instead of copying them.
     */
    public const PLUGIN_MARKETPLACE = 'scrapkit';

    public const PLUGIN_ID = 'engineering-kit@scrapkit';

    /**
     * The plugin subtree, relative to the package root. Claude Code
     * materialises only this directory when the plugin is installed, so
     * anything a skill or a hook reads at runtime has to live under it —
     * docs/, standards/ and claude/ never reach a plugin-only install.
     */
    public const PLUGIN_PATH = 'plugins/engineering-kit';

    /**
     * The Boost copies of the skills, kept identical to the plugin ones.
     */
    public const BOOST_SKILLS_PATH = 'resources/boost/skills';

    /**
     * @return array{extraKnownMarketplaces: array<string, mixed>, enabledPlugins: array<string, bool>}
     */
    public static function pluginSettings(): array
    {
        return [
            'extraKnownMarketplaces' => [
                self::PLUGIN_MARKETPLACE => [
                    'source' => ['source' => 'github', 'repo' => 'scrapkit/engineering-kit'],
                ],
            ],
            'enabledPlugins' => [self::PLUGIN_ID => true],
        ];
    }

    public static function packagePath(string $path = ''): string
    {
        return dirname(__DIR__, 2).($path === '' ? '' : '/'.ltrim($path, '/'));
    }

    /**
     * Files managed by engineering-kit:install / engineering-kit:update,
     * as [target relative to the app's base path => absolute source path].
     *
     * @return array<string, string>
     */
    public static function copies(): array
    {
        return [
            'pint.json' => self::packagePath('configs/php/pint.json'),
            'phpstan.neon' => self::packagePath('stubs/phpstan.neon.stub'),
            '.github/PULL_REQUEST_TEMPLATE.md' => self::packagePath('templates/pull-request-template.md'),
            '.github/ISSUE_TEMPLATE/default.md' => self::packagePath('templates/issue-template.md'),
            'docs/rfc/0000-template.md' => self::packagePath('templates/rfc-template.md'),
        ];
    }

    /**
     * Guideline documents each skill cites, as [skill => docs/ filenames].
     * The skills reference them by a path relative to their own directory,
     * so the checklist travels with the prompt on every route — plugin,
     * Boost, or Composer — instead of depending on a vendor/ path that only
     * exists where the package is installed.
     *
     * @return array<string, list<string>>
     */
    public static function skillReferences(): array
    {
        return [
            'code-review' => ['pull-request-guidelines.md', 'security-guidelines.md'],
            'feature-development' => ['architecture-guidelines.md', 'rfc-guidelines.md'],
            'quality-audit' => ['coding-guidelines.md', 'architecture-guidelines.md', 'security-guidelines.md'],
            'refactoring' => [],
        ];
    }

    /**
     * Files generated inside the plugin subtree from a canonical source
     * elsewhere in the repository, as [generated => canonical], both relative
     * to the package root. `composer sync-claude-assets` writes them and a
     * test asserts they are byte-identical to their source.
     *
     * @return array<string, string>
     */
    public static function generatedCopies(): array
    {
        $copies = [self::PLUGIN_PATH.'/claude/CLAUDE.md' => 'claude/CLAUDE.md'];

        foreach (self::skillReferences() as $skill => $documents) {
            foreach ($documents as $document) {
                $copies[self::PLUGIN_PATH."/skills/{$skill}/references/{$document}"] = "docs/{$document}";
            }
        }

        return $copies;
    }

    /**
     * Prompt copies that releases before 2.0 installed into .claude/commands/.
     * The plugin is now the only route for the prompts, so
     * engineering-kit:update removes these when they match the installed
     * version, and with --force when they were edited locally.
     *
     * @return array<string, string>
     */
    public static function legacyCopies(): array
    {
        return [
            '.claude/commands/code-review.md' => self::packagePath('plugins/engineering-kit/skills/code-review/SKILL.md'),
            '.claude/commands/feature-development.md' => self::packagePath('plugins/engineering-kit/skills/feature-development/SKILL.md'),
            '.claude/commands/refactoring.md' => self::packagePath('plugins/engineering-kit/skills/refactoring/SKILL.md'),
            '.claude/commands/quality-audit.md' => self::packagePath('plugins/engineering-kit/skills/quality-audit/SKILL.md'),
        ];
    }
}
