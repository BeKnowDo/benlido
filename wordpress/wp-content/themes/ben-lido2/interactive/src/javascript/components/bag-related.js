import 'element-closest'
export class LidoBagDetail {
  constructor () {
    this.swatches = document.querySelector('#picker_pa_color') || undefined
    this.bagListingSwatches = document.querySelectorAll('.bl-product-swatches-container').length > 0 ? document.querySelectorAll('.bl-product-swatches-container') : undefined
    this.thumbnails = document.querySelector('.flex-control-thumbs') || undefined
    this.bagSwatches = document.querySelectorAll('.bl-product-swatches') || undefined
    this.bagsDescription = document.querySelectorAll('div.hero-product .hero-product-copy') || undefined
    this.swatchColor = document.querySelectorAll('.normal.swatch-color') || undefined
    this.swatchColorCompact = document.querySelectorAll('.compact.swatch-color') || undefined
    this.currentSwatch = null
  }

  init () {
    if (this.swatches !== undefined) {
      this.attachClick()
      // this.attachHover();
    }

    if (this.swatchColor) {
      this.setSwatchColor()
    }

    if (this.swatchColorCompact) {
      this.setSwatchColorCompact()
    }

    if (this.bagSwatches !== undefined) {
      this.restoreSwatch()
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
    if (this.bagListingSwatches !== undefined) {
      this.bagListingSwatches.forEach((item, index) => {
        this.bagListingActivateSwatch(item, index)
      })
    }

  }

  bagListingActivateSwatch (item, index) {
    let swatches = item.querySelectorAll('.swatch-wrapper')
    let trackIndex = index

    if (item.querySelector('.selected') === null) {
      let defaultColor = item.querySelector('.default-color')
      
      if (defaultColor === null && swatches.length > 0) {
        if (typeof swatches[trackIndex] != 'undefined') {
          swatches[trackIndex].querySelector('.swatch-anchor').click()
        } 
        
      } else {
        defaultColor.querySelector('.swatch-anchor').click()
      }
    }
  }

  setSwatchColor () {
    if (this.swatchColor.length > 0) {
      this.swatchColor.forEach(el => {
        let parent = el.parentElement
        let parentHolder = parent.parentElement
        let swatchNodes
        let data = el.dataset
        let variation_id = data.variation_id ? data.variation_id : 0
        let product_name = data.product_name || ''
        let product_sku = data.product_sku || ''
        let product_category_name = data.product_category_name || ''
        let price = data.price || ''
        if (parent.classList.contains('selected')) {
          this.currentSwatch = variation_id
          let primary_image = data.hero_image ? data.hero_image : ''
          let primary_image_retina = data.hero_image_retina
            ? data.hero_image_retina
            : ''
          let index = data.index ? data.index : 0
          if (primary_image.length > 0) {
            let div_id = 'hero-' + index
            if (primary_image_retina.length > 1) {
              primary_image_retina += ' 2x'
            }
            document.getElementById(div_id).src = primary_image
            document
              .getElementById(div_id)
              .setAttribute('srcset', primary_image_retina)
          }
        }
        if (typeof parentHolder !== 'undefined') {
          swatchNodes = parentHolder.querySelectorAll('.select-option')
        }
        el.addEventListener('click', e => {
          e.preventDefault()
          let changed = false

          let product_id = data.product_id ? data.product_id : 0
          let index = data.index ? data.index : 0

          let hero_image = data.hero_image ? data.hero_image : ''
          let hero_image_retina = data.hero_image_retina
            ? data.hero_image_retina
            : ''

          if (hero_image_retina.length > 1) {
            hero_image_retina += ' 2x'
          }
          if (variation_id > 0) {
            if (swatchNodes.length > 0) {
              // console.log(swatchNodes);
              swatchNodes.forEach(listElement => {
                listElement.classList.remove('selected')
              })
            }

            if (variation_id != this.currentSwatch) {
              changed = true
            } else {
              changed = false
            }
            el.parentNode.classList.add('selected')
            let addButton = document.getElementById('button-' + index)
            let div_id = 'hero-' + index
            if (addButton) {
              addButton.setAttribute('data-variation_id', variation_id)
              addButton.setAttribute('data-product_sku', product_sku)
              addButton.setAttribute('data-product_name', product_name)
              addButton.setAttribute('data-product_category_name', product_category_name)
              addButton.setAttribute('data-price', price)
              addButton.removeAttribute('disabled')
              document.getElementById(div_id).src = hero_image
              document
                .getElementById(div_id)
                .setAttribute('srcset', hero_image_retina)
              if (changed == false) {
                addButton.classList.remove('changed')
              } else {
                addButton.classList.add('changed')
              }
            }
          }
        })
      })
    }
  }


  setSwatchColorCompact () {
    if (this.swatchColorCompact.length > 0) {
      this.swatchColorCompact.forEach(el => {
        let scope = el
        let parent = scope.parentElement
        let parentHolder = parent.parentElement
        let swatchNodes

        // Grab all element's data variables
        let data = scope.dataset
        let variationId = data.variation_id ? data.variation_id : 0
        let productName = data.product_name || ''
        let productSku = data.product_sku || ''
        let productCategoryName = data.product_category_name || ''
        let price = data.price || ''
        const primaryImage = data.hero_image ? data.hero_image : ''

        let primaryImageRetina = data.hero_image_retina
          ? data.hero_image_retina
          : ''

        // if there's a swatch color selected, update the target hero image
        // if (parent.classList.contains('selected')) {
        //   this.currentSwatch = variationId

        //   const index = data.index ? data.index : 0

        //   if (primaryImage.length > 0) {
        //     let targetHeroImage = document.querySelectorAll(`#hero-${index}`) || undefined

        //     if (primaryImageRetina.length > 1) {
        //       primaryImageRetina += ' 2x'
        //     }
        //   }
        // }

        if (typeof parentHolder !== 'undefined') {
          swatchNodes = parentHolder.querySelectorAll('.select-option')
        }

        scope.addEventListener('click', e => {
          e.preventDefault()
          e.stopImmediatePropagation()
          e.stopPropagation()

          const parentSlide = e.target.closest('.benlido-compact-product-compact-item')

          // Set state variables
          let changed = false
          let index = data.index ? data.index : 0
          let heroImageRetina = data.hero_image_retina
            ? data.hero_image_retina
            : ''

          if (heroImageRetina.length > 1) {
            heroImageRetina += ' 2x'
          }

          // Check if variation value is greater than ZERO...for a reason I don't understand :)
          if (variationId > 0) {
            // TODO: Stay functional - break this out
            if (parentSlide != null || parentSlide != undefined) {

              const heroSwatches = parentSlide.querySelectorAll('.bl-product-swatches-container')

              heroSwatches.forEach(swatch => {
                let swatchTarget = swatch.querySelectorAll('.swatch-color')
  
                swatchTarget.forEach(item => {
                  item.parentNode.classList.remove('selected')
                })
  
                let i
                const max = swatchTarget.length
  
                for (i = 0; i < max; i++) {
                  if (swatchTarget[i].dataset.variation_id === variationId) {
                    swatchTarget[i].parentNode.classList.add('selected')
                    return false
                  }
                }
              });

            }


            // Keep track of state change...this is why state management is needed...this is a headache
            if (variationId !== this.currentSwatch) {
              changed = true
            } else {
              changed = false
            }

            const addButton = document.querySelectorAll(`#button-${index}`) || undefined
            const targetHeroImage = document.querySelectorAll(`#hero-${index}`) || undefined

            if (targetHeroImage !== undefined) {
              targetHeroImage.forEach(hero => {
                hero.src = primaryImage
                hero.setAttribute('srcset', primaryImageRetina)
              })
            }

            if (addButton !== undefined) {
              addButton.forEach(button => {
                button.setAttribute('data-variation_id', variationId)
                button.setAttribute('data-product_sku', productSku)
                button.setAttribute('data-product_name', productName)
                button.setAttribute('data-product_category_name', productCategoryName)
                button.setAttribute('data-price', price)
                button.removeAttribute('disabled')

                if (changed === false) {
                  button.classList.remove('changed')
                } else {
                  button.classList.add('changed')
                }
              })
            }
          }
        })
      })
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

  restoreSwatch () {
    const targets = this.bagSwatches

    targets.forEach((target, index) => {
      const targetSwatch = target.querySelector('.select-option.selected') || undefined

      if (targetSwatch !== undefined || targetSwatch != null) {
        const target = targetSwatch.querySelector('a')
        const src = target.dataset.hero_image
        let hero = undefined
        let heroProductImageContainer = targetSwatch.closest('.hero-product-bag-image-container') || undefined
        if (heroProductImageContainer != undefined || heroProductImageContainer != null) {
          hero = heroProductImageContainer.querySelector('img.hero-product-bag-image') || undefined
        }
        if (hero !== undefined || hero != null) {
          hero.src = src
        }

        return false
      }
    })
  }
}
