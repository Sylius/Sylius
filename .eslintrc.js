module.exports = {
  extends: 'airbnb-base',
  rules: {
    'function-paren-newline': ['error', 'consistent'],
    'max-len': ['warn', 120, 2, {
      ignoreUrls: true,
      ignoreComments: false,
      ignoreRegExpLiterals: true,
      ignoreStrings: true,
      ignoreTemplateLiterals: true,
    }],
  },
};
