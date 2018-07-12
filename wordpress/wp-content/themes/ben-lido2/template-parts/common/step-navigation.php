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
    $next_copy = get_field('next_copy');
    $next_css_class = get_field('next_css_class');
    $stepNavigation = array(
        'previousStep' => $previous_url,
        'back' => $previous_copy,
        'current' => $current_section_name,
        'nextStep' => $next_url,
        'next' => $next_copy,
        'nextCss' => $next_css_class
    );
}
if (is_shop() || is_product_category()) {
    // if it is shop page, let's see if we're in a middle of adding or swapping

    $is_kit_add = false;
    $is_swap = false;
    $kit_url = '';
    if (function_exists('bl_is_kit_add')) {
        $is_kit_add = bl_is_kit_add();
    }

    if (function_exists('bl_is_swap')) {
        $is_swap = bl_is_swap();
    }

    if ($is_kit_add == true || $is_swap == true) {
        if (function_exists('bl_get_current_kit_id')) {
            $kit_id = bl_get_current_kit_id();
        }
        if (!empty($kit_id) && function_exists('bl_get_kit_page_url')) {
            $kit_url = bl_get_kit_page_url($kit_id);
        }
        $previousStep = $kit_url;
        $back = 'Back to Kit Page';
        $current = 'Add Item to Kit';
        $nextStep = '#';
        $bext = '';
        $stepNavigation = array(
            'previousStep' => $previousStep,
            'back' => $back,
            'current' => $current,
            'nextStep' => $nextStep,
            'next' => $next
        );
    }
    else {
        // else, we assume you are building your own kit, and just go back to the bags page or go to the frequency page
        if (function_exists('get_field')) {
            $delivery_frequency_page = get_field('delivery_frequency_page','option');
            $bags_page = get_field('bags_page','option');
        }
        if (!empty($delivery_frequency_page) && is_object($delivery_frequency_page)) {
            $nextStep = get_permalink($delivery_frequency_page);
        }
        if (!empty($bags_page) && is_object($bags_page)) {
            $previousStep = get_permalink($bags_page);
        }
        $back = 'Back to Bags';
        $current = 'Add Item to Kit';
        $next = 'Delivery Frequency';
        $stepNavigation = array(
            'previousStep' => $previousStep,
            'back' => $back,
            'current' => $current,
            'nextStep' => $nextStep,
            'next' => $next
        );
    }
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