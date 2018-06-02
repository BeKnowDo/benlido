<div id="hero" class="foam">
    <div class="grid-container">
        <div class="row no-margin">
            <div id="hero-copy" class="column hd-5 no-margin-left">
                <h1><?php the_title();?></h1>
                <?php the_content();?>
                <?php
                    if (function_exists('get_field')) {
                        $call_to_action_button_text = get_field('button_copy');
                        $call_to_action_url = get_field('button_url');
                        $hero_image = get_field('hero_image');
                        $hero_image_retina = get_field('hero_image_retina');
                    }
                ?>
                <?php if (!empty($call_to_action_button_text)):?>
                    <a href="<?php echo $call_to_action_url;?>" class="generic-button center" title="<?php echo esc_attr($call_to_action_button_text);?>"><span><?php echo $call_to_action_button_text;?></span></a>
                <?php endif;?>
            </div>
            <?php if (!empty($hero_image)):?>
            <div id="hero-image" class="column hd-7 no-margin-right">
                <img src="<?php echo $hero_image['url'];?>" srcset="<?php if (!empty($hero_image_retina)) { echo $hero_image_retina['url'] . ' 2x ';} ?>" alt="<?php echo esc_attr($hero_image['title']);?>">
            </div>
            <?php endif;?>
        </div>
    </div>
</div>