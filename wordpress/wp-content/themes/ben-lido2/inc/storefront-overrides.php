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

	if (is_cart()) {
		add_action('storefront_page','bl_add_back_to_top',100);
	}


	add_action('bl_thank_you','bl_add_frequency_to_thankyou',10);
	add_action( 'wp_print_scripts', 'bl_remove_password_strength', 10 ); // remove strong password check
	add_action('woocommerce_account_dashboard', 'bl_my_account_dashboard',10);

	add_action('woocommerce_product_tabs','bl_remove_product_tabs',99);

	add_action( 'woocommerce_single_product_summary', 'bl_product_detail_description', 80 );

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

	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 ); // moving related products down

	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

	if (!is_product_category() && !is_shop()) {
		add_action('woocommerce_after_main_content', 'woocommerce_output_related_products', 100); // moving related products down
	}


	// also removing all the storefront_footer actions as well
	remove_action('storefront_footer','storefront_footer_widgets',10);
	remove_action('storefront_footer','storefront_credit',20);

	remove_action('woocommerce_sidebar','woocommerce_get_sidebar',10);

	if (is_woocommerce()) {
		if (!is_search()) {
			add_action('woocommerce_before_main_content','bl_breadcrumb',4);
		}

		if (is_shop() && !is_search() ) {
			//add_action('woocommerce_before_main_content','bl_bag_hero',5);
		}

		if (is_shop() || is_product_category() || is_product_tag()) {
			if (!is_search()) {
				add_action('woocommerce_before_main_content','bl_header',5);
			}

		}

		if(is_product_category() || is_shop()) {
			if (!is_search()) {
				add_action('woocommerce_sidebar', 'bl_back_to_top', 50);
			// the int is the order of where it's placed
			}

		}

	}

}

add_action( 'wp', 'bl_storefront_overrides' );


// check for empty-cart get param to clear the cart
add_action( 'init', 'woocommerce_clear_cart_url' );
function woocommerce_clear_cart_url() {
  global $woocommerce;
	
	if ( isset( $_GET['empty-cart'] ) ) {
		$woocommerce->cart->empty_cart(); 
	}
}


// adding my accounts kits menu and subpage
function bl_add_my_account_endpoints() {
	add_rewrite_endpoint( 'mykits', EP_PAGES );
}
add_action( 'init', 'bl_add_my_account_endpoints' );

include_once (get_stylesheet_directory(). '/inc/my-account/kits.php');
add_action( 'woocommerce_account_mykits_endpoint','bl_account_mykits_endpoint');

// adding it to the my accounts menu
add_filter( 'woocommerce_account_menu_items', 'bl_account_menu_items');

function bl_account_menu_items( $items ) {
	// adding kits to 2nd position
	//print_r ($items);
	$kits_menu = array('kits'=>'Kits');
	$holder = array();
	$k=0;
	foreach ($items as $key => $item) {
		if ($k == 2) {
			$holder['mykits'] = 'Kits';
		}
		$k++;
		$holder[$key] = $item;
	}
	return $holder;
}

/**
 * Change number of products that are displayed per page (shop page)
 */
add_filter( 'loop_shop_per_page', 'bl_loop_shop_per_page', 20 );

function bl_loop_shop_per_page( $cols ) {
  // $cols contains the current number of products per page based on the value stored on Options -> Reading
  // Return the number of products you wanna show per page.
  $cols = 100;
  return $cols;
}


function bl_remove_product_page_skus( $enabled ) {
    if ( ! is_admin() && is_product() ) {
        return false;
    }

    return $enabled;
}
add_filter( 'wc_product_sku_enabled', 'bl_remove_product_page_skus' );


function bl_login_redirect( $redirect, $user ) {
    $redirect_page_id = url_to_postid( $redirect );
    $checkout_page_id = wc_get_page_id( 'checkout' );

    if( $redirect_page_id == $checkout_page_id ) {
        return $redirect;
    }

    return wc_get_page_permalink( 'shop' );
}

add_filter( 'woocommerce_login_redirect', 'bl_login_redirect' );

