<?php
    $bottom_background_image = null;
    $bottom_background_image_retina = null;
    $bottom_copy = $bottom_button_copy = $bottom_button_link = null;
    $image = '';
    if (function_exists('get_field')) {
        $bottom_background_image = get_field('bottom_background_image');
        $bottom_background_image_retina = get_field('bottom_background_image_retina');
        $bottom_copy = get_field('bottom_copy');
        $bottom_button_copy = get_field('bottom_button_copy');
        $bottom_button_link = get_field('bottom_button_link');
    }
    if (!empty($bottom_background_image)) {
        $retina = '';
        if (!empty($bottom_background_image_retina)) {
            $retina = 'srcset="' . $bottom_background_image_retina['url'] . ' 2x"';
        }
        $image = '<img src="' . $bottom_background_image['url'] . '" title="' . esc_attr($bottom_background_image['title']) . '" ' . $retina . ' />';
    }
?>
<div id="adventure" class="anchor-card-container grid-container">
    <div class="column hd-12 no-margin">
        <div class="anchor-card">
            <?php echo $image;?>
            <div class="anchor-info-container">
                <div class="anchor-content center hd-7">
                    <h2><?php echo $bottom_copy;?></h2>
                </div>
                <?php if (!empty($bottom_button_copy)):?>
                    <a href="<?php echo $bottom_button_link;?>" class="generic-button center"><span><?php echo $bottom_button_copy;?></span></a>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>