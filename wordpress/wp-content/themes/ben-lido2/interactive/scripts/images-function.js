const fs = require('fs-extra')
const paths = require('../config/paths')
const chalk = require('chalk')
const wordpressCheck = require('../scripts/wordpress-check').check()
const log = console.log
const imagemin = require('imagemin')
const imageminJpegtran = require('imagemin-jpegtran')
const imageminPngquant = require('imagemin-pngquant')

const copyImage = () => {
  const destinationPath = wordpressCheck
    ? paths.wordpressImageDestination
    : paths.imageDestination

  fs.ensureDirSync(destinationPath)

  imagemin([`${paths.imageSrc}/*.{jpg,png}`], `${destinationPath}`, {
    plugins: [imageminJpegtran(), imageminPngquant({ quality: '70-80' })]
  })
    .then(files => {
      files.map(file => {
        log(chalk.cyan.bgBlack(`Writing image to: ${file.path}`))
      })
    })
    .then(() => {
      log(chalk.green(`\nWrote images to: ${destinationPath}`))
    })
}

module.exports.images = () => copyImage()
