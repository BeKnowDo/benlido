const fs = require("fs");
const express = require("express");
const myPaths = require("../../config/paths");
const log = console.log;

// Dummy data for routes
const usersData = `${myPaths.fakeData}/categories.json`;

// Initialize our express router
const router = express.Router();

// Now declare our routes
router.get("/categories", (req, res) => {
  // Create query object
  let queries = {};
  queries = Object.assign(queries, req.body.form);

  if (Object.keys(queries).length <= 0) {
    return res.json({
      error: "Empty Results"
    });
  }

  // Read dummy data
  let users = JSON.parse(fs.readFileSync(usersData, "utf-8"));

  // Iterate through query parameters and filter dataset
  Object.keys(queries).forEach((key, index) => {
    users = users.filter(item => {
      let name = item[key] ? item[key].toString().toLowerCase() : null;
      let value = queries[key].toLowerCase();
      // log({
      //   queryValue: value,
      //   name: name
      // })
      return name.includes(value);
    });
  });

  if (users.length <= 0) {
    return res.json({
      error: "Empty Results"
    });
  } else {
    // log(users.length)
    return res.json(users);
  }
});

module.exports = router;
