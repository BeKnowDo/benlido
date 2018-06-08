import { endpoints } from "../../../config/endpoints";

export class Cart {
  constructor() {
    this.counter = document.querySelector("#navbar-item-counter") || undefined;
    this.listContainer =
      document.querySelector("#navbar-bag-list") || undefined;
    this.addToCartButtons =
      document.querySelectorAll(".add-to-cart") || undefined;
    this.removeFromCartButton =
      document.querySelectorAll(".remove-from-cart") || undefined;
    this.swapFromCartButton =
      document.querySelectorAll(".swap-from-cart") || undefined;
    this.cart = document.querySelector("#benlido-cart") || undefined;
    this.cartContainer =
      document.querySelector("#navbar-bag-container") || undefined;
  }

  init() {
    this.enable();
  }

  enable() {
    if (this.counter) {
      this.getCurrentItems();
    }

    if (this.addToCartButtons) {
      this.addItem();
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
        this.removeItem();
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
      this.counter.innerHTML = count;
    } else {
      this.counter.innerHTML = 0;
    }
  }

  receiveItem() {}

  removeItem() {
    const cartItems = this.listContainer.querySelectorAll(
      ".navbar-remove-item"
    );

    cartItems.forEach(item => {
      item.addEventListener("click", e => {
        e.preventDefault();
        if (e.target.dataset) {
          const target = e.target.dataset;
          const sku = target.sku ? target.sku : undefined;
          const name = target.name ? target.name : undefined;
          const category = target.category ? target.category : undefined;

          if (
            sku !== undefined &&
            name !== undefined &&
            category !== undefined
          ) {
            const removeItem = {
              sku,
              name,
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
                }
              });
          }
        }
      });
    });
  }

  swapItem() {}

  addItem() {
    if (this.addToCartButtons.length > 0) {
      this.addToCartButtons.forEach(button => {
        button.addEventListener("click", e => {
          e.preventDefault();

          const productID = e.target.dataset.sku
            ? e.target.dataset.sku
            : undefined;

          const categoryID = e.target.dataset.category
            ? e.target.dataset.category
            : undefined;

          const productName = e.target.dataset.name
            ? e.target.dataset.name
            : undefined;

          if (
            productID !== undefined &&
            categoryID !== undefined &&
            productName !== undefined
          ) {
            const newItem = {
              sku: productID,
              category: categoryID,
              name: productName
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
                }
              });
          }
          this.cartContainer.classList.add("active");
        });
      });
    }
  }
}
