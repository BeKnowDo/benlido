import KUTE from 'kute.js'
import { endpoints } from '../../../config/endpoints'

export class MyAccount {
  constructor () {
    this.frequencyTiles = document.querySelectorAll('.delivery-frequency-tile') || undefined
    this.mainFrequencyTile = document.querySelector('.delivery-frequency-tile-main') || undefined
  }

  init () {
    if (this.frequencyTiles.length > 0 && this.mainFrequencyTile !== undefined) {
      this.tiles()
    }
  }

  tiles () {
    const tiles = this.frequencyTiles

    tiles.forEach(tile => {
      const buttons = tile.querySelectorAll('.delivery-frequency-tile-main button')

      buttons.forEach(button => {
        this.showTiles(button)
      })
    })
  }

  showTiles (button) {
    const targetId = button.dataset.id
    const targetNode = document.getElementById(targetId) || undefined

    if (targetNode !== undefined) {
      this.tileActions(targetNode)
      button.onclick = e => {
        e.preventDefault()
        targetNode.classList.add('active')

        this.mainFrequencyTile.classList.add('hide')
        KUTE.fromTo(
          targetNode,
          {
            opacity: 0
          },
          {
            opacity: 1
          },
          {
            easing: 'easingSinusoidalIn',
            duration: 150
          }
        ).start()
      }
    }
  }

  tileActions (tile) {
    const buttons = tile.getElementsByTagName('button')
    let i

    for (i = 0; i < buttons.length; i++) {
      buttons[i].onclick = e => {
        e.preventDefault()

        const type = e.target.dataset.type
        const value = e.target.dataset.value || undefined

        if (type === 'cancel') {
          this.closeAnimation(tile)
          console.log('cancelling')
        }

        if (type === 'proceed') {
          console.log('proceeding')
          fetch(endpoints.setFrequency)
            .then(response => {
              this.closeAnimation(tile)
            })
            .catch(() => {
              console.log('error found?')
              this.closeAnimation(tile)
            })
        }
      }
    }
  }

  closeAnimation (tile) {
    KUTE.fromTo(
      tile,
      {
        opacity: 1
      },

      {
        opacity: 0
      },
      {
        easing: 'easingSinusoidalIn',
        duration: 150,
        complete: () => {
          tile.classList.remove('active')
          this.mainFrequencyTile.classList.remove('hide')
        }
      }
    ).start()
  }
}
