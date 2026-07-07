// Consumer project eslint.config.js
//
// The shared config is the base layer; entries added after it win, so
// project-specific ignores and rule overrides go below the spread.
import scrapkit from '@scrapkit/engineering-kit/eslint';

/** @type {import('eslint').Linter.Config[]} */
export default [
    ...scrapkit,
    {
        ignores: [
            // Generated code this project should not lint.
            'resources/js/actions/**',
            'resources/js/routes/**',
            'resources/js/wayfinder/**',
            'resources/js/components/ui/*',
        ],
    },
    {
        rules: {
            // Project-level overrides win over the shared base.
            // 'no-console': 'error',
        },
    },
];
