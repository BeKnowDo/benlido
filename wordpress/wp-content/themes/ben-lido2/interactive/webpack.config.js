const path = require("path");
const paths = require("./config/paths");
const webpack = require("webpack");
const chalk = require("chalk");
const wordpressCheck = require("./scripts/wordpress-check").check();
const log = console.log;

const destination = wordpressCheck
  ? path.join(paths.wordpressJSPath)
  : path.join(paths.jsPath);

log(chalk.black.bgWhite(destination));

module.exports = {
  devtool: "source-map", // enhance debugging by adding meta info for the browser devtools
  entry: [require.resolve("./polyfills"), path.join(paths.jsEntry)],
  output: {
    path: destination,
    filename: "[name].js",
    publicPath: "/",
    sourceMapFilename: "[name].map"
  },

  resolve: {
    extensions: [".js"], // extensions that are used
    modules: [path.join(process.cwd(), "src"), "node_modules"] // directories where to look for modules
  },

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: {
            presets: ["env"]
          }
        }
      }
    ]
  }
};
