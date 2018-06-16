const fs = require("fs-extra");
const express = require("express");
const config = require("../../config/paths");
const products = require("../../fake-data/products.json");
const readJsonFile = require("../read-json-file");
const log = console.log;
const clearConsole = require("../clear-console");

// Dummy data for routes
const cartFile = `${config.fakeData}/cart.json`;

// Initialize our express router
const router = express.Router();

// Get Cart
router.get("/products", (req, res) => {
  clearConsole.clear();
  // Check if cart JSON file exists
  const check = fs.existsSync(cartFile);
  // If cart JSON exists, store existing values
  if (check) {
    const cartItems = readJsonFile.read();
    const results = products;
    console.log(results);
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

    return res.send(results);
  } else {
    return res.send(products);
  }
});

module.exports = router;
