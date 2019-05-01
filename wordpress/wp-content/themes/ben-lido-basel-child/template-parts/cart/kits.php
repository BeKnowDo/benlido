<?php
$cart = array();
if (function_exists('bl_get_basel_cart')) {
    $cart = bl_get_basel_cart();
}

?>
<div class="widget bl-kits-container">
<?php print_r ($cart);?>
</div>