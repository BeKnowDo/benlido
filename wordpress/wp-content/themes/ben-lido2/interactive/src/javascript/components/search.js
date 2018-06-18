import KUTE from "kute.js";

export class Search {
  constructor() {
    this.openTrigger = document.getElementById("navbar-search") || undefined;
    this.closeTrigger = document.getElementById("search-exit") || undefined;
    this.searchBox =
      document.getElementById("benlido-search-container") || undefined;
  }

  init() {
    this.search();
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
    if (this.searchBox) {
      const revealAnimation = KUTE.fromTo(
        this.searchBox,
        { translate3d: [0, "-110%", 0], opacity: 0 },
        { translate3d: [0, 0, 0], opacity: 1 },
        { duration: 150 }
      );
      revealAnimation.start();
    }
  }

  closeAnimationAnimation() {
    if (this.searchBox) {
      const hideAnimation = KUTE.fromTo(
        this.searchBox,
        { translate3d: [0, 0, 0], opacity: 1 },
        { translate3d: [0, "-100%", 0], opacity: 0 },
        { duration: 150 }
      );
      hideAnimation.start();
    }
  }
}
