<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package storefront
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<div class="column col-xs-12 col-sm-12 col-md-12 col-3" role="complementary">
	<div id="category-list" class="category-list ">
		<div id="category-list-wrapper" class="category-list-wrapper">
			<h4 id="category-list-breadcrumbs" class="category-list-breadcrumbs">
				<a id="category-list-all-header" class="category-list-all-header">Shop All Categories:</a>
			</h4>
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		</div>	
	</div>
</div>
<!-- #secondary -->
