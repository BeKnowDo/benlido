import { Navigation, ScrollToTop } from "./components";

const initializeNavigation = new Navigation(
  "#navbar-trigger",
  "#navbar-dropdown",
  "#navbar-exit",
  "#dimmed-overlay"
).init();

const initializeScrollToTop = new ScrollToTop("#benlido-back-to-top").init();
