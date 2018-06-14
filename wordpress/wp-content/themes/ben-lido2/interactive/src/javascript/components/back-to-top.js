import KUTE from "kute.js";

export class ScrollToTop {
  constructor() {
    this.clickTarget = document.getElementById("back-to-top") || undefined;
  }
  init() {
    if (this.clickTarget) {
      this.enable();
    }
  }

  enable() {
    this.clickTarget.onclick = () => {
      KUTE.to(
        "window",
        { scroll: 0 },
        { easing: "easingCubicOut", duration: 500 }
      ).start();
    };
  }
}
