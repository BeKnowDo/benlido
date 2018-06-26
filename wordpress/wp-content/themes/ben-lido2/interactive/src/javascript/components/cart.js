import { endpoints } from "../../../config/endpoints";
import KUTE from "kute.js";
import mojs from "mo-js";

export class Cart {
  constructor() {
    this.counter = document.getElementById("navbar-item-counter") || undefined;
    this.kitID = document.getElementById("bl_kit_id") || undefined;
    this.listContainer =
      document.getElementById("navbar-bag-list") || undefined;
    this.addToCartButtons =
      document.querySelectorAll(".add-to-cart") || undefined;
    this.removeFromKitButtons =
      document.querySelectorAll(".remove-from-cart") || undefined;
    this.removeIcons =
      document.querySelectorAll(".fa-minus-circle") || undefined;
    this.swapFromCartButtons =
      document.querySelectorAll(".swap-from-cart") || undefined;
    this.cart = document.getElementById("benlido-cart") || undefined;
    this.cartContainer =
      document.getElementById("navbar-bag-container") || undefined;
      this.addEmptyProduct =
      document.querySelectorAll(".bl-add-empty-product") || undefined;
  }

  init() {
    if (this.counter) {
      this.getCurrentItems();
    }

    if (this.addToCartButtons) {
      this.addItem();
    }

    if (this.removeIcons) {
      this.removeItem();
    }

    if (this.removeFromKitButtons) {
      this.removeFromKit();
    }

    if (this.swapFromCartButtons) {
      this.swapItem();
    }

    if (this.addEmptyProduct) {
      this.emptyProductButtons();
    }

    if (this.cart) {
      this.openCart();
    }
  }

  openCart() {
    this.cart.addEventListener("click", e => {
      e.preventDefault();
      this.cartContainer.classList.toggle("active");
    });
    this.counter.addEventListener("click", e => {
      e.preventDefault();
      this.cartContainer.classList.toggle("active");
    });
  }

  // takes the URL and does an AJAX call to change state of add item to kit to true
  emptyProductButtons() {
    if (this.addEmptyProduct.length > 0) {
      this.addEmptyProduct.forEach( el => {
        el.addEventListener("click", e => {
          e.preventDefault();
          let kit_id = document.getElementById('bl_kit_id') || 0;
          this.setKitStateAPI(kit_id.value,1,el.href);
        });
      });
    }
  }
  getCurrentItems() {
    fetch(endpoints.getCartItems, {
      credentials: "include",
      method: "POST"
    })
      .then(function(response) {
        return response.json();
      })
      .then(response => {
        this.updateCount(response);
        this.miniCart(response);
      });
  }

