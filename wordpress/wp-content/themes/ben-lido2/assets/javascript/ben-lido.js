(function ($) {
    $(document).ready(function($){

        // signup modal
        if ($.cookie('blsignup') === undefined || $('.mc4wp-alert').length > 0) { // show modal if no cookie or mailchimp has a response for us

            if ($('#modalSubscribe').hasClass('modal-subscribe')) {
                $('#modalSubscribe').modal({
                    overlayClose: true
                });
            }

            if ($('.mc4wp-alert').length > 0 && $('.mc4wp-success').length > 0) {
                $('.mc4wp-form input').hide();
                $.cookie('blsignup', 'true', {expires: 90, path: '/'});
            } else {
                $.cookie('blsignup', 'true', {path: '/'});
            }


        }

        if ($('#wc-stripe-new-payment-method').not(':checked')) {
            $('#wc-stripe-new-payment-method').click();
        }

        var cartAPI = '/bl-api/cart';
        // update cart totals
        $( document.body ).on( 'updated_cart_totals', function(){
            $.post(cartAPI,{},function(res) {
                var ct = res.length;
                var counter = document.getElementById("navbar-item-counter") || undefined;
                var position = counter.getBoundingClientRect();
                counter.innerHTML = ct;

                // var burst = new mojs.Burst({
                //     parent: counter.parentElement,
                //     top: position.y + 16,
                //     left: position.x + 6,
                //     radius: { 10: 19 },
                //     angle: 45,
                //     children: {
                //         shape: "line",
                //         radius: 4,
                //         scale: 2,
                //         stroke: "#195675",
                //         strokeDasharray: "100%",
                //         strokeDashoffset: { "-100%": "100%" },
                //         duration: 400,
                //         easing: "quad.out"
                //     },
                //     duration: 500
                //     });
                // burst.replay();
            },'json');
        });

        var fixSVG = function() {
            jQuery('img.svg').each(function(){
                var $img = jQuery(this);
                var imgID = $img.attr('id');
                var imgClass = $img.attr('class');
                var imgURL = $img.attr('src');
        
                jQuery.get(imgURL, function(data) {
                    // Get the SVG tag, ignore the rest
                    var $svg = jQuery(data).find('svg');
        
                    // Add replaced image's ID to the new SVG
                    if(typeof imgID !== 'undefined') {
                        $svg = $svg.attr('id', imgID);
                    }
                    // Add replaced image's classes to the new SVG
                    if(typeof imgClass !== 'undefined') {
                        $svg = $svg.attr('class', imgClass+' replaced-svg');
                    }
        
                    // Remove any invalid XML tags as per http://validator.w3.org
                    $svg = $svg.removeAttr('xmlns:a');
        
                    // Replace image with new SVG
                    $img.replaceWith($svg);
        
                }, 'xml');
        
            });
        };

        fixSVG();


    }); /// end document ready
})(jQuery);