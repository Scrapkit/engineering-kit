<?php

/**
 * Scrapkit shared Pest architecture presets.
 *
 * Consume from a project's tests/ArchTest.php:
 *
 *     require_once base_path('vendor/scrapkit/engineering-kit/configs/testing/pest.php');
 *
 *     scrapkit_arch_preset();
 *
 * Projects add their own arch() expectations alongside the preset, or skip
 * individual preset expectations with Pest's ->ignoring().
 */
if (! function_exists('scrapkit_arch_preset')) {
    function scrapkit_arch_preset(): void
    {
        arch('scrapkit: no debugging functions in production code')
            ->expect(['dd', 'dump', 'ray', 'var_dump'])
            ->each->not->toBeUsed();

        arch('scrapkit: php preset')->preset()->php();

        arch('scrapkit: security preset')->preset()->security();
    }
}
