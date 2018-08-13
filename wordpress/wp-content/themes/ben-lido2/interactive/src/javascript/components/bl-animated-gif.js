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

    documentFragment.appendChild(imageTag);
    this.destination.appendChild(documentFragment);

    const startRange = 1;
    const endRange = 19;

    let i = startRange;
    let countUp = true;
    let loopCount = 0;

    let intervalID = window.setInterval(animate, interval);

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

      if (loopCount > 4) {
        window.clearInterval(intervalID);
      }
    }
  }
}
