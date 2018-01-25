<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */


 if (function_exists('get_field')) {
	$thank_you_title = get_field('thank_you_title');
	$thank_you_copy = get_field('thank_you_copy');
	$signup_field_id = get_field('signup_field_id');
 }

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="signup-container">
	<div class="signup-copy-container">
		<h2><?php the_title();?></h2>
		<?php the_content();?>
	</div>
	<div class="thank-you-copy-container hide">
		 <h2><?php echo $thank_you_title;?></h2>
		 <?php echo $thank_you_copy;?>
	</div>
	<?php echo do_shortcode('[mc4wp_form id="' . $signup_field_id . '"]');?>
</div>
</article><!-- #post-## -->
