export class ProductQuantity {
  constructor(target) {
    this.target = document.querySelector(target) || undefined;
  }
  init() {
    if (this.target) {
      this.enable();
    }
  }
  enable() {}
}
