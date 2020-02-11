const path = require('path');
const webpack = require('webpack');
const Merge = require('webpack-merge');
const CommonConfig = require('./webpack.dev.js');
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


module.exports = Merge.smartStrategy({
    entry: 'replace'
})(CommonConfig, {
    entry: {
        app: './preview.js'
    },
    output: {
        filename: '[name].production.bundle.js',
        path: path.resolve(__dirname, './dist'),
    },
    plugins: [
        new webpack.DefinePlugin({
            'process.env': {
                'NODE_ENV': JSON.stringify('production')
            }
        }),
        UglifyJsSetting
    ]
});