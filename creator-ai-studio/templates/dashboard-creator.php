<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$table_name = $wpdb->prefix . CAS_TABLE_DRAFTS;

// Handle Generate Content form submission
if (isset($_POST['generate_topic'])) {
    $topic = sanitize_text_field($_POST['generate_topic']);
    $selected_suggestions = isset($_POST['suggestions']) ? array_map('sanitize_text_field', $_POST['suggestions']) : [];

    // Save draft with subtopics
    CAS_LocalAI::save_draft($topic, $selected_suggestions);

    echo '<div class="updated notice"><p>Draft generated successfully!</p></div>';
}

// Handle Edit Draft submission
if (isset($_POST['cas_edit_draft_nonce'], $_POST['draft_id']) && wp_verify_nonce($_POST['cas_edit_draft_nonce'], 'cas_edit_draft')) {
    $id          = intval($_POST['draft_id']);
    $topic       = sanitize_text_field($_POST['topic']);
    $content     = wp_kses_post($_POST['content']);
    $tags        = sanitize_text_field($_POST['tags']);
    $description = sanitize_text_field($_POST['description']);
    $subtopics   = isset($_POST['subtopics']) ? array_map('sanitize_text_field', $_POST['subtopics']) : [];

    CAS_LocalAI::update_draft($id, $topic, $content, $tags, $description, $subtopics);

    echo '<div class="updated notice"><p>Draft updated successfully!</p></div>';
}

// Check if editing a draft
$edit_draft = null;
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit') {
    $edit_draft = CAS_LocalAI::get_draft(intval($_GET['id']));
}

// Prepare subtopics array if editing
$edit_subtopics = [];
if ($edit_draft && !empty($edit_draft->subtopics)) {
    $edit_subtopics = maybe_unserialize($edit_draft->subtopics);
    if (!is_array($edit_subtopics)) $edit_subtopics = [];
}
?>

<div class="wrap">
    <h1>Creator AI Studio</h1>

    <!-- Generate Content Form -->
<h2>Generate New Draft</h2>
<form method="post" action="">

    <input type="text" name="generate_topic" 
           value="<?php echo isset($_POST['generate_topic']) ? esc_attr($_POST['generate_topic']) : ''; ?>" 
           placeholder="Enter topic" required style="width: 50%;">

    <?php
    $suggestions = [];

    if (isset($_POST['generate_topic']) && !empty($_POST['generate_topic'])) {
        $topic = sanitize_text_field($_POST['generate_topic']);
        $suggestions = CAS_LocalAI::get_suggestions_for_topic($topic);

        if (!is_array($suggestions)) {
            $suggestions = [];
        }
    }

    if (!empty($suggestions)) {
        echo '<div style="margin-top:15px;padding:15px;background:#fff;border:1px solid #ccd0d4;">';
        echo '<strong>SEO Subtopic Suggestions:</strong><br><br>';

        foreach ($suggestions as $suggestion) {
            echo '<label style="display:block;margin-bottom:5px;">';
            echo '<input type="checkbox" name="suggestions[]" value="' . esc_attr($suggestion) . '"> ';
            echo esc_html($suggestion);
            echo '</label>';
        }

        echo '</div>';
    }
    ?>

    <?php submit_button('Generate Content'); ?>

</form>


    <?php if ($edit_draft): ?>
        <hr>
        <h2>Edit Draft ID #<?php echo esc_html($edit_draft->id); ?></h2>
        <form method="post" action="">
            <input type="hidden" name="draft_id" value="<?php echo esc_attr($edit_draft->id); ?>">
            <?php wp_nonce_field('cas_edit_draft', 'cas_edit_draft_nonce'); ?>

            <p>
                <label>Topic:</label><br>
                <input type="text" name="topic" value="<?php echo esc_attr($edit_draft->topic); ?>" style="width: 50%;" required>
            </p>

            <p>
                <label>Content:</label><br>
                <?php
                wp_editor(
                    $edit_draft->content,
                    'content',
                    [
                        'textarea_name' => 'content',
                        'media_buttons' => true,
                        'textarea_rows' => 15,
                        'teeny'         => false,
                    ]
                );
                ?>
            </p>

            <p>
                <label>SEO Subtopic Suggestions:</label><br>
                <?php
                // Display checkboxes for editing
                if ($edit_subtopics) {
                    foreach ($edit_subtopics as $subtopic) {
                        echo '<label style="display:block;"><input type="checkbox" name="subtopics[]" value="' . esc_attr($subtopic) . '" checked> ' . esc_html($subtopic) . '</label>';
                    }
                }
                ?>
            </p>

            <p>
                <label>Tags (comma separated):</label><br>
                <input type="text" name="tags" value="<?php echo esc_attr($edit_draft->tags); ?>" style="width: 50%;">
            </p>

            <p>
                <label>Description:</label><br>
                <textarea name="description" rows="3" style="width: 100%;"><?php echo esc_textarea($edit_draft->description); ?></textarea>
            </p>

            <?php submit_button('Save Changes'); ?>
        </form>
    <?php endif; ?>

    <hr>
    <h2>Saved Drafts</h2>
    <?php CAS_LocalAI::display_saved_drafts(); ?>
</div>
