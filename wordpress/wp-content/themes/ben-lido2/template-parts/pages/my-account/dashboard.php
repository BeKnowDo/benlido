<div class="max-width-md">
<?php
// get the kits that will be shipped
$user_id = get_current_user_id();
$kits = array();
if ($user_id > 0 && function_exists('get_field')) {
    $recurring_orders = get_field('recurring_orders','user_'.$user_id);
}

if (function_exists('bl_get_kit_future_shippings') && !empty($recurring_orders)) {
    $kits = bl_get_kit_future_shippings($recurring_orders);
}



// kits loop
if (!empty($kits) && is_array($kits)) :
?>
    <div class="columns shipping-tile-container">
    <?php foreach ($kits as $kit) :?>
    <?php
        $data = array(
            'header' => $kit['recurring_name'],
            'copy' => 'Your next kit will arrive ' . date(get_option( 'date_format' ),strtotime($kit['next_send_date'])),
        );
    ?>
    <?php Timber::render( 'common/shipping/my-account.twig', $data);?>
    <?php endforeach;?>
    </div>
<?php endif;?>
</div>