function bl_add_back_to_top() {
	get_template_part( 'template-parts/common/back-to','top' );
}


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
		if (is_shop() || is_product_category() || is_product_tag() || is_cart() || is_checkout()):
		?>
		<div class="bg-grey">
			<?php if (is_search()):?>
				<?php get_template_part( 'template-parts/common/navigation/search-in','page' );?>
			<?php endif;?>
			<div class="max-width-xl shop-landing-featured">
				<div class="columns">
					<?php if (!is_search()):?>
						<?php do_action( 'storefront_sidebar' );?>
					<?php endif;?>
	<?php
		endif;
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
		if (is_shop() || is_product_category() || is_product_tag() || is_cart() || is_checkout()):
		?>

				</div>
			</div>
		</div>
		<?php
		endif;
	}
}

add_filter( 'the_title', 'bl_order_received_title', 10, 2 );
function bl_order_received_title( $title, $id ) {
	if ( function_exists( 'is_order_received_page' ) &&
	     is_order_received_page() && get_the_ID() === $id ) {
		$title = "Welcome to the Club";
	}
	return $title;
}


function bl_in_page_search_form() {
	get_template_part('template-parts/common/navigation/search-in','page');
}


function bl_back_to_top () {
	get_template_part('template-parts/common/back-to','top');
}

function bl_breadcrumb() {
	get_template_part('template-parts/common/step','navigation');
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
		if (is_search()) {
			echo '<div id="shop-landing-featured-products" class="column col-xs-12 col-sm-12 col-md-12 shop-landing-featured-products">';
		} else {
			echo '<div id="shop-landing-featured-products" class="column col-xs-12 col-sm-12 col-md-12 col-9 shop-landing-featured-products">';
		}

	} else {
		echo '<div class="column col-xs-12 col-sm-12 col-md-12 col-9 shop-landing-featured-products">';
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
		echo '<div class="">';
	}
}

function bl_storefront_main_content_wrapper_end() {
	echo '</div>';
}

function bl_bag_hero() {
	get_template_part('template-parts/common/hero/hero-product','list');
}

function bl_header() {
	get_template_part('template-parts/common/hero/hero-title','copy');
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

function bl_add_frequency_to_thankyou($order) {
	global $current_order;
	$current_order = $order;
	get_template_part( 'template-parts/pages/thankyou/frequency');
}


function bl_remove_password_strength() {
    wp_dequeue_script( 'wc-password-strength-meter' );
}

function bl_my_account_dashboard() {
	get_template_part( 'template-parts/pages/my-account/dashboard');
}

function bl_remove_product_tabs() {
	return array();
}

function bl_product_detail_description() {
	global $post;
	echo '<div class="description">';
	if (is_object($post)) {
		echo $post->post_content;
	}
	

	echo '</div>';
	$dont_show = false;
	// disclaimer
	// exception is the bag
	if (function_exists('get_field')) {
		$bag_category = get_field('bag_category','option');
	}
	if (function_exists('bl_get_product_category')) {
		$cat = bl_get_product_category($post->ID);
	}
	if (!empty($bag_category) && !empty($cat) && is_object($cat)) {
		if ($bag_category == $cat->term_id) {
			$dont_show = true;
		}
	}
	
	$disclaimer = '<div class="disclaimer"><strong>Disclaimer:</strong> While we work to ensure that product information is correct, on occasion manufacturers may alter their ingredient lists. Actual product packaging and materials may contain more and/or different information than that shown on our Web site. We recommend that you do not solely rely on the information presented and that you always read labels, warnings, and directions before using or consuming a product. For additional information about a product, please contact the manufacturer. Content on this site is for reference purposes and is not intended to substitute for advice given by a physician, pharmacist, or other licensed health-care professional. You should not use this information as self-diagnosis or for treating a health problem or disease. Contact your health-care provider immediately if you suspect that you have a medical problem. Information and statements regarding dietary supplements have not been evaluated by the Food and Drug Administration and are not intended to diagnose, treat, cure, or prevent any disease or health condition. BenLido.com assumes no liability for inaccuracies or misstatements about products.</div>';

	if ($dont_show == false) {
		echo $disclaimer;
	}
	
}




