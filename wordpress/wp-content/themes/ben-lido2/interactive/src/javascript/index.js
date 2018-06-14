import {
  Navigation,
  ScrollToTop,
  ProductImageCarousel,
  ProductQuantity,
  CategoryMenu,
  Cart,
  Search
} from "./components";

const initializeNavigation = new Navigation().init();

const cart = new Cart().init();
const initializeScrollToTop = new ScrollToTop().init();
const productCarousels = new ProductImageCarousel().init();
const productQuantity = new ProductQuantity().init();
const categoryMenu = new CategoryMenu().init();
const search = new Search().init();
