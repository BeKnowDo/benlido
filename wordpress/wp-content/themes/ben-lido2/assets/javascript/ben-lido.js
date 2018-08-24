(function ($) {
    $(document).ready(function(){
        var cartAPI = '/bl-api/cart';
        // update cart totals
        $( document.body ).on( 'updated_cart_totals', function(){
            $.post(cartAPI,{},function(res) {
                var ct = res.length;
                var counter = document.getElementById("navbar-item-counter") || undefined;
                var position = counter.getBoundingClientRect();
                counter.innerHTML = ct;

                var burst = new mojs.Burst({
                    parent: counter.parentElement,
                    top: position.y + 16,
                    left: position.x + 6,
                    radius: { 10: 19 },
                    angle: 45,
                    children: {
                        shape: "line",
                        radius: 4,
                        scale: 2,
                        stroke: "#195675",
                        strokeDasharray: "100%",
                        strokeDashoffset: { "-100%": "100%" },
                        duration: 400,
                        easing: "quad.out"
                    },
                    duration: 500
                    });
                burst.replay();
            },'json');
        });
    }); /// end document ready
})(jQuery);