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
    let tiles = this.frequencyTiles

    tiles.forEach(tile => {
      const buttons = tile.querySelectorAll('.delivery-frequency-tile-main button')

      buttons.forEach(button => {
        this.showTiles(button)
      })
    })
  }

  showTiles (button) {
    let targetId = button.dataset.id
    let targetNode = document.getElementById(targetId) || undefined

    if (targetNode !== undefined) {
      this.tileActions(targetNode)
      button.onclick = e => {
        e.preventDefault()
        targetNode.classList.add('active')
        targetNode.classList.remove('hide')

        button.parentElement.parentElement.parentElement.classList.add('hide')
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

        let type = e.target.dataset.type
        let value = e.target.dataset.value || undefined
        let id = e.target.dataset.id || 0;

        if (type === 'cancel') {
          this.closeAnimation(tile,id);
          // 
          console.log('cancelling')
        }

        if (type === 'save_name') {
          console.log('save name');
          let header = e.target.parentElement.parentElement.querySelector('.delivery-frequency-tile-header') || undefined
          if (header) {
            let inpt = header.querySelector('input[type="text"]');
            if (inpt) {
              let name = inpt.value;
              if (name && id) {

                let setOrderKitNameURL = endpoints.setOrderKitName + '/' + id;
                let data = {'name':name};
                fetch(setOrderKitNameURL, {
                  method: 'POST',
                  body: JSON.stringify(data),
                  credentials: 'include',
                  headers: {
                    'Content-Type': 'application/json'
                  }
                })
                  .then(res => res.json())
                  .catch(error => console.error('Error:', error))
                  .then(response => {
                    if (response.error) {} else {
                      if (response.parent) {
                        let parentBlock = document.getElementById(response.parent);
                        if (parentBlock) {
                          let header = parentBlock.querySelector('.delivery-frequency-tile-header');
                          header.innerHTML = name;
                        }
                        this.closeAnimation(tile,response.parent);
                      }
                      
                    }
                  })

              }
              
            }
          }
        }

        if (type === 'proceed') {
          console.log('proceeding')
          fetch(endpoints.setFrequency)
            .then(response => {
              this.closeAnimation(tile,id)
            })
            .catch(() => {
              console.log('error found?')
              this.closeAnimation(tile,id)
            })
        }
      }
    }
  }

  closeAnimation (tile,id) {
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
          document.getElementById(id).classList.remove('hide')
          tile.classList.add('hide')
        }
      }
    ).start()
  }
}
