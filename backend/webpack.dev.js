'use strict';

let path = require('path');
let webpack = require('webpack');

const {VueLoaderPlugin} = require('vue-loader');

const port = 6067;

module.exports = {
    mode: 'development',
    entry: [
        'babel-polyfill',
        './vue/main.js'
    ],
    output: {
        path: path.resolve(__dirname, './web/vue'),
        publicPath: `http://localhost:${port}/vue/`,
        filename: 'js/[name].js?[hash]',
        chunkFilename: 'js/[id].js?[hash]'
    },
    watch: true,
    devServer: {
        open: true,
        hot: true,
        port: port,
        host: '0.0.0.0',
        watchOptions: {
            poll: true,
        },
        historyApiFallback: true,
        noInfo: true,
        overlay: true,
        headers: {
            "Access-Control-Allow-Origin": "*"
        },
        allowedHosts: ['auto']
    },
    resolve: {
        extensions: ['.tsx', '.ts', '.js', '.vue', '.json'],
        alias: {
            'vue': 'vue/dist/vue.esm-bundler.js',
        }
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                use: 'vue-loader'
            },
            {
                test: /\.ts$/,
                exclude: /node_modules/,
                use: [
                    {
                        loader: "babel-loader",
                        options: {
                            babelrc: true
                        }
                    },
                    {
                        loader: "ts-loader",
                        options: {
                            appendTsSuffixTo: [/\.vue$/]
                        }
                    }
                ]
            },
            {
                test: /\.(s*)css$/,
                use: [
                    'vue-style-loader',
                    'css-loader',
                    {
                        loader: 'sass-loader',
                        options: {
                            additionalData:
                                '@import "./vue/assets/sass/variables.scss";' +
                                '@import "./vue/assets/sass/mixins.scss";'
                        }
                    }
                ],
            },
            {
                test: /\.js$/,
                loader: 'babel-loader'
            },
            {
                test: /\.(png|jpg|gif|svg|webp|woff|woff2|eot|ttf|pdf|csv|xls|xlsx)$/,
                loader: 'file-loader',
                options: {
                    name: 'files/[name].[ext]?[hash]',
                    esModule: false
                }
            }
        ]
    },
    plugins: [
        new VueLoaderPlugin(),
        new webpack.ProvidePlugin({
            Vue: ['vue/dist/vue.esm-bundler.js', 'default'],
            axios: 'axios',
            _: 'lodash'
        }),
        new webpack.DefinePlugin({
            __VUE_OPTIONS_API__: true,
            __VUE_PROD_DEVTOOLS__: false,
        })
    ],
    devtool: '#eval-source-map'
};