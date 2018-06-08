export class ProductQuantity {
  constructor() {
    this.target = document.querySelector(".product-quantity") || undefined;
  }
  init() {
    if (this.target) {
      this.enable();
    }
  }
  enable() {}
}
