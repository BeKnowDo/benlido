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

    <section class="modal-subscribe bl-newsletter-modal" id="modalSubscribe">

        <div class="bg-grey">

            <div class="bl-newsletter-modal-content">
                <h1 class="bl-newsletter-modal-header">
                    <?php echo $popup_header;?>
                </h1>
                <div class="bl-newsletter-modal-copy">
                    <?php echo $popup_copy;?>
                </div>
            </div>

            <div class="bl-newsletter-modal-form">
                <?php echo do_shortcode('[mc4wp_form id="' . $signup_field_id . '"]'); ?>
            </div>

        </div>

    </section>

<?php endif;?>