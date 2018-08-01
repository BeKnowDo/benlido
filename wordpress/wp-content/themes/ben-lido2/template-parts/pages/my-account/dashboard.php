
<!-- Start showing kit frequency -->
<div class="max-width-md">
    <?php
    // get the kits that will be shipped
    $user_id = get_current_user_id();
    $kits = array();
    if ($user_id > 0 && function_exists('get_field')) {
        $recurring_orders = get_field('recurring_orders','user_'.$user_id);
        // var_dump($recurring_orders);
    }
    
    if (function_exists('bl_get_kit_future_shippings') && !empty($recurring_orders)) {
        $kits = bl_get_kit_future_shippings($recurring_orders);
    }
    
    // var_dump($kits);
    // var_dump(is_array($kits));

    // kits loop
    if (!empty($kits) && is_array($kits)) :
    ?>
        <div class="columns delivery-frequency-tile-container">
        <h2 class="column col-12 delivery-frequency-tile-header">My Subscriptions</h2>
        
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
<!-- End showing kit frequency -->