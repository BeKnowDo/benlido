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

function bl_rename_kit_start(e,OBJ) {
    e.stopPropagation(); 
    e.preventDefault(); 
    var holder = jQuery(OBJ).closest('h3');
    if (holder) {
        jQuery(holder).addClass('edit');
    }

}
function bl_rename_kit(e,OBJ) {
    e.stopPropagation(); // this is
    e.preventDefault(); // the magic
    var data = {};
    var url = '';
    var index = jQuery(OBJ).data('index');
    console.log('this');
    console.log(OBJ);
    var title = jQuery(OBJ).closest('.edit-title').find('input[type="text"]').first().val();
    console.log('title');
    console.log(title);
    data = {'index':index,'kit_name':title};
    if (bl_api_url) {
        url = bl_api_url + '/kit/rename';
    }
    if (url.length > 0) {
        jQuery.post(url,data,function(response) {
            if (typeof response.fragments != 'undefined') {
                jQuery( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, null ] );
            }
            
        });
    }
}
function bl_rename_cancel(e,OBJ) {
    e.stopPropagation(); 
    e.preventDefault(); 
    var holder = jQuery(OBJ).closest('h3');
    if (holder) {
        jQuery(holder).removeClass('edit');
    }
}

function bl_start_add_bag(e,OBJ) {
    e.stopPropagation(); // this is
    e.preventDefault(); // the magic
    var index = jQuery(OBJ).data('index');
    var url = '';
    if (!index) {
        index = 0;
    }
    var data = {'index':index};
    if (bl_api_url) {
        url = bl_api_url + '/kit/start-add-bag';
    }
    if (url.length > 0) {
        jQuery.post(url,data,function(resp) {
            if (typeof resp.redirect_url != 'undefined') {
                document.location.href = resp.redirect_url;
            }
        },'json');
    }
}


