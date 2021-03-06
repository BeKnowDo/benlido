<?php
// this file contains overrides of standard storefront theme functions or actions

function bl_storefront_overrides() {
    add_action('storefront_before_header','bl_storefront_before_header',10); // adding custom header cover
    add_action('storefront_header','bl_storefront_burger',10); // adding menu burger
	add_action('storefront_header','bl_storefront_search_button',10); // adding search button
	
	add_action('storefront_before_content','storefront_primary_navigation',5);
	add_action('storefront_before_content','storefront_product_search',6);
	if (is_woocommerce() || is_cart() || is_checkout()) {
		add_action('storefront_before_content','bl_storefront_main_content_wrapper_start',10);
		add_action('storefront_before_footer','bl_storefront_main_content_wrapper_end',99);
	}


	add_action('storefront_footer','bl_footer_menus',10);

    remove_action('storefront_header','storefront_social_icons',10);
	remove_action('storefront_header','storefront_primary_navigation_wrapper',42);
	remove_action('storefront_header','storefront_secondary_navigation ',30);
    remove_action('storefront_header','storefront_primary_navigation',50);
    remove_action('storefront_header','storefront_primary_navigation_wrapper_close',68);
    remove_action('storefront_header','storefront_product_search',40);

	remove_action('storefront_footer','storefront_footer_widgets',10);



}
// for adding frequency to session
add_action( 'wp_loaded', 'bl_add_frequency_to_session', 100);
add_action( 'wp', 'bl_storefront_overrides' );


if ( ! function_exists( 'storefront_product_search' ) ) {
	/**
	 * Display Product Search
	 *
	 * @since  1.0.0
	 * @uses  storefront_is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function storefront_product_search() {
		if ( storefront_is_woocommerce_activated() ) { ?>
			<div id="search-menu">
				<div class="outer-nav-container">
					<img src="<?php echo get_stylesheet_directory_uri();?>/assets/images/icon-close.svg" alt="" class="menu-close">
				</div>
				<div class="grid-container">
					<?php get_product_search_form(); ?>
					<div class="category-container mpush-1 hd-8 push-3">
						<h2 class="search-cat-header">Categories</h2>
						<div class="row no-margin">

							<?php
								$locations = get_nav_menu_locations();
								$menu_id = $locations[ 'search-left' ];
								wp_nav_menu(
									array(
										'menu'				=> $menu_id,
										'menu_class'		=> 'category-group column hd-5 no-margin-left',
										'container'			=> '',
										)
								);
							?>

							<?php
								$locations = get_nav_menu_locations();
								$menu_id = $locations[ 'search-right' ];
								wp_nav_menu(
									array(
										'menu'				=> $menu_id,
										'menu_class'		=> 'category-group column hd-5 no-margin-left',
										'container'			=> '',
										)
								);
							?>

						</div>
					</div>
				</div>
			</div>
		<?php
		}
	}
}

if ( ! function_exists( 'storefront_site_branding' ) ) {
	/**
	 * Site branding wrapper and display
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_site_branding() {
		?>
        <div id="brand">
            <?php storefront_site_title_or_logo(); ?>
        </div>
		<?php
	}
}

if ( ! function_exists( 'storefront_header_cart' ) ) {
	/**
	 * Display Header Cart
	 *
	 * @since  1.0.0
	 * @uses  storefront_is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function storefront_header_cart() {
		if ( storefront_is_woocommerce_activated() ) {
			if ( is_cart() ) {
				$class = 'current-menu-item';
			} else {
				$class = '';
			}
		?>
		<div id="cart-item-count"><?php echo WC()->cart->get_cart_contents_count();?></div>
		<div id="cart-button">
			<a class="" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'storefront' ); ?>">
				<img src="<?php echo get_stylesheet_directory_uri();?>/assets/images/icon-cube.svg" alt="shopping cart button">
			</a>
		</div>
		<?php
		}
	}
}

if ( ! function_exists( 'storefront_primary_navigation' ) ) {
	/**
	 * Display Primary Navigation
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_primary_navigation() {
		?>
		<nav id="nav-menu" class="" role="navigation" aria-label="<?php esc_html_e( 'Primary Navigation', 'storefront' ); ?>">
			<div class="outer-nav-container">
				<img src="<?php echo get_stylesheet_directory_uri();?>/assets/images/icon-close.svg" alt="" class="menu-close">
			</div>
			<div class="grid-container">
				<div class="row no-margin">
				<?php
				wp_nav_menu(
					array(
						'theme_location'	=> 'primary',
						'menu_id'			=> 'primary-nav',
						'menu_class'		=> 'column mpush-1 hd-3 push-2 no-margin-left',
						'container'			=> '',
						)
				);
				?>

				<?php
				$locations = get_nav_menu_locations();
				$menu_id = $locations[ 'social-menu' ];
				wp_nav_menu(
					array(
						'menu'				=> $menu_id,
						'menu_id'			=> 'social-nav',
						'menu_class'		=> 'column mpush-1 hd-3 push-1',
						'container'			=> '',
						)
				);
				?>

				<div class="menu-brand column hd-2 push-1">
					<img src="<?php echo get_stylesheet_directory_uri();?>/assets/images/logo-diamond.svg" alt="">
				</div>

				</div>
			</div>
		</nav><!-- #site-navigation -->
		<?php
	}
}

if ( ! function_exists( 'storefront_credit' ) ) {
	/**
	 * Display the theme credit
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_credit() {
		?>
		<div class="footer-brand column hd-2 no-margin-left">
			<img src="<?php echo get_stylesheet_directory_uri();?>/assets/images/logo-diamond.svg" alt="">
			<div class="legal">
				© Ben Lido <?php echo date('Y');?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'storefront_handheld_footer_bar' ) ) {
	/**
	 * Display a menu intended for use on handheld devices
	 *
	 * @since 2.0.0
	 */
	function storefront_handheld_footer_bar() {
		return false;
	}
}



