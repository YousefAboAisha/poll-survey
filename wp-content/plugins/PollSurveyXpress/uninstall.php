<?php

// Check if the uninstall constant is defined to prevent direct access to this file
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Load WordPress database configuration
require_once(ABSPATH . 'wp-config.php');

// Drop the plugin's tables if clear_data option is not zero
global $wpdb;

$clearDataOptionValue = get_option('PSX_clear_data');

if ($clearDataOptionValue !== '0') {
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}polls_psx_survey_responses_data");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}polls_psx_survey_responses");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}polls_psx_survey_answers");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}polls_psx_survey_questions");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}polls_psx_polls");
}