'use strict';

// Modules
var webpack = require('webpack');
var autoprefixer = require('autoprefixer');
var HtmlWebpackPlugin = require('html-webpack-plugin');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var Dotenv = require('dotenv-webpack');
var CleanWebpackPlugin = require('clean-webpack-plugin');

// Setting environments to use it in build script as well!
require('dotenv').config();
const BUILD = true;

var config = {
  entry: {
    app: './resources/installer/app/main.js',
  },
  output: {
    // Absolute output directory
    path: __dirname + '/public/installer',

    // Output path from the view of the page
    // Uses webpack-dev-server in development
    publicPath: BUILD ? '/installer/' : 'http://localhost:8080/',

    // Filename for entry points
    // Only adds hash in build mode
    filename: BUILD ? '[name].[hash].js' : '[name].bundle.js',

    // Filename for non-entry points
    // Only adds hash in build mode
    chunkFilename: BUILD ? '[name].[hash].js' : '[name].bundle.js'
  },
};


/**
 * Loaders
 * Reference: http://webpack.github.io/docs/configuration.html#module-loaders
 * List: http://webpack.github.io/docs/list-of-loaders.html
 * This handles most of the magic responsible for converting modules
 */

// Initialize module
config.module = {
  rules: [{
    // JS LOADER
    // Reference: https://github.com/babel/babel-loader
    // Transpile .js files using babel-loader
    // Compiles ES6 and ES7 into ES5 code
    test: /\.js$/,
    use: { loader: 'babel-loader' },
    exclude: /node_modules/
  }, {
    // ASSET LOADER
    // Reference: https://github.com/webpack/file-loader
    // Copy png, jpg, jpeg, gif, svg, woff, woff2, ttf, eot files to output
    // Rename the file using the asset hash
    // Pass along the updated reference to your code
    // You can add here any file extension you want to get copied to your output
    test: /\.(png|jpg|jpeg|gif|svg|woff|woff2|ttf|eot)$/,
    loader: 'file-loader'
  }, {
    //SASS LOADER
    //Reference: https://github.com/jtangelder/sass-loader
    //Compiles the sass files to css
    test: /\.scss$/,
    use: [
      { loader: 'style-loader' },
      {
        loader: 'css-loader',
        options: {
          modules: false,
          importLoaders: 1,
          // localIdentName: '[path][name]-[local]',
          sourceMap: true,
          minimize: BUILD,
        }
      },
      {
        loader: 'sass-loader',
        options: {
          sourceMap: true,
          includePaths: ['src'],
        },
      }
    ],
  }]
};

/**
 * Plugins
 * Reference: http://webpack.github.io/docs/configuration.html#plugins
 * List: http://webpack.github.io/docs/list-of-plugins.html
 */
config.plugins = [
  new CleanWebpackPlugin(['public/installer'], {
    verbose: true,
    dry: false
  }),
  // Reference: https://github.com/webpack/extract-text-webpack-plugin
  // Extract css files
  // Disabled when in test mode or not in build mode
  new ExtractTextPlugin('[name].[hash].css', {
    disable: false,
  }),
  // To support environment variables
  // Reference: https://www.npmjs.com/package/dotenv-webpack
  new Dotenv(),
  // Create index file as a php file
  new HtmlWebpackPlugin({
    filename: 'index.php',
    template: './resources/installer/index.php',
    inject: 'body',
    title: process.env.APP_TITLE,
    minify: {
      removeAttributeQuotes: true,
    },
  })
];

/**
 * Dev server configuration
 * Reference: http://webpack.github.io/docs/configuration.html#devserver
 * Reference: http://webpack.github.io/docs/webpack-dev-server.html
 */
config.devServer = {
  port: 3000,
  contentBase: './public/installer',
  stats: {
    modules: false,
    cached: false,
    colors: true,
    chunk: false
  }
};

module.exports = config;
