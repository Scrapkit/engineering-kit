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
            '.claude/commands/code-review.md' => self::packagePath('claude/prompts/code-review.md'),
            '.claude/commands/feature-development.md' => self::packagePath('claude/prompts/feature-development.md'),
            '.claude/commands/refactoring.md' => self::packagePath('claude/prompts/refactoring.md'),
        ];
    }
}
