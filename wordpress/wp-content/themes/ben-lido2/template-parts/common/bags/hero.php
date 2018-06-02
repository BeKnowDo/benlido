<div id="hero" class="foam">
    <div class="grid-container">
        <div class="row no-margin">
            <div id="hero-copy" class="column hd-5 no-margin-left">
                <h1><?php the_title();?></h1>
                <?php the_content();?>
                    <div class="button-container center">
                    <?php
                        if (function_exists('get_field')) {
                            $hero_first_button_copy = get_field('hero_first_button_copy');
                            $hero_first_button_url = get_field('hero_first_button_url');
                            $hero_second_button_copy = get_field('hero_second_button_copy');
                            $hero_second_button_url = get_field('hero_second_button_url');
                            $hero_image = get_field('hero_image');
                            $hero_image_retina = get_field('hero_image_retina');
                            $float = ' float';
                            if (!empty($hero_first_button_copy) && empty($hero_second_button_copy)) {
                                $float = ' center';
                            }
                        }
                    ?>
                    <?php if (!empty($hero_first_button_copy)):?>
                        <a href="<?php echo $hero_first_button_url;?>" class="generic-button <?php echo $float;?>" title="<?php echo esc_attr($hero_first_button_copy);?>"><span><?php echo $hero_first_button_copy;?></span></a>
                    <?php endif;?>
                    <?php if (!empty($hero_second_button_copy)):?>
                        <a href="<?php echo $hero_second_button_url;?>" class="generic-button <?php echo $float;?>" title="<?php echo esc_attr($hero_second_button_copy);?>"><span><?php echo $hero_second_button_copy;?></span></a>
                    <?php endif;?>
                    </div>
            </div>
            <?php if (!empty($hero_image)):?>
            <div id="hero-image" class="column hd-7 no-margin-right">
                <img src="<?php echo $hero_image['url'];?>" srcset="<?php if (!empty($hero_image_retina)) { echo $hero_image_retina['url'] . ' 2x ';} ?>" alt="<?php echo esc_attr($hero_image['title']);?>">
            </div>
            <?php endif;?>
        </div>
    </div>
</div>