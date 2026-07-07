<?php

use Scrapkit\EngineeringKit\Support\Manifest;

it('ships every source file referenced by the manifest', function () {
    foreach (Manifest::copies() as $target => $source) {
        expect(file_exists($source))->toBeTrue("missing manifest source for {$target}: {$source}");
    }
});

it('ships valid JSON configs', function (string $path) {
    json_decode((string) file_get_contents(Manifest::packagePath($path)), flags: JSON_THROW_ON_ERROR);

    expect(true)->toBeTrue();
})->with([
    'configs/php/pint.json',
    'configs/javascript/tsconfig.base.json',
    'package.json',
]);

it('exposes every npm export from an existing file', function () {
    $package = json_decode((string) file_get_contents(Manifest::packagePath('package.json')), true, flags: JSON_THROW_ON_ERROR);

    foreach ($package['exports'] as $export => $path) {
        expect(file_exists(Manifest::packagePath($path)))->toBeTrue("npm export {$export} points to a missing file: {$path}");
    }
});

it('keeps the shared phpstan config free of project paths', function () {
    $lines = file(Manifest::packagePath('configs/php/phpstan.neon'), FILE_IGNORE_NEW_LINES) ?: [];

    $withoutComments = array_filter($lines, fn (string $line) => ! str_starts_with(ltrim($line), '#'));

    expect(implode("\n", $withoutComments))->not->toContain('paths:');
});
