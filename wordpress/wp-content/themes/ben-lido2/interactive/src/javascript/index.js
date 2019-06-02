import {
  Navigation,
  ScrollToTop,
  // ProductImageCarousel,
  CategoryMenu,
  Cart,
  Search,
  Frequency,
  MyAccount,
  StepNavigation,
  Parallax,
  BenLidoAnimations,
  NavigationPlatform,
  LidoBagDetail,
  CompactProductList
  // BenLidoBanners
  // BenHeader
} from './components'

// new BenHeader().init()
// new BenLidoBanners().init()
const navigation = new Navigation()
navigation.init()

const cart = new Cart()
cart.init()

const scrollToTop = new ScrollToTop()
scrollToTop.init()

// new ProductImageCarousel().init()

const categoryMenu = new CategoryMenu()
categoryMenu.init()

const search = new Search()
search.init()

const frequency = new Frequency()
frequency.init()

const myAccount = new MyAccount()
myAccount.init()

const stepNavigation = new StepNavigation()
stepNavigation.init()

const parralax = new Parallax()
parralax.init()

const benlidoAnimations = new BenLidoAnimations()
benlidoAnimations.init()

const navigationPlatform = new NavigationPlatform()
navigationPlatform.init()

const lidoBagDetail = new LidoBagDetail()
lidoBagDetail.init()

const compactProductList = new CompactProductList()
compactProductList.init()
