import KUTE from "kute.js";
import inView from "in-view";
import { debounce } from "lodash";

export class CategoryMenu {
  constructor() {
    this.openTrigger =
      document.querySelector("#category-list-all-header") || undefined;
    this.menu = document.querySelector("#category-list") || undefined;
    this.categoryList =
      document.querySelector("#category-list-wrapper") || undefined;
    this.parentCategoryContainer =
      document.querySelectorAll(".category-list-parent-group") || undefined;
    this.subCategories =
      document.querySelectorAll(".category-list-sub-items-group") || undefined;
    this.productGridContainer =
      document.querySelector("#shop-landing-featured-products") || undefined;
    this.categoryClone = this.categoryList
      ? this.categoryList.cloneNode(true)
      : undefined;
  }
  init() {
    if (this.menu) {
      this.enable();
    }
  }

  enable() {
    if (this.parentCategoryContainer) {
      const breakpoint = window.matchMedia("(min-width:840px)");
      if (breakpoint.matches) {
        // console.log(breakpoint);
        this.attachCategoryToggles();
        this.stickyCategoryNav();
      }
      // console.log(this.categoryClone.innerHTML);
    }
  }

  mobile() {}

  stickyCategoryNav() {
    const selector = `#${this.menu.id}`;
    const check = inView.is(this.categoryList);

    check
      ? this.menu.classList.remove("nav-fixed")
      : this.menu.classList.add("nav-fixed");

    inView(selector)
      .on("enter", el => {
        el.classList.remove("nav-fixed");
      })
      .on("exit", el => {
        const check = el.classList.contains("nav-fixed");
        !check ? el.classList.add("nav-fixed") : undefined;
      });
  }

  attachCategoryToggles() {
    this.parentCategoryContainer.forEach(item => {
      const parent = item.querySelector(".category-list-parent");
      const child = item.querySelector(".category-list-sub-items-group");

      parent.addEventListener("click", e => {
        e.preventDefault();
        this.toggleAll();
        this.toggleSubCategory(child, parent);
      });
    });
  }

  toggleAll() {
    let i;
    const parents = this.parentCategoryContainer;
    for (i = 0; i < parents.length; ++i) {
      const parent = parents[i].querySelector(".category-list-parent");
      const child = parents[i].querySelector(".category-list-sub-items-group");

      if (parent.classList.contains("active") === true) {
        parent.classList.remove("active");
      }
      if (child.classList.contains("active") === true) {
        child.classList.remove("active");
      }
    }
  }

  toggleSubCategory(target, parent) {
    const showSubCategory = KUTE.fromTo(
      target,
      {
        maxHeight: 0,
        opacity: 0
      },
      {
        maxHeight: 500,
        opacity: 1
      },
      {
        duration: 150,
        complete: () => {
          target.classList.toggle("active");
          parent.classList.toggle("active");
          if (this.productGridContainer) {
            const targetCategory = target.dataset.categoryId;
            targetCategory
              ? this.scrollFeaturedCategory(targetCategory)
              : undefined;
          }
        }
      }
    );
    showSubCategory.start();
  }

  scrollFeaturedCategory(id) {
    const targetCategory = id;
    const target =
      this.productGridContainer.querySelector(`#category-${id}`) || undefined;

    if (target) {
      const position = target.getBoundingClientRect();
      const absoluteElementTop = position.top + window.pageYOffset;
      const middle = absoluteElementTop - window.innerHeight / 2 + 500;
      KUTE.to(
        "window",
        { scroll: middle },
        { easing: "easingCubicOut", duration: 500 }
      ).start();
    }
  }
}
