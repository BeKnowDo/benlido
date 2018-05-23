<?php

    if (function_exists('get_field')) {
        $kitting_page = get_field('kitting_page','option');
    }
    if (!empty($kitting_page) && is_object($kitting_page)) {
        $kitting_page = get_permalink($kitting_page->ID);
    } 
    $category_id = $_REQUEST['id'];
    $category_title = '';
    if (!empty($category_id)) {
        $cat_obj = get_term_by('id',$category_id,'product_cat');
    }
    if (!empty($cat_obj) && is_object($cat_obj)) {
        $category_title = $cat_obj->name;
    }
?>
<div class="header-group row">
    <div class="grid-container">
        <div class="back-button">
            <a href="<?php echo $kitting_page;?>"><img src="<?php echo get_stylesheet_directory_uri();?>/assets/images/icon-back.svg" alt=""></a>
        </div>
        <h2>Category</h2>
        <h1><?php echo $category_title;?></h1>
    </div>
</div>