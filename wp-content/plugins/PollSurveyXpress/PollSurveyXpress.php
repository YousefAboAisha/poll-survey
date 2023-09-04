<?php
/*
Plugin Name:  #PollSurveyXpress
Plugin URI:   http://localhost/wordpress/wp-admin/admin.php?page=poll-survey-xpress-surveys
Description:  Poll & Survey Plugin
Text Domain: psx-poll-survey-plugin
Version:      1.0
Author:       Ibrahim 
Author URI:   http://localhost/wordpress/   
*/

register_activation_hook(__FILE__, 'PSX_add_database_tables');

global $wpdb;
$polls_table = $wpdb->prefix . 'polls_psx_polls';

$allowed_to_run = true;

if( $wpdb->get_var("SHOW TABLES LIKE '$polls_table'") !== $polls_table){
    $allowed_to_run = false;
}

if($allowed_to_run){
    require_once(plugin_dir_path(__FILE__) . 'functions.php');

}else{
    add_action( 'after_plugin_row', 'PSX_custom_after_plugin_row_content', 10, 3 );

}
add_action( 'init', 'PSX_load_textdomain' );
function PSX_load_textdomain() {
  load_plugin_textdomain( 'psx-poll-survey-plugin', false, dirname( plugin_basename( __FILE__ ) ) .'/languages' );
 
}


$plugin_name = plugin_basename(__FILE__);
add_filter('plugin_action_links_' . $plugin_name, 'PSX_settings_link');
function PSX_settings_link($links)
{
    // Build and escape the URL.
    $url = esc_url(add_query_arg(
        'page',
        'poll-survey-xpress-settings',
        get_admin_url() . 'admin.php'
    ));

    // Create the link.
    $settings_link = "<a href='$url'>" . __('Settings') . '</a>';

    // Adds the link to the end of the array.
    array_unshift($links, $settings_link);

    return $links;
}
function PSX_add_database_tables()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $option_name = 'installation_time_of_PollSurveyXpress';

    // Check if the option already exists
    $existing_option = get_option($option_name);

    if (!$existing_option) {
        // Option doesn't exist, so add it with the current time
        $current_time = current_time('timestamp');
        add_option($option_name, $current_time);
    }

    // Define your table structures
    $table_polls = "
        CREATE TABLE IF NOT EXISTS {$wpdb->prefix}polls_psx_polls (
            poll_id int(10) NOT NULL AUTO_INCREMENT,
            title varchar(255),
            cta_Text varchar(255),
            start_date datetime,
            end_date datetime,
            status enum('active', 'inactive', 'archived'),
            template enum('Multiple Choice', 'Open ended', 'Rating'),
            Short_Code varchar(50),
            color varchar(255),
            bgcolor varchar(255),
            sharing enum('true', 'false'),
            real_time_result_text varchar(255),
            min_votes int,
            deleted_at datetime,
            PRIMARY KEY (poll_id)
        ) $charset_collate;
    ";

    $table_survey_questions = "
        CREATE TABLE IF NOT EXISTS {$wpdb->prefix}polls_psx_survey_questions (
            question_id int(11) NOT NULL AUTO_INCREMENT,
            poll_id int(10),
            question_text varchar(255),
            PRIMARY KEY (question_id),
            FOREIGN KEY (poll_id) REFERENCES {$wpdb->prefix}polls_psx_polls(poll_id)
        ) $charset_collate;
    ";

    $table_survey_answers = "
        CREATE TABLE IF NOT EXISTS {$wpdb->prefix}polls_psx_survey_answers (
            answer_id int(11) NOT NULL AUTO_INCREMENT,
            poll_id int(10),
            question_id int(11),
            answer_text varchar(255),
            PRIMARY KEY (answer_id),
            FOREIGN KEY (question_id) REFERENCES {$wpdb->prefix}polls_psx_survey_questions(question_id),
            FOREIGN KEY (poll_id) REFERENCES {$wpdb->prefix}polls_psx_polls(poll_id)
        ) $charset_collate;
    ";

    $table_survey_responses = "
    CREATE TABLE IF NOT EXISTS {$wpdb->prefix}polls_psx_survey_responses (
        response_id int(11) NOT NULL AUTO_INCREMENT,
        poll_id int(10),
        ip_address varchar(255),
        user_id int(11),
        session_id varchar(255),
        PRIMARY KEY (response_id),
        FOREIGN KEY (poll_id) REFERENCES {$wpdb->prefix}polls_psx_polls(poll_id)
    ) $charset_collate;
";

    $table_survey_responses_data = "
    CREATE TABLE IF NOT EXISTS {$wpdb->prefix}polls_psx_survey_responses_data (
        response_id int(11) NOT NULL,
        question_id int(11),
        answer_id int(11),
        open_text_response varchar(255),
        FOREIGN KEY (response_id) REFERENCES {$wpdb->prefix}polls_psx_survey_responses(response_id),
        FOREIGN KEY (question_id) REFERENCES {$wpdb->prefix}polls_psx_survey_questions(question_id),
        FOREIGN KEY (answer_id) REFERENCES {$wpdb->prefix}polls_psx_survey_answers(answer_id)
    ) $charset_collate;
";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($table_polls);
        dbDelta($table_survey_questions);
        dbDelta($table_survey_answers);
        dbDelta($table_survey_responses);
        dbDelta($table_survey_responses_data);

    // Include the upgrade script



}
            
// Add custom content after a plugin's row in plugin settings page
function PSX_custom_after_plugin_row_content( $plugin_file, $plugin_data, $status ) {
    // Get the folder name and file name from $plugin_basename
    $plugin_basename = plugin_basename(__FILE__);
    $folder_name = dirname($plugin_basename);
    $file_name = basename($plugin_basename);

    // Check if the plugin matches the desired plugin
    if ( $file_name === basename($plugin_file) && $folder_name === dirname($plugin_file) ) {
        echo '<tr class="plugin-update-tr">
            <td colspan="3" class="plugin-update colspanchange">
                <div class="update-message notice inline notice-info notice-alt">
                    <p>🚨 Plugin is active, but it cannot work because the required tables were not created successfully. Please check your database privileges and then deactivate and activate the plugin again.  </p>
                </div>
            </td>
        </tr>';
    }
}
