<?php
$data = array();
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