<?php
if (!defined('ABSPATH')) exit;

class CAS_Admin {

    private $menu_slug = 'creator-ai-studio';

    public function __construct() {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_post_cas_generate_content', [$this, 'handle_generate_content']);
    }

    /**
     * Register Admin Menu
     */
    public function register_menu() {
        add_menu_page(
            'Creator AI Studio',
            'Creator AI Studio',
            'manage_options',
            $this->menu_slug,
            [$this, 'render_dashboard'],
            'dashicons-edit',
            27
        );

        // Settings submenu
        add_submenu_page(
            $this->menu_slug,
            'AI Settings',
            'Settings',
            'manage_options',
            $this->menu_slug . '-settings',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Enqueue CSS & JS only for plugin pages
     */
    public function enqueue_assets($hook) {
        if (!str_contains($hook, $this->menu_slug)) return;

        wp_enqueue_style(
            'cas-admin-style',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin-style.css',
            [],
            CAS_VERSION
        );

        wp_enqueue_script(
            'cas-admin-script',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/dashboard.js',
            ['jquery'],
            CAS_VERSION,
            true
        );
    }

    /**
     * Render the main dashboard
     */
    public function render_dashboard() {
        require plugin_dir_path(dirname(__FILE__)) . 'templates/dashboard-creator.php';
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>AI Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('cas_settings_group');
                do_settings_sections('cas-ai-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Handle content generation
     */
    public function handle_generate_content() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized user');

        check_admin_referer('cas_generate_nonce');

        if (!empty($_POST['topic'])) {
            $topic = sanitize_text_field($_POST['topic']);
            // Generate content and save to DB + WP
            CAS_LocalAI::save_draft($topic);
        }

        wp_redirect(admin_url('admin.php?page=' . $this->menu_slug));
        exit;
    }
}
