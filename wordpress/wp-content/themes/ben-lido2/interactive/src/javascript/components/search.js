import KUTE from "kute.js";

export class Search {
  constructor() {
    this.openTrigger = document.getElementById("navbar-search") || undefined;
    this.overlay = document.getElementById("dimmed-overlay") || undefined;
    this.closeTrigger = document.getElementById("search-exit") || undefined;
    this.searchBox = document.getElementById("benlido-search-container");
  }

  init() {
    this.search();
    this.handleOverlayClick();
    this.openNavigation();
    this.closeNavigation();
  }

  search() {
    if (this.searchIcon) {
      const button = this.searchIcon;

      button.addEventListener("click", e => {
        e.preventDefault();
      });
    }
  }

  openNavigation() {
    // add toggling event to target
    this.openTrigger
      ? (this.openTrigger.onclick = () => {
          this.openNavigationAnimation();
        })
      : undefined;
  }

  closeNavigation() {
    this.closeTrigger
      ? (this.closeTrigger.onclick = () => {
          this.closeAnimationAnimation();
        })
      : undefined;
  }

  openNavigationAnimation() {
    const revealAnimation = KUTE.fromTo(
      this.searchBox,
      { translate3d: [0, "-100%", 0], opacity: 0 },
      { translate3d: [0, 0, 0], opacity: 1 },
      { duration: 150 }
    );
    revealAnimation.start();
    this.toggleOverlay(this.overlay);
  }

  closeAnimationAnimation() {
    const hideAnimation = KUTE.fromTo(
      this.searchBox,
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
