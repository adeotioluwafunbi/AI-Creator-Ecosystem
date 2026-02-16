<?php
if (!defined('ABSPATH')) exit;

/**
 * Activation hook
 */
function eai_activate() {
    EAI_DB::create_tables();
}
register_activation_hook(__FILE__, 'eai_activate');

/**
 * Uninstall hook
 */
function eai_uninstall() {
    EAI_DB::drop_tables();
    delete_option('eai_demo_installed');
}
register_uninstall_hook(__FILE__, 'eai_uninstall');
