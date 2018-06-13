<?php
// this file contains overrides of standard storefront theme functions or actions

function bl_storefront_overrides() {
    //add_action('storefront_before_header','bl_storefront_before_header',10); // adding custom header cover
    //add_action('storefront_header','bl_storefront_burger',10); // adding menu burger
	//add_action('storefront_header','bl_storefront_search_button',10); // adding search button
	
	//add_action('storefront_before_content','storefront_primary_navigation',5);
	//add_action('storefront_before_content','storefront_product_search',6);
	if (is_woocommerce() || is_cart() || is_checkout()) {
		add_action('woocommerce_before_main_content','bl_storefront_main_content_wrapper_start',10);
		add_action('woocommerce_after_main_content','bl_storefront_main_content_wrapper_end',99);
	}


	//add_action('storefront_footer','bl_footer_menus',10);

	// basically removed all the storefront_header action because we are removing it from the template itself to accommodate for how Cesar is building the template
	remove_action('storefront_header','storefront_skip_links',0);
	remove_action('storefront_header','storefront_social_icons',10);
	remove_action('storefront_header','storefront_site_branding',20);
	remove_action('storefront_header','storefront_primary_navigation_wrapper',42);
	remove_action('storefront_header','storefront_secondary_navigation ',30);
	remove_action('storefront_header','storefront_primary_navigation',50);
	remove_action('storefront_header','storefront_header_cart',60);
    remove_action('storefront_header','storefront_primary_navigation_wrapper_close',68);
	remove_action('storefront_header','storefront_product_search',40);

	remove_action( 'storefront_content_top','woocommerce_breadcrumb', 10, 0);
	remove_action( 'woocommerce_after_shop_loop','storefront_sorting_wrapper', 9 );
	remove_action( 'woocommerce_after_shop_loop', 'storefront_sorting_wrapper_close', 31 );
	remove_action( 'woocommerce_before_shop_loop', 'storefront_sorting_wrapper', 9 );
	remove_action( 'woocommerce_before_shop_loop', 'storefront_sorting_wrapper_close', 31 );
	remove_action('woocommerce_before_shop_loop','woocommerce_catalog_ordering',30);
	remove_action( 'woocommerce_after_shop_loop','woocommerce_catalog_ordering',10 );
	remove_action( 'woocommerce_before_shop_loop','woocommerce_catalog_ordering',10 );
	remove_action('woocommerce_before_shop_loop','woocommerce_result_count',20);
	remove_action( 'woocommerce_after_shop_loop','woocommerce_result_count',20 );

	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 30 );
	remove_action( 'woocommerce_before_shop_loop', 'storefront_woocommerce_pagination', 30 );

	// also removing all the storefront_footer actions as well
	remove_action('storefront_footer','storefront_footer_widgets',10);
	remove_action('storefront_footer','storefront_credit',20);

	if (is_woocommerce()) {
		add_action('woocommerce_before_main_content','bl_breadcrumb',4);
		add_action('woocommerce_before_main_content','bl_header',5);
	}
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


if ( ! function_exists( 'storefront_before_content' ) ) {
	/**
	 * Before Content
	 * Wraps all WooCommerce content in wrappers which match the theme markup
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function storefront_before_content() {
		?>
		<div class="bg-grey">
			<div class="max-width-xl shop-landing-featured">
				<div class="columns">
		<?php do_action( 'storefront_sidebar' );?>
	<?php
	}
}

if ( ! function_exists( 'storefront_after_content' ) ) {
	/**
	 * After Content
	 * Closes the wrapping divs
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function storefront_after_content() {
		?>
				</div>
			</div>
		</div>
		<?php
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
	if (is_product()) {
		echo '<div class="max-width-xl">';
	} elseif (is_shop()) {
		echo '<div id="shop-landing-featured-products" class="column col-xs-12 col-sm-12 col-md-12 col-9 shop-landing-featured-products">';
	} else {
		echo '<div class="max-width-xl">';
	}
	
}

if ( ! function_exists( 'storefront_product_columns_wrapper' ) ) {
	/**
	 * Product columns wrapper
	 *
	 * @since   2.2.0
	 * @return  void
	 */
	function storefront_product_columns_wrapper() {
		$columns = storefront_loop_columns();
		echo '<div class="columns">';
	}
}

function bl_storefront_main_content_wrapper_end() {
	echo '</div>';
}

function bl_header() {
	get_template_part('template-parts/common/hero/hero-title','copy');
}

function bl_breadcrumb() {
	get_template_part('template-parts/common/step','navigation');
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


