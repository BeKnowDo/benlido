<?php
// get the main nav
if (function_exists('bl_get_main_nav')) {
    $nav = bl_get_main_nav();
}
$data = array('list'=>$nav);
Timber::render( 'common/navigation/navbar-desktop.twig',$data);