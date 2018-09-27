import Swiper from 'swiper'

export class CompactProductList {
  constructor () {
    this.thumbnailController = document.querySelector('.benlido-compact-product-list .swiper-container') || undefined
    this.detailSlides = document.querySelector('.benlido-compact-product-list-detail .swiper-container')
    this.thumbnailSwiper = null
    this.detailSwiper = null
    this.activeSlideClass = 'benlido-active-slide'
  }

  init () {
    if (this.thumbnailController !== undefined) {
      this.buildSwiper()
      this.showBag()
      this.bagDetailSlide()
    }
  }

  buildSwiper () {
    const target = this.thumbnailController

    const mySwiper = new Swiper(target, {
      autoHeight: true,
      spaceBetween: 10,
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

    mySwiper.on('slideChangeTransitionEnd', () => {
      this.removeActiveSlide()
      this.detailSwiper.slideTo(this.thumbnailSwiper.activeIndex)
      this.addActiveSlide()
    })
  }

  bagDetailSlide () {
    const targets = this.detailSlides
    const mySwiper = new Swiper(targets, {
      autoHeight: true,
      spaceBetween: 10
    })

    this.detailSwiper = mySwiper
  }

  addActiveSlide (slide) {
    const targets = slide || this.thumbnailSwiper.slides

    const loopSlides = () => {
      let i = 0
      let maxLength = targets.length

      for (i; i < maxLength; i++) {
        if (targets[i].classList.contains('swiper-slide-active')) {
          targets[i].classList.add(this.activeSlideClass)
          return false
        }
      }
    }

    const specificSlide = () => {
      targets.classList.add(this.activeSlideClass)
    }

    targets.length ? loopSlides() : specificSlide()

    // target.classList.add('benlido-active-slide')
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

  showBag () {
    const targets = this.thumbnailController.querySelectorAll('.swiper-slide')

    targets.forEach((slide, index) => {
      slide.addEventListener('click', async () => {
        this.detailSwiper.slideTo(index)
        this.thumbnailSwiper.slideTo(index)

        await this.removeActiveSlide(targets)
        this.addActiveSlide(slide)
      })
    })
  }
}
