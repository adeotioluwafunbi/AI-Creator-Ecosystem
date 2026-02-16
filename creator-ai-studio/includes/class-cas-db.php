<?php
if (!defined('ABSPATH')) exit;

class CAS_DB {

    public static function create_tables() {

        global $wpdb;
        $table = $wpdb->prefix . 'cas_drafts';
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            topic VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            status VARCHAR(50) DEFAULT 'draft',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function insert_draft($topic, $content, $status) {

        global $wpdb;
        $table = $wpdb->prefix . 'cas_drafts';

        return $wpdb->insert(
            $table,
            [
                'topic'   => $topic,
                'content' => $content,
                'status'  => $status
            ],
            ['%s', '%s', '%s']
        );
    }

    public static function get_all_drafts() {

        global $wpdb;
        $table = $wpdb->prefix . 'cas_drafts';

        return $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");
    }

    public static function get_draft($id) {

        global $wpdb;
        $table = $wpdb->prefix . 'cas_drafts';

        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id)
        );
    }

    public static function update_draft($id, $topic, $content) {

        global $wpdb;
        $table = $wpdb->prefix . 'cas_drafts';

        return $wpdb->update(
            $table,
            [
                'topic'   => $topic,
                'content' => $content
            ],
            ['id' => $id],
            ['%s', '%s'],
            ['%d']
        );
    }
}
