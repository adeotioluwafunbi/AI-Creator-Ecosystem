<?php
if (!defined('ABSPATH')) exit;

class GS_DB {

    public static function create_tables() {
        global $wpdb;

        $table_name = $wpdb->prefix . GS_TABLE_INSIGHTS;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT NOT NULL AUTO_INCREMENT,
            post_id BIGINT NOT NULL,
            insight TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}
