export class BLGoogleAnalytics {
    constructor () {
    }
  
    init () {
      //console.log('here')
    }

    addToCart (product) {
        //console.log(product);
        if (typeof ga == 'function') {
            ga('ec:addProduct', {
                'id': product.id,
                'name': product.name,
                'category': product.category,
                'brand': product.brand,
                'variant': product.variant,
                'price': product.price,
                'quantity': product.qty
              });
              ga('ec:setAction', 'add');
              ga('send', 'event', 'UX', 'click', 'add to cart');     // Send data using an event.
            
        }
        else if (typeof __gaTracker == 'function') {
            __gaTracker('ec:addProduct', {
                'id': product.id,
                'name': product.name,
                'category': product.category,
                'brand': product.brand,
                'variant': product.variant,
                'price': product.price,
                'quantity': product.qty
              });
              __gaTracker('ec:setAction', 'add');
              __gaTracker('send', 'event', 'UX', 'click', 'add to cart');     // Send data using an event.
            
        }
    }
}