<?php
if (!defined('ABSPATH')) exit;

function eai_create_demo_content() {

    if (get_option(EAI_OPTION_DEMO)) return;

    wp_insert_post([
        'post_title' => 'Ecosystem Demo Post',
        'post_content' => 'This is a demo post for Ecosystem AI.',
        'post_status' => 'publish'
    ]);

    update_option(EAI_OPTION_DEMO, true);
}
add_action('admin_init', 'eai_create_demo_content');
