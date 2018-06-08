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
const faker = require("faker");
const bodyParser = require("body-parser");
const logNotify = chalk.bgKeyword("white").keyword("red");
const errorNotify = chalk.bgYellow.red;
const log = console.log;

const categoriesAPI = require("./routes/categories-api");
const cartAPI = require("./routes/cart-api");

const app = express();

app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());

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

app.get("/shipping-schedule", (req, res) => {
  res.render("pages/shipping-schedule");
});

app.get("/product/:id", (req, res) => {
  res.render("pages/product");
});

app.get("/categories", (req, res) => {
  res.render("pages/categories", {
    products: [
      {
        categoryID: "123",
        categoryTitle: "Category Name 2",
        sku: "lll",
        name: "Product Name",
        image: "/images/product-example.png",
        description:
          "Colgate Total Advanced Pro-sheild Mouthwash Peppermint...",
        href: "/",
        price: "2.45",
        selected: {
          quantity: "2"
        }
      },
      {
        categoryID: "456",
        categoryTitle: "Category Name 3",
        sku: "mmm",
        name: "Product Name",
        image: "/images/product-example.png",
        description:
          "Colgate Total Advanced Pro-sheild Mouthwash Peppermint...",
        href: "/",
        price: "2.45",
        selected: {
          quantity: "2"
        }
      },
      {
        categoryID: "789",
        categoryTitle: "Category Name 4",
        sku: "nnn",
        name: "Product Name",
        image: "/images/product-example.png",
        description:
          "Colgate Total Advanced Pro-sheild Mouthwash Peppermint...",
        href: "/",
        price: "2.45",
        selected: {
          quantity: "2"
        }
      },
      {
        categoryID: "987",
        categoryTitle: "Category Name 5",
        sku: "ooo",
        name: "Product Name",
        image: "/images/product-example.png",
        description:
          "Colgate Total Advanced Pro-sheild Mouthwash Peppermint...",
        href: "/",
        price: "2.45",
        selected: {
          quantity: "2"
        }
      },
      {
        categoryID: "654",
        categoryTitle: "Category Name 6",
        sku: "ppp",
        name: "Product Name",
        image: "/images/product-example.png",
        description:
          "Colgate Total Advanced Pro-sheild Mouthwash Peppermint...",
        href: "/",
        price: "2.45",
        selected: {
          quantity: "2"
        }
      },
      {
        categoryID: "321",
        categoryTitle: "Category Name 1",
        sku: "qqq",
        name: "Product Name",
        image: "/images/product-example.png",
        description:
          "Colgate Total Advanced Pro-sheild Mouthwash Peppermint...",
        href: "/",
        price: "2.45",
        selected: {
          quantity: "2"
        }
      },

      {
        categoryID: "147",
        categoryTitle: "Category Name 2",
        sku: "rrr",
        name: "Product Name",
        image: "/images/product-example.png",
        description:
          "Colgate Total Advanced Pro-sheild Mouthwash Peppermint...",
        href: "/",
        price: "2.45",
        selected: {
          quantity: "2"
        }
      },
      {
        categoryID: "258",
        categoryTitle: "Category Name 3",
        sku: "sss",
        name: "Product Name",
        image: "/images/product-example.png",
        description:
          "Colgate Total Advanced Pro-sheild Mouthwash Peppermint...",
        href: "/",
        price: "2.45",
        selected: {
          quantity: "2"
        }
      },
      {
        categoryID: "369",
        categoryTitle: "Category Name 4",
        sku: "ttt",
        name: "Product Name",
        image: "/images/product-example.png",
        description:
          "Colgate Total Advanced Pro-sheild Mouthwash Peppermint...",
        href: "/",
        price: "2.45",
        selected: {
          quantity: "2"
        }
      },
      {
        categoryID: "753",
        categoryTitle: "Category Name 5",
        sku: "uuu",
        name: "Product Name",
        image: "/images/product-example.png",
        description:
          "Colgate Total Advanced Pro-sheild Mouthwash Peppermint...",
        href: "/",
        price: "2.45",
        selected: {
          quantity: "2"
        }
      },
      {
        categoryID: "159",
        categoryTitle: "Category Name 6",
        sku: "vvv",
        name: "Product Name",
        image: "/images/product-example.png",
        description:
          "Colgate Total Advanced Pro-sheild Mouthwash Peppermint...",
        href: "/",
        price: "2.45",
        selected: {
          quantity: "2"
        }
      }
    ]
  });
});

app.use("/json", categoriesAPI);
app.use("/json", cartAPI);

app.get("/search", (req, res) => {
  return res.send("search page");
});

const listening = function() {
  if (!isProduction) {
    browserSync({
      files: ["interactive/**/*.{html,twig}", "build/**/*.{css, js}"],
      online: false,
      open: false,
      notify: false,
      port: env.BROWSER_SYNC_PORT,
      proxy: "localhost:" + env.PROXY_PORT,
      ui: false,
      dalay: 200
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
