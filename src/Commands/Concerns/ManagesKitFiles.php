<?php

namespace Scrapkit\EngineeringKit\Commands\Concerns;

use Illuminate\Filesystem\Filesystem;
use Scrapkit\EngineeringKit\Support\Manifest;

trait ManagesKitFiles
{
    protected function writeManagedFile(Filesystem $files, string $target, string $source): void
    {
        $path = base_path($target);

        $files->ensureDirectoryExists(dirname($path));
        $files->copy($source, $path);
    }

    /**
     * Make sure the project's CLAUDE.md imports the org-wide Claude rules.
     * Creates the file when missing, prepends the import when absent, and
     * leaves the file untouched when the import is already there.
     */
    protected function ensureClaudeImport(Filesystem $files): string
    {
        $path = base_path('CLAUDE.md');

        if (! $files->exists($path)) {
            $files->put($path, Manifest::CLAUDE_IMPORT."\n\n# Project guidelines\n\nProject-specific instructions go here. Org-wide rules are imported above\nfrom scrapkit/engineering-kit.\n");

            return 'created';
        }

        if (str_contains($files->get($path), Manifest::CLAUDE_IMPORT)) {
            return 'up-to-date';
        }

        $files->put($path, Manifest::CLAUDE_IMPORT."\n\n".$files->get($path));

        return 'updated';
    }

    /**
     * Make sure the project's .claude/settings.json enables the
     * engineering-kit plugin from the scrapkit marketplace, so the prompts
     * reach everyone who clones the project through the plugin. Only missing
     * keys are added: existing settings — including a deliberately disabled
     * plugin — are left alone, as is a file that does not parse as JSON.
     */
    protected function ensurePluginEnabled(Filesystem $files): string
    {
        $path = base_path('.claude/settings.json');

        $settings = [];

        if ($files->exists($path)) {
            $settings = json_decode($files->get($path), true);

            if (! is_array($settings)) {
                return 'invalid';
            }
        }

        $marketplaces = $settings['extraKnownMarketplaces'] ?? [];
        $plugins = $settings['enabledPlugins'] ?? [];

        if (! is_array($marketplaces) || ! is_array($plugins)) {
            return 'invalid';
        }

        if (array_key_exists(Manifest::PLUGIN_MARKETPLACE, $marketplaces) && array_key_exists(Manifest::PLUGIN_ID, $plugins)) {
            return 'up-to-date';
        }

        $created = ! $files->exists($path);
        $defaults = Manifest::pluginSettings();

        $settings['extraKnownMarketplaces'] = $marketplaces + $defaults['extraKnownMarketplaces'];
        $settings['enabledPlugins'] = $plugins + $defaults['enabledPlugins'];

        $files->ensureDirectoryExists(dirname($path));
        $files->put($path, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

        return $created ? 'created' : 'updated';
    }
}
