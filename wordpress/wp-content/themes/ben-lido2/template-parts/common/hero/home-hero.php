<?php
$data = array();
if (empty($data)) {
    $data = array(
        'homePageHero'=> array(
            array(
                'triangleBackground'=> true,
                'header'=> 'Wander Well',
                'copy' => "Your favorite brands, in the right sizes, at the best prices—delivered to your door, ready to travel. You're good to go with Ben Lido.",
                'image'=> '/images/home-hero@2x.png',
                'altText'=> 'Hero image alt text'
            )
    
        )
    );
}

Timber::render( 'common/hero/home-hero.twig', $data);