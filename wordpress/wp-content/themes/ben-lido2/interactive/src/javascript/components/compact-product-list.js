import Swiper from 'swiper'
import KUTE from 'kute.js'
import 'element-closest'

export class CompactProductList {
  constructor () {
    this.bagOptions = document.querySelector('.benlido-compact-product-options .swiper-container') || undefined
    this.bagDetail = document.querySelector('.benlido-compact-product-list-detail .swiper-container') || undefined

    this.stylesOptions = document.querySelector('.benlido-compact-product-styles-list') || undefined
    this.stylesDetails = document.querySelector('.benlido-compact-product-styles-list-detail') || undefined

    this.thumbnailSwiper = null
    this.detailSwiper = null
    this.stylesOptionsSwiper = null
    this.stylesDetailsSwiper = null

    this.activeSlideClass = 'benlido-active-slide'
  }

  init () {
    if (this.bagOptions !== undefined) {
      this.bagOptionsSwiper()
    }

    if (this.bagDetail !== undefined) {
      this.showBagDetail()
      this.bagDetailSlide()
      this.findSelectedBag()
    }

    if (this.stylesOptions !== undefined) {
      this.buildStyleSwiper()
    }

    if (this.stylesDetails !== undefined) {
      this.buildStyleDetailSwiper()
    }

    if (this.stylesDetails !== undefined) {
      this.showStyleDetail()
    }
  }

  findSelectedBag () {
    const selectedProduct = this.bagDetail.querySelector('.hero-product-picked') || undefined

    if (selectedProduct !== undefined) {
      const targetDataset = selectedProduct.closest('.swiper-slide') ? selectedProduct.closest('.swiper-slide').dataset : undefined
      const targets = this.bagOptions.querySelectorAll('.swiper-slide')
      const targetId = targetDataset.productId

      let i = 0
      let max = targets.length

      for (i; i <= max; i++) {
        const scope = targets[i].dataset.productId
        if (scope === targetId) {
          this.bagOptions.classList.add('disabled')
          targets[i].classList.add(this.activeSlideClass)
          targets[i].click()

          this.stylesOptions.classList.add('active')
          this.stylesDetails.classList.add('active')

          return false
        }
      }
    }
  }

  bagOptionsSwiper () {
    const target = this.bagOptions

    if (target !== undefined) {
      const mySwiper = new Swiper(target, {
        // autoHeight: true,
        shortSwipes: true,
        breakpoints: {
          480: {
            slidesPerView: 1
          },
          768: {
            slidesPerView: 2
          },
          1024: {
            slidesPerView: 2
          },
          5000: {
            slidesPerView: 3
          }
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev'
        }
      })

      mySwiper.on('slideChangeTransitionEnd', () => {
        this.removeActiveSlideClass()
        this.addActiveSlideClass()
        this.bagDetail.classList.add(this.activeSlideClass)
        this.detailSwiper.slideTo(this.thumbnailSwiper.activeIndex)
      })

      this.thumbnailSwiper = mySwiper
    }
  }

  bagDetailSlide () {
    const targets = this.bagDetail
    if (targets) {
      const mySwiper = new Swiper(targets, {
        shortSwipes: false,
        autoHeight: false
      })
      this.detailSwiper = mySwiper
    }
  }

  addActiveSlideClass (slide) {
    const targets = slide || this.thumbnailSwiper.slides

    const findeSwiperActiveSlide = () => {
      let i = 0
      let maxLength = targets.length

      for (i; i < maxLength; i++) {
        if (targets[i].classList.contains('swiper-slide-active')) {
          targets[i].classList.add(this.activeSlideClass)
          return false
        }
      }
    }

    const addActiveClass = () => {
      targets.classList.add(this.activeSlideClass)
    }

    targets.length ? findeSwiperActiveSlide() : addActiveClass()
  }

  collapseBagDetailSlide (target) {
    if (target) {
      const scope = target
      scope.classList.remove('active')
      this.showStyleOptions()
    }
  }

  removeActiveSlideClass (slides) {
    const targets = slides || this.bagOptions.querySelectorAll('.swiper-slide')

    let i = 0
    let maxLength = targets.length

    for (i; i < maxLength; i++) {
      if (targets[i].classList.contains(this.activeSlideClass)) {
        targets[i].classList.remove(this.activeSlideClass)
        return false
      }
    }
  }

  buildStyleSwiper () {
    if (this.stylesOptions !== undefined) {
      const target = this.stylesOptions.querySelector('.swiper-container') || undefined
      if (target !== undefined) {
        const mySwiper = new Swiper(target, {
          breakpoints: {
            480: {
              slidesPerView: 1
            },
            768: {
              slidesPerView: 2
            },
            1024: {
              slidesPerView: 3
            },
            5000: {
              slidesPerView: 5
            }
          },
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
          }
        })

        this.stylesOptionsSwiper = mySwiper

        mySwiper.on('slideChangeTransitionEnd', () => {
          const slides = target.querySelectorAll('.swiper-slide')
          this.removeActiveSlideClass(slides)
          this.addActiveSlideClass(slides)
          this.stylesDetailsSwiper.slideTo(this.stylesOptionsSwiper.activeIndex)
        })
      }
    }
  }

  buildStyleDetailSwiper () {
    if (this.stylesDetails !== undefined) {
      const target = this.stylesDetails.querySelector('.swiper-container') || undefined
      if (target !== undefined) {
        const mySwiper = new Swiper(target, {
          shortSwipes: false,
          autoHeight: true
        })
        this.stylesDetailsSwiper = mySwiper
      }
    }
  }

  showStyleOptions () {
    if (this.stylesOptions !== undefined) {
      const target = this.stylesOptions
      target.classList.add('active')
    }

    if (this.stylesDetails !== undefined) {
      const target = this.stylesDetails
      target.classList.add('active')

      const coordinates = this.stylesOptions.querySelector('.benlido-compact-product-instruction-header').getBoundingClientRect()

      KUTE.to(
        'window',
        { scroll: coordinates.top },
        { easing: 'easingCubicOut', duration: 500 }
      ).start()
    }
  }

  toggleStylesOptions () {

  }

  showBagDetail () {
    const targets = this.bagOptions.querySelectorAll('.swiper-slide')

    targets.forEach((slide, index) => {
      slide.addEventListener('click', async () => {
        // reveal bag detail swiper
        this.bagDetail.parentElement.classList.add('active')

        // slide to targeted bag detail slide
        this.detailSwiper.slideTo(index)

        // DO WE NEED THIS??
        this.thumbnailSwiper.slideTo(index)

        // wait until class removal is complete
        await this.removeActiveSlideClass(targets)
        // add active class to slide
        this.addActiveSlideClass(slide)
      })
    })
  }

  showStyleDetail () {
    const targets = this.stylesOptions.querySelectorAll('.swiper-slide') || undefined
    targets.forEach((slide, index) => {
      slide.addEventListener('click', async () => {
        // Collapse bag detail
        // this.bagDetail.parentElement.classList.remove('active')
        this.stylesDetailsSwiper.slideTo(index)

        // DO WE NEED THIS??
        this.stylesOptionsSwiper.slideTo(index)

        // wait until class removal is complete
        await this.removeActiveSlideClass(targets)

        // add active class to slide
        this.addActiveSlideClass(slide)
      })
    })
  }
}
