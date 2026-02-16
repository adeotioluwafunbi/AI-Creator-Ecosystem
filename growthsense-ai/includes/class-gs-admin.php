<?php
if (!defined('ABSPATH')) exit;

// Include dummy AI class
require_once plugin_dir_path(__FILE__) . 'class-gs-ai.php';

class GS_Admin {

    private $menu_slug = 'growthsense-ai';

    public function __construct() {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_post_gs_analyze', [$this, 'handle_analysis']);
    }

    /**
     * Register Admin Menu
     */
    public function register_menu() {
        add_menu_page(
            'GrowthSense AI',
            'GrowthSense AI',
            'manage_options',
            $this->menu_slug,
            [$this, 'render_dashboard'],
            'dashicons-chart-line',
            25
        );
    }

    /**
     * Enqueue CSS & JS only on plugin page
     */
    public function enqueue_assets($hook) {
        if ($hook !== 'toplevel_page_' . $this->menu_slug) return;

        wp_enqueue_style(
            'gs-admin-style',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin.css',
            [],
            GS_VERSION
        );

        wp_enqueue_script(
            'gs-admin-script',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/admin.js',
            [],
            GS_VERSION,
            true
        );
    }

    /**
     * Render Dashboard
     */
    public function render_dashboard() {
        global $wpdb;
        $table_name = $wpdb->prefix . GS_TABLE_INSIGHTS;

        // -------------------------------
        // Auto-generate 5 demo posts if not exists
        // -------------------------------
        $demo_posts = [
            [
                'post_title'   => 'The Future of AI in Daily Life',
                'post_content' => 'AI is transforming our daily routines, from smart assistants to predictive analytics. This article explores its growing impact.',
            ],
            [
                'post_title'   => '10 Healthy Eating Tips for Teenagers',
                'post_content' => 'Eating well as a teenager is essential for growth and focus. Here are ten tips to stay healthy and energized.',
            ],
            [
                'post_title'   => 'Exploring the Wonders of Space Travel',
                'post_content' => 'Space exploration has fascinated humans for decades. From satellites to Mars missions, we look at recent advancements.',
            ],
            [
                'post_title'   => 'Mastering the Art of Photography',
                'post_content' => 'Photography is more than taking pictures—it’s about capturing moments. Learn the fundamentals to improve your skills.',
            ],
            [
                'post_title'   => 'The Importance of Cybersecurity in 2026',
                'post_content' => 'Cybersecurity is critical in protecting personal and business data. This post explores current threats and prevention tips.',
            ]
        ];

        foreach ($demo_posts as $post) {
            $query = new WP_Query([
                'title'        => $post['post_title'],
                'post_type'    => 'post',
                'post_status'  => 'any',
                'posts_per_page'=> 1,
                'fields'       => 'ids'
            ]);

            if (!$query->have_posts()) {
                wp_insert_post([
                    'post_title'   => $post['post_title'],
                    'post_content' => $post['post_content'],
                    'post_status'  => 'publish',
                    'post_author'  => get_current_user_id(),
                ]);
            }
            wp_reset_postdata();
        }

        // -------------------------------
        // Fetch latest insights
        // -------------------------------
        $results = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY created_at DESC");

        // Demo metrics
        $product_count = 3;
        $order_count   = 5;

        // Load dashboard template
        require plugin_dir_path(dirname(__FILE__)) . 'templates/dashboard.php';
    }

    /**
     * Handle Post Analysis
     */
    public function handle_analysis() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized user');

        check_admin_referer('gs_analyze_nonce');

        global $wpdb;
        $table_name = $wpdb->prefix . GS_TABLE_INSIGHTS;

        $posts = get_posts([
            'numberposts' => 5,
            'post_status' => 'publish'
        ]);

        foreach ($posts as $post) {
            $insight = GS_AI::analyze_post($post->ID);

            if ($insight) {
                // Insert or update insight
                $existing = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM {$table_name} WHERE post_id = %d LIMIT 1",
                    $post->ID
                ));

                if ($existing) {
                    $wpdb->update(
                        $table_name,
                        ['insight' => $insight, 'created_at' => current_time('mysql')],
                        ['id' => $existing]
                    );
                } else {
                    $wpdb->insert(
                        $table_name,
                        ['post_id' => $post->ID, 'insight' => $insight, 'created_at' => current_time('mysql')]
                    );
                }
            }
        }

        wp_redirect(admin_url('admin.php?page=' . $this->menu_slug));
        exit;
    }
}
