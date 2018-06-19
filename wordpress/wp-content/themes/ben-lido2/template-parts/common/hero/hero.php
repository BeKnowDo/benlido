<?php
$data = array();
$homePageHeroes = array();
// uses the featured cards section
if (function_exists('get_field')) {
    $left_feature_cards = get_field('left_feature_cards');
    $right_feature_cards = get_field('right_feature_cards');
    // this is made to be a carousel.. for now, we're just taking the first one

}
if (!empty($left_feature_cards) && is_array($left_feature_cards)) {
    $left_feature_card = $left_feature_cards[0];
}
if (!empty($right_feature_cards) && is_array($right_feature_cards)) {
    $right_feature_card = $right_feature_cards[0];
}

//print_r ($right_feature_card);
if (!empty($left_feature_card)) {
    if (isset($left_feature_card['image'])) {
        $left_feature_card['image'] = $left_feature_card['image']['url'];
    }
    if (isset($left_feature_card['title'])) {
        $left_feature_card['header'] = $left_feature_card['title'];
    }
}
if (!empty($right_feature_card)) {
    if (isset($right_feature_card['image'])) {
        $right_feature_card['image'] = $right_feature_card['image']['url'];
    }
    if (isset($right_feature_card['title'])) {
        $right_feature_card['header'] = $right_feature_card['title'];
    }
}
$homePageHeroes = array( $left_feature_card,$right_feature_card);
if (!empty($homePageHeroes)) {
    $data['homePageHeroes'] = $homePageHeroes;
}

if (empty($data)) {
    $data = array(
        'homePageHeroes' => array(
            array(
                'header' => 'How it Works',
                'copy' => "Premium, personalized bags packed with the products you love, delivered anywhere, with easy subscription reorders.",
                'image' => '/images/hero-1.png'
            ),   
            array(
                'header'=> 'The Brands You Love',
                'copy'=> "Unlike the aisle at your local retailer, if you want it, we'll have it - and we'll deliver it - right to your doorstep.",
                'image'=> '/images/hero-2.png'
            )
    
        )
    );
}

Timber::render( 'common/hero/hero.twig', $data);