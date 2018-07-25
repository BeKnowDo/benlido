import { endpoints } from "../../../config/endpoints";

export class Frequency {
    constructor() {
        this.frequencyForm =
      document.getElementById("shipping-frequency") || undefined;
      if (this.frequencyForm) {
        this.frequencyButtons = this.frequencyForm.querySelectorAll("button") || undefined;
      }
        
    }

    init() {
        this.submitForm();
        this.clickSubmit();
    }

    clickSubmit() {
        if (this.frequencyButtons.length > 0) {
            this.frequencyButtons.forEach(el => {
                el.addEventListener('click', e => {
                    //e.preventDefault();
                    let freq = el.value;
                    let field = document.getElementById("freq");
                    if (field) {
                        field.value = freq;
                    }
                    //this.frequencyForm.submit();
                    return true;
                }) 
            });
        }
    }

    submitForm() {
        if (this.frequencyForm) {
            this.frequencyForm.addEventListener("submit", e => {
                e.preventDefault();
                // How stupid is this: you cannot get the clicked button value using regular javascript
                let formData = new FormData(this.frequencyForm);
                let orderNumber = formData.get("order_id");
                let freq = formData.get("freq");
                let key = formData.get("key");
                //console.log(orderNumber);
                //console.log(freq);
                //console.log(key);
                if (orderNumber && freq) {
                    let url = endpoints.setFrequency+"/"+orderNumber+"/"+freq;
                    fetch(url, {
                        credentials: "include",
                        method: "POST"
                      })
                        .then(function(response) {
                          return response.json();
                        })
                        .then(response => {
                            // do something here
                            if (response.frequency) {
                                if (this.frequencyButtons.length > 0) {
                                    this.frequencyButtons.forEach(el => {
                                        let freq = el.value;
                                        if (freq == response.frequency) {
                                            el.classList.add("hero-product-picked");
                                        } else {
                                            el.classList.remove("hero-product-picked");
                                        }
                                    });
                                }
                            }
                        });
                }
            });
        }
    }

}