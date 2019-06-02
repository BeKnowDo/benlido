
export class StepNavigation {
  constructor () {
    this.isProductPage = document.querySelector('body.single-product') || undefined
    this.stepNavigation = this.isProductPage ? this.isProductPage.querySelector('.step-navigation') : undefined
  }

  init () {
    if (this.isProductPage && this.stepNavigation) {
      this.productBackButton()
    }
  }

  productBackButton () {
    const backButton = this.stepNavigation.querySelector('.fa-chevron-circle-left') || undefined
    if (backButton !== undefined) {
      backButton.onclick = e => {
        e.preventDefault()
        let history = window.history
        history.back()
      }
    }
  }
}
