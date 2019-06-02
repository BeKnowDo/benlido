<?php
if( ! function_exists( 'basel_header_block_logo' ) ) {
	function basel_header_block_logo() {

		$header_color_scheme = basel_get_opt( 'header_color_scheme' );

		// Get the logo
		$logo 		= BASEL_IMAGES . '/logo.png';
		$logo_white = BASEL_IMAGES . '/logo-white.png';

		$protocol = basel_http() . "://";

		$logo_uploaded = basel_get_opt('logo');
		$logo_white_uploaded = basel_get_opt('logo-white');
		$logo_sticky_uploaded = basel_get_opt('logo-sticky');
		$has_sticky_logo = ( isset( $logo_sticky_uploaded['url'] ) && ! empty( $logo_sticky_uploaded['url'] ) );

		if(isset($logo_white_uploaded['url']) && $logo_white_uploaded['url'] != '') {
			$logo_white = $logo_white_uploaded['url'];
		}
		if(isset($logo_uploaded['url']) && $logo_uploaded['url'] != '') {
			$logo = $logo_uploaded['url'];
		}

		if( $header_color_scheme == 'light' ) {
			$logo = $logo_white;
		}

		$logo = $protocol. str_replace(array('http://', 'https://'), '', $logo);

		?>
			<div class="site-logo">
				<div class="basel-logo-wrap<?php if( $has_sticky_logo ) echo " switch-logo-enable"; ?>">
					<a href="<?php echo esc_url( home_url('/') ); ?>" class="basel-logo basel-main-logo" rel="home">
						<?php echo '<img src="' . $logo . '" alt="' . get_bloginfo( 'name' ) . '" />'; ?>
					</a>
					<?php if ( $has_sticky_logo ): ?>
						<?php 
							$logo_sticky = $protocol . str_replace( array( 'http://', 'https://' ), '', $logo_sticky_uploaded['url'] );
						 ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="basel-logo basel-sticky-logo" rel="home">
							<?php echo '<img src="' . $logo_sticky . '" alt="' . get_bloginfo( 'name' ) . '" />'; ?>
						</a>
					<?php endif ?>
				</div>
			</div>
		<?php
	}
}

if( ! function_exists( 'basel_header_block_main_nav' ) ) {
	function basel_header_block_main_nav() {
		$location = apply_filters( 'basel_main_menu_location', 'main-menu');
		?>
			<div class="main-nav site-navigation basel-navigation menu-<?php echo esc_attr( basel_get_opt('menu_align') ); ?>" role="navigation">
				<?php 
					if( has_nav_menu( $location ) ) {
						wp_nav_menu(
							array(
								'theme_location' => $location,
								'menu_class' => 'menu',
								'walker' => new BASEL_Mega_Menu_Walker()
							)
						); 
					} else {
						$menu_link = get_admin_url( null, 'nav-menus.php' );
						?>
							<br>
							<h5><?php printf( __('Create your first <a href="%s"><strong>navigation menu here</strong></a>', 'basel'), $menu_link) ?></h5>
						<?php
					}
				 ?>
			</div><!--END MAIN-NAV-->
		<?php
	}
}

if( ! function_exists( 'basel_cart_count' ) ) {
	function basel_cart_count() {
		$count = WC()->cart->get_cart_contents_count();
		if (function_exists('bl_get_cart_count')) {
			
		}
		?>
			<span class="basel-cart-number"><?php echo $count; ?></span>
		<?php
	}
}

if( ! function_exists( 'basel_cart_subtotal' ) ) {
	function basel_cart_subtotal() {
		$subtotal = WC()->cart->get_cart_subtotal();
		if (function_exists('bl_get_subtotal')) {
			$subtotal = bl_get_subtotal();
		}
		?>
			<span class="basel-cart-subtotal"><?php echo  $subtotal; ?></span>
		<?php
	}
}