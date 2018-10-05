'use strict';

// Modules
var path = require('path');
var webpack = require('webpack');
var HtmlWebpackPlugin = require('html-webpack-plugin');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var Dotenv = require('dotenv-webpack');
var CleanWebpackPlugin = require('clean-webpack-plugin');

// Setting environments to use it in build script as well!
require('dotenv').config();
console.log(path.resolve(__dirname, './resources/spa/app'));
module.exports = function makeWebpackConfig (options) {
  /**
   * Environment type
   * BUILD is for generating minified builds
   * TEST is for generating test builds
   */
  var BUILD = !!options.BUILD;
  var TEST = !!options.TEST;

  /**
   * Config
   * Reference: http://webpack.github.io/docs/configuration.html
   * This is the object where all configuration gets set
   */
  var config = {};

  /**
   * Entry
   * Reference: http://webpack.github.io/docs/configuration.html#entry
   * Should be an empty object if it's generating a test build
   * Karma will set this when it's a test build
   */
  if (TEST) {
    config.entry = {}
  } else {
    config.entry = {
      app: './resources/spa/app/app.js'
    }
  }

  /**
   * Output
   * Reference: http://webpack.github.io/docs/configuration.html#output
   * Should be an empty object if it's generating a test build
   * Karma will handle setting it up for you when it's a test build
   */
  if (TEST) {
    config.output = {}
  } else {
    config.output = {
      // Absolute output directory
      path: __dirname + '/public/dist',

      // Output path from the view of the page
      // Uses webpack-dev-server in development
      publicPath: BUILD ? '/dist/' : 'http://localhost:8080/',

      // Filename for entry points
      // Only adds hash in build mode
      filename: BUILD ? '[name].[hash].js' : '[name].bundle.js',

      // Filename for non-entry points
      // Only adds hash in build mode
      chunkFilename: BUILD ? '[name].[hash].js' : '[name].bundle.js'
    }
  }

  /**
   * Devtool
   * Reference: http://webpack.github.io/docs/configuration.html#devtool
   * Type of sourcemap to use per build type
   */
  if (TEST) {
    config.devtool = 'inline-source-map';
  } else if (BUILD) {
    config.devtool = 'source-map';
    config.mode = 'production';
  } else {
    config.devtool = 'eval';
    config.mode = 'development';
  }

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
      use: { loader: 'file-loader' }
    },{
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
    },
    {
      test: /\.css$/,
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
      ],
    },
    {
      test: /\.html$/,
      use: { loader: 'ng-cache-loader?prefix=[dir]' },
    }]
  };


  /**
   * Plugins
   * Reference: http://webpack.github.io/docs/configuration.html#plugins
   * List: http://webpack.github.io/docs/list-of-plugins.html
   */
  config.plugins = [
    new CleanWebpackPlugin(['public/dist'], {
      verbose: true,
      dry: false
    }),
    // Reference: https://github.com/webpack/extract-text-webpack-plugin
    // Extract css files
    // Disabled when in test mode or not in build mode
    new ExtractTextPlugin('[name].[hash].css', {
      disable: !BUILD || TEST
    }),
    // To support environment variables
    // Reference: https://www.npmjs.com/package/dotenv-webpack
    new Dotenv(),
  ];

  // Skip rendering index.html in test mode
  if (!TEST) {
    // Reference: https://github.com/ampedandwired/html-webpack-plugin
    // Render index.html
    config.plugins.push(
      new HtmlWebpackPlugin({
        template: './resources/spa/index.ejs',
        inject: 'body',
        title: process.env.APP_TITLE,
        minify: {
          removeAttributeQuotes: true,
        },
      })
    )
  }

  /**
   * Dev server configuration
   * Reference: http://webpack.github.io/docs/configuration.html#devserver
   * Reference: http://webpack.github.io/docs/webpack-dev-server.html
   */
  config.devServer = {
    contentBase: './public/dist',
    stats: {
      modules: false,
      cached: false,
      colors: true,
      chunk: false
    }
  };

  return config;
};
