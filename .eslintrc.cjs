module.exports = {
  // https://eslint.org/docs/user-guide/configuring#configuration-cascading-and-hierarchy
  // This option interrupts the configuration hierarchy at this file
  // Remove this if you have an higher level ESLint config file (it usually happens into a monorepos)
  root: true,

  parserOptions: {
    parser: '@babel/eslint-parser',
    ecmaVersion: 2021, // Allows for the parsing of modern ECMAScript features
    sourceType: 'module' // Allows for the use of imports
  },

  env: {
    browser: true
  },

  // Rules order is important, please avoid shuffling them
  extends: [
    // Base ESLint recommended rules
    // 'eslint:recommended',

    // Uncomment any of the lines below to choose desired strictness,
    // but leave only one uncommented!
    // See https://eslint.vuejs.org/rules/#available-rules
    'plugin:vue/vue3-essential', // Priority A: Essential (Error Prevention)
    // 'plugin:vue/vue3-strongly-recommended', // Priority B: Strongly Recommended (Improving Readability)
    // 'plugin:vue/vue3-recommended', // Priority C: Recommended (Minimizing Arbitrary Choices and Cognitive Overhead)

    'airbnb-base'

  ],

  plugins: [
    // https://eslint.vuejs.org/user-guide/#why-doesn-t-it-work-on-vue-files
    // required to lint *.vue files
    'vue',

  ],

  globals: {
    ga: 'readonly', // Google Analytics
    cordova: 'readonly',
    __statics: 'readonly',
    __QUASAR_SSR__: 'readonly',
    __QUASAR_SSR_SERVER__: 'readonly',
    __QUASAR_SSR_CLIENT__: 'readonly',
    __QUASAR_SSR_PWA__: 'readonly',
    process: 'readonly',
    Capacitor: 'readonly',
    chrome: 'readonly'
  },

  // add your custom rules here
rules: {
  'max-len': 'off',
  'no-plusplus': 'off',
  'no-param-reassign': 'off',
  'no-void': 'off',
  'no-nested-ternary': 'off',
  'max-classes-per-file': 'off',
  'object-curly-newline': 'off',
  'prefer-promise-reject-errors': 'off',
  'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',

  // JS Core
  'no-unused-vars': 'off',
  'no-undef': 'off',
  'no-console': 'off',
  'no-alert': 'off',
  'no-var': 'off',
  'no-else-return': 'off',
  'no-unused-expressions': 'off',
  'no-unreachable': 'off',
  'no-case-declarations': 'off',
  'no-shadow': 'off',
  'no-return-assign': 'off',
  'no-multi-assign': 'off',
  'no-mixed-operators': 'off',
  'no-redeclare': 'off',
  'no-prototype-builtins': 'off',
  'no-empty': 'off',
  'no-fallthrough': 'off',
  'no-lonely-if': 'off',
  'no-continue': 'off',
  'no-cond-assign': 'off',
  'no-irregular-whitespace': 'off',
  'no-useless-escape': 'off',
  'no-useless-constructor': 'off',
  'no-async-promise-executor': 'off',
  'no-class-assign': 'off',
  'no-const-assign': 'off',
  'no-duplicate-case': 'off',
  'no-empty-function': 'off',
  'no-ex-assign': 'off',
  'no-extra-boolean-cast': 'off',
  'no-extra-semi': 'off',
  'no-inner-declarations': 'off',
  'no-mixed-spaces-and-tabs': 'off',
  'no-negated-in-lhs': 'off',
  'no-new-symbol': 'off',
  'no-obj-calls': 'off',
  'no-self-assign': 'off',
  'no-sparse-arrays': 'off',
  'no-this-before-super': 'off',
  'no-unsafe-finally': 'off',
  'no-unsafe-negation': 'off',
  'use-isnan': 'off',
  'valid-typeof': 'off',

  // Style
  'camelcase': 'off',
  'quotes': 'off',
  'semi': 'off',
  'comma-dangle': 'off',
  'indent': 'off',
  'eol-last': 'off',
  'space-before-function-paren': 'off',
  'space-infix-ops': 'off',
  'keyword-spacing': 'off',
  'brace-style': 'off',
  'arrow-body-style': 'off',
  'arrow-parens': 'off',
  'func-names': 'off',
  'func-style': 'off',
  'prefer-arrow-callback': 'off',
  'prefer-const': 'off',
  'object-shorthand': 'off',
  'consistent-return': 'off',
  'consistent-this': 'off',
  'spaced-comment': 'off',
  'block-spacing': 'off',
  'computed-property-spacing': 'off',
  'dot-notation': 'off',
  'key-spacing': 'off',
  'new-cap': 'off',
  'no-multi-spaces': 'off',
  'no-trailing-spaces': 'off',
  'one-var': 'off',
  'one-var-declaration-per-line': 'off',
  'padded-blocks': 'off',
  'quote-props': 'off',
  'semi-spacing': 'off',
  'yoda': 'off',

  // Vue
  'vue/max-attributes-per-line': 'off',
  'vue/html-self-closing': 'off',
  'vue/singleline-html-element-content-newline': 'off',
  'vue/multiline-html-element-content-newline': 'off',
  'vue/name-property-casing': 'off',
  'vue/no-v-html': 'off',
  'vue/require-default-prop': 'off',
  'vue/require-prop-types': 'off',
  'vue/html-indent': 'off',
  'vue/script-indent': 'off',
  'vue/attributes-order': 'off',
  'vue/order-in-components': 'off',
  'vue/html-closing-bracket-spacing': 'off',
  'vue/no-mutating-props': 'off',
  'vue/no-use-v-if-with-v-for': 'off',

  // Import
  'import/first': 'off',
  'import/named': 'off',
  'import/namespace': 'off',
  'import/default': 'off',
  'import/export': 'off',
  'import/extensions': 'off',
  'import/no-unresolved': 'off',
  'import/no-extraneous-dependencies': 'off',
  'import/prefer-default-export': 'off',

  // Promises
  'promise/always-return': 'off',
  'promise/no-return-wrap': 'off',
  'promise/param-names': 'off',
  'promise/catch-or-return': 'off',
  'promise/no-native': 'off',
  'promise/no-nesting': 'off',
  'promise/no-promise-in-callback': 'off',
  'promise/no-callback-in-promise': 'off',
  'promise/avoid-new': 'off',

  // Outras que costumam incomodar
  'class-methods-use-this': 'off',
  'no-use-before-define': 'off',
  'no-await-in-loop': 'off',
  'no-template-curly-in-string': 'off',
  'no-useless-catch': 'off',
  'no-unneeded-ternary': 'off',
  'no-useless-return': 'off',
  'require-await': 'off',
}


}
