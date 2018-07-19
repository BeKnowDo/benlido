<div>
    <?php if ( have_posts() ) :?>
        <ul class="columns">
            <?php while ( have_posts() ) : the_post();?>
            <?php
            // converting post to product
            $product_id = get_the_ID();
            if (get_post_type($product_id) == 'product') {
                    global $product;
                    $product = wc_get_product( $product_id );
                    wc_get_template_part( 'content', 'product' );
            }
            ?>
            <?php endwhile;?>
        </ul>
    <?php else:?>
    <h3>No Results.</h3>
    <?php endif;?>
</div>