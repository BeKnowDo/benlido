const path = require("path");
const fs = require("fs-extra");
const paths = require("../config/paths");
const chalk = require("chalk");
const wordpressCheck = require("../scripts/wordpress-check").check();
const log = console.log;

const copyFonts = () => {
  const destinationPath = wordpressCheck
    ? paths.wordpressFontsDestination
    : paths.fontsDestination;

  fs.ensureDirSync(destinationPath);

  fs.copy(`${paths.fontsSource}`, `${destinationPath}`, err => {
    log(chalk.cyan(`Copying fonts to: ${destinationPath}`));
    if (err) return log(err);
  });
};

module.exports.fonts = () => copyFonts();
