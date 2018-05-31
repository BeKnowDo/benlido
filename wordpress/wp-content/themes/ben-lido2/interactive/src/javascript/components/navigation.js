import KUTE from "kute.js";

export class Navigation {
  constructor(openTrigger, menu, closeTrigger, overlay) {
    this.openTrigger = document.querySelector(openTrigger) || undefined;
    this.menu = document.querySelector(menu) || undefined;
    this.closeTrigger = document.querySelector(closeTrigger) || undefined;
    this.overlay = document.querySelector(overlay) || undefined;
  }
  init() {
    this.enable();
  }

  enable() {
    this.openNavigation();
    this.closeNavigation();
    this.handleOverlayClick();
  }

  openNavigation() {
    // add toggling event to target
    this.openTrigger
      ? (this.openTrigger.onclick = () => {
          this.openNavigationAnimation();
        })
      : undefined;
  }

  openNavigationAnimation() {
    const revealAnimation = KUTE.fromTo(
      this.menu,
      { translate3d: [0, "-100%", 0], opacity: 0 },
      { translate3d: [0, 0, 0], opacity: 1 },
      { duration: 150 }
    );
    revealAnimation.start();
    this.toggleOverlay(this.overlay);
  }

  closeNavigation() {
    this.closeTrigger
      ? (this.closeTrigger.onclick = () => {
          this.closeAnimationAnimation();
        })
      : undefined;
  }

  closeAnimationAnimation() {
    const hideAnimation = KUTE.fromTo(
      this.menu,
      { translate3d: [0, 0, 0], opacity: 1 },
      { translate3d: [0, "-100%", 0], opacity: 0 },
      { duration: 150 }
    );
    hideAnimation.start();
    this.toggleOverlay(this.overlay);
  }

  toggleOverlay() {
    if (this.overlay) {
      this.overlay.classList.toggle("active");
    }
  }

  handleOverlayClick() {
    this.overlay
      ? (this.overlay.onclick = () => {
          this.closeAnimationAnimation();
        })
      : undefined;
  }
}
