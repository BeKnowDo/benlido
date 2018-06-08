const fs = require("fs-extra");
const express = require("express");
const config = require("../../config/paths");
const log = console.log;

// Dummy data for routes
const cartPath = `${config.fakeData}`;
const cartFile = `${config.fakeData}/cart.json`;

// Initialize our express router
const router = express.Router();

function readJsonFile() {
  return JSON.parse(fs.readFileSync(cartFile, "utf-8"));
}

function groupCartItems() {
  const cartItems = readJsonFile();

  const results = [];

  cartItems.map(item => {
    const result = results.find((product, index) => {
      if (product.sku === item.sku) {
        let count = results[index].count;
        count++;
        results[index].count = count;
      }
      return product.sku === item.sku;
    });

    if (result === undefined) {
      item.count = 1;
      results.push(item);
    }
  });

  let count = 0;

  cartItems.map(item => {
    if (item.count) {
      count = count + item.count;
    }
  });

  return results;
}

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

  const cartItem = {
    sku: queries.sku,
    category: queries.category,
    name: queries.name
  };

  // Check if cart JSON file exists
  const check = fs.existsSync(cartFile);

  // If cart JSON exists, store existing values
  if (check) {
    // Store existing values
    let cartItems = readJsonFile();

    // Check if we have MAX limit of items in a bag
    if (cartItems.length >= config.cartLimit) {
      return res.send({
        error: `Whoops! Your bags full.`
      });
    }

    // Add item to cart JSON
    cartItems.push(cartItem);

    // Write cart items JSON to ile
    fs.writeFileSync(cartFile, JSON.stringify(cartItems));
    cartItems = groupCartItems();

    return res.send(cartItems);
  } else {
    // log({ message: `Cart JSON file doesn't exist. Let's create one` });

    // Create array to push 1st item
    fs.writeFileSync(cartFile, JSON.stringify([cartItem]));
    const cartItems = groupCartItems();
    return res.send(cartItems);
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
    category: queries.category,
    name: queries.name
  };

  // Check if cart JSON file exists
  const check = fs.existsSync(cartFile);

  // If cart JSON exists, store existing values
  if (check) {
    let cartItems = readJsonFile();
    // Store existing values
    let i;
    for (i = 0; i < cartItems.length; i++) {
      const sku = cartItems[i].sku;
      const name = cartItems[i].name;
      const category = cartItems[i].category;
      if (
        sku === removeItem.sku &&
        name === removeItem.name &&
        category === removeItem.category
      ) {
        cartItems.splice(i, 1);

        // Write cart items JSON to ile
        fs.writeFileSync(cartFile, JSON.stringify(cartItems));
        const results = groupCartItems();
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
    const results = groupCartItems();
    return res.send(results);
  } else {
    const cartItems = [];
    return res.send(cartItems);
  }
});

module.exports = router;
