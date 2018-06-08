<?php
$data = array();
if (empty($heroData)) {
    $heroData = array(
        'header'=> 'PICK YOUR TRAVEL COMPANION',
        'copy'=> 'No shipping costs and all personal care items up to 75% free with bag purchase'
    );
}
$data = array('heroData'=>$heroData);
Timber::render( 'common/step-navigation.twig', $data);