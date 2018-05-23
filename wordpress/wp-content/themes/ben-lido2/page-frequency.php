<?php
/**
 * Template Name: Ben Lido Delivery Frequency Page
 */

 // see if we are removing the breadcrumb
 if (function_exists('get_field')) {
     $remove_woocommerce_breadcrumb = get_field('remove_woocommerce_breadcrumb');
     if ($remove_woocommerce_breadcrumb == true) {
        remove_action( 'storefront_content_top','woocommerce_breadcrumb', 10, 0);
     }
 }
 
get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php while ( have_posts() ) : the_post();

            do_action( 'storefront_page_before' );

            get_template_part( 'template-parts/page/frequency');

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
