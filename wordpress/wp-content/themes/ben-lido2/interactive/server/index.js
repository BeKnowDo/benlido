// load in environment variables
require("dotenv").load();
const env = process.env;
const myConfiguration = require("../config/paths");
const isProduction = "production" === process.env.NODE_ENV;
const fs = require("fs");
const express = require("express");
const browserSync = require("browser-sync");
const chalk = require("chalk");
const Twig = require("twig");
const path = require("path");
const opn = require("opn");
const logNotify = chalk.bgKeyword("white").keyword("red");
const errorNotify = chalk.bgYellow.red;
const log = console.log;

const app = express();

app.set("etag", isProduction);
app.set("views", path.join(myConfiguration.twigViews));
app.set("view engine", "twig");

app.use("/images", express.static(path.join(myConfiguration.imagePath)));
app.use("/javascript", express.static(path.join(myConfiguration.jsPath)));

app.use("/styles", express.static(path.join(myConfiguration.cssDestination)));

app.get("/", (req, res) => {
  res.render("pages/home");
});

app.get("/build-a-kit", (req, res) => {
  res.render("pages/build-a-kit");
});

app.get("/pick-a-kit", (req, res) => {
  res.render("pages/pick-a-kit");
});

app.get("/kit-selected", (req, res) => {
  res.render("pages/kit-selected");
});

app.get("/product/:id", (req, res) => {
  res.render("pages/product");
});

app.get("/search", (req, res) => {
  return res.send("search page");
});

const listening = function() {
  if (!isProduction) {
    browserSync({
      files: ["interactive/**/*.{html,twig}", "build/**/*.{css, js}"],
      online: false,
      open: false,
      port: env.BROWSER_SYNC_PORT,
      proxy: "localhost:" + env.PROXY_PORT,
      ui: false
    });
  }
};

// Let's listen on the imported PORT env variable
app.listen(env.PROXY_PORT, () => {
  log(`Server's started on port ${env.PROXY_PORT}`);
  listening();
});

// Open a browser instance for convenience
// opn(`${env.LOCAL_HOST}:${env.PROXY_PORT}`, { app: "google chrome" });

log(logNotify(`Let's get this party started...`));
