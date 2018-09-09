<?php
$data = array();
$header = get_the_title();
$copy = get_the_content();
$triangleBackground = true;
if (function_exists('get_field')) {
    $hero_image = get_field('hero_image');
    $pre_header = get_field('pre_header');
    $hero_call_to_action_buttons = get_field('hero_call_to_action_buttons');


    if (!empty(get_field('hero_image_companion'))) {
      $companion_image = get_field('hero_image_companion')['url'];
    } else {
      $companion_image = NULL;
    }

    if (!empty($hero_image) && isset($hero_image['url'])) {
      $image = $hero_image['url'];
    }

}
if (function_exists('bl_process_acf_buttons')) {
    $hero_call_to_action_buttons = bl_process_acf_buttons($hero_call_to_action_buttons);
}
$homePageHero = array(
    'triangleBackground' => $triangleBackground,
    'preheader' => $pre_header,
    'header' => $header,
    'copy'=>$copy,
    'image'=>$image,
    'hero_call_to_action_buttons' => $hero_call_to_action_buttons,
    'hero_image_companion' => $companion_image
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
                'altText'=> 'Hero image alt text',
                'hero_call_to_action_buttons' => array(
                    array("title"=>"Build your own kit","url"=>"https:\/\/www.urbanpixels.com\/","classes"=>"","target"=>true),
                    array("title"=>"Start with a prebuilt kit","url"=>"https:\/\/www.google.com\/","classes"=>"","target"=>true)
                )
            )

        )
    );
}
//print_r(json_encode($data));
//print_r(json_encode($data));
Timber::render( 'common/hero/home-hero.twig', $data);