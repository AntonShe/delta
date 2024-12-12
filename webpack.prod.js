'use strict';

const path = require('path');
const webpack = require('webpack');

const {VueLoaderPlugin} = require('vue-loader');

const UglifyJsPlugin = require('uglifyjs-webpack-plugin');

module.exports = [
    {
        mode: 'production',
        entry: [
            './frontend/web/js/base/styles.js'
        ],
        output: {
            path: path.resolve(__dirname, './frontend/web/novue'),
            publicPath: '/novue/',
            filename: 'js/[name].js?[hash]',
            chunkFilename: "js/[id].js?[hash]"
        },
        module: {
            rules: [
                {
                    test: /\.scss$/,
                    exclude: /node_modules/,
                    use: [
                        {
                            loader: 'file-loader',
                            options: {
                                outputPath: 'css/',
                                name: '[name].min.css'
                            }
                        },
                        'sass-loader'
                    ],
                },
                {
                    test: /\.js$/,
                    loader: 'babel-loader'
                },
                {
                    test: /\.(png|jpg|gif|svg|webp|pdf|csv|xls|xlsx)$/,
                    loader: 'file-loader',
                    options: {
                        name: 'files/[name].[ext]?[hash]',
                        esModule: false
                    }
                },
                {
                    test: /\.(woff|woff2|eot|ttf)$/,
                    loader: 'file-loader',
                    options: {
                        name: 'fonts/[name].[ext]?[hash]',
                        esModule: false
                    }
                }
            ]
        },
    },
    {
        mode: 'production',
        entry: {
            main: [
                'babel-polyfill',
                './frontend/vue/main.js'
            ],
            search: [
                './frontend/vue/search.js'
            ]
        },
        output: {
            path: path.resolve(__dirname, './frontend/web/vue'),
            publicPath: '/vue/',
            filename: 'js/[name].js?[hash]',
            chunkFilename: "js/[id].js?[hash]"
        },
        resolve: {
            extensions: ['.tsx', '.ts', '.js', '.vue', '.json'],
            alias: {
                'vue': 'vue/dist/vue.esm-bundler.js',
            }
        },
        optimization: {
            minimizer: [
                new UglifyJsPlugin({
                    cache: true,
                    parallel: true
                })
            ],
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
                            options: {babelrc: true}
                        },
                        {
                            loader: "ts-loader",
                            options: {appendTsSuffixTo: [/\.vue$/]}
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
                                additionalData: '@import "./frontend/web/css/sass/_variables.scss";'
                                    + '@import "./frontend/web/css/sass/_mixins.scss";'
                                    + '@import "./frontend/web/css/sass/_functions.scss";'
                                    + '@import "./frontend/web/css/sass/components/_icons.scss";'
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
    }
];