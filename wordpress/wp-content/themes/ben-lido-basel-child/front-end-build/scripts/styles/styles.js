const sass = require('node-sass')
const chalk = require('chalk')
const fs = require('fs-extra')
const cleanCSS = require('clean-css')
const postCSS = require('postcss')
const paths = require('../../config')

const isProduction = process.env.NODE_ENV === 'production'

const compileSass = () => {
  const log = console.log
  let cssContent
  const cssDestination = paths.cssDestination
  // const destinationPath = paths.appBuild


  const cssDestinationFile = `${cssDestination}/style.css`
  const cssMapFilePath = `${cssDestination}/style.css.map`

  // fs.ensureDirSync(destinationPath)
  // log(
  //   chalk.black.bgYellow(`\n\nCreated destination path set: ${destinationPath}`)
  // )

  // create styles destination paths
  
  console.log(cssDestination)

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
