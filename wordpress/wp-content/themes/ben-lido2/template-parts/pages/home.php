<?php 
$show_hero_section = $show_feature_cards = $show_bottom_section = false;
if (function_exists('get_field')) {
    $show_hero_section = get_field('show_hero_section');
    $show_feature_cards = get_field('show_feature_cards');
    $show_bottom_section = get_field('show_bottom_section');
}

if ($show_hero_section == true) {
    get_template_part( 'template-parts/partials/home/hero'); 
}

if ($show_feature_cards == true) {
    get_template_part( 'template-parts/partials/home/feature','cards'); 
}

if ($show_bottom_section == true) {
    get_template_part( 'template-parts/partials/home/bottom'); 
}

