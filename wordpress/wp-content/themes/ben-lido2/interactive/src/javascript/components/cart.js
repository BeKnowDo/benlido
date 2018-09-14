import { endpoints } from '../../../config/endpoints'
import KUTE from 'kute.js'
import mojs from 'mo-js'
import swal from 'sweetalert'

export class Cart {
  constructor () {
    this.counter = document.getElementById('navbar-item-counter') || undefined

    this.kitID = document.getElementById('bl_kit_id') || undefined

    this.listContainer =
      document.getElementById('navbar-bag-list') || undefined

    this.addToCartButtons =
      document.querySelectorAll('.add-to-cart') || undefined

    this.singleAddToCartButtons =
    document.querySelectorAll('button.single_add_to_cart_button') || undefined

    this.removeFromKitButtons =
      document.querySelectorAll('.remove-from-cart') || undefined

    this.removeIcons =
      document.querySelectorAll('.fa-minus-circle') || undefined

    this.swapFromCartButtons =
      document.querySelectorAll('.swap-from-cart') || undefined

    this.cart = document.getElementById('benlido-cart') || undefined

    this.cartContainer =
      document.getElementById('navbar-bag-container') || undefined

    this.addEmptyProduct =
      document.querySelectorAll('.bl-add-empty-product') || undefined

    this.addKitToCartButtons =
      document.querySelectorAll('.add-kit-to-cart') || undefined

    this.addBagProduct =
      document.querySelectorAll('.bl-add-bag-product') || undefined

    this.removeBagProduct =
      document.querySelectorAll('.bl-remove-bag-product') || undefined

    this.swatchColor = document.querySelectorAll('.swatch-color') || undefined

    this.clearCartButton = document.querySelector('#ben-lido-clear-cart')

    if (this.kitID) {
      this.kitID = this.kitID.value
    }

    this.currentSwatch = null
  }

  init () {
    // if (this.cart) {
    //   this.openCart();
    // }

    if (this.counter) {
      this.getCurrentItems()
      this.listenForUpdate()
    }

    if (this.addToCartButtons) {
      this.addItem()
    }

    if (this.removeIcons) {
      this.removeItem()
    }

    if (this.removeFromKitButtons) {
      this.removeFromKit()
    }

    if (this.swapFromCartButtons) {
      this.swapItem()
    }

    if (this.addEmptyProduct) {
      this.emptyProductButtons()
    }

    if (this.removeBagProduct) {
      this.removeBagFromCart()
    }

    if (this.addKitToCartButtons) {
      this.addKitToCart()
    }

    if (this.singleAddToCartButtons) {
      this.singleAddToCart()
    }

    if (this.addBagProduct) {
      this.addBagProductToCart()
    }

    if (this.swatchColor) {
      this.setSwatchColor()
    }

    if (this.clearCartButton) {
      this.clearCart()
    }
  }

