import KUTE from "kute.js";

export class CategoryMenu {
  constructor() {
    this.openTrigger =
      document.querySelector("#category-list-all-header") || undefined;
    this.menu = document.querySelector("#category-list-wrapper") || undefined;
    this.subCategories = document.querySelectorAll(
      ".category-list-sub-items-group"
    );
  }
  init() {
    if (this.menu) {
      this.enable();
    }
  }

  enable() {
    this.navigation();
    this.parentCategories();
  }

  navigation() {
    // add toggling event to target
    if (this.openTrigger) {
      this.openTrigger.onclick = () => {
        this.menu.classList.contains("active")
          ? this.closeNavigationAnimation()
          : this.openNavigationAnimation();
      };
    }
  }

  openNavigationAnimation() {
    const revealAnimation = KUTE.fromTo(
      this.menu,
      { translate3d: ["-100%", 0, 0], opacity: 0 },
      { translate3d: [0, 0, 0], opacity: 1 },
      {
        duration: 150,
        complete: () => {
          this.menu.classList.toggle("active");
        }
      }
    );
    revealAnimation.start();
  }

  closeNavigationAnimation() {
    const revealAnimation = KUTE.fromTo(
      this.menu,
      { translate3d: [0, 0, 0], opacity: 1 },
      { translate3d: ["-100%", 0, 0], opacity: 0 },
      {
        duration: 150,
        complete: () => {
          this.menu.classList.toggle("active");
        }
      }
    );
    revealAnimation.start();
  }

  parentCategories() {
    const parents = this.menu.querySelectorAll(".category-list-parent a");
    parents.forEach(item => {
      item.addEventListener("click", e => {
        e.preventDefault();
        const target = e.target;
        const category = target.dataset.categoryId;

        this.closeNavigationAnimation();

        const targetCategory = this.getSubCategory(category);

        if (targetCategory) {
          this.openSubCategory(targetCategory);
        }
      });
    });
  }

  getSubCategory(id) {
    const targetCategory = id;

    const result = Array.from(this.subCategories).find(category => {
      if (category.dataset.categoryId === targetCategory) {
        return category;
      }
    });

    return result;
  }

  openSubCategory(target) {
    const revealAnimation = KUTE.fromTo(
      target,
      { translate3d: ["-150%", 0, 0], opacity: 0 },
      { translate3d: [0, 0, 0], opacity: 1 },
      {
        duration: 150,
        complete: () => {
          target.classList.toggle("active");
        }
      }
    );
    revealAnimation.start();
  }

  closeSubCategory(target) {
    const revealAnimation = KUTE.fromTo(
      target,
      { translate3d: [0, 0, 0], opacity: 1 },
      { translate3d: ["-150%", 0, 0], opacity: 0 },
      {
        duration: 150,
        complete: () => {
          target.classList.toggle("active");
        }
      }
    );
    revealAnimation.start();
  }
}
