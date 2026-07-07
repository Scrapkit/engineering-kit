import tailwindcss from 'prettier-plugin-tailwindcss';

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
 * @type {import('prettier').Config}
 */
export default {
    semi: true,
    singleQuote: true,
    singleAttributePerLine: false,
    htmlWhitespaceSensitivity: 'css',
    printWidth: 80,
    tabWidth: 4,
    plugins: [tailwindcss],
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
