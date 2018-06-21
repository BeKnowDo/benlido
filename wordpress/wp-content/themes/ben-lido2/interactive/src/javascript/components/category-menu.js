import KUTE from "kute.js";
import inView from "in-view";
import { debounce } from "lodash";

export class CategoryMenu {
  constructor() {
    this.mobileNav = false;
    this.desktopNav = false;

    this.menu = document.getElementById("category-list") || undefined;

    this.openTrigger =
      document.getElementById("category-list-all-header") || undefined;

    this.categoryList =
      document.getElementById("category-list-wrapper") || undefined;

    this.menuCategoryHeader =
      document.getElementById("category-list-breadcrumbs") || undefined;

    this.parentCategoryContainer =
      document.querySelectorAll(".menu-item-has-children") || undefined;

    // this.subCategories = document.querySelectorAll(".sub-menu") || undefined;

    this.productGridContainer =
      document.getElementById("shop-landing-featured-products") || undefined;
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
      const fragment = document.createDocumentFragment();
      const menu = this.menu;
      const mobileMenu = this.categoryList.cloneNode(true);
      const menuHeader = mobileMenu.querySelector(".category-list-breadcrumbs");
      const categories = mobileMenu.querySelectorAll(".menu-item-has-children");

      mobileMenu.removeAttribute("class");
      mobileMenu.classList.add("mobile-navigation");
      mobileMenu.removeAttribute("id");
      menuHeader.removeAttribute("id");

      // Toggle showing categories (parents)
      menuHeader.addEventListener("click", e => {
        e.preventDefault();
        mobileMenu.classList.toggle("show-categories");
      });

      // Attach parent category toggle
      categories.forEach(category => {
        category.addEventListener("click", e => {
          e.stopPropagation();
          e.preventDefault();

          const target = category.querySelector(".sub-menu");

          this.toggleAll();
          this.toggleSubCategory(target);
        });
      });

      fragment.appendChild(mobileMenu);
      menu.appendChild(fragment);

      // now that we've cloned the parent category list container and items,
      // we'll need to reassign this.parentCategoryContainer to include both desktop and mobile items
      // this way we can reuse toggleAll()
      this.parentCategoryContainer = document.querySelectorAll(
        ".menu-item-has-children"
      );
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
      const parent = item.querySelector("a") || undefined;
      const child = item.querySelector(".sub-menu") || undefined;

      if (parent && child) {
        parent.addEventListener("click", e => {
          e.preventDefault();
          e.stopPropagation();
          this.toggleAll();
          this.toggleSubCategory(child);
        });
      }
    });
  }

  toggleAll() {
    let i;
    const parents = this.parentCategoryContainer;

    parents.forEach(item => {
      const parent = item;
      const child = parent.querySelector(".sub-menu");

      if (parent && child) {
        if (parent.classList.contains("active") === true) {
          parent.classList.remove("active");
        }
        if (child.classList.contains("active") === true) {
          child.classList.remove("active");
        }
      }
    });
  }

  toggleSubCategory(target) {
    if (target) {
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