function bl_storefront_before_header () {
    echo '<div id="ui-cover"></div>';
}

function bl_storefront_burger() {
    echo '<div id="burger-button"><img src="' . get_stylesheet_directory_uri() . '/assets/images/icon-menu.svg" alt="navigation toggle"></div>';
}

function bl_storefront_search_button() {
    echo '<div id="search-button"><img src="' . get_stylesheet_directory_uri() . '/assets/images/icon-search.svg" alt="search toggle"></div>';
}

function bl_storefront_main_content_wrapper_start() {
	echo '<div class="grid-container">';
}
function bl_storefront_main_content_wrapper_end() {
	echo '</div>';
}

function bl_footer_menus() {
	?>
	<div id="link-container" class="column hd-10 no-margin-right">
		<div class="row no-margin">
			<?php
				$locations = get_nav_menu_locations();
				$menu_id = $locations[ 'footer' ];
				wp_nav_menu(
					array(
						'menu'				=> $menu_id,
						'menu_class'		=> 'footer-links column hd-4 push-1',
						'container'			=> '',
						)
				);
			?>

			<?php
				$locations = get_nav_menu_locations();
				$menu_id = $locations[ 'social-menu-footer' ];
				wp_nav_menu(
					array(
						'menu'				=> $menu_id,
						'menu_class'		=> 'footer-links column hd-4 push-1',
						'container'			=> '',
						)
				);
			?>

			<?php
				$locations = get_nav_menu_locations();
				$menu_id = $locations[ 'contact' ];
				wp_nav_menu(
					array(
						'menu'				=> $menu_id,
						'menu_class'		=> 'footer-links column hd-4 push-1',
						'container'			=> '',
						)
				);
			?>


		</div>
	</div>
	<?php
}

if (!function_exists('bl_add_frequency_to_session')) {
	function bl_add_frequency_to_session() {
		if (isset($_POST['frequency'])) {
			$frequency = intval($_POST['frequency']);
			if( function_exists('WC')) {
				WC()->session->set( 'frequency', $frequency);
				global $woocommerce;
				$cart_url = $woocommerce->cart->get_cart_url();
				wp_redirect($cart_url);
			}
		}
	 }
}


