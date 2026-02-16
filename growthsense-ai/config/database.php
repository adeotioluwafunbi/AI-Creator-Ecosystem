<?php
if (!defined('ABSPATH')) exit;

return [
    'insights' => "
        id BIGINT NOT NULL AUTO_INCREMENT,
        post_id BIGINT NOT NULL,
        insight TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    "
];
