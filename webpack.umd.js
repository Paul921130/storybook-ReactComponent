const path = require('path');
const webpack = require('webpack');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const UglifyJsSetting = new UglifyJsPlugin({
    uglifyOptions: {
        warnings: false,
        output: {
            comments: false,
            beautify: false,
        },
    }
});

module.exports = {
    entry: {
        magaele: './magaele/umd.js',
    },
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, './static'),
        library: 'magaele',
        libraryTarget: 'umd'
    },
    module: {
        rules: [
            {
                test: /\.css$|\.scss$/,
                use: [
                    {
                        loader: 'style-loader'
                    },
                    {
                        loader: 'css-loader',
                        options: {
                            modules: true,
                            localIdentName: '[local]'
                        }
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            plugins: function () {
                                return [
                                    require('autoprefixer')
                                ];
                            }
                        }
                    },
                    {
                        loader: 'sass-loader'
                    }
                ]
            },
            {
                test: /\.js?$|\.jsx?$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader'
                }
            },
            {
                test: /\.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[name].[ext]',
                            outputPath: './files/',
                            publicPath: './static/files/'
                        }
                    }
                ]

            }
        ]
    },
    plugins: [
        // new webpack.optimize.CommonsChunkPlugin({
        //     names: ['flightSeoM', 'flightSeoPc', 'hotelSeoM', 'magaele'],
        //     minChunks: 2
        // }),
        new webpack.DefinePlugin({
            'process.env': {
                'NODE_ENV': JSON.stringify('production')
            }
        }),
        UglifyJsSetting
    ],
    externals: {
        'react': 'React',
        'react-dom': 'ReactDOM',
        'moment': 'moment',
    }
};