(function($){
    $(document).ready(function($) {



        $('.create-kit').height($('.last_kit').height());

        $('.js-variation-select').on('click', function () {
            var variation_id = $(this).data('variation_id');
            if (variation_id) {
                $('#bag-'+ $(this).data('product_id')).data('variation_id', variation_id);
                var el = $(this).closest('.product-grid-single');
                $('.choices-container a',el).each(function(idx,elem) {
                    console.log(elem);
                    $(elem).data('variation_id', variation_id);
                });
                $('#product-id-'+ $(this).data('product_id')+' div').removeClass('selected');
                $(this).addClass('selected');
            }

        });
        $('.needs-variation').on('click',function(e) {
            e.preventDefault();
            var _that = this;
            var variation_id = $(_that).data('variation_id');
            if (!variation_id) {
                e.stopPropagation();
                alert("Please select a color");
                setTimeout(function() {
                    jQuery(_that).removeClass('loading');
                },100);
            }
        });

        $('body.single-product .single_add_to_cart_button').on('click',function(e) {
            e.preventDefault();
        });



        

        $('.bl-add-kit-to-cart').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var _that = this;
            $(this).addClass('loading');
            if (url) {
                $.post(url,{},function(response) {
                    jQuery( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, null ] );
                    $(_that).removeClass('loading');
                },'json');
            }
        });

    }); // end document ready


})(jQuery);

function bl_add_item_to_kit(OBJ) {
    var has_variations = jQuery(OBJ).data('has_variations');
    var variation_id = jQuery(OBJ).data('variation_id');
    variation_id = parseInt(variation_id); 
    var product_id = jQuery(OBJ).data('product_id');
    var category_id = jQuery(OBJ).data('category_id');
    var index = jQuery(OBJ).data('index');
    var url = '/?wc-ajax=add_to_cart';
    if (has_variations == 1 && variation_id > 0) {
        var data = {'index':index,'product_id':product_id,'category_id':category_id,'variation_id':variation_id,'quantity':1};
        jQuery.post(url,data,function(response) {
            // Trigger event so themes can refresh other areas.
            jQuery( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, null ] );
        });
    } else {
        //alert('Please select a color');
    }

}

function bl_add_product_to_kit(OBJ) {
    var index = jQuery(OBJ).data('index');
    if (!index) {
        index = 0;
    }
    jQuery(OBJ).closest('.wrap-price').find('a.add_to_cart_button').first().data('index',index);
    jQuery(OBJ).closest('.wrap-price').find('a.add_to_cart_button').first().click();
}

function bl_swap_product(OBJ) {
    var index = jQuery(OBJ).data('index');
    var product_id = jQuery(OBJ).data('product_id');
    var variation_id = jQuery(OBJ).data('variation_id');
    var category_id = jQuery(OBJ).data('category_id');
    var data = {'product_id':product_id,'variation_id':variation_id,'category_id':category_id,'index':index};
    if (bl_swap_url) {
        url = bl_swap_url+'/'+index+'/'+product_id+'/'+category_id;
        jQuery.post(url,data,function(response) {
            // redirecting to the right section
            if (response.url) {
                location.href = response.url;
            }
        });
    }
}

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

function bl_update_choices_containers() {
    console.log('update choices containers');
    if (bl_has_multi) {
        url = bl_has_multi;
    }
    if (jQuery('body.post-type-archive-product .choices-container, body.tax-product_cat .choices-container').hasClass('choices-container')) {
        jQuery.post(url,{}, function(response) {

            jQuery('body.post-type-archive-product .choices-container, body.tax-product_cat .choices-container').each(function() {
                jQuery(this).html(response);
            });
    
        },'html');
    }

}


jQuery(document).ready(function() {
    bl_update_choices_containers();
})


