const fs = require('fs-extra')
const paths = require('../config/paths')
const chalk = require('chalk')
const log = console.log

const copyTwigs = () => {
  const destinationPath = paths.wordpressTwigDestination

  fs.ensureDirSync(destinationPath)

  fs.copy(`${paths.twigViews}`, `${destinationPath}`, err => {
    log(chalk.black.bgGreen(`Copying twig templates to: ${destinationPath}`))
    if (err) return log(err)
  })
}

module.exports.twig = () => copyTwigs()
