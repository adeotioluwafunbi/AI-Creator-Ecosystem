<?php
/**
 * Plugin Name: Ecosystem AI
 * Description: Demo plugin to show site metrics and insights.
 * Version: 1.0.0
 * Author: Adeoti Oluwafunbi
 */

if (!defined('ABSPATH')) exit;

define('EAI_TABLE_INSIGHTS', 'eai_insights');
define('EAI_OPTION_DEMO', 'eai_demo_installed');

// Includes
require_once plugin_dir_path(__FILE__) . 'includes/class-eai-admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-eai-ai.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-eai-db.php';
require_once plugin_dir_path(__FILE__) . 'includes/eai-functions.php';

// Initialize admin
if (is_admin()) {
    $eai_admin = new EAI_Admin();
}
