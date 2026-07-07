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
}
