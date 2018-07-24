<?php
global $current_order;
$frequencies = array();
if (function_exists('get_field')) {
    $frequencies = get_field('frequencies','option');
}
?>
<?php get_template_part('template-parts/common/category','header');?>
<form id="shipping-frequency" method="post">
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
                    $shipping = array('header'=>$button['button_copy'],'image'=>$icon,'number_of_days'=>$number_of_days);
                    $data['shipping'] = $shipping;
                ?>
                 <div class="column col-4 col-xs-12 col-sm-12">
                    <?php Timber::render( 'common/shipping/shipping-tile.twig', $data);?>
                </div>
            <?php endforeach;?>
            
        <?php endif;?>
    </div>
</form>