<?php
// required variables for the nested twigs:
// socialMediaLinks, className, list
$list = array();// this is the navigation list
$socialMediaLinks = array(); // this is the social media nav
$className = '';
if (function_exists('bl_get_top_nav')) {
    $list = bl_get_top_nav();
}
if (function_exists('bl_get_social_media_nav')) {
    $socialMediaLinks = bl_get_social_media_nav();
}
$data = array('list'=>$list,'socialMediaLinks'=>$socialMediaLinks);
Timber::render( 'common/header.twig', $data);