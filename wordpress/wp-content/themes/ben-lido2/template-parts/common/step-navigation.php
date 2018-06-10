<?php
$data = array();
$stepNavigation = array();
if (function_exists('get_field')) {
    // maybe we put the breadcrumb data here
    $previous_url = get_field('previous_url');
    $previous_copy = get_field('previous_copy');
    $current_section_name = get_field('current_section_name');
    $next_url = get_field('next_url');
    $next_copy = get_field('next_copy');
    $stepNavigation = array(
        'previousStep' => $previous_url,
        'back' => $previous_copy,
        'current' => $current_section_name,
        'nextStep' => $next_url,
        'next' => $next_copy
    );
}
if (empty($stepNavigation)) {
    $stepNavigation = array(
        'previousStep' => '/',
        'back' => 'Back',
        'current' => 'Pick a bag',
        'nextStep' => '/products',
        'next' => 'Next'
    );
}
// forcing no nav
//$stepNavigation = null;
$data = array('stepNavigation'=>$stepNavigation);
Timber::render( 'common/step-navigation.twig', $data);