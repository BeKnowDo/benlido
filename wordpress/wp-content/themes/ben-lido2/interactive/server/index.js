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
const productAPI = require("./routes/product-api");
const productData = require("./product-data");
const originalProductData = require("./product-data");
const categoryItems = require("./categories-data");
const cartJson = require("./read-json-file");

// Dummy data for routes
const cartFile = `${myConfiguration.fakeData}/cart.json`;

// Twig cache
Twig.cache(false);
const app = express();

app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());

app.set("etag", true);
app.set("view engine", "twig");
app.set("view cache", false);
app.set("views", path.join(myConfiguration.twigViews));

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
  const check = fs.existsSync(cartFile);
  // If cart JSON exists, store existing values
  if (check) {
    const cartItems = cartJson.read();
    const results = productData;

    if (cartItems.length > 0) {
      log(`We have items in our cart so add counts for each matched product`);
      results.map(item => {
        let i;
        for (i = 0; i < cartItems.length; i++) {
          const prodSku = item.sku;
          const prodCategory = item.categoryID;
          const cartSku = cartItems[i].sku;
          const cartCategory = cartItems[i].category;

          if (prodSku === cartSku && prodCategory === cartCategory) {
            const count = cartItems[i].count;
            item.count = count;
          }
        }
      });

      res.render("pages/kit-selected", {
        products: results
      });
    } else {
      log(`We don't have items in our cart so just send product list`);

      res.render("pages/kit-selected", {
        products: originalProductData
      });
    }
  } else {
    log(`We don't have items in our cart so just send product list`);

    res.render("pages/kit-selected", {
      products: originalProductData
    });
  }
});

app.get("/categories/:id", (req, res) => {
  const requestedCategory = req.params.id ? req.params.id : undefined;

  const heroData = categoryItems.filter(item => {
    if (requestedCategory === item.id) {
      return item;
    }
  })[0].name;

  const check = fs.existsSync(cartFile);
  // If cart JSON exists, store existing values
  if (check) {
    const cartItems = cartJson.read();
    const results = productData;

    if (cartItems.length > 0) {
      log(`We have items in our cart so add counts for each matched product`);
      results.map(item => {
        let i;
        for (i = 0; i < cartItems.length; i++) {
          const prodSku = item.sku;
          const prodCategory = item.categoryID;
          const cartSku = cartItems[i].sku;
          const cartCategory = cartItems[i].category;

          if (prodSku === cartSku && prodCategory === cartCategory) {
            const count = cartItems[i].count;
            item.count = count;
          }
        }
      });

      res.render("pages/categories", {
        products: results,
        customizeKit: true,
        heroData: [{ header: heroData }]
      });
    } else {
      log(`We don't have items in our cart so just send product list`);

      res.render("pages/categories", {
        products: originalProductData,
        customizeKit: true,
        heroData: [{ header: heroData }]
      });
    }
  } else {
    log(`We don't have items in our cart so just send product list`);

    res.render("pages/categories", {
      products: originalProductData,
      customizeKit: true,
      heroData: [{ header: heroData }]
    });
  }
});

app.get("/shop-landing", (req, res) => {
  const check = fs.existsSync(cartFile);
  // If cart JSON exists, store existing values
  if (check) {
    const cartItems = cartJson.read();
    const results = productData;

    if (cartItems.length > 0) {
      log(`We have items in our cart so add counts for each matched product`);

      categoryItems.map(category => {
        category.featured.map(item => {
          let i;
          for (i = 0; i < cartItems.length; i++) {
            const prodSku = item.sku;
            const prodCategory = item.categoryID;
            const cartSku = cartItems[i].sku;
            const cartCategory = cartItems[i].category;

            if (prodSku === cartSku && prodCategory === cartCategory) {
              const count = cartItems[i].count;
              item.count = count;
            }
          }
        });
      });
      res.render("pages/shop-landing", {
        categoryItems: categoryItems,
        customizeKit: true
      });
    } else {
      log(`We don't have items in our cart so just send product list`);

      categoryItems.map(category => {
        category.featured.map(item => {
          let i;
          for (i = 0; i < cartItems.length; i++) {
            const prodSku = item.sku;
            const prodCategory = item.categoryID;
            const cartSku = cartItems[i].sku;
            const cartCategory = cartItems[i].category;

            if (prodSku === cartSku && prodCategory === cartCategory) {
              const count = cartItems[i].count;
              item.count = count;
            }
          }
        });
      });

      res.render("pages/shop-landing", {
        categoryItems: categoryItems,
        customizeKit: true
      });
    }
  } else {
    log(`We don't have items in our cart so just send product list`);
    res.render("pages/shop-landing", {
      categoryItems: categoryItems,
      customizeKit: true
    });
  }
});

app.get("/shipping-schedule", (req, res) => {
  res.render("pages/shipping-schedule");
});

app.get("/product/:id", (req, res) => {
  res.render("pages/product");
});

app.use("/json", categoriesAPI);
app.use("/json", cartAPI);
app.use("/json", productAPI);

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
      dalay: 400
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
