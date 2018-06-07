<?php
$data = array();
$header = get_the_title();
$copy = get_the_content();
$triangleBackground = true;
if (function_exists('get_field')) {
    $hero_image = get_field('hero_image');
    $hero_call_to_action_buttons = get_field('hero_call_to_action_buttons');
    if (!empty($hero_image) && isset($hero_image['url'])) {
        $image = $hero_image['url'];
    }   
    
}
if (function_exists('bl_process_acf_buttons')) {
    $hero_call_to_action_buttons = bl_process_acf_buttons($hero_call_to_action_buttons);
}
$homePageHero = array(
    'triangleBackground' => $triangleBackground,
    'header' => $header,
    'copy'=>$copy,
    'image'=>$image,
    'hero_call_to_action_buttons' => $hero_call_to_action_buttons
);
if (!empty($homePageHero)) {
    $data['homePageHero'] = array($homePageHero);
}
if (empty($data)) {
    $data = array(
        'homePageHero'=> array(
            array(
                'triangleBackground'=> true,
                'header'=> 'Wander Well',
                'copy' => "Your favorite brands, in the right sizes, at the best prices—delivered to your door, ready to travel. You're good to go with Ben Lido.",
                'image'=> get_stylesheet_directory_uri() .'/assets/images/home-hero@2x.png',
                'altText'=> 'Hero image alt text'
            )
    
        )
    );
}
Timber::render( 'common/hero/home-hero.twig', $data);