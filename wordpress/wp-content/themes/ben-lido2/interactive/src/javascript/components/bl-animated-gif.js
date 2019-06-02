const inViewport = require("in-viewport");

export class BenLidoAnimations {
  constructor() {
    this.destination = document.querySelector("#bl-animated-gif") || undefined;
  }

  init() {
    if (this.destination !== undefined) {
      this.callAnimation();
    }
  }
  callAnimation() {
    const interval = 250;

    const path = `/wp-content/themes/ben-lido2/assets/images/bag-animation`;
    const documentFragment = document.createDocumentFragment();
    const imageTag = document.createElement("img");

    imageTag.src = `${path}/BL__1.jpg`;
    imageTag.alt = `Ben Lido Bag Animation`;

    documentFragment.appendChild(imageTag);
    this.destination.appendChild(documentFragment);

    const startRange = 1;
    const endRange = 19;

    let i = startRange;
    let countUp = true;
    let loopCount = 0;

    function animate() {
      imageTag.src = `${path}/BL__${i}.jpg`;

      if (countUp) {
        i++;
        if (i >= endRange) {
          countUp = false;
          loopCount++;
        }
      } else {
        i--;
        if (i <= startRange) {
          countUp = true;
        }
      }
    }

    let intervalID;

    const runAnimation = () => {
      intervalID = setInterval(animate, interval);
    };

    const stopAnimation = () => {
      clearInterval(intervalID);
    };

    const isInViewport = inViewport(
      this.destination,
      { debounce: 300 },
      visible
    );

    function visible() {
      isInViewport ? runAnimation() : stopAnimation();
    }
  }
}
