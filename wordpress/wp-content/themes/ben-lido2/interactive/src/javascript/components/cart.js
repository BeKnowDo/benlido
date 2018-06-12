import { endpoints } from "../../../config/endpoints";
import KUTE from "kute.js";
import mojs from "mo-js";

export class Cart {
  constructor() {
    this.counter = document.querySelector("#navbar-item-counter") || undefined;
    this.listContainer =
      document.querySelector("#navbar-bag-list") || undefined;
    this.addToCartButtons =
      document.querySelectorAll(".add-to-cart") || undefined;
    this.removeFromKitButtons =
      document.querySelectorAll(".remove-from-cart") || undefined;
    this.removeIcons =
      document.querySelectorAll(".fa-minus-circle") || undefined;
    this.swapFromCartButtons =
      document.querySelectorAll(".swap-from-cart") || undefined;
    this.cart = document.querySelector("#benlido-cart") || undefined;
    this.cartContainer =
      document.querySelector("#navbar-bag-container") || undefined;
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

    if (this.cart) {
      this.openCart();
    }
  }

  openCart() {
    this.cart.addEventListener("click", e => {
      e.preventDefault();
      this.cartContainer.classList.toggle("active");
    });
  }

  getCurrentItems() {
    fetch(endpoints.getCartItems)
      .then(function(response) {
        return response.json();
      })
      .then(response => {
        this.updateCount(response);
        this.fillCart(response);
      });
  }

  fillCart(items) {
    if (items.length > 0) {
      this.listContainer.innerHTML = `
        <ul class="navbar-bag-list-container">
        ${items
          .map(item => {
            return `<li class="navbar-bag-item columns col-gapless">

              <p class="column col-7 navbar-product-name">${
                item.count
              }x &nbsp; ${item.name} ${item.sku}</p>

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
        left: position.x + 2,
        radius: { 4: 19 },
        angle: 45,
        children: {
          shape: "line",
          radius: 6,
          scale: 2,
          stroke: "#195675",
          strokeDasharray: "100%",
          strokeDashoffset: { "-100%": "100%" },
          duration: 400,
          easing: "quad.out"
        },
        duration: 500,
        onComplete() {}
      });

      burst.replay();

      document.addEventListener("click", function(e) {
        burst
          .tune({ x: e.pageX, y: e.pageY })
          .setSpeed(3)
          .replay();
      });
    } else {
      this.counter.innerHTML = 0;
    }
  }

  removeFromKit() {
    const remove = this.removeFromKitButtons;
    if (remove.length > 0) {
      remove.forEach(swap => {
        swap.addEventListener("click", e => {
          e.preventDefault();

          if (e.target.dataset) {
            const target = e.target.dataset;
            const sku = target.sku ? target.sku : undefined;
            const category = target.category ? target.category : undefined;

            if (sku !== undefined && category !== undefined) {
              const removeItem = {
                sku,
                category
              };

              let parentNode =
                e.target.parentElement.parentElement.parentElement || undefined;
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

  swapItem() {
    const swaps = this.swapFromCartButtons;
    if (swaps.length > 0) {
      swaps.forEach(swap => {
        swap.addEventListener("click", e => {
          e.preventDefault();
          // User sends the product they wish to swap out
          // Then we direct them to the shop landing page
        });
      });
    }
  }

  removeItemAPI(item) {
    fetch(endpoints.removeFromCart, {
      method: "POST",
      body: JSON.stringify(item),
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
          this.fillCart(response);
          this.updateTileQuantity(response);
        }
      });
  }

  updateTileQuantity(response) {}

  removeFromMiniCart() {
    const cartItems = this.listContainer.querySelectorAll(
      ".navbar-remove-item"
    );

    cartItems.forEach(item => {
      item.addEventListener("click", e => {
        e.preventDefault();

        if (e.target.dataset) {
          const target = e.target.dataset;
          const sku = target.sku ? target.sku : undefined;
          const category = target.category ? target.category : undefined;

          if (sku !== undefined && category !== undefined) {
            const removeItem = {
              sku,
              category
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

        // button.addEventListener("click", e => {});

        addItemIcon.addEventListener("click", e => {
          e.preventDefault();
          const addIcon = e.target;
          const sku = addIcon.dataset.sku ? addIcon.dataset.sku : undefined;
          const category = addIcon.dataset.category
            ? addIcon.dataset.category
            : undefined;
          const name = addIcon.dataset.name ? addIcon.dataset.name : undefined;

          if (sku !== undefined && category !== undefined) {
            const newItem = {
              sku,
              category,
              name
            };

            fetch(endpoints.addToCart, {
              method: "POST",
              body: JSON.stringify(newItem),
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
                  this.fillCart(response);
                  // TODO: DRY
                  const match = response.filter(item => {
                    return item.sku === sku && item.category === category;
                  });

                  if (match.length > 0) {
                    text.innerHTML = match[0].count + inCartText;
                    button.classList.add("in-cart");
                    removeItemIcon.classList.remove("hidden");
                  }
                }
              });
          }
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
                    this.fillCart(response);
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
