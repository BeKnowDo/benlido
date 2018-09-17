const args = require('args')
const puppeteer = require('puppeteer')
const devices = require('puppeteer/DeviceDescriptors')
const path = require('./config/paths')

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
    this.pageList = [
      {
        name: 'Home',
        url: `${host}`
      },
      {
        name: 'Bags',
        url: `${host}/bags/`
      },
      {
        name: 'Kits',
        url: `${host}/kits`
      },
      {
        name: 'Shop',
        url: `${host}/shop`
      },
      {
        name: 'About',
        url: `${host}/about`
      },
      {
        name: 'Help',
        url: `${host}/help`
      },
      {
        name: 'NAVIGATOR',
        url: `${host}/kitting/?id=1746`
      },
      {
        name: 'JETSETTER',
        url: `${host}/kitting/?id=1769`
      },
      {
        name: 'OCEANSIDER',
        url: `${host}/kitting/?id=1771`
      },
      {
        name: 'EXPLORER',
        url: `${host}/kitting/?id=1772`
      },
      {
        name: 'DAY-TRIPPER',
        url: `${host}/kitting/?id=1773`
      }
    ]

    this.targets = flags.p !== undefined ? this.pageList.filter(item => item.name === `${flags.p}`) : this.pageList

    this.deviceList = [
      {
        'name': 'iPhone 6 Plus'
      },
      {
        'name': 'iPhone 7'
      },
      {
        'name': 'iPhone 7 Plus'
      },
      {
        'name': 'iPhone 8'
      },
      {
        'name': 'iPhone 8 Plus'
      },
      {
        'name': 'iPhone X'
      },
      {
        'name': 'Pixel 2'
      },
      {
        'name': 'iPad'
      },
      {
        'name': 'iPad Pro'
      }

    ]

    // // console.log(this.pageList.filter(item => item.name === flags.p))
    // console.log(this.targets)
  }

  process () {
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

new BenLidoPuppet().process()
