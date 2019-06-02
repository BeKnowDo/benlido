<?php 
$show_hero_section = $show_feature_cards = $show_bottom_section = false;
if (function_exists('get_field')) {
    $show_hero_section = get_field('show_hero_section');
}

if ($show_hero_section == true) {
    get_template_part( 'template-parts/partials/frequency/hero'); 
}


get_template_part( 'template-parts/partials/frequency/frequency'); 



