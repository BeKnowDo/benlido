import Selectivity from "selectivity";
require("selectivity/dropdown");
require("selectivity/inputs/single");

export class ProductQuantity {
  constructor(target) {
    this.target = document.querySelector(target) || undefined;
  }
  init() {
    if (this.target) {
      this.enable();
    }
  }
  enable() {
    const singleInput = new Selectivity.Inputs.Single({
      element: this.target
    });
  }
}
