<?php
    $left_feature_cards = array();
    $right_feature_cards = array();
    $activate_left_carousel = false;
    $activate_right_carousel = false;
    if (function_exists('get_field')) {
        $left_feature_cards = get_field('left_feature_cards');
        $right_feature_cards = get_field('right_feature_cards');
    }
    //print_r ($left_feature_cards);
    //print_r ($right_feature_cards);
    // see if we have a carousel or just 1
    if (count($left_feature_cards)> 1) {
        $activate_left_carousel = true;
    }
    if (count($right_feature_cards)> 1) {
        $activate_right_carousel = true;
    }
?>
<div id="feature-cards" class="grid-container">
    <div class="row no-margin">

        <div class="card-container column hd-6 no-margin-left ">
            <div class="<?php echo ($activate_left_carousel == true ? ' owl-carousel owl-theme ':'');?> left-cards">
                <?php if (count($left_feature_cards) > 0):?>
                <?php foreach ($left_feature_cards as $left_card):?>
                <div class="item">
                    <div class="feature-card">
                        <div class="feature-image">
                            <img src="<?php echo $left_card['image']['url'];?>" srcset="<?php echo (!empty($left_card['image_retina']['url'])?$left_card['image_retina']['url']. ' 2x':'');?>" alt="<?php echo $left_card['image']['title'];?>">
                        </div>
                        <div class="feature-content">
                            <h2 class="header-title"><?php echo $left_card['title'];?></h2>
                            <?php echo $left_card['copy'];?>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
                <?php endif;?>

            </div>
        </div>
        <?php if ($activate_left_carousel == true):?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.left-cards').owlCarousel(
                    {
                        'items':1
                    }
                );
            });
        </script>
        <?php endif;?>

        <div class="card-container column hd-6 no-margin-right">
            <div class="<?php echo ($activate_right_carousel == true ? ' owl-carousel owl-theme ':'');?> right-cards">
                <?php if (count($right_feature_cards) > 0):?>
                <?php foreach ($right_feature_cards as $right_card):?>
                <div class="item">
                    <div class="feature-card">
                        <div class="feature-image">
                        <img src="<?php echo $right_card['image']['url'];?>" srcset="<?php echo (!empty($right_card['image_retina']['url'])?$right_card['image_retina']['url']. ' 2x':'');?>" alt="<?php echo $right_card['image']['title'];?>">
                        </div>
                        <div class="feature-content">
                            <h2 class="header-title"><?php echo $right_card['title'];?></h2>
                            <?php echo $right_card['copy'];?>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
                <?php endif;?>
            </div>
        </div>

        <?php if ($activate_right_carousel == true):?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.right-cards').owlCarousel(
                    {
                        'items':1
                    }
                );
            });
        </script>
        <?php endif;?>


    </div>
</div>