  clearCart () {
    const button = this.clearCartButton

    button.addEventListener('click', e => {
      e.preventDefault()

      swal({
        title: 'Clear all items from your cart?',
        icon: 'info',
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          swal('Poof! Your cart is now empty!', {
            icon: 'success'
          }).then(() => {
            window.location = button.href
          })
        } else {
          return true
        }
      })
    })
  }

  openCart () {
    this.cart.addEventListener('click', e => {
      e.preventDefault()
      this.cartContainer.classList.toggle('active')
    })
    this.counter.addEventListener('click', e => {
      e.preventDefault()
      this.cartContainer.classList.toggle('active')
    })
  }

  addKitToCart () {
    if (this.addKitToCartButtons.length > 0) {
      let setKitUrl = endpoints.setKit + '/' + this.kitID
      this.addKitToCartButtons.forEach(el => {
        el.addEventListener('click', e => {
          e.preventDefault()
          fetch(setKitUrl, {
            credentials: 'include',
            method: 'POST'
          })
            .then(function (response) {
              return response.json()
            })
            .then(response => {
              if (response.success == 1) {
                document.location.href = el.href
              }
            })
        })
      })
    }
  }

  singleAddToCart () {
    if (this.singleAddToCartButtons.length > 0) {
      let addKitCartUrl = endpoints.addToKitCart

      this.singleAddToCartButtons.forEach(el => {
        let quantities = document.getElementsByName('quantity')

        if (el.value) {
          addKitCartUrl += '/' + el.value
        }

        if (quantities.length > 0) {
          let qty = quantities[0]
          if (qty && qty.value) {
            addKitCartUrl += '/' + qty.value
          } else {
            addKitCartUrl += '/1'
          }
        } else {
          addKitCartUrl += '/1'
        }
        el.addEventListener('click', e => {
          e.preventDefault()
          fetch(addKitCartUrl, {
            credentials: 'include',
            method: 'POST'
          })
            .then(function (response) {
              return response.json()
            })
            .then(response => {
              if (response.success == 1 && response.href.length > 1) {
                // alert(response.href);
                document.location.href = response.href
              }
            })
        })
      })
    }
  }

  // add the bag to either the cart or the kit
  addBagProductToCart () {
    if (this.addBagProduct.length > 0) {
      this.addBagProduct.forEach(el => {
        // first, see if we have variations
        if (el.classList.contains('has-variations')) {
          // first, see if we are a bag or a kit
          el.addEventListener('click', e => {
            e.preventDefault()
            if (el.classList.contains('hero-product-picked')) {
              let returnURL = el.href
              document.location.href = returnURL
            }
            if (el.dataset) {
              let variation_id = el.dataset.variation_id || ''
              let product_id = el.dataset.product_id || ''
              let category_id = el.dataset.category_id || ''
              let returnURL = el.href
              if (variation_id.length > 0 && product_id.length > 0) {
                if (
                  el.classList.contains('self-kit') ||
                  el.classList.contains('prebuilt-kit') ||
                  el.classList.contains('in-kit-detail')
                ) {
                  if (el.classList.contains('in-kit')) {
                    returnURL = 'in-kit'
                  }
                  if (el.classList.contains('changed')) {
                    this.addItemToCart(
                      product_id,
                      category_id,
                      variation_id,
                      1,
                      returnURL
                    )
                  } else {
                    document.location.href = returnURL
                  }
                }
              }
            }
          })
        }
      })
    }
  }

  // takes the URL and does an AJAX call to change state of add item to kit to true
  emptyProductButtons () {
    if (this.addEmptyProduct.length > 0) {
      this.addEmptyProduct.forEach(el => {
        el.addEventListener('click', e => {
          e.preventDefault()
          let kit_id = document.getElementById('bl_kit_id') || 0
          this.setKitStateAPI(kit_id.value, 1, el.href)
        })
      })
    }
  }

  getCurrentItems () {
    fetch(endpoints.getCartItems, {
      credentials: 'include',
      method: 'POST'
    })
      .then(function (response) {
        return response.json()
      })
      .then(response => {
        this.updateCount(response)
        // this.miniCart(response);
        this.updateTileQuantity(response, null)
      })
  }

  listenForUpdate () {
    // NOTE: since the cart page product remove/update is triggered via jQuery,
    //       we cannot use addEventListener here to detect the custom event and update the cart count.
    //       So, we need to do this in jQuery
  }

  miniCart (items) {
    if (items.length > 0) {
      this.listContainer.innerHTML = `
        <ul class="navbar-bag-list-container">
        ${items
    .map(item => {
      return `
            <li class="navbar-bag-item columns col-gapless">
              <div class="column col-2 navbar-product-thumbnail">
                <img src="${item.image}" alt="Product image of: ${item.name}" />
              </div>
              <p class="column col-5 navbar-product-name">${
  item.count
}x &nbsp; ${item.name}</p>

              <div class="column col-5 text-right">

                <a
                  href="/cart"
                  class="navbar-edit-item"
                  data-product_id="${item.id}"
                  data-variation_id="${item.variation_id}"
                  data-sku="${item.sku}"
                  data-name="${item.name}"
                  data-category="${item.category}"
                  aria-label="Edit cart item"
                >
                  <i class="fal fa-edit"></i>
                </a>

                <span class="navbar-remove-item" data-product_id="${
  item.id
}" data-variation_id="${item.variation_id}">
                  <i class="fal fa-times"></i>
                </span>

              </div>

            </li>`
    })
    .join('')}
        </ul>
      `

      if (this.listContainer) {
        this.removeFromMiniCart()
      }
    } else {
      this.listContainer.innerHTML = `<h4 class="navbar-bag-empty">Your cart is empty...</h4>`
    }
  }

  updateCount (items) {
    if (items.length > 0) {
      let count = 0
      items.map(item => {
        if (item.count) {
          count = parseInt(count) + parseInt(item.count)
        }
      })
      const position = this.counter.getBoundingClientRect()
      this.counter.innerHTML = count

      const burst = new mojs.Burst({
        parent: this.counter.parentElement,
        top: position.y + 16,
        left: position.x + 6,
        radius: { 10: 19 },
        angle: 45,
        children: {
          shape: 'line',
          radius: 4,
          scale: 2,
          stroke: '#195675',
          strokeDasharray: '100%',
          strokeDashoffset: { '-100%': '100%' },
          duration: 400,
          easing: 'quad.out'
        },
        duration: 500
      })
      burst.replay()
    } else {
      this.counter.innerHTML = 0
    }
  }

  removeFromKit () {
    const removeGroup = this.removeFromKitButtons
    if (removeGroup.length > 0) {
      removeGroup.forEach(el => {
        el.addEventListener('click', e => {
          e.preventDefault()
          if (el.dataset) {
            let target = el.dataset
            let product_id = target.product_id ? target.product_id : undefined
            let category_id = target.category_id
              ? target.category_id
              : undefined

            if (product_id !== undefined && category_id !== undefined) {
              let removeItem = {
                product_id: product_id,
                category_id: category_id
              }

              let parentNode =
                el.parentElement.parentElement.parentElement.parentElement
                  .parentElement || undefined
              if (parentNode) {
                parentNode.style.overflow = 'hidden'
                KUTE.to(
                  parentNode,
                  {
                    width: 0,
                    opacity: 0
                  },
                  {
                    easing: 'easingCubicOut',
                    duration: 300,
                    complete: () => {
                      parentNode.parentElement.removeChild(parentNode)
                    }
                  }
                ).start()

                this.removeItemAPI(removeItem)
              }
            }
          }
        })
      })
    }
  }

  // sets the session to be in the "add item to kit" state (or unset it)
  setKitStateAPI (kitID, isAdd, redirectURL) {
    let setKitStateURL = endpoints.setKitState + '/' + kitID + '/' + isAdd
    fetch(setKitStateURL, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json'
      }
    })
      .then(res => res.json())
      .catch(error => console.error('Error:', error))
      .then(response => {
        if (response.error) {
        } else {
          // need to set timeoout here
          setTimeout(function () {
            document.location = redirectURL
          }, 100)
        }
      })
  }

  swapItem () {
    const swaps = this.swapFromCartButtons
    if (swaps.length > 0) {
      swaps.forEach(swap => {
        swap.addEventListener('click', e => {
          swap.parentElement.classList.add('loading')
          e.preventDefault()

          let el = swap.dataset
          let kit_id = el.kit_id ? el.kit_id : 0
          let cat_id = el.cat_id ? el.cat_id : 0
          let prod_id = el.prod_id ? el.prod_id : 0

          let swapURL = endpoints.swapItemFromKit
          // add to kit is: kit_id, product_id, cat_id
          swapURL += '/' + kit_id + '/' + prod_id + '/' + cat_id
          fetch(swapURL, {
            method: 'POST',
            credentials: 'include',
            headers: {
              'Content-Type': 'application/json'
            }
          })
            .then(res => res.json())
            .catch(swap.parentElement.classList.remove('loading'))
            .then(response => {
              if (response.error) {
                swap.parentElement.classList.remove('loading')
              } else {
                if (response.url) {
                  setTimeout(function () {
                    swap.parentElement.classList.remove('loading')
                    document.location.href = response.url
                  }, 100)
                }
              }
            })
        })
      })
    }
  }

  removeItemAPI (item) {
    let kit_id = document.getElementById('bl_kit_id')
    let fromCart = item.from_cart ? item.from_cart : false
    if (kit_id && kit_id.value) {
      kit_id = parseInt(kit_id.value)
    }
    let removeURL = endpoints.removeFromCart
    // if there is a kit ID, that means we're removing it from the kit. Otherwise, we are removing it from the cart
    if (fromCart == true) {
      var product_id = item.product_id ? item.product_id : 0
      var variation_id = item.variation_id ? item.variation_id : 0
      removeURL =
        endpoints.removeFromCart + '/' + product_id + '/' + variation_id
    }
    if (kit_id > 0 && item.product_id && fromCart == false) {
      removeURL =
        endpoints.removeFromKit + '/' + kit_id + '/' + item.product_id
      if (item.category_id) {
        removeURL += '/' + item.category_id
      }
    }
    fetch(removeURL, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json'
      }
    })
      .then(res => res.json())
      .catch(error => console.error('Error:', error))
      .then(response => {
        if (response.error) {
        } else {
          this.updateCount(response)
          this.miniCart(response)
          this.updateTileQuantity(response, item)
        }
      })
  }

  resetButtonCopy () {
    // first, we clear everything
    document.querySelectorAll('a.in-cart').forEach(el => {
      el.classList.remove('in-cart')
      el.querySelectorAll('.add-to-cart-text').forEach(c => {
        if (c.dataset) {
          c.innerHTML = c.dataset.defaultText || 'Add to kit'
        }
      })
      el.querySelectorAll('.fa-minus-circle').forEach(f => {
        if (!f.classList.contains('hidden')) {
          f.classList.add('hidden')
        }
      })
    })
  }

  updateTileQuantity (response, item) {
    this.resetButtonCopy()
    // console.log(response.length);
    // console.log(response);
    if (Array.isArray(response)) {
      response.forEach(item => {
        // console.log(item);
        let itemCount
        if (item) {
          let html_id = ''
          if (item.id) {
            html_id = '.post-' + item.id
          }
          // console.log(html_id);
          if (item.count) {
            itemCount = item.count
          }

          let productBlocks = document.querySelectorAll(html_id)
          // console.log(productBlocks);
          // productBlocks = null;
          if (productBlocks && productBlocks.length > 0) {
            productBlocks.forEach(el => {
              let defaultText
              let cartText
              let targetButton = el.querySelector('.add-to-cart') || undefined
              let removeIcon =
                el.querySelector('.fa-minus-circle') || undefined
              let buttonText =
                el.querySelector('.add-to-cart-text') || undefined

              if (typeof buttonText !== 'undefined' && buttonText.dataset) {
                defaultText = buttonText.dataset.defaultText || ''
                cartText = buttonText.dataset.cartText || ''
              }

              // prevent native clicks on 'add-to-cart' anchors
              targetButton.addEventListener('click', e => {
                e.preventDefault()
              })

              if (
                typeof itemCount !== 'undefined' &&
                typeof buttonText !== 'undefined' &&
                typeof removeIcon !== 'undefined' &&
                typeof targetButton !== 'undefined'
              ) {
                buttonText.innerHTML = `${itemCount} ${cartText}`
                removeIcon.classList.remove('hidden')
                targetButton.classList.add('in-cart')
              } else {
                if (buttonText && targetButton && removeIcon) {
                  buttonText.innerHTML = defaultText
                  targetButton.classList.remove('in-cart')
                  removeIcon.classList.add('hidden')
                }
              }
            })
          } // end if productBlocks
        }
      })
    } // end if isArray

    //
  } // end updateTileQuantity()

  removeFromMiniCart () {
    const cartItems = this.listContainer.querySelectorAll(
      '.navbar-remove-item'
    )

    cartItems.forEach(item => {
      item.addEventListener('click', e => {
        e.preventDefault()
        if (item.dataset) {
          let target = item.dataset
          let product_id = target.product_id ? target.product_id : undefined
          let variation_id = target.variation_id
            ? target.variation_id
            : undefined

          if (product_id !== undefined && variation_id !== undefined) {
            let removeItem = {
              product_id: product_id,
              variation_id: variation_id,
              from_cart: true
            }

            this.removeItemAPI(removeItem)
          }
        }
      })
    })
  }

  addItem () {
    if (this.addToCartButtons.length > 0) {
      this.addToCartButtons.forEach(button => {
        // const removeItemIcon = button.querySelector(".fa-minus-circle");
        const addItemIcon = button.querySelector('.fa-plus-circle')
        // const text = button.querySelector('.add-to-cart-text')
        // const inCartText = text.dataset.cartText;

        addItemIcon.addEventListener('click', e => {
          e.preventDefault()

          button.classList.add('loading')

          let el = addItemIcon.dataset
          let kit_id = el.kit_id ? el.kit_id : 0
          let cat_id = el.cat_id ? el.cat_id : 0
          let prod_id = el.prod_id ? el.prod_id : 0
          let var_id = el.var_id ? el.var_id : 0
          let swap = el.swap ? el.swap : 0
          let quantity = 1
          var product_url = button.href ? button.href : ''
          if (button.classList.contains('has-variations')) {
            document.location.href = product_url
            return true
          }

          let addURL = endpoints.addToCart
          if (kit_id > 0) {
            addURL = endpoints.addToKit
          }
          if (swap > 0 && kit_id > 0) {
            addURL = endpoints.selectSwap
          }
          // add to kit is: kit_id, product_id, cat_id
          if (kit_id > 0) {
            addURL += '/' + kit_id + '/' + prod_id + '/' + cat_id
          } else {
            addURL +=
              '/' + prod_id + '/' + cat_id + '/' + var_id + '/' + quantity
          }

          fetch(addURL, {
            method: 'POST',
            credentials: 'include',
            headers: {
              'Content-Type': 'application/json'
            }
          })
            .then(res => res.json())
            .catch(error => console.error('Error:', error))
            .then(response => {
              if (response.error) {
                button.classList.remove('loading')
              } else {
                if (typeof response.items !== 'undefined') {
                  this.updateCount(response.items)
                  this.miniCart(response.items)
                  this.updateTileQuantity(response.items, null)
                  button.classList.remove('loading')
                }
                if (typeof response.url !== 'undefined') {
                  // we will get a return URL
                  setTimeout(function () {
                    // button.classList.remove('loading')
                    document.location.href = response.url
                  }, 100) // setTimeout to bust promise
                }
              }
            })
        })
      })
    }
  }

  removeItem () {
    if (this.removeIcons.length > 0) {
      this.removeIcons.forEach(removeIcon => {
        removeIcon.addEventListener('click', e => {
          e.preventDefault()
          if (removeIcon.dataset) {
            let target = removeIcon.dataset
            let product_id = target.product_id ? target.product_id : undefined
            let variation_id = target.variation_id ? target.variation_id : 0

            if (product_id !== undefined) {
              let button = e.target.parentElement
              let text = button.querySelector('.add-to-cart-text')
              // let inCartText = text.dataset.cartText;
              // let defaultText = text.dataset.defaultText;

              let removeURL =
                endpoints.removeFromCart +
                '/' +
                product_id +
                '/' +
                variation_id
              fetch(removeURL, {
                method: 'POST',
                credentials: 'include',
                headers: {
                  'Content-Type': 'application/json'
                }
              })
                .then(res => res.json())
                .catch(error => console.error('Error:', error))
                .then(response => {
                  if (response.error) {
                  } else {
                    if (typeof response.items !== 'undefined') {
                      this.updateCount(response.items)
                      this.miniCart(response.items)
                      this.updateTileQuantity(response.items, null)
                    }
                    if (typeof response.url !== 'undefined') {
                      // we will get a return URL
                      setTimeout(function () {
                        document.location.href = response.url
                      }, 100) // setTimeout to bust promise
                    }
                  }
                })
            }
          }
        })
      })
    }
  }

  removeBagFromCart () {
    if (this.removeBagProduct.length > 0) {
      this.removeBagProduct.forEach(removeBagProduct => {
        removeBagProduct.addEventListener('click', e => {
          e.preventDefault()
          if (removeBagProduct.dataset) {
            let target = removeBagProduct.dataset
            let product_id = target.product_id ? target.product_id : undefined
            let variation_id = target.variation_id ? target.variation_id : 0

            if (product_id !== undefined) {
              let button = e.target.parentElement
              let text = button.querySelector('.add-to-cart-text')

              let removeURL =
                endpoints.removeFromCart +
                '/' +
                product_id +
                '/' +
                variation_id
              fetch(removeURL, {
                method: 'POST',
                credentials: 'include',
                headers: {
                  'Content-Type': 'application/json'
                }
              })
                .then(res => res.json())
                .catch(error => console.error('Error:', error))
                .then(response => {
                  if (response.error) {
                  } else {
                    if (typeof response.items !== 'undefined') {
                      removeBagProduct.classList.remove('show')
                      this.updateCount(response.items)
                      this.miniCart(response.items)
                      this.updateTileQuantity(response.items, null)
                    }
                    if (typeof response.url !== 'undefined') {
                      // we will get a return URL
                    }
                  }
                })
            }
          }
        })
      })
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

  // NOTE: not being used right now
  getProductData (div_id, product_id, variation_id) {
    // add to kit is: kit_id, product_id, cat_id
    let productURL = endpoints.getProductData
    productURL += '/' + product_id + '/' + variation_id
    fetch(productURL, {
      method: 'GET',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json'
      }
    })
      .then(res => res.json())
      .catch(error => console.error('Error:', error))
      .then(response => {
        if (response.error) {
        } else {
          if (response.image && response.image.url) {
            document.getElementById(div_id).src = response.image.url
          }
        }
      })
  }

  addItemToCart (product_id, category_id, variation_id, quantity, returnURL) {
    let url =
      endpoints.addToCart +
      '/' +
      product_id +
      '/' +
      category_id +
      '/' +
      variation_id +
      '/' +
      quantity
    fetch(url, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json'
      }
    })
      .then(res => res.json())
      .catch(error => console.error('Error:', error))
      .then(response => {
        if (response.error) {
        } else {
          if (returnURL.length > 0 && returnURL != 'in-cart') {
            document.location.href = returnURL
          } else {
            this.updateCount(response)
            this.miniCart(response)
            this.updateTileQuantity(response.items, item)
          }
        }
      })
  }
}
