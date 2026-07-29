/**
 * Scrapkit shared Prettier base config.
 *
 * Consume from a project's prettier.config.js:
 *
 *     import base from '@scrapkit/engineering-kit/prettier';
 *
 *     export default {
 *         ...base,
 *         tailwindStylesheet: 'resources/css/app.css', // project-specific
 *     };
 *
 * The Tailwind plugin is named as a string rather than imported: it is ESM-only
 * and exposes no default export (only `options`, `parsers`, `printers`), so
 * `import tailwindcss from 'prettier-plugin-tailwindcss'` fails to link in every
 * consumer that has the plugin installed — and the failure comes from this file,
 * where a consumer cannot override it. Prettier resolves plugin strings itself.
 *
 * @type {import('prettier').Config}
 */
export default {
    semi: true,
    singleQuote: true,
    singleAttributePerLine: false,
    htmlWhitespaceSensitivity: 'css',
    printWidth: 80,
    tabWidth: 4,
    plugins: ['prettier-plugin-tailwindcss'],
    tailwindFunctions: ['clsx', 'cn', 'cva'],
    overrides: [
        {
            files: '**/*.yml',
            options: {
                tabWidth: 2,
            },
        },
    ],
};
