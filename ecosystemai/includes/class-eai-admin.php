<?php
if (!defined('ABSPATH')) exit;

class EAI_Admin {

    private $menu_slug = 'ecosystem-ai';

    public function __construct() {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Register Admin Menu
     */
    public function register_menu() {
        add_menu_page(
            'Ecosystem AI',
            'Ecosystem AI',
            'manage_options',
            $this->menu_slug,
            [$this, 'render_dashboard'],
            'dashicons-networking',
            26
        );
    }

    /**
     * Enqueue CSS & JS Only on Plugin Page
     */
    public function enqueue_assets($hook) {
        if ($hook !== 'toplevel_page_' . $this->menu_slug) {
            return;
        }

        // Admin CSS
        wp_enqueue_style(
            'eai-admin-style',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin-style.css',
            [],
            '1.0.0'
        );

        // Chart.js (CDN)
        wp_enqueue_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js',
            [],
            '4.3.0',
            true
        );

        // Dashboard JS (depends on Chart.js)
        wp_enqueue_script(
            'eai-admin-script',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/dashboard.js',
            ['jquery', 'chartjs'],
            '1.0.0',
            true
        );
    }

    /**
     * Render Dashboard
     */
    public function render_dashboard() {
        global $wpdb;

        // Fetch insights table
        $table_name = $wpdb->prefix . 'eai_insights';
        $results = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY created_at DESC");

        // Demo insights if empty
        if (empty($results)) {
            $results = [
                (object)[
                    'insight' => 'Site has a strong content base with multiple published posts.',
                    'created_at' => current_time('mysql'),
                    'category' => 'Content'
                ],
                (object)[
                    'insight' => 'User registration is growing steadily.',
                    'created_at' => current_time('mysql'),
                    'category' => 'Users'
                ],
                (object)[
                    'insight' => 'Comment activity indicates high engagement.',
                    'created_at' => current_time('mysql'),
                    'category' => 'Comments'
                ],
                (object)[
                    'insight' => 'Active plugins cover SEO, performance, and security.',
                    'created_at' => current_time('mysql'),
                    'category' => 'Plugins'
                ],
                (object)[
                    'insight' => 'Site ecosystem metrics look healthy and balanced.',
                    'created_at' => current_time('mysql'),
                    'category' => 'General'
                ],
            ];
        }

        require plugin_dir_path(dirname(__FILE__)) . 'templates/dashboard-ecosystem.php';
    }
}
