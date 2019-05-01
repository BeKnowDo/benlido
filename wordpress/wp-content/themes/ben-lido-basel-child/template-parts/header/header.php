<div class="container">
<div class="wrapp-header">
    <?php echo basel_header_block_logo();?><!-- END SITE LOGO -->
    <?php echo basel_header_block_main_nav();?><!--END MAIN-NAV-->

    <div class="right-column">
        <div class="header-links my-account-with-text">
            <ul>
                <li class="my-account"><a href="http://benlido.urbanpixels.localhost/my-account/">My Account</a></li>
                <li class="logout"><a href="http://benlido.urbanpixels.localhost/my-account/customer-logout/?_wpnonce=8583282a6b">Logout</a></li>
            </ul>		
        </div>
        <div class="search-button basel-search-full-screen">
            <a href="#">
                <i class="fa fa-search"></i>
            </a>
            <div class="basel-search-wrapper">
                <div class="basel-search-inner">
                    <span class="basel-close-search">close</span>
                    <form role="search" method="get" id="searchform" class="searchform  basel-ajax-search" action="http://benlido.urbanpixels.localhost/" data-thumbnail="1" data-price="1" data-count="5" data-post_type="product">
                        <div>
                            <label class="screen-reader-text">Search for:</label>
                            <input type="text" class="search-field" placeholder="Search for products" value="" name="s" id="s" autocomplete="off">
                            <input type="hidden" name="post_type" id="post_type" value="product">
                            <button type="submit" id="searchsubmit" value="Search">Search</button>
                            
                        </div>
                    </form>
                    <div class="search-results-wrapper">
                        <div class="basel-scroll has-scrollbar">
                            <div class="basel-search-results basel-scroll-content" tabindex="0" style="right: -14px; padding-right: 14px;">
                                <div class="autocomplete-suggestions" style="position: absolute; display: none; max-height: 300px; z-index: 9999;"></div>
                            </div>
                            <div class="basel-scroll-pane" style="display: none;">
                                <div class="basel-scroll-slider" style="transform: translate(0px, 0px);"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end search -->
        <div class="shopping-cart basel-cart-design-1 basel-cart-icon cart-widget-opener">
            <a href="<?php echo esc_url(wc_get_cart_url()); ?>">
				<span><?php esc_html_e('Cart', 'basel'); ?> (<span>o</span>)</span>
				<span class="basel-cart-totals">
                <?php basel_cart_count(); ?>
					<span class="subtotal-divider">/</span> 
					<?php basel_cart_subtotal(); ?>
                </span>
			</a>
        </div>
        <div class="mobile-nav-icon">
				<span class="basel-burger"></span>
        </div><!--END MOBILE-NAV-ICON-->
    </div>
</div>
</div>