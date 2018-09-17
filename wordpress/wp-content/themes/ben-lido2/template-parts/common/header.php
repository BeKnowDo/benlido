<?php
// required variables for the nested twigs:
// socialMediaLinks, className, list
$list = array();// this is the navigation list
$socialMediaLinks = array(); // this is the social media nav
$className = 'navbar-dropdown-primary-items';
$logo = '';
$diamond_logo = '';
$cart_url = get_permalink(wc_get_page_id( 'cart' ));
$my_account_url = get_permalink(wc_get_page_id( 'myaccount' ));
$logout_url = wp_logout_url('/');
$register_url = $my_account_url . '?action=register';
$user_is_logged_in = false;

if (function_exists('get_field')) {
    $bannerText = get_field('top_banner_text');
    $bannerURL = get_field('top_banner_url');
    $banner = [
        'text' => $bannerText,
        'url' => $bannerURL
    ];
}



if (is_user_logged_in()) {
    $user_is_logged_in = true;
}
if (function_exists('bl_get_site_logo')) {
    $logo = bl_get_site_logo();
    $diamond_logo = bl_get_site_logo(true);
}


if (function_exists('bl_get_top_nav')) {
    $list = bl_get_top_nav();
}
if (function_exists('bl_get_social_media_nav')) {
    $socialMediaLinks = bl_get_social_media_nav();
}

$data = array(
    'list'=>$list,
    'socialMediaLinks' => $socialMediaLinks,
    'banner' => $banner,
    'logo' => $logo, 'diamond_logo' => $diamond_logo, 'className' => $className,
    'user_is_logged_in' => $user_is_logged_in, 'my_account_url' => $my_account_url,
    'login_url' => $my_account_url,
    'logout_url' => $logout_url,
    'cart_url' => $cart_url,
    'register_url' => $register_url
);

Timber::render( 'common/header.twig', $data);