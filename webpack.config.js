const path = require('path');
const { execSync } = require('child_process');
const { VueLoaderPlugin } = require('vue-loader');
const webpack = require('webpack');

module.exports = {
    entry: {
        adminSettings: path.join(__dirname, 'src', 'adminSettings.js'),
		main: path.join(__dirname, 'src', 'main.js'),
        dashboard: path.join(__dirname, 'src', 'dashboard.js'),
    },
    output: {
        path: path.join(__dirname, 'js'),
        publicPath: '/js/',
    },
    devtool: 'source-map',
    mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',
    module: {
        rules: [
            {
                test: /\.css$/,
                use: ['vue-style-loader', 'css-loader']
            },
            {
                test: /\.scss$/,
                use: ['vue-style-loader', 'css-loader', 'sass-loader']
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/
            },
        ],
    },
    plugins: [
        new VueLoaderPlugin(),
        new webpack.DefinePlugin({
            RIOT_WEB_HASH: JSON.stringify(execSync('git rev-parse HEAD', { cwd: path.resolve(__dirname, './3rdparty/riot-web') }).toString()),
            RIOT_WEB_VERSION: JSON.stringify(execSync('git describe --exact-match HEAD', { cwd: path.resolve(__dirname, './3rdparty/riot-web') }).toString()),
        }),
    ],
    resolve: {
        extensions: ['.js', '.vue'],
        fallback: {
            path: require.resolve("path-browserify"),
        },
    },
};
