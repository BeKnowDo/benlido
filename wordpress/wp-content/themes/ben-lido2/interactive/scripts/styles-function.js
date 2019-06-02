const sass = require('node-sass')
const chalk = require('chalk')
const paths = require('../config/paths.js')
const fs = require('fs-extra')
const cleanCSS = require('clean-css')
const postCSS = require('postcss')
const wordpressCheck = require('./wordpress-check')
const isProduction = process.env.NODE_ENV === 'production'
// const autoprefixer = require('autoprefixer')({
//   browsers: ['> 1%', 'last 2 versions', 'ie >= 11']
// })

const compileSass = () => {
  const log = console.log
  let cssContent
  let result = wordpressCheck.check()

  const destinationPath = result ? paths.wordpressPath : paths.appBuild
  const cssDestination = result ? paths.wordpressCssPath : paths.cssDestination
  console.log(cssDestination)
  const cssDestinationFile = `${cssDestination}/styles.css`
  const cssMapFilePath = `${cssDestination}/styles.css.map`

  fs.ensureDirSync(destinationPath)
  log(
    chalk.black.bgYellow(`\n\nCreated destination path set: ${destinationPath}`)
  )

  // create styles destination paths
  fs.ensureDirSync(cssDestination)
  log(chalk.black.bgYellow(`CSS Destination set to: ${cssDestination}`))

  const sassOutput = sass.renderSync({
    file: paths.styles,
    precision: 6,
    outputStyle: !isProduction ? 'compact' : 'compressed',
    outFile: cssDestinationFile,
    sourceMap: true
  })

  // Don't minify if you're in Dev
  if (!isProduction) {
    fs.writeFileSync(cssDestinationFile, sassOutput.css, () => true)
    fs.writeFileSync(cssMapFilePath, sassOutput.map, () => true)
    log(chalk.black.bgYellow(`CSS File written to: ${cssDestinationFile}`))
    log(chalk.black.bgYellow(`CSS Map File written to: ${cssMapFilePath}`))
  } else {
    postCSS()
      .process(sassOutput, { from: undefined })
      .then(styles => {
        cssContent = styles.css
        // Clean CSS options
        const options = {
          level: {
            2: {
              all: true,
              mergeNonAdjacentRules: false
            }
          }
        }
        cssContent = new cleanCSS(options).minify(styles.css).styles
        fs.writeFileSync(cssDestinationFile, cssContent, () => true)
        log(
          chalk.black.bgYellow(
            `\n\nCCSS File written to: ${cssDestinationFile}\n\n`
          )
        )
      })
  }
}

module.exports.styles = () => compileSass()
