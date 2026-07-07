// Consumer project vitest.config.ts
//
// mergeConfig deep-merges; project values win over the shared base.
import { defineConfig, mergeConfig } from 'vitest/config';
import base from '@scrapkit/engineering-kit/vitest';

export default mergeConfig(
    base,
    defineConfig({
        test: {
            setupFiles: ['resources/js/test/setup.ts'],
        },
    }),
);
