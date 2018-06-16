const fs = require("fs");
const express = require("express");
const myPaths = require("../../config/paths");
const log = console.log;

// Dummy data for routes
const categoryData = require(`${myPaths.fakeData}/categories.json`);
const productsData = require(`${myPaths.fakeData}/products.json`);
// Initialize our express router
const router = express.Router();

// Now declare our routes
router.get("/categories", (req, res) => {
  res.json(categoryData);
});

module.exports = router;
