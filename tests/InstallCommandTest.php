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

it('enables the engineering-kit plugin in .claude/settings.json', function () {
    $this->artisan('engineering-kit:install')->assertSuccessful();

    $settings = json_decode(file_get_contents(base_path('.claude/settings.json')), true);

    expect($settings['extraKnownMarketplaces'][Manifest::PLUGIN_MARKETPLACE]['source'])
        ->toBe(['source' => 'github', 'repo' => 'scrapkit/engineering-kit']);
    expect($settings['enabledPlugins'][Manifest::PLUGIN_ID])->toBeTrue();
});

it('merges the plugin settings into an existing .claude/settings.json', function () {
    (new Filesystem)->ensureDirectoryExists(base_path('.claude'));
    file_put_contents(base_path('.claude/settings.json'), json_encode([
        'permissions' => ['allow' => ['Bash(npm run lint)']],
        'enabledPlugins' => ['other@somewhere' => true],
    ]));

    $this->artisan('engineering-kit:install')->assertSuccessful();

    $settings = json_decode(file_get_contents(base_path('.claude/settings.json')), true);

    expect($settings['permissions'])->toBe(['allow' => ['Bash(npm run lint)']]);
    expect($settings['enabledPlugins'])
        ->toBe(['other@somewhere' => true, Manifest::PLUGIN_ID => true]);
    expect($settings['extraKnownMarketplaces'])->toHaveKey(Manifest::PLUGIN_MARKETPLACE);
});

it('respects a deliberately disabled plugin in .claude/settings.json', function () {
    (new Filesystem)->ensureDirectoryExists(base_path('.claude'));
    file_put_contents(base_path('.claude/settings.json'), json_encode([
        'extraKnownMarketplaces' => Manifest::pluginSettings()['extraKnownMarketplaces'],
        'enabledPlugins' => [Manifest::PLUGIN_ID => false],
    ]));

    $this->artisan('engineering-kit:install')->assertSuccessful();

    $settings = json_decode(file_get_contents(base_path('.claude/settings.json')), true);

    expect($settings['enabledPlugins'][Manifest::PLUGIN_ID])->toBeFalse();
});

it('leaves an invalid .claude/settings.json untouched', function () {
    (new Filesystem)->ensureDirectoryExists(base_path('.claude'));
    file_put_contents(base_path('.claude/settings.json'), '{not json');

    $this->artisan('engineering-kit:install')->assertSuccessful();

    expect(file_get_contents(base_path('.claude/settings.json')))->toBe('{not json');
});

it('does not overwrite existing files', function () {
    file_put_contents(base_path('pint.json'), '{"preset": "psr12"}');

    $this->artisan('engineering-kit:install')->assertSuccessful();

    expect(file_get_contents(base_path('pint.json')))->toBe('{"preset": "psr12"}');
});

it('is idempotent', function () {
    $this->artisan('engineering-kit:install')->assertSuccessful();

    $claudeBefore = file_get_contents(base_path('CLAUDE.md'));
    $settingsBefore = file_get_contents(base_path('.claude/settings.json'));

    $this->artisan('engineering-kit:install')->assertSuccessful();

    expect(file_get_contents(base_path('CLAUDE.md')))->toBe($claudeBefore);
    expect(file_get_contents(base_path('.claude/settings.json')))->toBe($settingsBefore);
});
