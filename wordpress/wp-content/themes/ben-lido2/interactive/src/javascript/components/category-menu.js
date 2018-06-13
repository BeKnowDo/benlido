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
    this.categoryList =
      document.querySelector("#category-list-wrapper") || undefined;
    this.menuCategoryHeader =
      document.querySelector("#category-list-breadcrumbs") || undefined;
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
      this.mobile();
      this.checkBreakpoint();
      this.attachCategoryToggles();
      this.stickyCategoryNav();
    }
  }

  mobile() {
    if (
      this.categoryList !== undefined &&
      this.menuCategoryHeader !== undefined
    ) {
      const menu = this.menu;
      const mobileMenu = this.categoryClone;

      mobileMenu.removeAttribute("class");
      mobileMenu.classList.add("mobile-navigation");
      mobileMenu.removeAttribute("id");

      const menuHeader = mobileMenu.querySelector(".category-list-breadcrumbs");
      const categories = mobileMenu.querySelectorAll(
        ".category-list-parent-group"
      );
      menuHeader.removeAttribute("id");
      const parentCategories = this.parentCategoryContainer;

      // Toggle showing categories (parents)
      menuHeader.addEventListener("click", e => {
        e.preventDefault();
        mobileMenu.classList.toggle("show-categories");
      });

      // Attach parent category toggle
      categories.forEach(category => {
        category.addEventListener("click", e => {
          e.preventDefault();
          e.stopPropagation();
          const target = e.target.parentElement.parentElement.querySelector(
            ".category-list-sub-items-group"
          );

          this.toggleAll(categories);
          this.toggleSubCategory(target);
        });
      });

      menu.appendChild(this.categoryClone);
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
          el.classList.remove("nav-fixed");
        })
        .on("exit", el => {
          const check = el.classList.contains("nav-fixed");
          !check ? el.classList.add("nav-fixed") : undefined;
        });
    }
  }

  checkBreakpoint() {
    const breakpoint = window.matchMedia("(min-width:839px)");

    const breakpointChecker = () => {
      if (breakpoint.matches) {
        // Desktop navigation version
        this.desktopNav = true;
        this.mobileNav = false;
      } else if (!breakpoint.matches) {
        // Mobile navigation version
        this.mobileNav = true;
        this.desktopNav = false;

        // remove sticky if enabled
        this.menu.classList.remove("nav-fixed");
        this.toggleAll(this.parentCategoryContainer);
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
        e.preventDefault();
        this.toggleAll(this.parentCategoryContainer);
        this.toggleSubCategory(child);
      });
    });
  }

  toggleAll(parentContainer) {
    let i;
    const parents = parentContainer;
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

  toggleSubCategory(target) {
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
          if (this.desktopNav === true) {
            if (this.productGridContainer) {
              const targetCategory = target.dataset.categoryId;
              targetCategory
                ? this.scrollFeaturedCategory(targetCategory)
                : undefined;
            }
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
