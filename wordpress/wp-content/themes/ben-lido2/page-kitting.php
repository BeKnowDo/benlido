<?php
/**
 * Template Name: Ben Lido Kitting Page
 */
//print_r (get_intermediate_image_sizes());
    $kit_id = 0;
    global $kit_id;
    $success = false;
    if ($_REQUEST['buy_kit']) {
        $buy_kit = $_REQUEST['buy_kit'];
        if ($buy_kit == 'true') {
            if (function_exists('bl_get_current_kit_id')) {
                $kit_id = bl_get_current_kit_id();
            }
            if (!empty($kit_id) && $kit_id > 0) {
                $success = bl_add_current_kit_to_cart();
            }
        }
    }
    if ($_REQUEST['id']) {
        $kit_id = $_REQUEST['id'];
    }
    $selected_products = array();
    $delivery_frequency_page = '';

    // when we come back to this page, we disable the is_adding_item_to_kit action
    if (function_exists('bl_set_kit_add') && $kit_id > 0) {
        bl_set_kit_add($kit_id,0);
    }

    if (function_exists('get_field')) {
        $delivery_frequency_page = get_field('delivery_frequency_page','option');
    }
    if (!empty($delivery_frequency_page) && is_object($delivery_frequency_page)) {
        $delivery_frequency_page = get_permalink($delivery_frequency_page->ID);
    }
    if (isset($_POST)) {
        if (!empty($_POST['bl_category']) && !empty($_POST['bl_product'])) {
            foreach ($_POST['bl_category'] as $key => $bl_category) {
                $bl_product = $_POST['bl_product'][$key];
                $selected_products[] = array('category'=>$bl_category,'product'=>$bl_product);
            }
        }

        if (!empty($selected_products) && is_array($selected_products)) {
            foreach ($selected_products as $selected_product) {
                $selected_cat = $selected_product['category'];
                $selected_prod = $selected_product['product'];
                if (is_numeric($selected_prod)) {
                    WC()->cart->add_to_cart($selected_prod,1);
                }
            }
            wp_redirect($delivery_frequency_page);
        }
    }
    if (isset($_REQUEST['cat']) && isset($_REQUEST['prod'])) {
        // overriding featured product for this category
        if (function_exists('bl_override_category_default')) {
            $cat_id = $_REQUEST['cat'];
            $prod_id = $_REQUEST['prod'];
            if (!empty($cat_id) && is_numeric($cat_id) && !empty($prod_id) && is_numeric($prod_id)) {
                bl_override_category_default($cat_id,$prod_id);
            }

        }
    }
    //print_r ($selected_products);

 // removing the add to cart message
add_filter( 'wc_add_to_cart_message_html', '__return_null' );
 // see if we are removing the breadcrumb
 if (function_exists('get_field')) {
     $remove_woocommerce_breadcrumb = get_field('remove_woocommerce_breadcrumb');
     if ($remove_woocommerce_breadcrumb == true) {
        remove_action( 'storefront_content_top','woocommerce_breadcrumb', 10, 0);
     }
 }

get_header(); ?>

<div id="primary" class="content-area benlido-kits-page">
    <main id="main" class="site-main" role="main">

        <?php while ( have_posts() ) : the_post();

            do_action( 'storefront_page_before' );

            get_template_part( 'template-parts/pages/kitting');

            get_template_part( 'template-parts/common/back-to','top' );

            /**
             * Functions hooked in to storefront_page_after action
             *
             * @hooked storefront_display_comments - 10
             */
            do_action( 'storefront_page_after' );

        endwhile; // End of the loop. ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
