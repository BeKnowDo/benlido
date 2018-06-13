import KUTE from "kute.js";
import inView from "in-view";
import { debounce } from "lodash";

export class CategoryMenu {
  constructor() {
    this.mobileNav = false;
    this.desktopNav = false;

    this.openTrigger =
      document.querySelector("#category-list-all-header") || undefined;
    this.menu = document.querySelector("#category-list") || undefined;
    this.menuCategoryHeader =
      document.querySelector("#category-list-breadcrumbs") || undefined;
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
      this.checkBreakpoint();
      this.attachCategoryToggles();
      this.stickyCategoryNav();
      this.mobile();
    }
  }

  mobile() {
    if (
      this.categoryList !== undefined &&
      this.menuCategoryHeader !== undefined
    ) {
      const menu = this.menu;
      const menuHeader = this.menuCategoryHeader;
      const parentCategories = this.parentCategoryContainer;

      parentCategories.forEach(item => {
        const parent = item.querySelector(".category-list-parent");
        const child = item.querySelector(".category-list-sub-items-group");

        parent.addEventListener("click", e => {
          if (this.mobileNav === true) {
            e.preventDefault();
            console.log(this.mobileNav);
          }
        });
      });
    }
  }

  mobileStickyNav() {
    const breakpoint = window.matchMedia("(min-width:839px)");
    if (breakpoint.matches) {
      const selector = `#${this.menu.id}`;
      const check = inView.is(this.categoryList);

      check
        ? this.menu.classList.remove("mobile-active")
        : this.menu.classList.add("mobile-active");

      inView(selector)
        .on("enter", el => {
          if (this.mobileNav === true) {
            el.classList.remove("mobile-active");
          }
        })
        .on("exit", el => {
          if (this.mobileNav === true) {
            const check = el.classList.contains("mobile-active");
            !check ? el.classList.add("mobile-active") : undefined;
          }
        });
    }
  }

  stickyCategoryNav() {
    // This is strictly for desktop
    const breakpoint = window.matchMedia("(min-width:839px)");
    if (breakpoint.matches) {
      const selector = `#${this.menu.id}`;
      const check = inView.is(this.categoryList);

      check
        ? this.menu.classList.remove("nav-fixed")
        : this.menu.classList.add("nav-fixed");

      inView(selector)
        .on("enter", el => {
          if (this.desktopNav === true) {
            el.classList.remove("nav-fixed");
          }
        })
        .on("exit", el => {
          if (this.desktopNav === true) {
            const check = el.classList.contains("nav-fixed");
            !check ? el.classList.add("nav-fixed") : undefined;
          } else if (this.mobileNav === true) {
            console.log("now in mobile nav");
          }
        });
    }
  }

  checkBreakpoint() {
    const breakpoint = window.matchMedia("(min-width:839px)");

    const breakpointChecker = () => {
      if (breakpoint.matches) {
        console.log("in desktop");
        // Desktop navigation version
        this.desktopNav = true;
        this.mobileNav = false;
      } else if (!breakpoint.matches) {
        console.log("in mobile");
        // Mobile navigation version
        this.mobileNav = true;
        this.desktopNav = false;

        // remove sticky if enabled
        this.menu.classList.remove("nav-fixed");
        this.toggleAll();
      }
    };

    breakpoint.addListener(debounce(breakpointChecker));

    breakpointChecker();
  }

  attachCategoryToggles() {
    this.parentCategoryContainer.forEach(item => {
      const parent = item.querySelector(".category-list-parent");
      const child = item.querySelector(".category-list-sub-items-group");

      parent.addEventListener("click", e => {
        if (this.desktopNav === true) {
          e.preventDefault();
          this.toggleAll();
          this.toggleSubCategory(child, parent);
          console.log(e);
        }
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
