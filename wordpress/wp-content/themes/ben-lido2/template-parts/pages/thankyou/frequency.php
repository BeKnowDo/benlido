<?php
global $current_order;
$order_id = 0;
$key = $_REQUEST['key'];
if (empty($key)) {
    $key = $_POST['key'];
}
if (!empty($current_order) && is_object($current_order)) {
    $order_id = $current_order->get_order_number();
}
$frequencies = array();
if (function_exists('get_field')) {
    $frequencies = get_field('frequencies','option');
    $super_header = get_field('super_header','option');
    $header = get_field('header','option');
    $blurb = get_field('blurb','option');
}
// we will only show frequency selector if we have an order ID
?>
<?php if ($order_id > 0):?>
    <form id="shipping-frequency" method="post">
        <input type="hidden" name="order_id" value="<?php echo $order_id;?>" />
        <input type="hidden" name="key" value="<?php echo $key;?>" />
        <input id="freq" type="hidden" name="freq" value="" />
        <header class="entry-header"><h1 class="entry-title"><?php echo $header;?></h1></header>
        <div class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo $blurb;?></div>
        <div class="columns shipping-tile-container">
            
            <?php if (!empty($frequencies) && is_array($frequencies)):?>
                
                <?php foreach ($frequencies as $button):?>
                    <?php
                        //print_r ($button);
                        $number_of_days = $button['number_of_days'];
                        if (empty($number_of_days) || $number_of_days == 0) {
                            $number_of_days = -1;
                        }
                        $icon = $button['icon'];
                        if (!empty($icon) && is_array($icon) && isset($icon['url'])) {
                            $icon = $icon['url'];
                        }
                        $shipping = array('header'=>$button['button_copy'],'image'=>$icon,'copy'=>$button['frequency_copy'],'number_of_days'=>$number_of_days);
                        $data['shipping'] = $shipping;
                    ?>
                    <div class="column col-4 col-xs-12 col-sm-12">
                        <?php Timber::render( 'common/shipping/shipping-tile.twig', $data);?>
                    </div>
                <?php endforeach;?>
                
            <?php endif;?>
        </div>
    </form>
<?php endif;?>