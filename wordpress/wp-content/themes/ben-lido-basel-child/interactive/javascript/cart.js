(function($){
    $(document).ready(function($) {
        $('.create-kit').height($('.last_kit').height());

        $('.js-variation-select').on('click', function () {
            $('#bag-'+ $(this).data('product_id')).data('variation_id', $(this).data('variation_id'));
            $('#product-id-'+ $(this).data('product_id')+' div').removeClass('selected');
            $(this).addClass('selected');
        });

    }); // end document ready


})(jQuery);

function bl_create_new_kit() {

    var url = '';
    if (bl_new_kit_url) {
        url = bl_new_kit_url;
    }
    jQuery.post(url,{'kit':'new'}, function(response) {
        
        // Trigger event so themes can refresh other areas.
        jQuery( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, null ] );
    },'json');
}


