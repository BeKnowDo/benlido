const bs = require("browser-sync").create('benlido');

// Start the Browsersync server
bs.init({
  watch: true,
  proxy: "http://benlido.test",
  files: [
    {
      match: ["wp-content/themes/**/*.php"],
      fn: function (event, file) {
        console.log(`Files changed event`)
      },
      open: false,
      options: {
        ignored: '*.txt'
      }
    }
  ]
});


console.log(`We're running BrowserSync`)