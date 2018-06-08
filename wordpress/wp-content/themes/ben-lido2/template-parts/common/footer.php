<?php
$data = array();
$className = 'footer-primary-items';
$list = array();
$socialMediaLinks = array();
$contact = array();
$diamond_logo = '';
$copyright_date = date('Y');
if (function_exists('bl_get_site_logo')) {
    $diamond_logo = bl_get_site_logo(true);
}

if (function_exists('bl_get_footer_nav')) {
    $list = bl_get_footer_nav();
}
if (function_exists('bl_get_social_media_nav')) {
    $socialMediaLinks = bl_get_social_media_nav();
}
if (function_exists('bl_get_contact_nav')) {
    $contact = bl_get_contact_nav();
}

$data = array(
    'className'=>$className,
    'list'=>$list,
    'socialMediaLinks'=>$socialMediaLinks,
    'logo'=>$diamond_logo,
    'copyright_date'=>$copyright_date,
    'contact'=>$contact
);

//print_r (json_encode($contact));

Timber::render( 'common/footer.twig', $data);