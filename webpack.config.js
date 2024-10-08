const path = require('path');
const { execSync } = require('child_process');
const { VueLoaderPlugin } = require('vue-loader');
const webpack = require('webpack');

const RIOT_WEB_VERSION = execSync('git describe --abbrev=0 --tags', { cwd: path.resolve(__dirname, './3rdparty/riot-web') }).toString();
const RIOT_WEB_HASH = execSync(`git rev-parse -- ${RIOT_WEB_VERSION}`, { cwd: path.resolve(__dirname, './3rdparty/riot-web') }).toString();
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
    entry: {
        adminSettings: path.join(__dirname, 'src', 'adminSettings.js'),
        main: path.join(__dirname, 'src', 'main.js'),
        logout: path.join(__dirname, 'src', 'logout.js'),
    },
    output: {
        path: path.join(__dirname, 'js'),
        publicPath: '/js/',
    },
    devtool: 'source-map',
    mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',
    optimization: {
        minimize: true,
        minimizer: [
            new TerserPlugin({
                terserOptions: {
                    mangle: {
                        reserved: ['closePickerIframe'],
                        keep_fnames: true,
                    },
                    dead_code: false,
                    keep_fnames: true,
                },
            }),
        ],
    },
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
            RIOT_WEB_HASH: JSON.stringify(RIOT_WEB_HASH),
            RIOT_WEB_VERSION: JSON.stringify(RIOT_WEB_VERSION),
        }),
    ],
    resolve: {
        extensions: ['.js', '.vue'],
    },
};