  miniCart(items) {
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
                  data-sku="${item.sku}"
                  data-name="${item.name}"
                  data-category="${item.category}"
                >
                  <i class="fal fa-edit"></i>
                </a>
                
                <span class="navbar-remove-item">
                  <i
                    class="fal fa-times"
                    data-sku="${item.sku}"
                    data-name="${item.name}"
                    data-category="${item.category}"
                  ></i>
                </span>

              </div>
              
            </li>`;
          })
          .join("")}
        </ul>
      `;

      if (this.listContainer) {
        this.removeFromMiniCart();
      }
    } else {
      this.listContainer.innerHTML = `<h4 class="navbar-bag-empty">Your cart is empty...</h4>`;
    }
  }

  updateCount(items) {
    if (items.length > 0) {
      let count = 0;
      items.map(item => {
        if (item.count) {
          count = count + item.count;
        }
      });
      const position = this.counter.getBoundingClientRect();
      this.counter.innerHTML = count;

      const burst = new mojs.Burst({
        parent: this.counter.parentElement,
        top: position.y + 16,
        left: position.x + 6,
        radius: { 10: 19 },
        angle: 45,
        children: {
          shape: "line",
          radius: 4,
          scale: 2,
          stroke: "#195675",
          strokeDasharray: "100%",
          strokeDashoffset: { "-100%": "100%" },
          duration: 400,
          easing: "quad.out"
        },
        duration: 500
      });
      burst.replay();
    } else {
      this.counter.innerHTML = 0;
    }
  }

  removeFromKit() {
    const removeGroup = this.removeFromKitButtons;
    if (removeGroup.length > 0) {
      removeGroup.forEach( el => {
        el.addEventListener("click", e => {
          e.preventDefault();
          if (el.dataset) {
            let target = el.dataset;
            let product_id = target.product_id ? target.product_id : undefined;
            let category_id = target.category_id ? target.category_id : undefined;

            if (product_id !== undefined && category_id !== undefined) {
              let removeItem = {
                'product_id':product_id,
                'category_id':category_id
              };

              let parentNode =
              el.parentElement.parentElement.parentElement.parentElement.parentElement || undefined;
              if (parentNode) {
                parentNode.style.overflow = "hidden";
                KUTE.to(
                  parentNode,
                  {
                    width: 0,
                    opacity: 0
                  },
                  {
                    easing: "easingCubicOut",
                    duration: 300,
                    complete: () => {
                      parentNode.parentElement.removeChild(parentNode);
                    }
                  }
                ).start();

                this.removeItemAPI(removeItem);
              }
            }
          }
        });
      });
    }
  }

  // sets the session to be in the "add item to kit" state (or unset it)
  setKitStateAPI(kitID, isAdd, redirectURL) {
    let setKitStateURL = endpoints.setKitState + '/' + kitID + '/' + isAdd;
    fetch(setKitStateURL, {
      method: "POST",
      credentials: "include",
      headers: {
        "Content-Type": "application/json"
      }
    })
    .then(res => res.json())
    .catch(error => console.error("Error:", error))
    .then(response => {
      if (response.error) {
      } else {
        // need to set timeoout here
        setTimeout(function() {
          document.location = redirectURL;
        },100);
      }
    })
  }

  swapItem() {
    const swaps = this.swapFromCartButtons;
    if (swaps.length > 0) {
      swaps.forEach(swap => {
        swap.addEventListener("click", e => {
          e.preventDefault();
          let el = swap.dataset;
          let kit_id = el.kit_id ? el.kit_id : 0;
          let cat_id = el.cat_id ? el.cat_id : 0;
          let prod_id = el.prod_id ? el.prod_id : 0;

          let swapURL = endpoints.swapItemFromKit;
          // add to kit is: kit_id, product_id, cat_id
          swapURL += '/' + kit_id + '/' + prod_id + '/' + cat_id;
          fetch(swapURL, {
            method: "POST",
            credentials: "include",
            headers: {
              "Content-Type": "application/json"
            }
          })
          .then(res => res.json())
          .catch(error => console.error("Error:", error))
          .then(response => {
            if (response.error) {
            } else {
              if (response.url) {
                setTimeout(function() {
                  document.location.href = response.url;
                },100);
              }
            }
          });
        });
      });
    }
  }

  removeItemAPI(item) {
    let kit_id = document.getElementById('bl_kit_id');
    if (kit_id && kit_id.value) {
      kit_id = parseInt(kit_id.value);
    }
    let removeURL = endpoints.removeFromCart;
    // if there is a kit ID, that means we're removing it from the kit. Otherwise, we are removing it from the cart
    if (kit_id > 0 && item.product_id) {
      removeURL = endpoints.removeFromKit+'/'+kit_id+'/'+item.product_id;
      if (item.category_id) {
        removeURL += '/'+item.category_id;
      }
    }
    fetch(removeURL, {
      method: "POST",
      credentials: "include",
      headers: {
        "Content-Type": "application/json"
      }
    })
      .then(res => res.json())
      .catch(error => console.error("Error:", error))
      .then(response => {
        if (response.error) {
        } else {
          this.updateCount(response);
          this.miniCart(response);
          this.updateTileQuantity(response, item);
        }
      });
  }

  updateTileQuantity(response, item) {
    const buttons = this.addToCartButtons;
    let targetButton;
    let i, o;
    let count;

    const sku = item.sku;
    const category = item.category;

    for (o = 0; o < response.length; o++) {
      const scope = response[o];
      if (sku === scope.sku && category === scope.category) {
        count = scope.count;
      }
    }

    for (i = 0; i < buttons.length; i++) {
      const button = buttons[i];
      const buttonSku = button.dataset.sku;
      const buttonCategory = button.dataset.category;
      if (sku === buttonSku && category === buttonCategory) {
        targetButton = button;
        break;
      }
    }

    const removeIcon = targetButton.querySelector(".fa-minus-circle");
    const buttonText = targetButton.querySelector(".add-to-cart-text");
    const defaultText = buttonText.dataset.defaultText;
    const cartText = buttonText.dataset.cartText;

    if (count) {
      buttonText.innerHTML = `${count} ${cartText}`;
    } else {
      buttonText.innerHTML = defaultText;
      targetButton.classList.remove("in-cart");
      removeIcon.classList.add("hidden");
    }

    //
  }

  removeFromMiniCart() {
    const cartItems = this.listContainer.querySelectorAll(
      ".navbar-remove-item"
    );

    cartItems.forEach(item => {
      item.addEventListener("click", e => {
        e.preventDefault();

        if (this.target.dataset) {
          let target = e.target.dataset;
          let product_id = target.product_id ? target.product_id : undefined;
          let category_id = target.category_id ? target.category_id : undefined;

          if (product_id !== undefined && category_id !== undefined) {
            let removeItem = {
              'product_id':product_id,
              'category_id':category_id
            };

            this.removeItemAPI(removeItem);
          }
        }
      });
    });
  }

  addItem() {
    if (this.addToCartButtons.length > 0) {
      this.addToCartButtons.forEach(button => {
        const removeItemIcon = button.querySelector(".fa-minus-circle");
        const addItemIcon = button.querySelector(".fa-plus-circle");
        const text = button.querySelector(".add-to-cart-text");
        const inCartText = text.dataset.cartText;

        addItemIcon.addEventListener("click", e => {
          e.preventDefault();
          let el = addItemIcon.dataset;
          let kit_id = el.kit_id ? el.kit_id : 0;
          let cat_id = el.cat_id ? el.cat_id : 0;
          let prod_id = el.prod_id ? el.prod_id : 0;
          let swap = el.swap ? el.swap : 0;

          let addURL = endpoints.addToCart;
          if (kit_id > 0) {
            addURL = endpoints.addToKit;
          }
          if (swap > 0 && kit_id > 0) {
            addURL = endpoints.selectSwap;
          }
          // add to kit is: kit_id, product_id, cat_id
          addURL += '/' + kit_id + '/' + prod_id + '/' + cat_id;

            fetch(addURL, {
              method: "POST",
              credentials: "include",
              headers: {
                "Content-Type": "application/json"
              }
            })
              .then(res => res.json())
              .catch(error => console.error("Error:", error))
              .then(response => {
                if (response.error) {
                } else {

                  if ( typeof response.items != 'undefined') {
                    this.updateCount(response.items);
                    this.miniCart(response.items);
                  }
                  if (typeof response.url != 'undefined') {
                    // we will get a return URL
                    setTimeout(function() {
                      document.location.href = response.url;
                    },100); // setTimeout to bust promise
                    
                  }
                }
              });
          });
      });
    }
  }

  removeItem() {
    const removeIcons = this.removeIcons;
    if (removeIcons.length > 0) {
      removeIcons.forEach(removeIcon => {
        removeIcon.addEventListener("click", e => {
          e.preventDefault();

          if (e.target.dataset) {
            const target = e.target.dataset;
            const sku = target.sku ? target.sku : undefined;
            const category = target.category ? target.category : undefined;

            if (sku !== undefined && category !== undefined) {
              const button = e.target.parentElement;
              const text = button.querySelector(".add-to-cart-text");
              const inCartText = text.dataset.cartText;
              const defaultText = text.dataset.defaultText;

              const removeItem = {
                sku,
                category
              };
              fetch(endpoints.removeFromCart, {
                method: "POST",
                credentials: "include",
                body: JSON.stringify(removeItem),
                headers: {
                  "Content-Type": "application/json"
                }
              })
                .then(res => res.json())
                .catch(error => console.error("Error:", error))
                .then(response => {
                  if (response.error) {
                  } else {
                    this.updateCount(response);
                    this.miniCart(response);
                    // TODO: DRY
                    const match = response.filter(item => {
                      return item.sku === sku && item.category === category;
                    });

                    if (match.length > 0) {
                      text.innerHTML = match[0].count + inCartText;
                      button.classList.add("in-cart");
                      removeIcon.classList.remove("hidden");
                    } else {
                      button.classList.remove("in-cart");
                      text.innerHTML = defaultText;
                      removeIcon.classList.add("hidden");
                    }
                  }
                });
            }
          }
        });
      });
    }
  }
}
