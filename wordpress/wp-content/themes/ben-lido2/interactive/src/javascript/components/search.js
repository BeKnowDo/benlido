import KUTE from "kute.js";

export class Search {
  constructor() {
    this.openTrigger = document.getElementById("navbar-search") || undefined;
    this.closeTrigger = document.getElementById("search-exit") || undefined;
    this.searchBox =
      document.getElementById("benlido-search-container") || undefined;
    this.searchButton =
      document.getElementById("navbar-search-button") || undefined;

    this.searchForm =
      document.getElementById("navbar-search-form") || undefined;
    this.searchInput =
      document.getElementById("benlido-search-input") || undefined;

    this.searchUrl = "?s=";
    this.keyword = null;
  }

  init() {
    this.openNavigation();
    this.closeNavigation();
    //this.updateUrl();
    this.submitForm();
  }

  submitForm() {
    if (this.searchForm) {
      this.searchForm.addEventListener("submit", e => {
        e.preventDefault();
        const url = e.target.getAttribute("action");
        window.location.href = url;
      });
    }
  }

  search() {
    if (this.searchButton) {
      const button = this.searchButton;

      button.addEventListener("click", e => {
        console.log(this.keyword);
        if (this.keyword !== null) {
          return true;
        } else {
          e.preventDefault();
        }
      });
    }
  }

  // not being used
  updateUrl() {
    if (this.searchInput) {
      let finalUrl;

      this.searchInput.addEventListener("keyup", e => {
        // console.log(e.target.value);
        this.keyword = e.target.value;
        finalUrl = `${this.searchUrl}${this.keyword}&post_type=product`;
        this.searchForm.setAttribute("action", finalUrl);
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
