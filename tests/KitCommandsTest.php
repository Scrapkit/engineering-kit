<?php

/**
 * The install and update tests live in one file on purpose: they all mutate
 * the same Testbench skeleton on disk (base_path()), and Pest's --parallel
 * distributes per file — two files means two processes racing on the same
 * directory. Keep every test that writes to base_path() in this file.
 */

use Illuminate\Filesystem\Filesystem;
use Scrapkit\EngineeringKit\Support\Manifest;

beforeEach(fn () => cleanupKitFiles());
afterEach(fn () => cleanupKitFiles());

describe('engineering-kit:install', function () {
    it('creates every managed file in a pristine project', function () {
        $this->artisan('engineering-kit:install')->assertSuccessful();

        $files = new Filesystem;

        foreach (array_keys(Manifest::copies()) as $target) {
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
});

describe('engineering-kit:update', function () {
    it('creates missing managed files', function () {
        $this->artisan('engineering-kit:update')->assertSuccessful();

        $files = new Filesystem;

        foreach (array_keys(Manifest::copies()) as $target) {
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
});
