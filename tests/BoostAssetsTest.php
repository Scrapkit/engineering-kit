<?php

use Scrapkit\EngineeringKit\Support\Manifest;

dataset('skills', ['code-review', 'feature-development', 'refactoring', 'quality-audit']);

it('ships the Boost core guidelines', function () {
    $path = Manifest::packagePath('resources/boost/guidelines/core.blade.php');

    expect(file_exists($path))->toBeTrue()
        ->and((string) file_get_contents($path))->toContain('vendor/scrapkit/engineering-kit/docs/');
});

it('keeps verbatim blocks balanced in the Boost guidelines', function () {
    $content = (string) file_get_contents(Manifest::packagePath('resources/boost/guidelines/core.blade.php'));

    // An unbalanced @verbatim would break Boost's Blade render in every consumer.
    expect(substr_count($content, '@verbatim'))->toBe(substr_count($content, '@endverbatim'));
});

/**
 * @return list<string> every file under $directory, relative to it
 */
function filesUnder(string $directory): array
{
    if (! is_dir($directory)) {
        return [];
    }

    $found = [];

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($files as $file) {
        if ($file->isFile()) {
            $found[] = substr($file->getPathname(), strlen($directory) + 1);
        }
    }

    sort($found);

    return $found;
}

it('ships a Boost copy identical to the canonical plugin skill', function (string $skill) {
    // Boost's SkillWriter copies the skill directory whole, so the comparison
    // has to cover references/ too — a missing one there would break the
    // relative paths the skill uses on that route.
    $plugin = Manifest::packagePath(Manifest::PLUGIN_PATH."/skills/{$skill}");
    $boost = Manifest::packagePath(Manifest::BOOST_SKILLS_PATH."/{$skill}");

    expect(is_dir($boost))->toBeTrue("missing Boost copy for skill {$skill}")
        ->and(filesUnder($boost))->toBe(filesUnder($plugin));

    foreach (filesUnder($plugin) as $file) {
        expect(file_get_contents("{$boost}/{$file}"))
            ->toBe(file_get_contents("{$plugin}/{$file}"), "Boost copy of {$skill}/{$file} is out of sync");
    }
})->with('skills');

it('bundles every guideline a skill cites', function (string $skill) {
    $documents = Manifest::skillReferences()[$skill];

    if ($documents === []) {
        expect(is_dir(Manifest::packagePath(Manifest::PLUGIN_PATH."/skills/{$skill}/references")))
            ->toBeFalse("skill {$skill} cites no guideline but ships a references/ directory");

        return;
    }

    foreach ($documents as $document) {
        $bundled = Manifest::packagePath(Manifest::PLUGIN_PATH."/skills/{$skill}/references/{$document}");

        expect(file_exists($bundled))->toBeTrue("skill {$skill} is missing references/{$document}")
            ->and(file_get_contents($bundled))
            ->toBe(file_get_contents(Manifest::packagePath("docs/{$document}")), "references/{$document} has drifted from docs/{$document}");
    }
})->with('skills');

it('cites only paths the plugin route can resolve', function (string $skill) {
    $content = (string) file_get_contents(Manifest::packagePath(Manifest::PLUGIN_PATH."/skills/{$skill}/SKILL.md"));

    // A vendor/ path resolves only where Composer installed the package; the
    // plugin cache holds nothing but plugins/engineering-kit/.
    expect($content)->not->toContain('vendor/scrapkit/engineering-kit');

    foreach (Manifest::skillReferences()[$skill] as $document) {
        expect($content)->toContain("references/{$document}");
    }
})->with('skills');

it('regenerates every copy from a canonical source that exists', function () {
    foreach (Manifest::generatedCopies() as $target => $source) {
        expect(file_exists(Manifest::packagePath($source)))->toBeTrue("missing canonical source {$source}")
            ->and(file_get_contents(Manifest::packagePath($target)))
            ->toBe(file_get_contents(Manifest::packagePath($source)), "{$target} has drifted from {$source}");
    }
});

it('lists the same skills in both locations', function () {
    $names = function (string $dir): array {
        $paths = glob(Manifest::packagePath($dir).'/*/SKILL.md') ?: [];
        $names = array_map(fn (string $path): string => basename(dirname($path)), $paths);
        sort($names);

        return $names;
    };

    expect($names('resources/boost/skills'))->toBe($names('plugins/engineering-kit/skills'));
});

it('gives every skill the frontmatter Boost requires', function (string $skill) {
    $content = (string) file_get_contents(Manifest::packagePath("plugins/engineering-kit/skills/{$skill}/SKILL.md"));

    expect($content)->toStartWith("---\n");

    preg_match('/\A---\n(.*?)\n---\n/s', $content, $matches);
    $frontmatter = $matches[1] ?? '';

    expect($frontmatter)->toMatch("/^name: {$skill}\$/m")
        ->toMatch('/^description: \S/m');
})->with('skills');
