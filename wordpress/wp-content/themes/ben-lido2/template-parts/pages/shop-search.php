<?php
    get_template_part( 'template-parts/common/navigation/search-in','page' );
?>

<div class="bg-grey search-results-container">

<?php if ( have_posts() ) :?>

    <ul class="columns">
    <?php
        while ( have_posts() ) : the_post();

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
    <?php
        get_template_part( 'template-parts/common/back-to','top' );
    ?>
    <?php else:?>
    <h3>No Results.</h3>
    
    <?php
        endif
    ;?> 
    
</div>