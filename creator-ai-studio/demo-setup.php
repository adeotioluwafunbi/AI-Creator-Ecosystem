<?php
if (!defined('ABSPATH')) exit;

function cas_demo_setup() {
    $topics = ['AI in Education', 'Healthy Recipes for Teens', 'Travel Blogging Tips', 'Cybersecurity Essentials', 'Photography Basics'];
    foreach ($topics as $topic) {
        CAS_AI::generate_draft($topic);
    }
}
