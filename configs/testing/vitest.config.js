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
 *
 * Shipped as JavaScript on purpose. Vite externalizes dependencies instead of
 * bundling them, so this file reaches Node's ESM loader as-is, and Node refuses
 * to strip types from anything under `node_modules`
 * (ERR_UNSUPPORTED_NODE_MODULES_TYPE_STRIPPING) — as `.ts` it was unloadable by
 * every consumer. The `@scrapkit/engineering-kit/vitest` specifier is unchanged.
 *
 * @type {import('vitest/config').ViteUserConfig}
 */
export default defineConfig({
    test: {
        environment: 'jsdom',
        globals: true,
        include: ['resources/js/**/*.test.{ts,tsx}'],
    },
});
