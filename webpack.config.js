var webpack = require('webpack');
var path = require('path');
var ExtractTextPlugin = require("extract-text-webpack-plugin");

module.exports = {
    entry: './Resources/js/index.js',
    devtool: process.env.WEBPACK_DEVTOOL || 'source-map',
    output: {
        path: path.join(__dirname, 'Resources/public/dist'),
        filename: 'bundle.js'
    },
    module: {
        loaders: [
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/
            },
            {
                test: /\.scss$/,
                loader: ExtractTextPlugin.extract('css!sass')
            },

            // {
            //     test: /\.woff2?(\?v=[0-9]\.[0-9]\.[0-9])?$/,
            //     loader: "url?limit=10000"
            // },
            // {
            //     test: /\.(ttf|eot|svg)(\?[\s\S]+)?$/,
            //     loader: 'file'
            // },

            // Use one of these to serve jQuery for Bootstrap scripts:

            // // Bootstrap 4
            // { test: /bootstrap\/dist\/js\/umd\//, loader: 'imports?jQuery=jquery' },

            // Bootstrap 3
            // { test: /bootstrap-sass\/assets\/javascripts\//, loader: 'imports?jQuery=jquery' },
            // {
            //     test: /\.woff2?$|\.ttf$|\.eot$|\.svg$/,
            //     loader: 'file-loader'
            // }
        ]
    },
    plugins: [
        // new webpack.optimize.UglifyJsPlugin({compress: {warnings: false}}),
        new webpack.DefinePlugin({'process.env': {'NODE_ENV': JSON.stringify('production')}}),
        new ExtractTextPlugin('style.css')
    ]
};