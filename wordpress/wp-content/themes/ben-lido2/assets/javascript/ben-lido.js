(function ($) {
    $(document).ready(function(){

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
    }); /// end document ready
})(jQuery);