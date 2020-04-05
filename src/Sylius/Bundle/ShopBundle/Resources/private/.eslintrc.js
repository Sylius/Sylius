module.exports = {
  extends: 'airbnb-base',
  env: {
    browser: true,
  },
  rules: {
    'object-shorthand': ['error', 'always', {
      avoidQuotes: true,
      avoidExplicitReturnArrows: true,
    }],
    'function-paren-newline': ['error', 'consistent'],
    'max-len': ['warn', 120, 2, {
      ignoreUrls: true,
      ignoreComments: false,
      ignoreRegExpLiterals: true,
      ignoreStrings: true,
      ignoreTemplateLiterals: true,
    }],
  },
  settings: {
    'import/resolver': {
      'babel-module': {
        alias: {
          'sylius/ui': './src/Sylius/Bundle/UiBundle/Resources/private/js',
        },
      },
    },
  },
};
