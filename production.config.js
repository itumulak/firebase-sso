const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const PurgeCssPlugin = require('purgecss-webpack-plugin');
const path = require('path');
const glob = require('glob');
const WebpackBar = require('webpackbar');

// change these variables to fit your project
const jsPublicPath = './src/public/js';
const cssPublicPath = './src/public/styles';
const jsAdminPath = './src/admin/js';
const cssAdminPath = './src/admin/styles';
const outputPath = 'dist';
const entryPoints = {
	// 'app' is the output name, people commonly use 'bundle'
	// you can have more than 1 entry point
	'sso-fb': `${jsPublicPath}/base.auth.js`,
	'sso-fb-admin': jsAdminPath + '/admin.js',
	admin: cssAdminPath + '/admin.scss',
	login: cssPublicPath + '/login.scss',
};

const PATHS = {
	src: path.join(__dirname, 'src'),
};

module.exports = {
	entry: entryPoints,
	output: {
		path: path.resolve(__dirname, outputPath),
		filename: '[name].js',
	},
	optimization: {
		splitChunks: {
			cacheGroups: {
				styles: {
					name: 'styles',
					test: /\.css$/,
					chunks: 'all',
					enforce: true,
				},
			},
		},
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: '[name].css',
		}),
		new WebpackBar(),
		new PurgeCssPlugin({
			paths: glob.sync(`${PATHS.src}/**/*`, { nodir: true }),
		}),
	],
	module: {
		rules: [
			{
				test: /\.(s(a|c)ss)$/,
				use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader'],
			},
			{
				test: /\.(jpg|jpeg|png|gif|woff|woff2|eot|ttf|svg)$/i,
				use: 'url-loader?limit=1024',
			},
		],
	},
};
