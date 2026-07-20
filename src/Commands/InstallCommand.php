<?php

namespace Scrapkit\EngineeringKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Scrapkit\EngineeringKit\Commands\Concerns\ManagesKitFiles;
use Scrapkit\EngineeringKit\Support\Manifest;

class InstallCommand extends Command
{
    use ManagesKitFiles;

    public $signature = 'engineering-kit:install';

    public $description = 'Install the Scrapkit engineering standards into this project (configs, templates, Claude Code guidelines)';

    public function handle(Filesystem $files): int
    {
        foreach (Manifest::copies() as $target => $source) {
            if ($files->exists(base_path($target))) {
                $this->components->twoColumnDetail($target, '<fg=yellow>skipped (already exists)</>');

                continue;
            }

            $this->writeManagedFile($files, $target, $source);
            $this->components->twoColumnDetail($target, '<fg=green>created</>');
        }

        $this->components->twoColumnDetail('CLAUDE.md', match ($this->ensureClaudeImport($files)) {
            'created' => '<fg=green>created with org-wide import</>',
            'updated' => '<fg=green>org-wide import prepended</>',
            default => '<fg=gray>import already present</>',
        });

        $this->components->twoColumnDetail('.claude/settings.json', match ($this->ensurePluginEnabled($files)) {
            'created' => '<fg=green>created (engineering-kit plugin enabled)</>',
            'updated' => '<fg=green>engineering-kit plugin enabled</>',
            'invalid' => '<fg=yellow>skipped (not valid JSON, enable the plugin manually)</>',
            default => '<fg=gray>plugin already configured</>',
        });

        $this->newLine();
        $this->components->info('Engineering kit installed.');
        $this->line('Next steps for the JavaScript side:');
        $this->line('  1. npm install --save-dev @scrapkit/engineering-kit');
        $this->line('  2. Extend the shared configs (see vendor/scrapkit/engineering-kit/examples/laravel-react/):');
        $this->line('     - eslint.config.js   -> import from @scrapkit/engineering-kit/eslint');
        $this->line('     - prettier.config.js -> import from @scrapkit/engineering-kit/prettier');
        $this->line('     - tsconfig.json      -> "extends": "@scrapkit/engineering-kit/tsconfig.base.json"');
        $this->line('  3. Wire CI using the reusable workflows from Scrapkit/ci-pipeline.');

        return self::SUCCESS;
    }
}
