const bs = require("browser-sync").create('benlido')
const chalk = require('chalk')
const paths = require('../config')

const cssDestination = paths.cssDestination

console.log(
  chalk.red(`${cssDestination}`)
  )

// Start the Browsersync server
bs.init({
  watch: true,
  proxy: "http://benlido.test",
  open: false
});

bs.watch(`${cssDestination}/*.css`, function (event, file) {
  if (event === "change") {
      bs.reload();
  }
});


console.log(`We're running BrowserSync`)