const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const path = require('path');
const WebpackBar = require('webpackbar');

// change these variables to fit your project
const jsPath = './js';
const cssPath = './styles';
const outputPath = 'dist';
const localDomain = 'https://wp-hubspot.docker.localhost';
const entryPoints = {
	// 'app' is the output name, people commonly use 'bundle'
	// you can have more than 1 entry point
	app: jsPath + '/main.js',
	admin: cssPath + '/admin.scss',
	login: cssPath + '/login.scss',
};

module.exports = {
	entry: entryPoints,
	output: {
		path: path.resolve(__dirname, outputPath),
		filename: '[name].js',
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: '[name].css',
		}),
		new WebpackBar(),

		// Uncomment this if you want to use CSS Live reload
		// new BrowserSyncPlugin({
		//   proxy: localDomain,
		//   files: [ outputPath + '/*.css' ],
		//   injectCss: true,
		// open: false
		// }, { reload: false, }),
	],
	module: {
		rules: [
			{
				test: /\.(s(a|c)ss)$/,
				use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader'],
			},
			// {
			// 	test: /\.sass$/i,
			// 	use: [
			// 		MiniCssExtractPlugin.loader,
			// 		'css-loader',
			// 		{
			// 			loader: 'sass-loader',
			// 			options: {
			// 				sassOptions: { indentedSyntax: true },
			// 			},
			// 		},
			// 	],
			// },
			{
				test: /\.(jpg|jpeg|png|gif|woff|woff2|eot|ttf|svg)$/i,
				use: 'url-loader?limit=1024',
			},
		],
	},
};
