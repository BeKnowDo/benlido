// load in environment variables
require('dotenv').load()
const env = process.env
const myConfiguration = require('../config/paths')
const isProduction = process.env.NODE_ENV === 'production'
const fs = require('fs')
const express = require('express')
const nocache = require('nocache')
const browserSync = require('browser-sync')
const chalk = require('chalk')
const Twig = require('twig')
const path = require('path')

const bodyParser = require('body-parser')
const logNotify = chalk.bgKeyword('white').keyword('red')
const errorNotify = chalk.bgYellow.red
const log = console.log

const categoriesAPI = require('./routes/categories-api')
const cartAPI = require('./routes/cart-api')
const productAPI = require('./routes/product-api')
const productData = require('../fake-data/products.json')
const productImages = require('./product-data')
const associatedProducts = require('./product-data')
const originalProductData = require('../fake-data/products.json')
const categoryItems = require('../fake-data/categories.json')
const cartJson = require('./read-json-file')

// Dummy data for routes
const cartFile = `${myConfiguration.fakeData}/cart.json`

// Twig cache
// Twig.cache(false);
const app = express()
app.use(nocache())
app.use(bodyParser.urlencoded({ extended: true }))
app.use(bodyParser.json())

app.set('view engine', 'twig')
app.set('views', path.join(myConfiguration.twigViews))

app.use('/images', express.static(path.join(myConfiguration.imagePath)))
app.use('/javascript', express.static(path.join(myConfiguration.jsPath)))

app.use('/styles', express.static(path.join(myConfiguration.cssDestination)))

app.get('/', (req, res) => {
  res.render('pages/home', {
    categoryItems: categoryItems
  })
})

app.get('/build-a-kit', (req, res) => {
  res.render('pages/build-a-kit', {
    categoryItems: categoryItems,
    stepNavigation: {
      previousStep: '/',
      back: 'Back',
      current: 'Pick a Kit',
      nextStep: '/shop-landing',
      next: 'Next: Shop Products'
    }
  })
})

app.get('/pick-a-kit', (req, res) => {
  res.render('pages/pick-a-kit', {
    categoryItems: categoryItems,
    stepNavigation: {
      previousStep: '/',
      back: 'Back',
      current: 'Pick a Kit',
      nextStep: '/shop-landing',
      next: 'Next: Customize Kit'
    }
  })
})

app.get('/kit-selected', (req, res) => {
  const check = fs.existsSync(cartFile)
  // If cart JSON exists, store existing values
  if (check) {
    const cartItems = cartJson.read()
    const results = productData

    if (cartItems.length > 0) {
      log(`We have items in our cart so add counts for each matched product`)
      results.map(item => {
        let i
        for (i = 0; i < cartItems.length; i++) {
          const prodSku = item.sku
          const prodCategory = item.categoryID
          const cartSku = cartItems[i].sku
          const cartCategory = cartItems[i].category

          if (prodSku === cartSku && prodCategory === cartCategory) {
            const count = cartItems[i].count
            item.count = count
          }
        }
      })

      res.render('pages/kit-selected', {
        products: results,
        customizeKit: false,
        categoryItems: categoryItems,
        stepNavigation: {
          previousStep: '',
          back: 'Back',
          current: 'Customzie Your Kit',
          nextStep: '/shipping-schedule',
          next: 'Next: Scheduler'
        }
      })
    } else {
      log(`We don't have items in our cart so just send product list`)
      res.render('pages/kit-selected', {
        products: originalProductData.products,
        categoryItems: categoryItems,
        customizeKit: false,
        stepNavigation: {
          previousStep: '',
          back: 'Back',
          current: 'Customzie Your Kit',
          nextStep: '/shipping-schedule',
          next: 'Next: Scheduler'
        }
      })
    }
  } else {
    log(`We don't have items in our cart so just send product list`)

    res.render('pages/kit-selected', {
      products: originalProductData.products,
      customizeKit: false,
      stepNavigation: {
        previousStep: '',
        back: 'Back',
        current: 'Customzie Your Kit',
        nextStep: '/shipping-schedule',
        next: 'Next: Scheduler'
      }
    })
  }
})

app.get('/categories/:id', (req, res) => {
  const requestedCategory = req.params.id ? req.params.id : undefined

  const heroData = categoryItems.filter(item => {
    if (requestedCategory === item.categoryID) {
      return item
    }
  })[0].categoryTitle

  const check = fs.existsSync(cartFile)
  // If cart JSON exists, store existing values
  if (check) {
    const cartItems = cartJson.read()
    let results = originalProductData.filter(item => {
      if (item.categoryID === requestedCategory) {
        return item
      }
    })

    if (cartItems.length > 0) {
      log(`We have items in our cart so add counts for each matched product`)
      results.map(item => {
        let i
        for (i = 0; i < cartItems.length; i++) {
          const prodSku = item.sku
          const prodCategory = item.categoryID
          const cartSku = cartItems[i].sku
          const cartCategory = cartItems[i].category

          if (prodSku === cartSku && prodCategory === cartCategory) {
            const count = cartItems[i].count
            item.count = count
          }
        }
      })

      res.render('pages/categories', {
        products: results,
        customizeKit: true,
        hideCategory: true,
        categoryItems: categoryItems,
        heroData: [{ header: heroData }],
        stepNavigation: {
          previousStep: '/shop-landing',
          back: 'Back',
          current: 'Choose Products',
          nextStep: '/shipping-schedule',
          next: 'Next: Set Schedule'
        }
      })
    } else {
      log(`We don't have items in our cart so just send product list`)

      res.render('pages/categories', {
        products: originalProductData,
        customizeKit: true,
        hideCategory: true,
        categoryItems: categoryItems,
        heroData: [{ header: heroData }]
      })
    }
  } else {
    log(`We don't have items in our cart so just send product list`)

    res.render('pages/categories', {
      products: originalProductData,
      customizeKit: true,
      hideCategory: true,
      categoryItems: categoryItems,
      heroData: [{ header: heroData }]
    })
  }
})

