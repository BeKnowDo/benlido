import Swiper from 'swiper'
import { debounce } from 'lodash'

export class ProductImageCarousel {
  constructor () {
    this.target = document.querySelector('.swiper-container') || undefined
  }

  init () {
    if (this.target) {
      this.enable()
    }
  }

  toggleClass () {
    const toggleClass = this.target.dataset.toggleClass
    this.target.classList.toggle(toggleClass)
  }

  enable () {
    const breakpoint = window.matchMedia('(min-width:600px)')

    // keep track of swiper to destroy later
    let swiper

    const breakpointChecker = () => {
      if (breakpoint.matches) {
        if (swiper !== undefined) {
          swiper.destroy(true, true)
          this.toggleClass()
        } else {
          // or/and do nothing

        }
      } else if (!breakpoint.matches) {
        // fire small viewport version of swiper
        return enableSwiper()
      }
    }

    const enableSwiper = () => {
      swiper = new Swiper(this.target, {
        loop: true,
        speed: 400,
        autoHeight: true,
        spaceBetween: 100,
        centeredSlides: true,
        keyboardControl: true,
        grabCursor: true,
        pagination: {
          el: '.swiper-pagination'
        }
      })
      this.toggleClass()
    }

    // keep an eye on viewport size changes
    breakpoint.addListener(debounce(breakpointChecker))

    // kickstart
    breakpointChecker()
  }
}
