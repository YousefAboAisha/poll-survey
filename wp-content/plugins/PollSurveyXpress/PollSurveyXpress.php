<?php
/*
Plugin Name:  #PollSurveyXpress
Plugin URI:   http://localhost/wordpress/wp-admin/admin.php?page=test-plugin-page 
Description:  Poll & Survey Plugin
Text Domain: 
Version:      1.0
Author:       Ibrahim 
Author URI:   http://localhost/wordpress/   
*/

require_once(plugin_dir_path(__FILE__) . 'functions.php');

register_activation_hook(__FILE__, array($survey_plugin, 'PSX_add_database_tables'));

$plugin_name = plugin_basename(__FILE__);
add_filter('plugin_action_links_' . $plugin_name, 'nc_settings_link');

function nc_settings_link($links)
{
    // Build and escape the URL.
    $url = esc_url(add_query_arg(
        'page',
        'poll-survey-xpress-surveys',
        get_admin_url() . 'admin.php'
    ));

    // Create the link.
    $settings_link = "<a href='$url'>" . __('Settings') . '</a>';

    // Adds the link to the end of the array.
    array_unshift($links, $settings_link);

    return $links;
}