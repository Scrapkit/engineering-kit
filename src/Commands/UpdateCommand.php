<?php

namespace Scrapkit\EngineeringKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Scrapkit\EngineeringKit\Commands\Concerns\ManagesKitFiles;
use Scrapkit\EngineeringKit\Support\Manifest;

class UpdateCommand extends Command
{
    use ManagesKitFiles;

    public $signature = 'engineering-kit:update {--force : Overwrite files that differ from the package version}';

    public $description = 'Sync the managed engineering-kit files with the installed package version';

    public function handle(Filesystem $files): int
    {
        $skipped = [];

        foreach (Manifest::copies() as $target => $source) {
            if (! $files->exists(base_path($target))) {
                $this->writeManagedFile($files, $target, $source);
                $this->components->twoColumnDetail($target, '<fg=green>created</>');

                continue;
            }

            if ($files->get(base_path($target)) === $files->get($source)) {
                $this->components->twoColumnDetail($target, '<fg=gray>up to date</>');

                continue;
            }

            if ($this->option('force')) {
                $this->writeManagedFile($files, $target, $source);
                $this->components->twoColumnDetail($target, '<fg=green>overwritten</>');

                continue;
            }

            $skipped[] = $target;
            $this->components->twoColumnDetail($target, '<fg=yellow>differs from package (kept local version)</>');
        }

        $this->components->twoColumnDetail('CLAUDE.md', match ($this->ensureClaudeImport($files)) {
            'created' => '<fg=green>created with org-wide import</>',
            'updated' => '<fg=green>org-wide import prepended</>',
            default => '<fg=gray>import already present</>',
        });

        if ($skipped !== []) {
            $this->newLine();
            $this->components->warn('Some files differ from the package version (local changes were kept).');
            $this->line('Review them and re-run with --force to overwrite: '.implode(', ', $skipped));
        }

        return self::SUCCESS;
    }
}
