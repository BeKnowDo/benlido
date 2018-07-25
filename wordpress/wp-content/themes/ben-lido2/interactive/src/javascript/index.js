import {
  Navigation,
  ScrollToTop,
  ProductImageCarousel,
  CategoryMenu,
  Cart,
  Search,
  Frequency
} from "./components";

const initializeNavigation = new Navigation().init();

const cart = new Cart().init();
const initializeScrollToTop = new ScrollToTop().init();
const productCarousels = new ProductImageCarousel().init();
const categoryMenu = new CategoryMenu().init();
const search = new Search().init();
const frequency = new Frequency().init();
