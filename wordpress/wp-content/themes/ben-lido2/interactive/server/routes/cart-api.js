const fs = require("fs-extra");
const express = require("express");
const config = require("../../config/paths");
const readJsonFile = require("../read-json-file");
const log = console.log;

// Dummy data for routes
const cartPath = `${config.fakeData}`;
const cartFile = `${config.fakeData}/cart.json`;

// Initialize our express router
const router = express.Router();

// Add to Cart
router.post("/add-to-cart", (req, res) => {
  // Ensure directory exists
  fs.ensureDirSync(cartPath);

  // Create query object
  let queries = {};
  queries = req.body;

  if (Object.keys(queries).length <= 0) {
    return res.json({
      error: "Product not provided"
    });
  }

  // Check if cart JSON file exists
  const check = fs.existsSync(cartFile);

  // new item object
  const newCartItem = {
    sku: queries.sku,
    category: queries.category,
    name: queries.name,
    count: 1
  };

  log(newCartItem);

  // If cart JSON exists, store existing values
  if (check) {
    // Store existing values
    let cartItems = readJsonFile.read();

    if (cartItems.length > 0) {
      // Count current items and determine if limit is reached
      let totalCount = 0;
      cartItems.map(item => {
        if (item.count) {
          totalCount = totalCount + item.count;
        }
      });

      // Check if we have MAX limit of items in a bag
      if (totalCount >= config.cartLimit) {
        return res.send({
          error: `Whoops! Your bag's full.`
        });
      }

      let alreadyInCart = false;
      let i;
      for (i = 0; i < cartItems.length; i++) {
        if (cartItems[i].sku === newCartItem.sku) {
          alreadyInCart = true;
          cartItems[i].count = parseInt(cartItems[i].count) + 1;
        }
      }
      if (alreadyInCart === false) {
        cartItems.push(newCartItem);
      }
      fs.writeFileSync(cartFile, JSON.stringify(cartItems));
      let result = readJsonFile.read();
      return res.send(result);
    } else {
      // File already exists except it's an empty array
      // Simply push the newCartItem object
      fs.writeFileSync(cartFile, JSON.stringify([newCartItem]));
      let result = readJsonFile.read();
      return res.send(result);
    }
  } else {
    log({ message: `Cart JSON file doesn't exist. Let's create one` });
    // Create array to push 1st item
    fs.writeFileSync(cartFile, JSON.stringify([newCartItem]));
    let result = readJsonFile.read();
    return res.send(result);
  }
});

// Remove from Cart
router.post("/remove-from-cart", (req, res) => {
  // Ensure directory exists
  fs.ensureDirSync(cartPath);

  // Create query object
  let queries = {};
  queries = req.body;

  if (Object.keys(queries).length <= 0) {
    return res.json({
      error: "Product not provided"
    });
  }

  const removeItem = {
    sku: queries.sku,
    category: queries.category
  };

  // Check if cart JSON file exists
  const check = fs.existsSync(cartFile);

  // If cart JSON exists, store existing values
  if (check) {
    let cartItems = readJsonFile.read();
    // Store existing values
    let i;
    for (i = 0; i < cartItems.length; i++) {
      const sku = cartItems[i].sku;
      const category = cartItems[i].category;
      if (sku === removeItem.sku && category === removeItem.category) {
        cartItems[i].count = parseInt(cartItems[i].count) - 1;
        log(cartItems[i].count);
        if (cartItems[i].count <= 0) {
          cartItems.splice(i, 1);
        }

        // Write cart items JSON to ile
        fs.writeFileSync(cartFile, JSON.stringify(cartItems));
        const results = readJsonFile.read();
        return res.send(results);
      }
    }
  }
});

// Get Cart
router.get("/cart", (req, res) => {
  // Check if cart JSON file exists
  const check = fs.existsSync(cartFile);

  // If cart JSON exists, store existing values
  if (check) {
    const results = readJsonFile.read();
    return res.send(results);
  } else {
    const cartItems = [];
    return res.send(cartItems);
  }
});

module.exports = router;
