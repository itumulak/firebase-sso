module.exports = {
	env: {
		es6: true,
		browser: true,
		node: true,
		jquery: true,
		amd: true,
	},
	extends: [
		'eslint:recommended',
		'plugin:@wordpress/eslint-plugin/recommended',
	],
	rules: {},
	globals: {
		wp: true,
		jQuery: true,
	},
	ignorePatterns: [
		'tests/**/*.js',
		'temp.js',
		'/vendor/**/**/*.js',
		'/node_modules/**/**/*.js',
	],
};
