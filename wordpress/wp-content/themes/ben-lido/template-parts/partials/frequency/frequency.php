<?php
    $super_header = $header = '';
    $blurb = '';
    $frequencies = array();
    if (function_exists('get_field')) {
        $super_header = get_field('super_header');
        $header = get_field('header');
        $blurb = get_field('blurb');
        $frequencies = get_field('frequencies');
    }
    //print_r ($frequencies);
?>
<form id="frequency-form" method="post">
<a name="frequency"></a>
<input class="input-frequency" type="hidden" name="frequency" value="0"/>
<div id="frequency" class="anchor-card-container grid-container pad-top">
    <div class="column hd-12 no-margin">
        <div class="anchor-card">
            <div class="anchor-info-container">
                <div class="anchor-content center hd-5">
                    <h2><?php echo $super_header;?></h2>
                    <h1><?php echo $header;?></h1>
                    <?php echo $blurb;?>
                </div>
                <div class="button-container center">
                    <?php if (!empty($frequencies) && is_array($frequencies)):?>
                    <?php foreach ($frequencies as $button):?>
                    <button type="submit" value="<?php echo $button['number_of_days'];?>" class="generic-button float"><span><?php echo $button['button_copy'];?></span></button>
                    <?php endforeach;?>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>
</form>