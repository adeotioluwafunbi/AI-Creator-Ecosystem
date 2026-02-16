<?php
if (!defined('ABSPATH')) exit;

class GS_AI {

    /**
     * Analyze a post and return an AI-style insight (Demo)
     */
    public static function analyze_post($post_id) {
        $post = get_post($post_id);
        if (!$post) return null;

        $title = $post->post_title;
        $content = wp_strip_all_tags($post->post_content);

        // Extract first 20 words as "summary"
        $words = wp_trim_words($content, 20, '...');

        // Generate a pseudo AI insight
        $insights = [
            "Key takeaway: {$words}",
            "Insight: This post on '{$title}' highlights important points for readers.",
            "Summary: The post discusses critical aspects of {$title}.",
            "Actionable tip: Focus on the main idea - '{$words}'",
            "Recommendation: Readers should pay attention to '{$title}' concepts."
        ];

        // Pick a random insight for variety
        return $insights[array_rand($insights)];
    }
}
