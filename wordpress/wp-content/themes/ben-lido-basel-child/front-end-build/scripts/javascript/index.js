const sass = require('node-sass')
const chalk = require('chalk')
const fs = require('fs-extra')
const paths = require('../../config')

const isProduction = process.env.NODE_ENV === 'production'

const copyJavaScript = () => {
  const log = console.log
  let cssContent
  const jsDestination = paths.jsPath


  fs.ensureDirSync(jsDestination)
  log(chalk.black.bgYellow(`JavaScript Destination set to: ${jsDestination}`))


  // Don't minify if you're in Dev
  if (!isProduction) {
    fs.writeFileSync(jsDestinationFile, sassOutput.css, () => true)
    fs.writeFileSync(cssMapFilePath, sassOutput.map, () => true)
    log(chalk.black.bgYellow(`CSS File written to: ${jsDestinationFile}`))
    log(chalk.black.bgYellow(`CSS Map File written to: ${cssMapFilePath}`))
  } else {
    postCSS()
      .process(sassOutput, { from: undefined })
      .then(styles => {
        cssContent = style.css
        // Clean CSS options
        const options = {
          level: {
            2: {
              all: true,
              mergeNonAdjacentRules: false
            }
          }
        }
        cssContent = new cleanCSS(options).minify(style.css).styles
        fs.writeFileSync(jsDestinationFile, cssContent, () => true)
        log(
          chalk.black.bgYellow(
            `\n\nCCSS File written to: ${jsDestinationFile}\n\n`
          )
        )
      })
  }
}

module.exports.styles = () => copyJavaScript()
