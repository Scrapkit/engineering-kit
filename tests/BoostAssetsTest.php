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

it('ships a Boost copy identical to the canonical plugin skill', function (string $skill) {
    $boost = Manifest::packagePath("resources/boost/skills/{$skill}/SKILL.md");

    expect(file_exists($boost))->toBeTrue("missing Boost copy for skill {$skill}")
        ->and(file_get_contents($boost))
        ->toBe(file_get_contents(Manifest::packagePath("plugins/engineering-kit/skills/{$skill}/SKILL.md")));
})->with('skills');

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
