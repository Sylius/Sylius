import js from '@eslint/js';
import globals from 'globals';

export default [
    js.configs.recommended,
    {
        ignores: ['vendor/', 'node_modules/', 'public/']
    },
    {
        languageOptions: {
            globals: {
                ...globals.browser,
                ...globals.node
            }
        },
        rules: {
            'indent': ['error', 4],
            'semi': ['error', 'always'],
            'comma-dangle': ['error', 'never'],
            'quotes': ['error', 'single'],
            'no-console': ['error', {'allow': ['warn', 'error']}]
        }
    }
];
