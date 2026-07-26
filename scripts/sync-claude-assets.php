<?php

/**
 * Regenerates the Claude assets that have to live inside the plugin subtree.
 *
 * Claude Code materialises only `plugins/engineering-kit/` when the plugin is
 * installed, and Laravel Boost copies a skill directory whole. So the
 * guidelines a skill cites, and the org-wide rules the SessionStart hook
 * injects, are copied next to what reads them. `docs/` and `claude/` stay the
 * canonical, human-facing sources; everything this script writes is generated
 * and committed.
 *
 * Usage: composer sync-claude-assets
 */

use Scrapkit\EngineeringKit\Support\Manifest;

require_once __DIR__.'/../vendor/autoload.php';

$root = dirname(__DIR__);

/** @var array<string, true> $expected Generated paths, relative to the root. */
$expected = [];

/** @var list<string> $changed */
$changed = [];

/** @var list<string> $pruned */
$pruned = [];

$fail = function (string $message): never {
    fwrite(STDERR, $message.PHP_EOL);

    exit(1);
};

$put = function (string $relative, string $content) use (&$expected, &$changed, $root, $fail): void {
    $expected[$relative] = true;
    $path = $root.'/'.$relative;

    if (is_file($path) && file_get_contents($path) === $content) {
        return;
    }

    if (! is_dir(dirname($path)) && ! mkdir(dirname($path), 0755, true)) {
        $fail('Failed to create directory: '.dirname($relative));
    }

    if (file_put_contents($path, $content) === false) {
        $fail('Failed to write: '.$relative);
    }

    $changed[] = $relative;
};

/**
 * @return list<string> every file under $relative, relative to the root
 */
$filesIn = function (string $relative) use ($root): array {
    $path = $root.'/'.$relative;

    if (! is_dir($path)) {
        return [];
    }

    $found = [];

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($files as $file) {
        if ($file->isFile()) {
            $found[] = substr($file->getPathname(), strlen($root) + 1);
        }
    }

    sort($found);

    return $found;
};

foreach (Manifest::generatedCopies() as $target => $source) {
    if (! is_file($root.'/'.$source)) {
        $fail("Missing canonical source: {$source} (needed by {$target})");
    }

    $put($target, (string) file_get_contents($root.'/'.$source));
}

// The Boost copies mirror the plugin skills verbatim, references included:
// Boost's SkillWriter copies the directory tree, so the relative paths the
// skills use resolve on that route too.
$skills = Manifest::PLUGIN_PATH.'/skills';

foreach ($filesIn($skills) as $file) {
    $put(Manifest::BOOST_SKILLS_PATH.substr($file, strlen($skills)), (string) file_get_contents($root.'/'.$file));
}

// A document dropped from the map, or a renamed skill, must not survive as a
// stale copy — the generated trees hold nothing but what was just written.
$generatedTrees = [Manifest::PLUGIN_PATH.'/claude', Manifest::BOOST_SKILLS_PATH];

foreach (glob($root.'/'.Manifest::PLUGIN_PATH.'/skills/*/references') ?: [] as $references) {
    $generatedTrees[] = substr($references, strlen($root) + 1);
}

foreach ($generatedTrees as $tree) {
    foreach ($filesIn($tree) as $file) {
        if (isset($expected[$file])) {
            continue;
        }

        unlink($root.'/'.$file);
        $pruned[] = $file;
    }
}

// Directories left empty by pruning would otherwise linger untracked.
foreach ($generatedTrees as $tree) {
    if (! is_dir($root.'/'.$tree)) {
        continue;
    }

    $directories = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root.'/'.$tree, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($directories as $directory) {
        if ($directory->isDir() && (scandir($directory->getPathname()) ?: []) === ['.', '..']) {
            rmdir($directory->getPathname());
        }
    }

    if ((scandir($root.'/'.$tree) ?: []) === ['.', '..']) {
        rmdir($root.'/'.$tree);
    }
}

if ($changed === [] && $pruned === []) {
    echo 'Claude assets already in sync.'.PHP_EOL;

    exit(0);
}

foreach ($changed as $path) {
    echo '  wrote   '.$path.PHP_EOL;
}

foreach ($pruned as $path) {
    echo '  pruned  '.$path.PHP_EOL;
}
