(function ($) {
    $(document).ready(function($){
        $('.footer-links li.blue.title > a').on('click',function(e) {
            e.preventDefault();
        });
        $('.add-kit-to-cart-button').on('click',function(e) {
            e.preventDefault();
            $('#add-items-to-kit-form').submit();
        })
        $('#frequency button').on('click',function(e) {
            e.preventDefault();
            var freq = $(this).val();
            if (freq) {
                $('input.input-frequency').val(freq);
            }
            $('#frequency-form').submit();
        });
        // hero scrollto
        $('#hero a').on('click',function(e) {
            var target = this.hash.replace('#','');
            if ($(this).attr('href') == '#') {

            } else {
                if (target.length > 0) {
                    e.preventDefault();
                    if ($('a[name="'+target+'"]').offset()) {
                        $('html, body').animate({ 
                            scrollTop: ($('a[name="'+target+'"]').offset().top) 
                        }, 1000);
                    }

                }
            }
        });
    });
})(jQuery);