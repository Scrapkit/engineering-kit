<?php

use Illuminate\Filesystem\Filesystem;
use Scrapkit\EngineeringKit\Support\Manifest;
use Scrapkit\EngineeringKit\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Remove every file the kit commands manage from the Testbench skeleton so
 * each test starts from a pristine application.
 */
function cleanupKitFiles(): void
{
    $files = new Filesystem;

    $targets = array_keys(Manifest::copies());
    $targets[] = 'CLAUDE.md';

    foreach ($targets as $target) {
        $files->delete(base_path($target));
    }

    foreach (['.claude/commands', '.claude', '.github/ISSUE_TEMPLATE', '.github'] as $dir) {
        $path = base_path($dir);

        if ($files->isDirectory($path) && $files->files($path) === [] && $files->directories($path) === []) {
            $files->deleteDirectory($path);
        }
    }
}
