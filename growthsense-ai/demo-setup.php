<?php
if (!defined('ABSPATH')) exit;

function gs_create_demo_content() {

    if (get_option(GS_OPTION_DEMO)) return;

    wp_insert_post([
        'post_title' => 'GrowthSense Demo Post',
        'post_content' => 'This is a demo post for GrowthSense AI.',
        'post_status' => 'publish'
    ]);

    update_option(GS_OPTION_DEMO, true);
}
add_action('admin_init', 'gs_create_demo_content');
