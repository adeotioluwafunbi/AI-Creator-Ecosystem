<?php
if (!defined('ABSPATH')) exit;

class EAI_DB {

    public static function create_tables() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'eai_insights';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            insight TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function drop_tables() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'eai_insights';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}
