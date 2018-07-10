<?php
if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array(
        'key' => 'group_5a443a1b01966',
        'title' => 'Travel Kit Custom Fields',
        'fields' => array(
            array(
                'key' => 'field_5a443a2a201f7',
                'label' => 'General',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_5a443a36201f8',
                'label' => 'Instructions',
                'name' => '',
                'type' => 'message',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => 'This is where you create kits.
    A kit is composed of a title, description, and featured image.
    You will also need to choose a bag that is associated with this kit.
    Since the Kits page shows a wide image, we need to override the square product images with these wide ones. Upload them here.
    You will then add which category or subcategory of products each kit can contain as well as how many someone can choose.
    You can also select a featured product to show. If there is no featured product, then the first available product will be displayed.',
                'new_lines' => 'wpautop',
                'esc_html' => 0,
            ),
            array(
                'key' => 'field_5b2a5af67142c',
                'label' => 'Bag for this Kit',
                'name' => 'bag_for_this_kit',
                'type' => 'post_object',
                'instructions' => 'Select the bag that goes with this kit',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'post_type' => array(
                    0 => 'product',
                ),
                'taxonomy' => array(
                    0 => 'product_cat:bags',
                ),
                'allow_null' => 0,
                'multiple' => 0,
                'return_format' => 'object',
                'ui' => 1,
            ),
            array(
                'key' => 'field_5b4507f9226fb',
                'label' => 'Color Variation Image Overrides',
                'name' => 'color_variation_image_overrides',
                'type' => 'repeater',
                'instructions' => 'This is so that we can display a different size image for the different colors of the bags.
    Add in the color variation images for each of the colors here',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => '',
                'min' => 0,
                'max' => 0,
                'layout' => 'table',
                'button_label' => '',
                'sub_fields' => array(
                    array(
                        'key' => 'field_5b450833226fc',
                        'label' => 'Variation',
                        'name' => 'variation',
                        'type' => 'post_object',
                        'instructions' => 'Select the color variation for your bag',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'post_type' => array(
                            0 => 'product_variation',
                        ),
                        'taxonomy' => array(
                        ),
                        'allow_null' => 0,
                        'multiple' => 0,
                        'return_format' => 'object',
                        'ui' => 1,
                    ),
                    array(
                        'key' => 'field_5b450859226fd',
                        'label' => 'Image Override',
                        'name' => 'image_override',
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                    array(
                        'key' => 'field_5b450876226fe',
                        'label' => 'Image Override (Retina)',
                        'name' => 'image_override_retina',
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                ),
            ),
            array(
                'key' => 'field_5a443bb9201f9',
                'label' => 'Product Category Section',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_5a443bc9201fa',
                'label' => 'Product Categories',
                'name' => 'product_categories',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => '',
                'min' => 0,
                'max' => 0,
                'layout' => 'block',
                'button_label' => 'Add Product Category',
                'sub_fields' => array(
                    array(
                        'key' => 'field_5a443bdc201fb',
                        'label' => 'Category',
                        'name' => 'category',
                        'type' => 'taxonomy',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'taxonomy' => 'product_cat',
                        'field_type' => 'select',
                        'allow_null' => 0,
                        'add_term' => 1,
                        'save_terms' => 0,
                        'load_terms' => 0,
                        'return_format' => 'id',
                        'multiple' => 0,
                    ),
                    array(
                        'key' => 'field_5a443c00201fc',
                        'label' => 'Maximum number of items in this category',
                        'name' => 'maximum_number_of_items_in_this_category',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => 1,
                        'placeholder' => 1,
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5a6b444e0b99a',
                        'label' => 'Featured Product',
                        'name' => 'featured_product',
                        'type' => 'post_object',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'post_type' => array(
                            0 => 'product',
                        ),
                        'taxonomy' => array(
                        ),
                        'allow_null' => 0,
                        'multiple' => 0,
                        'return_format' => 'object',
                        'ui' => 1,
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'travel_kit',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ));
    
    endif;