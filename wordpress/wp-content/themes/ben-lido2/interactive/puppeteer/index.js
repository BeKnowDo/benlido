const args = require('args')
const puppeteer = require('puppeteer')
const devices = require('puppeteer/DeviceDescriptors')
const path = require('../config/paths')
const puppetConfig = require('./puppeteer')
console.clear()

args
  .option('page', 'The single page you want to scrap')

const flags = args.parse(process.argv)

// const host = 'http://dev.benlido.com'
const host = 'http://benlido.localhost'
const user = 'benlido'
const pass = 'benlido2018'

class BenLidoPuppet {
  constructor () {
    this.pageList = puppetConfig.puppetConfig.pages

    this.targets = flags.p !== undefined ? this.pageList.filter(item => item.name === `${flags.p}`) : this.pageList

    this.deviceList = puppetConfig.puppetConfig.devices

    // // console.log(this.pageList.filter(item => item.name === flags.p))
    // console.log(this.targets)
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
              // console.log(devices[`${deviceName}`].viewport.height)

              const puppet = await browser.newPage()
              await puppet.authenticate({ username: `${user}`, password: `${pass}` })
              await puppet.emulate(devices[`${deviceName}`])
              await puppet.goto(`${url}`)

              // other actions...
              await puppet.screenshot({
                path: `${path.puppeteerDestination}/${name}-${deviceName.replace(/\s/g, '')}-${devices[`${deviceName}`].viewport.width}x${devices[`${deviceName}`].viewport.height}.png`,
                fullPage: true
              })

              await puppet.close()
            }
          }
          await browser.close()
        })
    })()
  }
}

new BenLidoPuppet().takeScreenshots()
