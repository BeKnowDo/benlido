const fs = require("fs-extra");
const paths = require("../config/paths");

// Dummy data for routes
const cartFile = `${paths.fakeData}/cart.json`;

const read = () => {
  return JSON.parse(
    fs.readFileSync(cartFile, {
      encoding: "utf-8",
      flag: "rs+"
    })
  );
};

module.exports.read = () => read();
