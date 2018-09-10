export class LidoBagDetail {
  constructor() {
    this.swatches = document.querySelector("#picker_pa_color") || undefined;
    this.thumbnails =
      document.querySelector(".flex-control-thumbs") || undefined;

    this.bagSwatches =
      document.querySelectorAll(".bl-product-swatches") || undefined;
  }
  init() {
    if (this.swatches) {
      this.attachClick();
      // this.attachHover();
    }

    if (this.bagSwatches) {
      this.defaultBagColor();
    }
  }

  defaultBagColor() {
    const bagSwatches = this.bagSwatches;
    let activatedSwatchIndex = 0;

    bagSwatches.forEach((bagSwatch, index) => {
      const swatches = bagSwatch.querySelectorAll(".swatch-anchor");

      let i = 0;
      let swatchLength = swatches.length;

      for (i; i < swatchLength; i++) {
        const scope = swatches[i];
        if (scope.parentNode.classList.contains("selected")) {
          return false;
        }
      }

      activatedSwatchIndex = 0;
    });
  }

  attachClick() {
    const swatches = this.swatches.querySelectorAll(".select-option");
    swatches.forEach(item => {
      this.handleClick(item);
    });
  }

  handleClick(item) {
    const target = item;

    target.addEventListener("click", e => {
      // WooCommerce...grrrr
      const color = e.currentTarget.dataset.value;

      this.thumbnails === undefined
        ? (this.thumbnails = document.querySelector(".flex-control-thumbs"))
        : undefined;

      const images = this.thumbnails.querySelectorAll("img");

      let i = 0;
      for (i; i < images.length; i++) {
        const test = images[i].src.toString().indexOf(color) !== -1;
        if (test === true) {
          images[i].parentNode.classList.remove("bl-hide-thumbnail");
          images[i].parentNode.classList.add("bl-show-thumbnail");
        } else {
          images[i].parentNode.classList.add("bl-hide-thumbnail");
          images[i].parentNode.classList.remove("bl-show-thumbnail");
        }
      }
    });
  }

  attachHover() {
    const swatches = this.swatches.querySelectorAll(".select-option");
    swatches.forEach(element => {
      this.handleHover(element);
    });
  }

  handleHover(item) {
    item.addEventListener("mouseover", e => {
      e.preventDefault();
      e.stopPropagation();
      const target = e.currentTarget;
      target.click();
    });

    item.addEventListener("mouseout", e => {
      e.preventDefault();
      e.stopPropagation();
      const target = e.currentTarget;
      target.click();
    });
  }
}
