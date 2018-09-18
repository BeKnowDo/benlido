export class LidoBagDetail {
  constructor () {
    this.swatches = document.querySelector('#picker_pa_color') || undefined
    this.bagListingSwatches = document.querySelectorAll('.bl-product-swatches-container').length > 0 ? document.querySelectorAll('.bl-product-swatches-container') : undefined
    this.thumbnails =
      document.querySelector('.flex-control-thumbs') || undefined
    this.bagSwatches =
      document.querySelectorAll('.bl-product-swatches') || undefined
    this.bagsDescription = document.querySelectorAll('div.hero-product .hero-product-copy') || undefined
  }

  init () {
    if (this.swatches !== undefined) {
      this.attachClick()
      // this.attachHover();
    }

    setTimeout(() => {
      // For Bag Detail Pages
      if (this.swatches) {
        this.defaultBagDetailSwatch()
      }

      if (this.bagListingSwatches !== undefined) {
        this.defaultBagListingSwatch()
      }
    }, 500)

    if (this.bagsDescription !== undefined) {

    }
  }

  enableReadMore () {
    // console.log(this.bagsDescription)
  }

  defaultBagListingSwatch () {
    this.bagListingSwatches.forEach((item, index) => {
      this.bagListingActivateSwatch(item, index)
    })
  }

  bagListingActivateSwatch (item, index) {
    const swatches = item.querySelectorAll('.swatch-wrapper')
    let trackIndex = index

    if (item.querySelector('.selected') === null) {
      let defaultColor = item.querySelector('.default-color');
      if (defaultColor === null) {
          swatches[trackIndex].querySelector('.swatch-anchor').click();
      } else {
        defaultColor.querySelector('.swatch-anchor').click();
      }
    }
  }

  defaultBagDetailSwatch () {
    const bagSwatches = this.swatches.querySelectorAll('.swatch-wrapper')
    let activatedSwatchIndex = 0

    bagSwatches.forEach((bagSwatch, index) => {
      const swatches = bagSwatch.querySelectorAll('.swatch-anchor')

      let i = 0
      let swatchLength = swatches.length

      for (i; i < swatchLength; i++) {
        const scope = swatches[i]

        if (scope.parentNode.classList.contains('selected')) {
          return false
        }
      }

      activatedSwatchIndex = 0

      // bagSwatch.querySelector('.swatch-anchor').click()
    })

    bagSwatches[0].querySelector('.swatch-anchor').click()
  }

  attachClick () {
    const swatches = this.swatches.querySelectorAll('.select-option')
    swatches.forEach(item => {
      this.handleClick(item)
    })
  }

  handleClick (item) {
    const target = item

    target.addEventListener('click', e => {
      // WooCommerce...grrrr
      const color = e.currentTarget.dataset.value

      this.thumbnails === undefined
        ? (this.thumbnails = document.querySelector('.flex-control-thumbs'))
        : undefined

      const images = this.thumbnails.querySelectorAll('img')

      let i = 0
      for (i; i < images.length; i++) {
        const test = images[i].src.toString().indexOf(color) !== -1
        if (test === true) {
          images[i].parentNode.classList.remove('bl-hide-thumbnail')
          images[i].parentNode.classList.add('bl-show-thumbnail')
        } else {
          images[i].parentNode.classList.add('bl-hide-thumbnail')
          images[i].parentNode.classList.remove('bl-show-thumbnail')
        }
      }
    })
  }

  attachHover () {
    const swatches = this.swatches.querySelectorAll('.select-option')
    swatches.forEach(element => {
      this.handleHover(element)
    })
  }

  handleHover (item) {
    item.addEventListener('mouseover', e => {
      e.preventDefault()
      e.stopPropagation()
      const target = e.currentTarget
      target.click()
    })

    item.addEventListener('mouseout', e => {
      e.preventDefault()
      e.stopPropagation()
      const target = e.currentTarget
      target.click()
    })
  }
}
