import Swiper from 'swiper'
import KUTE from 'kute.js'

export class CompactProductList {
  constructor () {
    this.thumbnailController = document.querySelector('.benlido-compact-product-options .swiper-container') || undefined
    this.detailSlides = document.querySelector('.benlido-compact-product-list-detail .swiper-container') || undefined
    this.stylesOptions = document.querySelector('.benlido-compact-product-styles-list') || undefined

    this.thumbnailSwiper = null
    this.detailSwiper = null
    this.activeSlideClass = 'benlido-active-slide'
  }

  init () {
    if (this.thumbnailController !== undefined) {
      this.buildBagThumbnailSwiper()
      this.showBag()
      this.bagDetailSlide()
    }

    if (this.stylesOptions !== undefined) {
      this.buildStyleSwiper()
    }
  }

  buildBagThumbnailSwiper () {
    const target = this.thumbnailController

    if (target !== undefined) {
      const mySwiper = new Swiper(target, {
        autoHeight: true,
        shortSwipes: false,
        watchSlidesVisibility: true,
        loopFillGroupWithBlank: false,
        breakpoints: {
          320: {
            slidesPerView: 2
          },
          480: {
            slidesPerView: 2
          },
          768: {
            slidesPerView: 3
          },
          1024: {
            slidesPerView: 4
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

      this.thumbnailSwiper = mySwiper

      mySwiper.on('click', e => {

      })

      mySwiper.on('slideChangeTransitionEnd', () => {
        this.removeActiveSlide()
        this.addActiveSlideClass()
        this.detailSwiper.slideTo(this.thumbnailSwiper.activeIndex)
      })
    }
  }

  bagDetailSlide () {
    const targets = this.detailSlides
    if (targets) {
      const mySwiper = new Swiper(targets, {
        shortSwipes: false,
        autoHeight: true
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
      const height = scope.dataset.height ? scope.dataset.height : scope.offsetHeight

      scope.dataset.height = height

      KUTE.fromTo(scope,
        {
          height: height > 0 ? height : 0
        },
        {
          height: height <= 0 ? height : 0
        },
        {
          duration: 150,
          complete: () => {
            this.setStyleOption()
          }
        })
        .start()
    } else {
      const slides = this.detailSlides || undefined
      let i
      let max = slides.length

      console.log(slides)

      if (slides !== undefined) {
        for (i = 0; i >= max; i++) {
          console.log(slides[i])
        }
      }
    }
  }

  removeActiveSlide (slides) {
    const targets = slides || this.thumbnailController.querySelectorAll('.swiper-slide')

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
          autoHeight: true,
          shortSwipes: false,
          watchSlidesVisibility: true,
          loopFillGroupWithBlank: false,
          breakpoints: {
            320: {
              slidesPerView: 2
            },
            480: {
              slidesPerView: 2
            },
            768: {
              slidesPerView: 3
            },
            1024: {
              slidesPerView: 4
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
      }
    }
  }

  setStyleOption () {
    if (this.stylesOptions !== undefined) {
      const target = this.stylesOptions
      target.classList.add('active')
    }
  }

  toggleStylesOptions () {

  }

  showBag () {
    const targets = this.thumbnailController.querySelectorAll('.swiper-slide')

    targets.forEach((slide, index) => {
      slide.addEventListener('click', async () => {
        this.detailSwiper.slideTo(index)
        this.thumbnailSwiper.slideTo(index)

        this.collapseBagDetailSlide()

        await this.removeActiveSlide(targets)
        this.addActiveSlideClass(slide)
      })
    })
  }
}
