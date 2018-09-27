const webpack = require('webpack')
const path = require('path')
const paths = require('./config/paths')
const chalk = require('chalk')
const wordpressCheck = require('./scripts/wordpress-check').check()
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin

// const CompressionPlugin = require('compression-webpack-plugin')

const log = console.log

const destination = wordpressCheck
  ? path.join(paths.wordpressJSPath)
  : path.join(paths.jsPath)

log(chalk.black.bgWhite(destination))

module.exports = {
  plugins: [
    // new CompressionPlugin(),
    // new BundleAnalyzerPlugin()
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
    splitChunks: {
      chunks: 'all',
      minSize: 30000,
      maxSize: 0,
      minChunks: 1,
      maxAsyncRequests: 5,
      maxInitialRequests: 3,
      automaticNameDelimiter: '~',
      name: true,
      cacheGroups: {
        vendors: {
          test: /[\\/]node_modules[\\/]/,
          priority: -10
        },
        default: {
          minChunks: 2,
          priority: -20,
          reuseExistingChunk: true
        }
      }
    }
  },

  // optimization: {
  //   // runtimeChunk: 'single',
  //   splitChunks: {
  //     // chunks: 'all',
  //     // maxInitialRequests: Infinity,
  //     // minSize: 30000,
  //     cacheGroups: {
  //       vendor: {
  //         test: /[\\/]node_modules[\\/]/,
  //         chunks: 'all',
  //         priority: 1
  //       }
  //     }
  //   }
  // },

  resolve: {
    extensions: ['.js'], // extensions that are used
    modules: [path.join(process.cwd(), 'src'), 'node_modules'] // directories where to look for modules
  },

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules|bower_components)\/(?!(dom7|ssr-window|swiper)\/).*/,
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
