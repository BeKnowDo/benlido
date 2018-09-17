export const endpoints = {
  getCartItems: '/bl-api/cart',
  addToCart: '/bl-api/cart/add',
  addToKitCart: '/bl-api/kit/kit-add', // add the item to the cart, but really it's the kit
  addToKit: '/bl-api/kit/add',
  removeFromCart: '/bl-api/cart/remove',
  removeFromKit: '/bl-api/kit/remove',
  swapItemFromKit: '/bl-api/kit/swap',
  swapBag: '/bl-api/bag/swap',
  selectSwap: '/bl-api/kit/select',
  setKitState: '/bl-api/kit/state',
  setKit: '/bl-api/kit/set',
  getProductData: '/bl-api/product/get',
  setFrequency: '/bl-api/frequency/set'
}
