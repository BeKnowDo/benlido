<?php
/**
 * Displays header site branding
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>
<div class="site-branding">
	<div class="wrap hero-wrap">

		<?php
			$logo = '';
			if (function_exists('get_field')) {
				$logo = get_field('logo');
				$hero_title = get_field('hero_title');
				if (!empty($logo)) {
					$logo = $logo['url'];
				}
			}
		?>
		<div class="logo">
			<a href="<?php echo home_url();?>" title="<?php echo esc_attr(get_bloginfo('description'));?>"><img src="<?php echo $logo;?>" /> <h1 class="site-title"><?php echo get_bloginfo('title');?></h1></a>
		</div>
		<div class="row hero">
		<div class="site-branding-text">
			<?php
			$description = get_bloginfo( 'description', 'display' );

			if ( $description || is_customize_preview() ) :
			?>
				<h2 class="hero-title"><?php echo $hero_title;?></h2>
				<p class="site-description"><?php echo $description; ?></p>
			<?php endif; ?>
		</div><!-- .site-branding-text -->
		<div class="coming-soon-product-image">
			<?php 
			$product_image = '';
			if (function_exists('get_field')) {
				$product_image = get_field('product_image');
				if (!empty($product_image)) {
					$product_image = $product_image['url'];
				}
			}
			if (!empty($product_image)) {
				$product_image = '<img src="' . $product_image . '" class="coming-soon-product-image-image" />';
			}
			?>
			<?php echo $product_image;?>
		</div>
		</div>

	</div><!-- .wrap -->
</div><!-- .site-branding -->
