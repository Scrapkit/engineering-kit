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

it('removes pristine legacy prompt copies from .claude/commands', function () {
    $files = new Filesystem;

    foreach (Manifest::legacyCopies() as $target => $source) {
        $files->ensureDirectoryExists(dirname(base_path($target)));
        $files->copy($source, base_path($target));
    }

    $this->artisan('engineering-kit:update')->assertSuccessful();

    foreach (array_keys(Manifest::legacyCopies()) as $target) {
        expect($files->exists(base_path($target)))->toBeFalse("expected {$target} to be removed");
    }

    expect($files->isDirectory(base_path('.claude/commands')))->toBeFalse();
});

it('keeps locally modified legacy prompt copies without --force', function () {
    $files = new Filesystem;
    $files->ensureDirectoryExists(base_path('.claude/commands'));
    file_put_contents(base_path('.claude/commands/code-review.md'), '# custom prompt');

    $this->artisan('engineering-kit:update')->assertSuccessful();

    expect(file_get_contents(base_path('.claude/commands/code-review.md')))->toBe('# custom prompt');
});

it('removes locally modified legacy prompt copies with --force', function () {
    $files = new Filesystem;
    $files->ensureDirectoryExists(base_path('.claude/commands'));
    file_put_contents(base_path('.claude/commands/code-review.md'), '# custom prompt');

    $this->artisan('engineering-kit:update', ['--force' => true])->assertSuccessful();

    expect($files->exists(base_path('.claude/commands/code-review.md')))->toBeFalse();
    expect($files->isDirectory(base_path('.claude/commands')))->toBeFalse();
});

it('enables the engineering-kit plugin in .claude/settings.json', function () {
    $this->artisan('engineering-kit:update')->assertSuccessful();

    $settings = json_decode(file_get_contents(base_path('.claude/settings.json')), true);

    expect($settings['enabledPlugins'][Manifest::PLUGIN_ID])->toBeTrue();
});

it('restores the CLAUDE.md import when it was removed', function () {
    $this->artisan('engineering-kit:install')->assertSuccessful();

    file_put_contents(base_path('CLAUDE.md'), "# My project\n");

    $this->artisan('engineering-kit:update')->assertSuccessful();

    expect(file_get_contents(base_path('CLAUDE.md')))->toStartWith(Manifest::CLAUDE_IMPORT);
});
