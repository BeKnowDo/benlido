<?php
/**
 * Template Name: Ben Lido Home Page
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main bl-home-page" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

            <?php do_action( 'storefront_page_before' );?>

            <?php $show_hero_section = $show_feature_cards = $show_bottom_section = false;
            if (function_exists('get_field')) {
                $show_hero_section = get_field('show_hero_section');
                $show_feature_cards = get_field('show_feature_cards');
                $show_bottom_section = get_field('show_bottom_section');
            }
            ?>

            <?php if ($show_hero_section == true):?>
                <div>
                    <?php get_template_part( 'template-parts/common/hero/home','hero'); ?>
                </div>
            <?php endif;?>

            <?php if ($show_feature_cards == true):?>
                <div class="bg-grey hero-row">
                    <div class="max-width-xl">
                        <?php get_template_part( 'template-parts/common/hero/hero'); ?>
                    </div>
                </div>
            <?php endif;?>

            <?php if ($show_bottom_section == true):?>

            <?php endif;?>

            <div class="bl-about-us">
                <?php get_template_part( 'template-parts/common/bag','animation'); ?>
            </div>

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
