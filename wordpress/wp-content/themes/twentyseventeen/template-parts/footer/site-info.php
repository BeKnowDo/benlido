<?php
/**
 * Displays footer site info
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

 if (function_exists('get_field')) {
	 $footer_logo = get_field('footer_logo');
	 if(!empty($footer_logo)) {
		 $footer_logo = '<img src="' . $footer_logo['url'] . '" />';
	 }
 }
?>
<div class="site-info">
	<?php echo $footer_logo;?>
	<p>&copy; Ben Lido <?php echo date("Y");?></p>
</div><!-- .site-info -->


<?php

	$locations = get_nav_menu_locations();
	$menu_id = $locations[ 'social' ] ;
	$menu = wp_nav_menu(array(
		'menu'          => $menu_id,
		'echo' => false
	));
?>
<?php if (!empty($menu)):?>
<div class="follow-menu">
	<h4>Follow</h4>
	<?php echo $menu;?>
</div>
<?php endif;?>

<?php

	$locations = get_nav_menu_locations();
	$menu_id = $locations[ 'contact' ] ;
	$menu = wp_nav_menu(array(
		'menu'          => $menu_id,
		'echo' => false
	));
?>

<?php if (!empty($menu)):?>
<div class="follow-menu">
	<h4>Contact</h4>
	<?php echo $menu;?>
</div>
<?php endif;?>
