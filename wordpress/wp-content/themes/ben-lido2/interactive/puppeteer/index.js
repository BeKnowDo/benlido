const args = require('args')
const fs = require('fs-extra')
const puppeteer = require('puppeteer')
const devices = require('puppeteer/DeviceDescriptors')
const path = require('../config/paths')
const chalk = require('chalk')
const puppetConfig = require('./puppeteer')
// console.clear()

args
  .option('page', 'The single page you want to scrap')

const flags = args.parse(process.argv)

// const host = 'http://dev.benlido.com'
const user = 'benlido'
const pass = 'benlido2018'

class BenLidoPuppet {
  constructor () {
    this.screenshotExtension = 'jpeg'
    this.pageList = puppetConfig.pages
    this.targets = flags.p !== undefined ? this.pageList.filter(item => item.name === `${flags.p}`) : this.pageList
    this.deviceList = puppetConfig.devices
  }

  async getMainNavigation () {
    // navbar-dropdown-primary-items desktop hide-md
    const homePage = this.pageList.filter(item => item.name === 'Home').length ? this.pageList.filter(item => item.name === 'Home')[0] : undefined

    if (homePage !== 'undefined') {
      const targetUrl = homePage.url
      const browser = await puppeteer.launch()
      const puppet = await browser.newPage()

      await puppet.goto(`${targetUrl}`)

      const mainLinks = '.navbar-dropdown-primary-items > li > a'

      // Extract the results from the puppet.
      const links = await puppet.evaluate(mainLinks => {
        const anchors = Array.from(document.querySelectorAll(mainLinks))
        return anchors.map(anchor => {
          const title = anchor.textContent.split('|')[0].trim()
          return {
            name: title,
            url: anchor.href
          }
        })
      }, mainLinks)

      this.pageList = links

      await puppet.close()
      await browser.close()
      return this.takeScreenshots()
    }
  }

  takeScreenshots () {
    let i = 0

    ;(async () => {
      puppeteer
        .launch({
          args: ['--disable-dev-shm-usage']
        })
        .then(async browser => {
          for (i; i < this.deviceList.length; i++) {
            let o = 0

            for (o; o < this.targets.length; o++) {
              const deviceName = this.deviceList[i].name
              const url = this.targets[o].url
              const name = this.targets[o].name
              const folder = deviceName.replace(/\s/g, '')
              const rootUrl = `${path.puppeteerDestination}/${folder}/`
              const width = devices[deviceName].viewport.width
              const height = devices[deviceName].viewport.height

              console.log(chalk.red(`fetching screenshot for: ${deviceName}`))

              const puppet = await browser.newPage()
              await puppet.authenticate({ username: `${user}`, password: `${pass}` })
              await puppet.emulate(devices[`${deviceName}`])
              await puppet.goto(`${url}`)

              const destinationPath = `${rootUrl}`

              try {
                if (!fs.existsSync(destinationPath)) {
                  fs.ensureDirSync(destinationPath)
                }

                console.log(chalk.bgGreenBright.black(`Generated image into: ${folder}/${name}`))
                console.log(chalk.red.bgWhite(`File name is: ${name}-${folder}-${width}x${height}.${this.screenshotExtension}`))

                // other actions...
                await puppet.screenshot({
                  path: `${rootUrl}/${name}-${folder}-${width}x${height}.${this.screenshotExtension}`,
                  type: this.screenshotExtension,
                  quality: 60,
                  fullPage: true
                })

              } catch (err) {
                console.error(err)
              }

              await puppet.close()
            }
          }
          await browser.close()
        })
    })()
  }
}

// new BenLidoPuppet().getMainNavigation()
new BenLidoPuppet().takeScreenshots()
