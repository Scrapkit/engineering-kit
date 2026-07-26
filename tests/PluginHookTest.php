<?php

use Scrapkit\EngineeringKit\Support\Manifest;

/**
 * Runs the SessionStart hook against a project directory and returns stdout.
 */
function runSessionStartHook(string $projectDir): string
{
    $pluginRoot = Manifest::packagePath(Manifest::PLUGIN_PATH);

    $command = sprintf(
        'CLAUDE_PLUGIN_ROOT=%s CLAUDE_PROJECT_DIR=%s bash %s 2>/dev/null',
        escapeshellarg($pluginRoot),
        escapeshellarg($projectDir),
        escapeshellarg($pluginRoot.'/hooks/session-start')
    );

    return (string) shell_exec($command);
}

function makeRepository(string $remote): string
{
    $directory = sys_get_temp_dir().'/engineering-kit-hook-'.bin2hex(random_bytes(6));
    mkdir($directory, 0755, true);

    shell_exec(sprintf(
        'git -C %s init --quiet && git -C %s remote add origin %s',
        escapeshellarg($directory),
        escapeshellarg($directory),
        escapeshellarg($remote)
    ));

    return $directory;
}

function hasGit(): bool
{
    return trim((string) shell_exec('command -v git 2>/dev/null')) !== '';
}

it('declares a SessionStart hook Claude Code can run', function () {
    $path = Manifest::packagePath(Manifest::PLUGIN_PATH.'/hooks/hooks.json');

    expect(file_exists($path))->toBeTrue();

    $config = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

    expect($config['hooks']['SessionStart'][0]['matcher'])->toBe('startup|clear|compact')
        ->and($config['hooks']['SessionStart'][0]['hooks'][0]['command'])
        ->toContain('${CLAUDE_PLUGIN_ROOT}/hooks/run-hook.cmd');
});

it('ships the hook scripts executable', function () {
    // Git tracks the executable bit; without it Claude Code cannot run them.
    foreach (['session-start', 'run-hook.cmd'] as $script) {
        $path = Manifest::packagePath(Manifest::PLUGIN_PATH."/hooks/{$script}");

        expect(file_exists($path))->toBeTrue("missing hook script {$script}")
            ->and(is_executable($path))->toBeTrue("hook script {$script} is not executable");
    }
});

it('stays quiet outside a git repository', function () {
    $directory = sys_get_temp_dir().'/engineering-kit-hook-'.bin2hex(random_bytes(6));
    mkdir($directory, 0755, true);

    expect(runSessionStartHook($directory))->toBe('');
});

it('stays quiet in a repository that is not Scrapkit', function () {
    $directory = makeRepository('git@github.com:someone-else/their-project.git');

    expect(runSessionStartHook($directory))->toBe('');
})->skip(! hasGit(), 'git is not available');

it('injects the org rules where Composer does not reach', function () {
    $directory = makeRepository('git@github.com:Scrapkit/notification-kit.git');

    $output = json_decode(runSessionStartHook($directory), true, 512, JSON_THROW_ON_ERROR);
    $context = $output['hookSpecificOutput']['additionalContext'];

    expect($output['hookSpecificOutput']['hookEventName'])->toBe('SessionStart')
        ->and($context)->toContain('Never commit secrets')
        ->and($context)->toContain('take precedence');
})->skip(! hasGit(), 'git is not available');

it('defers to the Composer route when the package is installed', function () {
    $directory = makeRepository('git@github.com:Scrapkit/laravel-starter-kit.git');
    $vendor = $directory.'/vendor/scrapkit/engineering-kit/claude';
    mkdir($vendor, 0755, true);
    file_put_contents($vendor.'/CLAUDE.md', '# rules');

    // The project's CLAUDE.md already imports these rules; stating them twice
    // is the duplication this hook exists to avoid.
    expect(runSessionStartHook($directory))->toBe('');
})->skip(! hasGit(), 'git is not available');
