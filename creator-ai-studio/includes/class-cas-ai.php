<?php
if (!defined('ABSPATH')) exit;

class CAS_AI {

    public static function generate_content($topic) {

        $api_key = get_option('cas_openai_api_key');

        if (!empty($api_key)) {

            $response = wp_remote_post(
                'https://api.openai.com/v1/chat/completions',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $api_key,
                        'Content-Type'  => 'application/json'
                    ],
                    'body' => json_encode([
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'user', 'content' => 'Write a detailed blog post about: ' . $topic]
                        ],
                        'temperature' => 0.7,
                        'max_tokens' => 800
                    ]),
                    'timeout' => 60
                ]
            );

            if (!is_wp_error($response)) {

                $body = json_decode(wp_remote_retrieve_body($response), true);

                if (isset($body['choices'][0]['message']['content'])) {
                    return trim($body['choices'][0]['message']['content']);
                }
            }
        }

        // Fallback
        return self::local_fallback($topic);
    }

    private static function local_fallback($topic) {

        return "This article explores {$topic} in depth.\n\n"
             . "Understanding {$topic} is important in today’s world. "
             . "Here are key points to consider:\n\n"
             . "- What {$topic} means\n"
             . "- Why it matters\n"
             . "- How to apply it\n\n"
             . "In conclusion, {$topic} continues to grow in relevance.\n\n"
             . "---\nGenerated using Creator AI Studio (Local Mode)";
    }
}
