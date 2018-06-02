import {
  Navigation,
  ScrollToTop,
  ProductImageCarousel,
  ProductQuantity
} from "./components";

const initializeNavigation = new Navigation(
  "#navbar-trigger",
  "#navbar-dropdown",
  "#navbar-exit",
  "#dimmed-overlay"
).init();

const initializeScrollToTop = new ScrollToTop("#benlido-back-to-top").init();
const productCarousels = new ProductImageCarousel(".swiper-container").init();
const productQuantity = new ProductQuantity(".product-quantity").init();
