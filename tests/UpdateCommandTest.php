<?php

use Illuminate\Filesystem\Filesystem;
use Scrapkit\EngineeringKit\Support\Manifest;

beforeEach(fn () => cleanupKitFiles());
afterEach(fn () => cleanupKitFiles());

it('creates missing managed files', function () {
    $this->artisan('engineering-kit:update')->assertSuccessful();

    $files = new Filesystem;

    foreach (Manifest::copies() as $target => $source) {
        expect($files->exists(base_path($target)))->toBeTrue("expected {$target} to be created");
    }
});

it('keeps locally modified files without --force', function () {
    $this->artisan('engineering-kit:install')->assertSuccessful();

    file_put_contents(base_path('pint.json'), '{"preset": "psr12"}');

    $this->artisan('engineering-kit:update')->assertSuccessful();

    expect(file_get_contents(base_path('pint.json')))->toBe('{"preset": "psr12"}');
});

it('overwrites locally modified files with --force', function () {
    $this->artisan('engineering-kit:install')->assertSuccessful();

    file_put_contents(base_path('pint.json'), '{"preset": "psr12"}');

    $this->artisan('engineering-kit:update', ['--force' => true])->assertSuccessful();

    expect(file_get_contents(base_path('pint.json')))
        ->toBe(file_get_contents(Manifest::packagePath('configs/php/pint.json')));
});

it('restores the CLAUDE.md import when it was removed', function () {
    $this->artisan('engineering-kit:install')->assertSuccessful();

    file_put_contents(base_path('CLAUDE.md'), "# My project\n");

    $this->artisan('engineering-kit:update')->assertSuccessful();

    expect(file_get_contents(base_path('CLAUDE.md')))->toStartWith(Manifest::CLAUDE_IMPORT);
});
