const webpack = require('webpack')
const path = require('path')
const paths = require('./config/paths')
const chalk = require('chalk')
const wordpressCheck = require('./scripts/wordpress-check').check()
// const CompressionPlugin = require('compression-webpack-plugin')
const Visualizer = require('webpack-visualizer-plugin')

const log = console.log

const destination = wordpressCheck
  ? path.join(paths.wordpressJSPath)
  : path.join(paths.jsPath)

log(chalk.black.bgWhite(destination))

module.exports = {
  plugins: [
    // new CompressionPlugin(),
    new Visualizer({
      filename: './statistics.html'
    })
  ],

  // devtool: 'source-map', // enhance debugging by adding meta info for the browser devtools

  entry: [require.resolve('./polyfills'), path.join(paths.jsEntry)],

  output: {
    path: destination,
    filename: '[name].js',
    publicPath: '/'
    // sourceMapFilename: '[name].map'
  },

  optimization: {
    runtimeChunk: 'single',
    splitChunks: {
      chunks: 'all',
      maxInitialRequests: Infinity,
      minSize: 30000,
      cacheGroups: {
        vendor: {
          test: /[\\/]node_modules[\\/]/,
          name (module) {
            // get the name. E.g. node_modules/packageName/not/this/part.js
            // or node_modules/packageName
            const packageName = module.context.match(/[\\/]node_modules[\\/](.*?)([\\/]|$)/)[1]

            // npm package names are URL-safe, but some servers don't like @ symbols
            return `npm.${packageName.replace('@', '')}`
          }
        }
      }
    }
  },

  resolve: {
    extensions: ['.js'], // extensions that are used
    modules: [path.join(process.cwd(), 'src'), 'node_modules'] // directories where to look for modules
  },

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      }
    ]
  }
}
