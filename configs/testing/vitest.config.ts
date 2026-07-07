import { defineConfig } from 'vitest/config';

/**
 * Scrapkit shared Vitest base config.
 *
 * Consume from a project's vitest.config.ts:
 *
 *     import { defineConfig, mergeConfig } from 'vitest/config';
 *     import base from '@scrapkit/engineering-kit/vitest';
 *
 *     export default mergeConfig(
 *         base,
 *         defineConfig({
 *             test: { setupFiles: ['resources/js/test/setup.ts'] },
 *         }),
 *     );
 *
 * The jsdom environment requires `jsdom` in the project's devDependencies.
 */
export default defineConfig({
    test: {
        environment: 'jsdom',
        globals: true,
        include: ['resources/js/**/*.test.{ts,tsx}'],
    },
});
