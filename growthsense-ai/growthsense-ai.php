<?php
/**
 * Plugin Name: GrowthSense AI
 * Description: AI-powered insights for posts and WooCommerce products.
 * Version: 1.0.0
 * Author: Adeoti Oluwafunbi Aduraseyi
 */

if (!defined('ABSPATH')) exit;

// Load Config
require_once plugin_dir_path(__FILE__) . 'config/constants.php';
require_once plugin_dir_path(__FILE__) . 'config/database.php';
require_once plugin_dir_path(__FILE__) . 'config/defaults.php';

// Load Core Classes
require_once plugin_dir_path(__FILE__) . 'includes/class-gs-db.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-gs-ai.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-gs-admin.php';
require_once plugin_dir_path(__FILE__) . 'demo-setup.php';

// Activation Hook
register_activation_hook(__FILE__, ['GS_DB', 'create_tables']);

// Initialize
function gs_init_plugin() {
    new GS_Admin();
}
add_action('plugins_loaded', 'gs_init_plugin');