app.get('/shop-landing', (req, res) => {
  const check = fs.existsSync(cartFile)
  // If cart JSON exists, store existing values
  if (check) {
    const cartItems = cartJson.read()

    if (cartItems.length > 0) {
      log(`We have items in our cart so add counts for each matched product`)

      categoryItems.map(category => {
        category.featured.map(item => {
          let i
          for (i = 0; i < cartItems.length; i++) {
            const prodSku = item.sku
            const prodCategory = item.categoryID
            const cartSku = cartItems[i].sku
            const cartCategory = cartItems[i].category

            if (prodSku === cartSku && prodCategory === cartCategory) {
              const count = cartItems[i].count
              item.count = count
            }
          }
        })
      })
      res.render('pages/shop-landing', {
        categoryItems: categoryItems,
        heroData: [
          {
            header:
              'Customize Your Kit <br/>Anything can go here. A hero. A simle header, etc...'
          }
        ],
        customizeKit: true,
        stepNavigation: {
          previousStep: '/build-a-kit',
          back: 'Back',
          current: 'Choose Products',
          nextStep: '/shipping-schedule',
          next: 'Next: Set Schedule'
        }
      })
    } else {
      log(`We don't have items in our cart so just send product list`)
      res.render('pages/shop-landing', {
        categoryItems: categoryItems,
        heroData: [
          {
            header:
              'Customize Your Kit <br/>Anything can go here. A hero. A simle header, etc...'
          }
        ],
        customizeKit: true,
        stepNavigation: {
          previousStep: '/build-a-kit',
          back: 'Back',
          current: 'Choose Products',
          nextStep: '/shipping-schedule',
          next: 'Next: Set Schedule'
        }
      })
    }
  } else {
    log(`We don't have items in our cart so just send product list`)
    res.render('pages/shop-landing', {
      categoryItems: categoryItems,
      heroData: [
        {
          header:
            'Customize Your Kit <br/>Anything can go here. A hero. A simle header, etc...'
        }
      ],
      customizeKit: true,
      stepNavigation: {
        previousStep: '/build-a-kit',
        back: 'Back',
        current: 'Choose Products',
        nextStep: '/shipping-schedule',
        next: 'Next: Set Schedule'
      }
    })
  }
})

app.get('/shipping-schedule', (req, res) => {
  res.render('pages/shipping-schedule', {
    categoryItems: categoryItems,
    stepNavigation: {
      previousStep: '/shop-landing',
      back: 'Back',
      current: 'Set Schedule',
      nextStep: '?',
      next: 'Next: Review Your Kit'
    }
  })
})

app.get('/product/:id', (req, res) => {
  res.render('pages/product', {
    customizeKit: true,
    associatedProducts: associatedProducts.associatedProducts,
    productImages: productImages.productImages,
    categoryItems: categoryItems,
    productInformation: [
      {
        category: '123',
        sku: 'AC7808',
        header: 'Aiden Travel Toiletry Bag By Kipling',
        description:
          "Our recommendation: this case makes a perfect companion for your travel adventures. Finished with a special side snap tab to easily hook up to your backpack or suitcase, this lightweight travel kit is especially easy to tow on-the-go. Fill it up with makeup or shower essentials and go! Bonus: You can personalize this pouch with a custom monogram. Add a touch of flair with initials, emoji's, or a fun saying (up to 9 characters).",
        price: '2.25',
        dimensions: '11.25" L x 5.5" H x 4" D',
        weight: '0.29 lbs.',
        href: '/kit-selected',
        image: '/images/product-image.png',
        bagURL: '/product/123',
        byLine: '( This item is free with the purchase of a Ben Lido Bag )'
      }
    ]
  })
})

app.get('/search', (req, res) => {
  const searchQuery = req.query

  const queryTerm = searchQuery.search

  const results = productData.filter(item => {
    const test = item.name
      .toString()
      .toLowerCase()
      .indexOf(queryTerm)
    if (test !== -1) {
      return item
    }
  })

  res.render('pages/search', {
    categoryItems: categoryItems,
    queryTerm,
    stepNavigation: {
      previousStep: '/shop-landing',
      back: 'Back',
      current: 'Search Results',
      nextStep: '/shipping-schedule',
      next: 'Next: Scheduler'
    },
    products: results,
    count: results.length
  })
})

app.get('/about', (req, res) => {
  res.render('pages/about', {

  })
})

app.use('/json', categoriesAPI)
app.use('/json', cartAPI)
app.use('/json', productAPI)

const listening = function () {
  if (!isProduction) {
    browserSync({
      files: ['**/*.{html,twig}', 'build/**/*.{css, js}'],
      online: false,
      open: false,
      notify: true,
      port: env.BROWSER_SYNC_PORT,
      proxy: `${env.LOCAL_HOST}:${env.PROXY_PORT}`,
      ui: false,
      dalay: 400
    })
  }
}

// Let's listen on the imported PORT env variable
app.listen(env.PROXY_PORT, () => {
  log(`Server's started on port ${env.PROXY_PORT}`)
  listening();
})

// Open a browser instance for convenience
// opn(`${env.LOCAL_HOST}:${env.PROXY_PORT}`, { app: "google chrome" });

log(logNotify(`Let's get this party started...`))
