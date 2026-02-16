<?php
/*
Plugin Name: Creator AI Studio
Description: AI-powered content generation studio for WordPress.
Version: 1.0.0
Author: Adeoti Oluwafunbi Aduraseyi
*/

if (!defined('ABSPATH')) exit;

// Plugin constants
define('CAS_VERSION', '1.0.0');
define('CAS_TABLE_DRAFTS', 'cas_drafts');

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/cas-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-cas-admin.php';

// Activation: create database table for drafts
register_activation_hook(__FILE__, 'cas_activate_plugin');
function cas_activate_plugin() {
    global $wpdb;

    $table_name = $wpdb->prefix . CAS_TABLE_DRAFTS;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        topic VARCHAR(255) NOT NULL,
        content LONGTEXT NOT NULL,
        tags VARCHAR(255) DEFAULT NULL,
        description TEXT DEFAULT NULL,
        status ENUM('draft','published') NOT NULL DEFAULT 'draft',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Initialize admin dashboard
add_action('plugins_loaded', function() {
    if (is_admin()) {
        new CAS_Admin();
    }
});

// Helper class for local AI fallback
if (!class_exists('CAS_LocalAI')) {
    class CAS_LocalAI {
        // Generate a local draft content
        public static function generate_content($topic) {
            $content = "This is a generated draft for the topic: {$topic}\n\n";
            $content .= "### Suggested Headings\n";
            $content .= "- Introduction\n- Main Points\n- Conclusion\n\n";
            $content .= "### SEO Description\n";
            $content .= "An informative article about {$topic}.\n\n";
            $content .= "---\nGenerated using Creator AI Studio (Local Fallback Mode)";
            return $content;
        }

        // Save draft to DB and WordPress
        public static function save_draft($topic) {
            global $wpdb;

            $content = self::generate_content($topic);

            // Save in plugin DB
            $wpdb->insert(
                $wpdb->prefix . CAS_TABLE_DRAFTS,
                [
                    'topic' => $topic,
                    'content' => $content,
                    'status' => 'draft',
                ]
            );

            // Save as WordPress draft post
            wp_insert_post([
                'post_title' => $topic,
                'post_content' => $content,
                'post_status' => 'draft',
                'post_author' => get_current_user_id(),
            ]);
        }

        // Display saved drafts in dashboard
        public static function display_saved_drafts() {
            global $wpdb;
            $table = $wpdb->prefix . CAS_TABLE_DRAFTS;
            $drafts = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");

            if (!$drafts) {
                echo "<p>No drafts saved yet.</p>";
                return;
            }

            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>ID</th><th>Topic</th><th>Status</th><th>Created At</th></tr></thead><tbody>';

            foreach ($drafts as $d) {
                echo "<tr>
                        <td>{$d->id}</td>
                        <td>{$d->topic}</td>
                        <td>{$d->status}</td>
                        <td>{$d->created_at}</td>
                      </tr>";
            }

            echo '</tbody></table>';
        }
    }
}
