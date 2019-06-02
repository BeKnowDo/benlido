<?php
/**
 * Template Name: Ben Lido Bags Page
 */

$show_hero_section = false;
if (function_exists('get_field')) {
    $show_hero_section = get_field('show_hero_section');
    $show_step_navigation = get_field('show_step_navigation');
    $show_hero_title_section = get_field('show_hero_title_section');
}
get_header(); ?>

<div id="primary" class="content-area bl-bags-page">
    <main id="main" class="site-main" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

            <?php do_action( 'storefront_page_before' );?>

            <?php if ($show_step_navigation == true):?>
                <?php get_template_part( 'template-parts/common/step','navigation'); ?>
            <?php endif;?>

            <?php if ($show_hero_title_section):?>
            <?php get_template_part( 'template-parts/common/hero/hero-title','copy'); ?>
            <?php endif;?>

            <?php if ($show_hero_section == true):?>
                <div class="max-width-xl">
                    <?php get_template_part( 'template-parts/common/hero/home','hero'); ?>
                </div>
            <?php endif;?>


            <div class="bg-white">
                <div class="max-width-xl">
                    <?php get_template_part( 'template-parts/common/hero/hero-product','list'); ?>
                </div>
            </div>

            <?php
                get_template_part( 'template-parts/common/hero/hero-no','bag');
            ?>

            <?php
            /**
             * Functions hooked in to storefront_page_after action
             *
             * @hooked storefront_display_comments - 10
             */
            do_action( 'storefront_page_after' );
            ?>

        <?php endwhile; // End of the loop. ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
