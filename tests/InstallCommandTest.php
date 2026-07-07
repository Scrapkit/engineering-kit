<?php

use Illuminate\Filesystem\Filesystem;
use Scrapkit\EngineeringKit\Support\Manifest;

beforeEach(fn () => cleanupKitFiles());
afterEach(fn () => cleanupKitFiles());

it('creates every managed file in a pristine project', function () {
    $this->artisan('engineering-kit:install')->assertSuccessful();

    $files = new Filesystem;

    foreach (Manifest::copies() as $target => $source) {
        expect($files->exists(base_path($target)))->toBeTrue("expected {$target} to be created");
    }
});

it('copies the managed files with the package content', function () {
    $this->artisan('engineering-kit:install')->assertSuccessful();

    $files = new Filesystem;

    expect($files->get(base_path('pint.json')))
        ->toBe($files->get(Manifest::packagePath('configs/php/pint.json')));

    expect($files->get(base_path('phpstan.neon')))
        ->toContain('vendor/scrapkit/engineering-kit/configs/php/phpstan.neon');
});

it('creates CLAUDE.md with the org-wide import', function () {
    $this->artisan('engineering-kit:install')->assertSuccessful();

    expect(file_get_contents(base_path('CLAUDE.md')))
        ->toContain(Manifest::CLAUDE_IMPORT);
});

it('prepends the import to an existing CLAUDE.md without touching its content', function () {
    file_put_contents(base_path('CLAUDE.md'), "# My project\n\nLocal rules.\n");

    $this->artisan('engineering-kit:install')->assertSuccessful();

    $content = file_get_contents(base_path('CLAUDE.md'));

    expect($content)
        ->toStartWith(Manifest::CLAUDE_IMPORT)
        ->toContain("# My project\n\nLocal rules.\n");
});

it('does not overwrite existing files', function () {
    file_put_contents(base_path('pint.json'), '{"preset": "psr12"}');

    $this->artisan('engineering-kit:install')->assertSuccessful();

    expect(file_get_contents(base_path('pint.json')))->toBe('{"preset": "psr12"}');
});

it('is idempotent', function () {
    $this->artisan('engineering-kit:install')->assertSuccessful();

    $before = file_get_contents(base_path('CLAUDE.md'));

    $this->artisan('engineering-kit:install')->assertSuccessful();

    expect(file_get_contents(base_path('CLAUDE.md')))->toBe($before);
});
