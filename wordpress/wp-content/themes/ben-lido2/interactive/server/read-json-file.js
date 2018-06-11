const fs = require("fs-extra");
const paths = require("../config/paths");

// Dummy data for routes
const cartFile = `${paths.fakeData}/cart.json`;

const read = () => {
  return JSON.parse(fs.readFileSync(cartFile, "utf-8"));
};

module.exports.read = () => read();
