// Consumer project prettier.config.js
//
// Spread the shared base, then set project-specific values (they win).
import base from '@scrapkit/engineering-kit/prettier';

/** @type {import('prettier').Config} */
export default {
    ...base,
    // Required by prettier-plugin-tailwindcss v4 class sorting:
    tailwindStylesheet: 'resources/css/app.css',
};
