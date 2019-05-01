(function($){
    $(document).ready(function($) {

        var add_to_cart_url = '/bl-api/cart/add';
        var add_to_kit_url = '/bl-api/kit/add';
        var select_swap_url = '/bl-api/kit/select';
        // ajax add to cart
        $('.add_to_cart_button').on('click', function(e) {
            e.preventDefault();
            var quantity = 1;
            var kit_id = $(this).data('kit_id');
            var prod_id = $(this).data('prod_id');
            var cat_id = $(this).data('cat_id');
            var var_id = $(this).data('var_id');
            if (!var_id) {
                var_id = 0;
            }
            var swap = $(this).data('swap');
            var has_variations = false;
            if ($(this).hasClass('has-variations')) {
                has_variations = true;
            }
            var add_url = add_to_cart_url;
            if (kit_id > 0) {
                add_url = add_to_kit_url;
            }
            if (swap > 0 && kit_id > 0) {
                add_url = select_swap_url;
            }

            // add to kit is: kit_id, product_id, cat_id
            if (kit_id > 0) {
                add_url += '/' + kit_id + '/' + prod_id + '/' + cat_id;
            } else {
                add_url += '/' + prod_id + '/' + cat_id + '/' + var_id + '/' + quantity;
            }
            $.post(add_url,{},function(response){
                var fragments = response.fragments;
                var cart_hash = response.cart_hash;
                $( document.body ).trigger( 'added_to_cart', [ fragments, cart_hash, $thisbutton ] );
            },'json');

        });


    }); // end document ready
})(jQuery);