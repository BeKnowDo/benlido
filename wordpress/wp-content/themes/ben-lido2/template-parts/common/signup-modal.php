<?php
$enable_signup_popup = false;
$contact_form = null;
if (function_exists('get_field')) {
    $enable_signup_popup = get_field('enable_signup_popup','option');
    $popup_header = get_field('popup_header','option');
    $popup_copy = get_field('popup_copy','option');
    $contact_form = get_field('contact_form','option');
}
if (!empty($contact_form)) {
    $signup_field_id = $contact_form->ID;
}
?>
<?php if ($enable_signup_popup == true):?>

    <div class="modal-subscribe bl-newsletter-capture" id="modalSubscribe" >
        <div class="inner-content">
            <h3><?php echo $popup_header;?></h3>
            <div class="intro"><?php echo $popup_copy;?></div>
            <?php echo do_shortcode('[mc4wp_form id="' . $signup_field_id . '"]'); ?>
        </div>

    </div>
<?php endif;?>