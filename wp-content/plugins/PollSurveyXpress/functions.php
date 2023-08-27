<?php


class PollSurveyXpress
{

    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'PSX_enqueue_admin_scripts'));
        add_action('admin_menu', array($this, 'PSX_add_admin_menu_link'));
        add_action('admin_bar_menu', array($this, 'PSX_toolbar_link'), 99);
        add_action('admin_menu', array($this, 'PSX_view_template_action'));
        add_action('admin_menu', array($this, 'PSX_edit_templates_action'));
        add_action('admin_menu', array($this, 'PSX_show_templates_action'));
        add_action('wp_enqueue_scripts', array($this, 'PSX_enqueue_frontend_scripts'));


        add_action('wp_ajax_PSX_save_poll_Multiple_data', array($this, 'PSX_save_poll_Multiple_data'));
        add_action('wp_ajax_nopriv_PSX_save_poll_Multiple_data', array($this, 'PSX_save_poll_Multiple_dataa'));
        add_action('wp_ajax_PSX_save_poll_rating_data', array($this, 'PSX_save_poll_rating_data'));
        add_action('wp_ajax_nopriv_PSX_save_poll_rating_data', array($this, 'PSX_save_poll_rating_data'));
        add_action('wp_ajax_PSX_save_poll_open_ended_data', array($this, 'PSX_save_poll_open_ended_data'));
        add_action('wp_ajax_nopriv_PSX_save_poll_open_ended_data', array($this, 'PSX_save_poll_open_ended_data'));
        add_action("wp_ajax_PSX_archive_poll", array($this, "PSX_archive_poll"));
        add_action("wp_ajax_nopriv_PSX_archive_poll", array($this, "PSX_archive_poll")); // For non-logged-in users
        add_action("wp_ajax_PSX_restore_poll", array($this, "PSX_restore_poll"));
        add_action("wp_ajax_nopriv_PSX_restore_poll", array($this, "PSX_restore_poll")); // For non-logged-in users
        add_action("wp_ajax_PSX_permenant_delete", array($this, "PSX_permenant_delete"));
        add_action("wp_ajax_nopriv_PSX_permenant_delete", array($this, "PSX_permenant_delete")); // For non-logged-in users
        add_shortcode('poll', array($this, 'PSX_poll_shortcode_handler'));
        add_action("wp_ajax_PSX_update_poll_settings", array($this, "PSX_update_poll_settings"));
        add_action("wp_ajax_nopriv_PSX_update_poll_settings", array($this, "PSX_update_poll_settings")); // For non-logged-in users
        add_action('wp_ajax_PSX_save_poll_response', array($this, 'PSX_save_poll_response'));
        add_action('wp_ajax_nopriv_PSX_save_poll_response', array($this, 'PSX_save_poll_response')); // For non-logged-in users
        add_action('wp_ajax_PSX_save_changes_settings', array($this, 'PSX_save_changes_settings'));
        add_action('wp_ajax_nopriv_PSX_save_changes_settings', array($this, 'PSX_save_changes_settings')); // For non-logged-in users

    }

    // Enqueue scripts and styles for the frontend
    public function PSX_enqueue_frontend_scripts()
    {

        wp_enqueue_script('bootstrap-bundle-script', plugin_dir_url(__FILE__) . 'js/bootstrap.bundle.js', array('jquery'), false, true);
        wp_enqueue_script('bootstrap-min-script', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), false, true);
        wp_enqueue_script('popper-extension-script', plugin_dir_url(__FILE__) . 'js/popper.min.js');

        //enqueue Style files
        wp_enqueue_style('bootstrap-style', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css');
        wp_enqueue_style('soft-style', plugin_dir_url(__FILE__) . 'css/soft-ui-dashboard.css');

        wp_enqueue_script('jquery');
        wp_enqueue_script('bootstrap-min-script', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), false, true);
        wp_enqueue_script('plugin-custom', plugin_dir_url(__FILE__) . '/js/main.js', array('jquery'), '1.5', true);
        wp_localize_script('plugin-custom', 'my_ajax_object', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('my_ajax_nonce'),
        ));

        wp_enqueue_style('dashboard-styles', plugin_dir_url(__FILE__) . 'css/custom-styles.css', array(), "1.5");
        wp_enqueue_style('soft-style-map', plugin_dir_url(__FILE__) . 'css/soft-ui-dashboard.css.map');
        wp_enqueue_style('soft-style-min', plugin_dir_url(__FILE__) . 'css/soft-ui-dashboard.min.css');
        wp_enqueue_style('soft-style', plugin_dir_url(__FILE__) . 'css/soft-ui-dashboard.css', array(), "1.4");
    }

    // Enqueue scripts and styles for the admin area
    public function PSX_enqueue_admin_scripts()
    {
        //enqueue Script files
        if (isset($_GET['page']) && (($_GET['page'] === 'poll-survey-xpress-surveys' || $_GET['page'] === 'poll-survey-xpress-recycle' || $_GET['page'] === 'poll-survey-xpress-add' || $_GET['page'] === 'poll-survey-xpress-settings' || $_GET['page'] === 'view_template_page' || $_GET['page'] === 'edit_template_page' || $_GET['page'] === 'poll-survey-xpress-recycle' || $_GET['page']
            === 'show_template_page'))) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('plugin-custom', plugin_dir_url(__FILE__) . '/js/main.js', array('jquery'), '1.0', true);
            wp_enqueue_script('bootstrap-script', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), false, true);
            wp_enqueue_script('bootstrap-notify-script', plugin_dir_url(__FILE__) . 'js/bootstrap-notify.js', array('jquery'), false, true);
            wp_enqueue_script('bootstrap-bundle-script', plugin_dir_url(__FILE__) . 'js/bootstrap.bundle.js', array('jquery'), false, true);
            wp_enqueue_script('bootstrap-min-script', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), false, true);
            wp_enqueue_script('choices-extension-script', plugin_dir_url(__FILE__) . 'js/choices.min.js');
            wp_enqueue_script('fullcalendar-extension-script', plugin_dir_url(__FILE__) . 'js/fullcalendar.min.js');
            wp_enqueue_script('perfect-scrollbar-extension-script', plugin_dir_url(__FILE__) . 'js/perfect-scrollbar.min.js');
            wp_enqueue_script('popper-extension-script', plugin_dir_url(__FILE__) . 'js/popper.min.js');
            wp_enqueue_script('chartjs-extension-script', plugin_dir_url(__FILE__) . 'js/chartjs.min.js');


            wp_enqueue_script('smooth-scrollbar-extension-script', plugin_dir_url(__FILE__) . 'js/smooth-scrollbar.min.js');
            wp_localize_script('plugin-custom', 'my_ajax_object', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('my_ajax_nonce'),
            ));

            //enqueue Style files
            wp_enqueue_style('fontawesome-style', plugin_dir_url(__FILE__) . 'css/all.min.css');
            wp_enqueue_style('dashboard-styles', plugin_dir_url(__FILE__) . 'css/custom-styles.css', array(), "1.3");
            wp_enqueue_style('nucleo-icons', plugin_dir_url(__FILE__) . 'css/nucleo-icons.css');
            wp_enqueue_style('nucleo-style', plugin_dir_url(__FILE__) . 'css/nucleo-svg.css');
            wp_enqueue_style('bootstrap-style', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css');
            wp_enqueue_style('soft-style-map', plugin_dir_url(__FILE__) . 'css/soft-ui-dashboard.css.map');
            wp_enqueue_style('soft-style-min', plugin_dir_url(__FILE__) . 'css/soft-ui-dashboard.min.css');
            wp_enqueue_style('soft-style', plugin_dir_url(__FILE__) . 'css/soft-ui-dashboard.css');
        }
    }

    //Add database tables and option when activate plugin
    public function PSX_add_database_tables()
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



        // Include the upgrade script
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Create tables
        dbDelta($table_polls);
        dbDelta($table_survey_questions);
        dbDelta($table_survey_answers);
        dbDelta($table_survey_responses);
        dbDelta($table_survey_responses_data);
    }

    // Add main menu page (PollSurveyXpress)
    public function PSX_add_admin_menu_link()
    {
        add_menu_page(
            'PollSurveyXpress',                  // the page title of Plugin
            'PollSurveyXpress',                  // the Title that appears in the menu bar
            'manage_options',               // permissions that can see the menu (admin OR higher) => capability
            'poll-survey-xpress',             // unique menu slug
            array($this, 'PSX_poll_survey_xpress_surveys_callback'),    // method for output
            'dashicons-media-document', // You can add the link of custom icon 
            70
        );

        // Add submenu pages (Surveys, Add New, Settings)
        add_submenu_page('poll-survey-xpress', 'Surveys', ' Surveys ', 'manage_options', 'poll-survey-xpress-surveys', array($this, 'PSX_poll_survey_xpress_surveys_callback'));
        add_submenu_page('poll-survey-xpress', 'Add New', ' Add New ', 'manage_options', 'poll-survey-xpress-add', array($this, 'PSX_poll_survey_xpress_add_callback'));
        add_submenu_page('poll-survey-xpress', 'Recycle Bin', ' Recycle Bin ', 'manage_options', 'poll-survey-xpress-recycle', array($this, 'PSX_poll_survey_xpress_recycle_callback'));
        add_submenu_page('poll-survey-xpress', 'Settings', ' Settings ', 'manage_options', 'poll-survey-xpress-settings', array($this, 'PSX_poll_survey_xpress_settings_callback'));

        remove_submenu_page('poll-survey-xpress', 'poll-survey-xpress');
    }

    // Callback method for the Recycle Bin page
    public function PSX_poll_survey_xpress_recycle_callback()
    {
        include 'poll_survey_xpress_recycle.php';
    }

    // Callback method for the Surveys page
    public function PSX_poll_survey_xpress_surveys_callback()
    {
        include 'poll_survey_xpress_survey.php';
    }

    // Callback method for the Add page
    public function PSX_poll_survey_xpress_add_callback()
    {
        if (!isset($_GET['view_template'])) {
            include 'poll_survey_xpress_add.php';
        }
    }

    // Callback method for the Settings page
    public function PSX_poll_survey_xpress_settings_callback()
    {
        include 'poll_survey_xpress_settings.php';
    }
    // Add menu link in top bar (PollSurveyXpress)
    public function PSX_toolbar_link($wp_admin_bar)
    {
        if (is_admin()) {

            $link_data = array(
                'id'    => 'poll_survey_xpress',
                'title' => '<span class="ab-icon dashicons-media-document"></span><span>PollSurveyXpress</span>',
                'href'  => admin_url('admin.php?page=poll-survey-xpress-surveys'),
            );

            // Add the main menu item
            $wp_admin_bar->add_node($link_data);
        }
    }
    //hidden menu to add pages of templates
    public function PSX_view_template_action()
    {
        add_submenu_page(
            null, // Parent slug (null creates a hidden menu page)
            'Template', // Page title
            'View Template', // Menu title
            'manage_options', // Capability
            'view_template_page', // Menu slug
            array($this, 'PSX_view_template_page_callback') // Callback function
        );
    }
    //render pages of templates
    public function PSX_view_template_page_callback()
    {
        if (isset($_GET['template']) && isset($_GET['page']) && $_GET['page'] === 'view_template_page') {
            // Get the template slug from the query parameter
            $templateSlug = sanitize_text_field($_GET['template']);

            // Include the template file
            include(plugin_dir_path(__FILE__) . 'templates/' . $templateSlug . '_template.php');
        }
    }

    //hidden menu to edit pages of templates
    public function PSX_edit_templates_action()
    {
        add_submenu_page(
            null,
            'Edit Template', // Page title
            'Edit Template', // Menu title
            'manage_options', // Capability
            'edit_template_page', // Menu slug
            array($this, 'PSX_view_edit_template_page_callback') // Callback function
        );
    }

    //render pages of templates
    public function PSX_view_edit_template_page_callback()
    {
        if (isset($_GET['template']) && isset($_GET['page']) && $_GET['page'] === 'edit_template_page') {

            // Include the edit template file
            include(plugin_dir_path(__FILE__) . 'templates/surveys_template_edit.php');
        }
    }

    //hidden menu to show pages of templates
    public function PSX_show_templates_action()
    {
        add_submenu_page(
            null,
            'Show Template', // Page title
            'Edit Template', // Menu title
            'manage_options', // Capability
            'show_template_page', // Menu slug
            array($this, 'PSX_show_templates_action_callback') // Callback function
        );
    }

    //render pages of templates
    public function PSX_show_templates_action_callback()
    {
        if (isset($_GET['template']) && isset($_GET['page']) && $_GET['page'] === 'show_template_page') {
            // Get the template slug from the query parameter
            $templateSlug = sanitize_text_field($_GET['template']);
            // Get the poll ID from the query parameter
            $pollId = isset($_GET['poll_id']) ? intval($_GET['poll_id']) : 0;

            // Include the edit template file
            include(plugin_dir_path(__FILE__) . 'templates/' . $templateSlug . '_template_view.php');
        }
    }
    //Function to change settings values in database

    public function PSX_save_changes_settings(){
        if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce') ) {
            wp_send_json_error('Invalid nonce.');
        }
    
        // Decode the JSON string into an associative array
        $settings_data = json_decode(stripslashes($_POST['settings_data']), true);
    
        update_option('PSX_gdpr', $settings_data['gdpr']);
        update_option('PSX_clear_data', $settings_data['clear_data']);
        update_option('PSX_email', $settings_data['email']);

        $admin_email = get_option('admin_email');
        // Get the submitted admin email from the form
        $submitted_admin_email = sanitize_email($settings_data['admin_email']);
        
        // Update the admin email option if it's different
        if ($submitted_admin_email !== $admin_email) {
            update_option('PSX_survey_email', $submitted_admin_email);
            $admin_email = $submitted_admin_email; // Update the admin email variable
        }
    
        // Send a success response
        wp_send_json_success('Settings saved successfully.');
    }
    
    
    //Method to save poll (Multiple Choice) data
    public function PSX_save_poll_Multiple_data()
    {
        global $wpdb;
        if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce') ) {
            wp_send_json_error('Invalid nonce.');
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["poll_data"])) {
            $poll_data_array = json_decode(stripslashes($_POST["poll_data"]), true);
        
            // Extract necessary data from $poll_data_array
            $surveyTitle = sanitize_text_field($poll_data_array['surveyTitle']);
            $pollCards = $poll_data_array['pollCards'];
            $settings = $poll_data_array['settings'];
            $template = sanitize_text_field($poll_data_array['template']);
        
            // Sanitize and validate date inputs
            $start_date = empty($settings['start_date']) ? current_time('mysql') : sanitize_text_field($settings['start_date']);
            $end_date = empty($settings['end_date']) ? date('Y-m-d H:i:s', strtotime('+100 years')) : sanitize_text_field($settings['end_date']);
        
            // Sanitize other settings
            $cta_Text = sanitize_text_field($settings['cta_Text']);
            $status = $settings['status'] ? 'active' : 'inactive';
            $color = sanitize_hex_color($settings['color']);
            $bgcolor = sanitize_hex_color($settings['bgcolor']);
            $sharing = isset($settings['sharing']) ? 'true' : 'false';
            $real_time_result_text = isset($settings['real_time_check']) ? '' : sanitize_text_field($settings['real_time_result_text']);
            $min_votes = absint($settings['min_votes']);
        
            // Insert data into polls_psx_polls table
            $poll_data_array_insert = array(
                'title' => $surveyTitle,
                'cta_Text' => $cta_Text,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => $status,
                'template' => $template,
                'Short_Code' => '',
                'color' => $color,
                'bgcolor' => $bgcolor,
                'sharing' => $sharing,
                'real_time_result_text' => $real_time_result_text,
                'min_votes' => $min_votes
            );
        
            // Insert the poll data into the polls_psx_polls table
            $wpdb->insert($wpdb->prefix . 'polls_psx_polls', $poll_data_array_insert);
            $poll_id = $wpdb->insert_id;
            
            // Generate the shortcode based on title and ID
            $shortcode = 'poll_' . sanitize_title_with_dashes($surveyTitle) . '_' . $poll_id;
        
            // Update the Short_Code field in polls_psx_polls table
            $wpdb->update(
                $wpdb->prefix . 'polls_psx_polls',
                array('Short_Code' => $shortcode),
                array('poll_id' => $poll_id)
            );
        
            foreach ($pollCards as $pollCard) {
                $question_data = array(
                    'poll_id' => $poll_id,
                    'question_text' => sanitize_text_field($pollCard['questionTitle']),
                );
                $wpdb->insert($wpdb->prefix . 'polls_psx_survey_questions', $question_data);
                $question_id = $wpdb->insert_id;
        
                // Insert data into polls_psx_survey_answers table
                foreach ($pollCard['options'] as $option) {
                    $answer_data = array(
                        'poll_id' => $poll_id,
                        'question_id' => $question_id,
                        'answer_text' => sanitize_text_field($option),
                    );
                    $wpdb->insert($wpdb->prefix . 'polls_psx_survey_answers', $answer_data);
                }
            }
        }
        wp_die();
    }        

    //Method to save poll (Rating) data

    public function PSX_save_poll_rating_data()
    {
        global $wpdb;
        if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce') ) {
            wp_send_json_error('Invalid nonce.');
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["poll_data"])) {
            $poll_data_array = json_decode(stripslashes($_POST["poll_data"]), true);

            // Extract necessary data from $poll_data_array
            $surveyTitle = sanitize_text_field($poll_data_array['surveyTitle']);
            $questions = $poll_data_array['questions'];
            $ratesArray = $poll_data_array['ratesArray'];
            $settings = $poll_data_array['settings'];
            $template = sanitize_text_field($poll_data_array['template']);

            // Sanitize and validate date inputs
            $start_date = empty($settings['start_date']) ? current_time('mysql') : sanitize_text_field($settings['start_date']);
            $end_date = empty($settings['end_date']) ? date('Y-m-d H:i:s', strtotime('+100 years')) : sanitize_text_field($settings['end_date']);

            // Sanitize other settings
            $cta_Text = sanitize_text_field($settings['cta_Text']);
            $status = $settings['status'] ? 'active' : 'inactive';
            $color = sanitize_hex_color($settings['color']);
            $bgcolor = sanitize_hex_color($settings['bgcolor']);
            $sharing = isset($settings['sharing']) ? 'true' : 'false';
            $real_time_result_text = isset($settings['real_time_check']) ? '' : sanitize_text_field($settings['real_time_result_text']);
            $min_votes = absint($settings['min_votes']);

            // Insert data into polls_psx_polls table
            $poll_data_array_insert = array(
                'title' => $surveyTitle,
                'cta_Text' => $cta_Text,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => $status,
                'template' => $template,
                'Short_Code' => '',
                'color' => $color,
                'bgcolor' => $bgcolor,
                'sharing' => $sharing,
                'real_time_result_text' => $real_time_result_text,
                'min_votes' => $min_votes
            );

            // Insert the poll data into the polls_psx_polls table
            $wpdb->insert($wpdb->prefix . 'polls_psx_polls', $poll_data_array_insert);
            $poll_id = $wpdb->insert_id;
            
            // Generate the shortcode based on title and ID
            $shortcode = 'poll_' . sanitize_title_with_dashes($surveyTitle) . '_' . $poll_id;

            // Update the Short_Code field in polls_psx_polls table
            $wpdb->update(
                $wpdb->prefix . 'polls_psx_polls',
                array('Short_Code' => $shortcode),
                array('poll_id' => $poll_id)
            );

            foreach ($questions as $question) {
                $question_data = array(
                    'poll_id' => $poll_id,
                    'question_text' => sanitize_text_field($question['questionTitle']),
                );
                $wpdb->insert($wpdb->prefix . 'polls_psx_survey_questions', $question_data);
                $question_id = $wpdb->insert_id;

                // Insert data into polls_psx_survey_answers table
                foreach ($ratesArray as $option) {
                    $answer_data = array(
                        'poll_id' => $poll_id,
                        'question_id' => $question_id,
                        'answer_text' => sanitize_text_field($option),
                    );
                    $wpdb->insert($wpdb->prefix . 'polls_psx_survey_answers', $answer_data);
                }
            }
        }
        wp_die();
    }


    //Method to save poll (Open Ended) data
    public function PSX_save_poll_open_ended_data()
    {
        global $wpdb;
        if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce') ) {
            wp_send_json_error('Invalid nonce.');
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["poll_data"])) {
            $poll_data_array = json_decode(stripslashes($_POST["poll_data"]), true);

            // Extract necessary data from $poll_data_array
            $surveyTitle = sanitize_text_field($poll_data_array['surveyTitle']);
            $questions = $poll_data_array['questions'];
            $settings = $poll_data_array['settings'];
            $template = sanitize_text_field($poll_data_array['template']);

            // Sanitize and validate date inputs
            $start_date = empty($settings['start_date']) ? current_time('mysql') : sanitize_text_field($settings['start_date']);
            $end_date = empty($settings['end_date']) ? date('Y-m-d H:i:s', strtotime('+100 years')) : sanitize_text_field($settings['end_date']);

            // Sanitize other settings
            $cta_Text = sanitize_text_field($settings['cta_Text']);
            $status = $settings['status'] ? 'active' : 'inactive';
            $color = sanitize_hex_color($settings['color']);
            $bgcolor = sanitize_hex_color($settings['bgcolor']);
            $sharing = isset($settings['sharing']) ? 'true' : 'false';
            $real_time_result_text = isset($settings['real_time_check']) ? '' : sanitize_text_field($settings['real_time_result_text']);
            $min_votes = absint($settings['min_votes']);

            // Insert data into polls_psx_polls table
            $poll_data_array_insert = array(
                'title' => $surveyTitle,
                'cta_Text' => $cta_Text,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => $status,
                'template' => $template,
                'Short_Code' => '',
                'color' => $color,
                'bgcolor' => $bgcolor,
                'sharing' => $sharing,
                'real_time_result_text' => $real_time_result_text,
                'min_votes' => $min_votes
            );

            // Insert the poll data into the polls_psx_polls table
            $wpdb->insert($wpdb->prefix . 'polls_psx_polls', $poll_data_array_insert);
            $poll_id = $wpdb->insert_id;
            
            // Generate the shortcode based on title and ID
            $shortcode = 'poll_' . sanitize_title_with_dashes($surveyTitle) . '_' . $poll_id;

            // Update the Short_Code field in polls_psx_polls table
            $wpdb->update(
                $wpdb->prefix . 'polls_psx_polls',
                array('Short_Code' => $shortcode),
                array('poll_id' => $poll_id)
            );

            foreach ($questions as $question) {
                $question_data = array(
                    'poll_id' => $poll_id,
                    'question_text' => sanitize_text_field($question['questionTitle']),
                );
                $wpdb->insert($wpdb->prefix . 'polls_psx_survey_questions', $question_data);
                // Add an answer for each question

                $question_id = $wpdb->insert_id;
                $answer_data = array(
                    'poll_id' => $poll_id,
                    'question_id' => $question_id,
                    'answer_text' => '',
                );
                $wpdb->insert($wpdb->prefix . 'polls_psx_survey_answers', $answer_data);
            }
        }
        wp_die();
    }

    //Method to change poll status (archived to inactive)
    public function PSX_restore_poll()
    {
        if (isset($_POST["poll_id"])) {
            $poll_id = intval($_POST["poll_id"]);

            // Sanitize the poll ID
            $poll_id = absint($poll_id);

            // Update the poll status in the database
            global $wpdb;
            $table_name = $wpdb->prefix . "polls_psx_polls";
            $wpdb->update(
                $table_name,
                array("status" => "inactive"),
                array("poll_id" => $poll_id)
            );
        }
        wp_die();
    }

    //Method to change poll status (active/inactive to archived)
    public function PSX_archive_poll()
    {
        if (isset($_POST["poll_id"])) {
            $poll_id = intval($_POST["poll_id"]);
    
            // Sanitize the poll ID
            $poll_id = absint($poll_id);
    
            // Update the poll status in the database
            global $wpdb;
            $table_name = $wpdb->prefix . "polls_psx_polls";
            $wpdb->update(
                $table_name,
                array("status" => "archived"),
                array("poll_id" => $poll_id)
            );
        }
        wp_die();
    }
    
    //Method to delete poll (delete from database)
    public function PSX_permenant_delete()
    {
        if (isset($_POST["poll_id"])) {
            $poll_id = intval($_POST["poll_id"]);

            // Sanitize the poll ID
            $poll_id = absint($poll_id);

            global $wpdb;

            $table_polls = $wpdb->prefix . "polls_psx_polls";
            $table_survey_questions = $wpdb->prefix . "polls_psx_survey_questions";
            $table_survey_answers = $wpdb->prefix . "polls_psx_survey_answers";
            $table_survey_responses = $wpdb->prefix . "polls_psx_survey_responses";
            $table_survey_responses_data = $wpdb->prefix . "polls_psx_response_data";

            $responses_id = $wpdb->get_results("SELECT response_id FROM {$wpdb->prefix}polls_psx_survey_responses WHERE poll_id = $poll_id", ARRAY_A);

            foreach ($responses_id as $response) {
                $response_id = $response['response_id'];
                $wpdb->delete($table_survey_responses_data, array("response_id" => $response_id));
            }

            // Delete from survey responses
            $wpdb->delete($table_survey_responses, array("poll_id" => $poll_id));

            // Delete from survey answers
            $wpdb->delete($table_survey_answers, array("poll_id" => $poll_id));

            // Delete from survey questions
            $wpdb->delete($table_survey_questions, array("poll_id" => $poll_id));

            // Delete from polls
            $wpdb->delete($table_polls, array("poll_id" => $poll_id));
        }

        wp_die();
    }

    // Add shortcode form to the frontend of the website
    
    public function PSX_poll_shortcode_handler($atts)
    {
        global $wpdb;
        
        $length = count($atts);
        // Sanitize the shortcode attributes
        $atts = array_map('sanitize_text_field', $atts);
    
        // Extract the poll ID from the shortcode
        $components = explode("_", $atts[0]);
        $poll_id = absint($components[2]);
        
        // Query the database
        $table_name = $wpdb->prefix . 'polls_psx_polls';
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
        $poll_data = $wpdb->get_results($query, ARRAY_A);
    
        if ($poll_data) {
            if ($poll_data[0]['status'] === 'active') {
                // Sanitize the poll title
                $poll_title = sanitize_text_field($poll_data[0]['title']);
                
                // Sanitize the template type
                $template_type = sanitize_text_field($poll_data[0]['template']);
                if ($length > 1) {
                    $output = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">' . $poll_data[0]['cta_Text'] . '</button>';
                }else{
                    if ($template_type === 'Multiple Choice') {
                        $output = '<form id="poll_form" method="post">';
                        
                        $output = '<div class="mt-4 container-fluid bg-transparent">';
                        $output .= '<input type="hidden" id="my-ajax-nonce" value="' .wp_create_nonce('my_ajax_nonce'). '"/>';
                        // Start generating the poll structure
                        // Fetch questions from the database
                        $table_name = $wpdb->prefix . 'polls_psx_survey_questions';
                        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
                        $questions = $wpdb->get_results($query, ARRAY_A);

                        $output .= '<h4 class="mb-3">' . $poll_data[0]['title'] . '</h4>';

                        $output .= '<div class="col">';
                        foreach ($questions as $question) {
                            $output .= '<div id="poll_card" data-card-id="' . $poll_id . '" class="poll-question-container position-relative flex-column gap-2 border rounded-3 bg-white p-4 m-0 mt-3">';

                            // Show results button
                            $output .= '<button disabled id="percentage_result_btn" tabindex="0" role="button" data-toggle="popover" data-trigger="click" title="Dismissible popover" data-content="And heres some amazing content. Its very engaging. Right?" data-question-id="' . $question['question_id'] . '" style="font-size:11px" class="btn btn-white shadow-none border p-2 position-absolute top-5 end-3 text-primary bg-white percentage-result-btn"> Show results </button>';

                            // Poll title     
                            $output .= '<h6 class="mb-4">' . $question['question_text'] . '</h6>';

                            // Fetch answers for each question
                            $table_name = $wpdb->prefix . 'polls_psx_survey_answers';
                            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d and question_id = %d", $poll_id, $question['question_id']);
                            $answers = $wpdb->get_results($query, ARRAY_A);

                            foreach ($answers as $answer) {
                                $output .= '<div class="poll-answer position-relative w-full d-flex align-items-center justify-content-between mb-2 p-2">';
                                $output .= '<div id="disable_text_questions" class="d-flex align-items-center">';
                                $output .= '<input data-question-id="' . $question['question_id'] . '" data-answer-id="' . $answer['answer_id'] . '" type="radio" class="poll-answer-radio" name="poll_answers_' . $question['question_id'] . '" value="' . $answer['answer_id'] . '" id="poll_answer_' . $question['question_id'] . '_' . $answer['answer_id'] . '">';
                                $output .= '<label for="poll_answer_' . $question['question_id'] . '_' . $answer['answer_id'] . '" class="m-0">' . $answer['answer_text'] . '</label>';
                                $output .= '</div>';
                                $output .= '</div>';
                            }

                            $output .= '</div>'; // Close the poll structure div
                            $output .= '<div class="spinner-border text-primary d-none" role="status">
                        </div>'; // Close the poll structure div
                        }
                        $output .= '</div>'; // Close the col div

                        $output .= '<button id="mcq_save_button" disabled
                            class="btn align-self-start text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold mb-0 mt-4">
                            Save
                        </button>';
                        $output .= '</div>'; // Close the container-fluid div
                        $output .= '</form>';

                    // Fetch questions from the database
                    } else if ($poll_data[0]['template'] === 'Open ended') {
                        // Start generating the poll structure
                        $table_name = $wpdb->prefix . 'polls_psx_survey_questions';
                        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
                        $questions = $wpdb->get_results($query, ARRAY_A);

                        $output = '<div class="mt-4 container-fluid bg-transparent">';
                        $output .= '<input type="hidden" id="my-ajax-nonce" value="' .wp_create_nonce('my_ajax_nonce'). '"/>';

                        $output .= '<h4 class="mb-3">' . $poll_data[0]['title'] . '</h4>';

                        $output .= '<div class="col">';
                        foreach ($questions as $question) {
                            $output .= '<div id="poll_card" data-card-id="' . $poll_id . '" class="position-relative flex-column gap-2 border rounded-3 bg-white p-4 m-0 mt-3">';
                            $output .= '<h6 class="mb-3">' . $question['question_text'] . '</h6>';
                            $output .= '<textarea data-question-id="' . $question['question_id'] . '" class="poll-question-textarea form-control mb-2 w-100 border rounded-1" placeholder="Edit the poll question title"></textarea>';
                            $output .= '</div>'; // Close the poll structure div
                        }

                        $output .= '</div>'; // Close the col div

                        $output .= '<button disabled id="open_ended_save_button"
                        class="align-self-start text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold mb-0 mt-4">
                        Save
                    </button>';
                        $output .= '</div>'; // Close the container-fluid div

                    } else if ($poll_data[0]['template'] === 'Rating') {
                        // Code for the 'Rating' template
                        $table_name = $wpdb->prefix . 'polls_psx_survey_questions';
                        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
                        $questions = $wpdb->get_results($query, ARRAY_A);
                        
                        $output = '<form id="poll_form" method="post">';
                        $output = '<div class="position-relative w-100 col-12 mt-4 bg-white border">';

                        $output .= '<input type="hidden" id="my-ajax-nonce" value="' .wp_create_nonce('my_ajax_nonce'). '"/>';
                    

                        $output .= '<div style="background-color: #EEE;" class="d-flex justify-content-between align-items-center mb-1 p-4 ">';

                        $output .= '<h4 class="m-0">' . $poll_data[0]['title'] . '</h4>';

                        $table_name = $wpdb->prefix . 'polls_psx_survey_answers';
                        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
                        $ratings = $wpdb->get_results($query, ARRAY_A);

                        // Start of rating buttons div
                        $output .= '<div class="d-flex justify-content-around align-items-center col-8 gap-2">';
                        $flag = false;
                        $flag_text = $ratings[0]["answer_text"];

                        foreach ($ratings as $rating) {
                            if ($flag_text === $rating["answer_text"] && $flag) {
                                break;
                            }
                            $output .= '<p class="m-0 text-sm ">' . $rating["answer_text"] . '</p>';
                            $flag = true;
                        }

                        $output .= '</div>';

                        $output .= '</div>'; // Close the Rating space-between div

                        $output .= '<div class="p-4">';
                        foreach ($questions as $question) {
                            // Start of the rating card
                            $output .= '<div id="poll_card" data-card-id="' . $poll_id . '" class="poll_card d-flex justify-content-between align-items-center mb-4">';
                            $output .= '<h6 class="m-0 text-break" data-question-id="' . $question['question_id'] . '">' . $question['question_text'] . '</h6>';

                            // Fetch answers for each question
                            $table_name = $wpdb->prefix . 'polls_psx_survey_answers';
                            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d and question_id = %d", $poll_id, $question['question_id']);
                            $answers = $wpdb->get_results($query, ARRAY_A);

                            $output .= '<div class="d-flex justify-content-around align-items-center col-8 gap-2">'; // Start the answers container

                            foreach ($answers as $answer) {
                                $output .= '<input data-question-id="' . $question['question_id'] . '" data-answer-id="' . $answer['answer_id'] . '" type="radio" class="poll-answer-radio" name="poll_answers_' . $question['question_id'] . '" value="' . $answer['answer_id'] . '" id="poll_answer_' . $question['question_id'] . '_' . $answer['answer_id'] . '">';
                            }

                            $output .= '</div>'; // End the answers container

                            // End of the rating card
                            $output .= "</div>";
                        }
                        $output .= '</div>';

                        $output .= '</div>';

                        $output .= '<div>';
                        $output .= '<button disabled type="submit" id="rating_save_button"
                        class="text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold m-0">
                        Save
                        </button>';
                        $output .= '</div>';
                    }
                }
            } else {
                $output = '<p>Poll not found.</p>';
            }
            return $output;
    }
    }

    // Function to update poll settings                                                                            
    public function PSX_update_poll_settings()
    {
        global $wpdb;
        if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce') ) {
            wp_send_json_error('Invalid nonce.');
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["poll_data"])) {
            $poll_data_array = json_decode(stripslashes($_POST["poll_data"]), true);

            // Sanitize inputs
            $poll_id = absint($poll_data_array["poll_id"]);
            $start_date = sanitize_text_field($poll_data_array["start_date"]);
            $end_date = sanitize_text_field($poll_data_array["end_date"]);
            $status = isset($poll_data_array["status"]) ? (bool) $poll_data_array["status"] : false;
            $color = sanitize_text_field($poll_data_array["color"]);
            $bgcolor = sanitize_text_field($poll_data_array["bgcolor"]);
            $sharing = isset($poll_data_array["sharing"]) ? sanitize_text_field($poll_data_array["sharing"]) : 'false';
            $real_time_result_text = sanitize_text_field($poll_data_array["real_time_result_text"]);
            $real_time_check = isset($poll_data_array["real_time_check"]) ? (bool) $poll_data_array["real_time_check"] : false;
            $min_votes = absint($poll_data_array["min_votes"]);
            $cta_text = sanitize_text_field($poll_data_array["cta_Text"]);

            $table_name = $wpdb->prefix . "polls_psx_polls";
            $wpdb->update(
                $table_name,
                array(
                    "start_date" => $start_date,
                    "end_date" => $end_date,
                    "status" => $status ? 'active' : 'inactive',
                    "color" => $color,
                    "bgcolor" => $bgcolor,
                    "sharing" => $sharing,
                    "real_time_result_text" => $real_time_check ? '' : $real_time_result_text,
                    "min_votes" => $min_votes,
                    "cta_Text" => $cta_text
                ),
                array("poll_id" => $poll_id)
            );
        }
    }

    //Function to save poll response
    public function PSX_save_poll_response()
    {
        global $wpdb;
        if ( !isset($_POST['nonce'])  || ! wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce'))  {
            wp_send_json_error('Invalid nonce.');
        }
        if (isset($_POST['poll_response'])) {
            $poll_response = json_decode(stripslashes($_POST['poll_response']), true);

            // Extract data from the poll_response object
            $poll_id = $poll_response['poll_id'];
            $user_id = is_user_logged_in() ? get_current_user_id() : 0;
            if (!isset($_SESSION['my_session_id'])) {
                $_SESSION['my_session_id'] = uniqid(); 
            }
            $session_id = $_SESSION['my_session_id'];

            if (get_option('gdpr') === '0') {
                if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    // Check if multiple IP addresses are provided via proxies
                    $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                    $userIP = $ipList[0];
                } else {
                    $userIP = $_SERVER['REMOTE_ADDR'];
                }
            } else {
                $userIP = '';
            }


            $responses = $poll_response['responses'];

            // Insert poll response data into the database
            $response_table = $wpdb->prefix . 'polls_psx_survey_responses';
            $wpdb->insert($response_table, array(
                'poll_id' => $poll_id,
                'ip_address' => $userIP,
                'user_id' => $user_id,
                'session_id' => $session_id,
            ));
            $response_id = $wpdb->insert_id;

            $responses_data_table = $wpdb->prefix . 'polls_psx_survey_responses_data';
            foreach ($responses as $response) {
                $wpdb->insert($responses_data_table, array(
                    'response_id' => $response_id,
                    'question_id' => $response['question_id'],
                    'answer_id' => $response['answer_id'],
                    'open_text_response' => $response['answer_text'],
                ));
            }

            $response_ids = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT response_id FROM {$wpdb->prefix}polls_psx_survey_responses WHERE poll_id = %d",
                    $poll_id
                )
            );
            $responses_data = array();
            foreach ($response_ids as $id) {
                $response_data = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT question_id, answer_id FROM {$wpdb->prefix}polls_psx_survey_responses_data WHERE response_id = %s",
                        $id
                    )
                );
                $responses_data[] = $response_data;
            }

            $answerCounts = array();
            foreach ($responses_data as $response) {
                foreach ($response as $answer) {
                    $questionId = $answer->question_id;
                    $answerId = $answer->answer_id;

                    if (!isset($answerCounts[$questionId])) {
                        $answerCounts[$questionId] = array();
                    }

                    if (!isset($answerCounts[$questionId][$answerId])) {
                        $answerCounts[$questionId][$answerId] = 0;
                    }

                    $answerCounts[$questionId][$answerId]++;
                }
            }

            // Step 2: Calculate total response count
            $totalResponses = count($responses_data);

            // Step 3: Calculate percentages
            $percentages = array();
            foreach ($answerCounts as $questionId => $answerData) {
                $percentages[$questionId] = array();
                foreach ($answerData as $answerId => $count) {
                    $percentage = ($count / $totalResponses) * 100;
                    $formattedPercentage = number_format($percentage, 2);
                    $percentages[$questionId][$answerId] = $formattedPercentage;
                }
            }

            // Calculate the total percentage for each question
            $totalPercentages = array();
            foreach ($percentages as $questionId => $answerData) {
                $totalPercentage = array_sum($answerData);
                $totalPercentages[$questionId] = $totalPercentage;
            }

            $table_name = $wpdb->prefix . 'polls_psx_survey_responses'; // Replace 'prefix' with your database table prefix
            $query = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE session_id = %s", $session_id);
            $count = $wpdb->get_var($query);

            // If the count is greater than 0, the session ID is found in the table
            $isSessionSaved = ($count > 0);
            $jsonResponse = '{"percentages":' . json_encode($percentages) . ',"isSessionSaved":' . json_encode($isSessionSaved) . '}';

            echo json_encode($jsonResponse);
        }
        wp_die();
    }
}
$survey_plugin = new PollSurveyXpress();
