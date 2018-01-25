(function ($) {
    $(document).ready(function(){
        if ($('form').hasClass('mc4wp-form-success')) {
            $('form').hide();
            $('.thank-you-copy-container').removeClass('hide');
            $('.signup-copy-container').addClass('hide');
        }
    });
})(jQuery);