<?php
if (!defined('ABSPATH')) exit;

class CAS_LocalAI {

    /**
     * Generate topic suggestions for SEO-rich content
     */
    public static function generate_topic_suggestions($topic) {
        // Basic local fallback algorithm for related topics
        $keywords = explode(' ', $topic);
        $suggestions = [];

        foreach ($keywords as $word) {
            $suggestions[] = "Benefits of " . $word;
            $suggestions[] = "Tips for " . $word;
            $suggestions[] = "Common mistakes in " . $word;
            $suggestions[] = "How to master " . $word;
        }

        // Remove duplicates
        return array_unique($suggestions);
    }

    /**
     * Generate local AI content with headings, SEO, and suggestions
     */
    public static function save_draft($topic, $selected_suggestions = []) {
        global $wpdb;
        $table_name = $wpdb->prefix . CAS_TABLE_DRAFTS;

        // Generate topic suggestions
        $suggestions = self::generate_topic_suggestions($topic);

        // Include user-selected suggestions in content
        $content  = "<h2>Introduction to {$topic}</h2>\n";
        $content .= "<p>This article explores <strong>{$topic}</strong> in depth and provides actionable insights for readers. Understanding {$topic} is essential in today's digital landscape.</p>\n";

        if (!empty($selected_suggestions)) {
            foreach ($selected_suggestions as $sugg) {
                $content .= "<h3>{$sugg}</h3>\n";
                $content .= "<p>Discussion on {$sugg} goes here. This helps improve SEO and content richness.</p>\n";
            }
        }

        $content .= "<h2>Key Concepts and Overview</h2>\n";
        $content .= "<p>We discuss the main ideas behind {$topic}, its importance, and practical applications.</p>\n";

        $content .= "<h3>Why {$topic} Matters</h3>\n";
        $content .= "<p>{$topic} plays a critical role in various aspects of modern life, including personal, professional, and educational domains.</p>\n";

        $content .= "<h3>Steps and Best Practices</h3>\n";
        $content .= "<p>Here are recommended strategies and steps to effectively apply the knowledge of {$topic}.</p>\n";

        $content .= "<h2>Conclusion</h2>\n";
        $content .= "<p>By understanding {$topic}, readers can enhance their skills and achieve meaningful results. This guide serves as a foundation for further exploration.</p>\n";

        $content .= "<hr>\n<p><em>Generated using Creator AI Studio (Local Mode)</em></p>";

        // Suggested tags
        $tags = implode(',', array_map('sanitize_text_field', explode(' ', $topic)));

        // SEO description
        $description = "Comprehensive guide on {$topic} with practical insights, key concepts, and tips for effective implementation.";

        // Insert into plugin DB
        $wpdb->insert(
            $table_name,
            [
                'topic'       => $topic,
                'content'     => $content,
                'tags'        => $tags,
                'description' => $description,
                'status'      => 'draft',
                'created_at'  => current_time('mysql')
            ]
        );

        // Also create WordPress draft post
        wp_insert_post([
            'post_title'   => $topic,
            'post_content' => $content,
            'post_status'  => 'draft',
            'post_author'  => get_current_user_id(),
        ]);
    }

    /**
     * Get all topic suggestions for a draft form
     */
    public static function get_suggestions_for_topic($topic) {
        $suggestions = self::generate_topic_suggestions($topic);
        $html = '<p><strong>Suggested subtopics:</strong></p>';
        $html .= '<ul>';
        foreach ($suggestions as $sugg) {
            $html .= '<li><input type="checkbox" name="suggestions[]" value="' . esc_attr($sugg) . '"> ' . esc_html($sugg) . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * Retrieve a single draft by ID
     */
    public static function get_draft($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . CAS_TABLE_DRAFTS;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
    }

    /**
     * Update a draft
     */
    public static function update_draft($id, $topic, $content, $tags, $description) {
        global $wpdb;
        $table_name = $wpdb->prefix . CAS_TABLE_DRAFTS;

        $wpdb->update(
            $table_name,
            [
                'topic'       => $topic,
                'content'     => $content,
                'tags'        => $tags,
                'description' => $description
            ],
            ['id' => intval($id)]
        );
    }

    /**
     * Display all saved drafts
     */
    public static function display_saved_drafts() {
        global $wpdb;
        $table_name = $wpdb->prefix . CAS_TABLE_DRAFTS;
        $drafts = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

        if (!$drafts) {
            echo "<p>No drafts saved yet.</p>";
            return;
        }

        echo '<table class="widefat striped">';
        echo '<thead><tr><th>ID</th><th>Topic</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>';
        echo '<tbody>';

        foreach ($drafts as $draft) {
            $edit_url    = admin_url('admin.php?page=creator-ai-studio&action=edit&id=' . $draft->id);
            $delete_url  = wp_nonce_url(admin_url('admin.php?page=creator-ai-studio&action=delete&id=' . $draft->id), 'cas_delete_draft');
            $publish_url = wp_nonce_url(admin_url('admin.php?page=creator-ai-studio&action=publish&id=' . $draft->id), 'cas_publish_draft');

            echo '<tr>';
            echo '<td>' . esc_html($draft->id) . '</td>';
            echo '<td>' . esc_html($draft->topic) . '</td>';
            echo '<td>' . esc_html($draft->created_at) . '</td>';
            echo '<td>' . esc_html($draft->status) . '</td>';
            echo '<td>
                <a href="' . esc_url($edit_url) . '" class="button">Edit</a>
                <a href="' . esc_url($delete_url) . '" class="button button-danger" onclick="return confirm(\'Delete this draft?\')">Delete</a>
                <a href="' . esc_url($publish_url) . '" class="button button-primary">Create WP Post</a>
            </td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    }

    /**
     * Delete a draft
     */
    public static function delete_draft($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . CAS_TABLE_DRAFTS;
        $wpdb->delete($table_name, ['id' => intval($id)]);
    }

    /**
     * Publish draft as WordPress post
     */
    public static function publish_draft($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . CAS_TABLE_DRAFTS;
        $draft = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        if ($draft) {
            wp_insert_post([
                'post_title'   => $draft->topic,
                'post_content' => $draft->content,
                'post_status'  => 'draft',
                'post_author'  => get_current_user_id(),
            ]);

            $wpdb->update($table_name, ['status' => 'published'], ['id' => intval($id)]);
        }
    }
}

