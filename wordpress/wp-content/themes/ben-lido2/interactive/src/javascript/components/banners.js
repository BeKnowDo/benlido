import KUTE from 'kute.js'

export class BenLidoBanners {
  constructor () {
    this.banners = document.querySelector('.benlido-banner') || undefined
  }

  init () {
    if (this.banners !== undefined) {
      const banners = this.banners
      // console.log({KUTE, banners})
    }
  }
}
