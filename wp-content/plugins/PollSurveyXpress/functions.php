<?php
class PollSurveyXpress
{

    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu_link'));
        add_action('admin_bar_menu', array($this, 'toolbar_link'), 99);
        add_action('admin_menu', array($this, 'view_template_action'));
        add_action('admin_menu', array($this, 'edit_templates_action'));
        add_action('admin_menu', array($this, 'show_templates_action'));


        add_action('wp_ajax_save_poll_Multiple_data', array($this, 'save_poll_Multiple_data'));
        add_action('wp_ajax_nopriv_save_poll_Multiple_data', array($this, 'save_poll_Multiple_dataa'));
        add_action('wp_ajax_save_poll_rating_data', array($this, 'save_poll_rating_data'));
        add_action('wp_ajax_nopriv_save_poll_rating_data', array($this, 'save_poll_rating_data'));
        add_action('wp_ajax_save_poll_open_ended_data', array($this, 'save_poll_open_ended_data'));
        add_action('wp_ajax_nopriv_save_poll_open_ended_data', array($this, 'save_poll_open_ended_data'));
        add_action("wp_ajax_archive_poll", array($this, "archive_poll"));
        add_action("wp_ajax_nopriv_archive_poll", array($this, "archive_poll")); // For non-logged-in users
        add_action("wp_ajax_restore_poll", array($this, "restore_poll"));
        add_action("wp_ajax_nopriv_restore_poll", array($this, "restore_poll")); // For non-logged-in users
        add_action("wp_ajax_permenant_delete", array($this, "permenant_delete"));
        add_action("wp_ajax_nopriv_permenant_delete", array($this, "permenant_delete")); // For non-logged-in users
        add_shortcode('poll', array($this, 'poll_shortcode_handler'));
    }

    // Enqueue scripts and styles for the admin area
    public function enqueue_admin_scripts()
    {
        //enqueue Script files
        if (isset($_GET['page']) && (($_GET['page'] === 'poll-survey-xpress-surveys' || $_GET['page'] === 'poll-survey-xpress-recycle' || $_GET['page'] === 'poll-survey-xpress-add' || $_GET['page'] === 'poll-survey-xpress-settings' || $_GET['page'] === 'view_template_page' || $_GET['page'] === 'show_template_page' || $_GET['page'] === 'poll-survey-xpress-recycle'))) {
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

            wp_enqueue_script('smooth-scrollbar-extension-script', plugin_dir_url(__FILE__) . 'js/smooth-scrollbar.min.js');
            wp_localize_script('plugin-custom', 'my_ajax_object', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('my_ajax_nonce'),
            ));

            //enqueue Style files
            wp_enqueue_style('fontawesome-style', plugin_dir_url(__FILE__) . 'css/all.min.css');
            wp_enqueue_style('dashboard-styles', plugin_dir_url(__FILE__) . 'css/custom-styles.css');
            wp_enqueue_style('nucleo-icons', plugin_dir_url(__FILE__) . 'css/nucleo-icons.css');
            wp_enqueue_style('nucleo-style', plugin_dir_url(__FILE__) . 'css/nucleo-svg.css');
            wp_enqueue_style('bootstrap-style', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css');
            wp_enqueue_style('soft-style-map', plugin_dir_url(__FILE__) . 'css/soft-ui-dashboard.css.map');
            wp_enqueue_style('soft-style-min', plugin_dir_url(__FILE__) . 'css/soft-ui-dashboard.min.css');
            wp_enqueue_style('soft-style', plugin_dir_url(__FILE__) . 'css/soft-ui-dashboard.css');
        }
    }

    //Add database tables and option when activate plugin
    public function add_database_tables()
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
                user_id int(11),
                session_id varchar(255),
                question_id int(11),
                answer_id int(11),
                open_text_response varchar(255),
                PRIMARY KEY (response_id),
                FOREIGN KEY (poll_id) REFERENCES {$wpdb->prefix}polls_psx_polls(poll_id),
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
    }

    // Add menu page (PollSurveyXpress)
    public function add_admin_menu_link()
    {
        add_menu_page(
            'PollSurveyXpress',                  // the page title of Plugin
            'PollSurveyXpress',                  // the Title that appears in the menu bar
            'manage_options',               // permissions that can see the menu (admin OR higher) => capability
            'poll-survey-xpress',             // unique menu slug
            array($this, 'poll_survey_xpress_surveys_callback'),    // method for output
            'dashicons-media-document', // You can add the link of custom icon 
            70
        );

        // Add submenu pages (Surveys, Add New, Settings)
        add_submenu_page('poll-survey-xpress', 'Surveys', ' Surveys ', 'manage_options', 'poll-survey-xpress-surveys', array($this, 'poll_survey_xpress_surveys_callback'));
        add_submenu_page('poll-survey-xpress', 'Add New', ' Add New ', 'manage_options', 'poll-survey-xpress-add', array($this, 'poll_survey_xpress_add_callback'));
        add_submenu_page('poll-survey-xpress', 'Recycle Bin', ' Recycle Bin ', 'manage_options', 'poll-survey-xpress-recycle', array($this, 'poll_survey_xpress_recycle_callback'));
        add_submenu_page('poll-survey-xpress', 'Settings', ' Settings ', 'manage_options', 'poll-survey-xpress-settings', array($this, 'poll_survey_xpress_settings_callback'));

        remove_submenu_page('poll-survey-xpress', 'poll-survey-xpress');
    }
    public function poll_survey_xpress_recycle_callback()
    {
        include 'poll_survey_xpress_recycle.php';
    }

    // Callback method for the Surveys page
    public function poll_survey_xpress_surveys_callback()
    {
        include 'poll_survey_xpress_survey.php';
    }

    // Callback method for the Add page
    public function poll_survey_xpress_add_callback()
    {
        if (!isset($_GET['view_template'])) {
            include 'poll_survey_xpress_add.php';
        }
    }

    // Callback method for the Settings page
    public function poll_survey_xpress_settings_callback()
    {
        include 'poll_survey_xpress_settings.php';
    }
    // Add menu link in top bar (PollSurveyXpress)
    public function toolbar_link($wp_admin_bar)
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
    public function view_template_action()
    {
        add_submenu_page(
            null, // Parent slug (null creates a hidden menu page)
            'Template', // Page title
            'View Template', // Menu title
            'manage_options', // Capability
            'view_template_page', // Menu slug
            array($this, 'view_template_page_callback') // Callback function
        );
    }
    //render pages of templates
    public function view_template_page_callback()
    {
        if (isset($_GET['template']) && isset($_GET['page']) && $_GET['page'] === 'view_template_page') {
            // Get the template slug from the query parameter
            $templateSlug = sanitize_text_field($_GET['template']);

            // Include the template file
            include(plugin_dir_path(__FILE__) . 'templates/' . $templateSlug . '_template.php');
        }
    }
    public function edit_templates_action()
    {
        add_submenu_page(
            null,
            'Edit Template', // Page title
            'Edit Template', // Menu title
            'manage_options', // Capability
            'edit_template_page', // Menu slug
            array($this, 'view_edit_template_page_callback') // Callback function
        );
    }
    public function view_edit_template_page_callback()
    {
        if (isset($_GET['template']) && isset($_GET['page']) && $_GET['page'] === 'edit_template_page') {
            // Get the template slug from the query parameter
            $templateSlug = sanitize_text_field($_GET['template']);
            // Get the poll ID from the query parameter
            $pollId = isset($_GET['poll_id']) ? intval($_GET['poll_id']) : 0;

            // Include the edit template file
            include(plugin_dir_path(__FILE__) . 'templates/surveys_template_edit.php');
        }
    }
    public function show_templates_action()
    {
        add_submenu_page(
            null,
            'Show Template', // Page title
            'Edit Template', // Menu title
            'manage_options', // Capability
            'show_template_page', // Menu slug
            array($this, 'show_templates_action_callback') // Callback function
        );
    }
    public function show_templates_action_callback()
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

    public function save_poll_Multiple_data()
    {
        global $wpdb;
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["poll_data"])) {
            $poll_data_array = json_decode(stripslashes($_POST["poll_data"]), true);

            // Extract necessary data from $poll_data_array
            $surveyTitle = $poll_data_array['surveyTitle'];
            $pollCards = $poll_data_array['pollCards'];
            $settings = $poll_data_array['settings'];
            $template = $poll_data_array['template'];

            // Insert data into polls_psx_polls table
            $poll_data_array_insert = array(
                'title' => $surveyTitle,
                'cta_Text' => $settings['cta_Text'],
                'start_date' => empty($settings['start_date']) ? current_time('mysql') : $settings['start_date'],
                'end_date' => empty($settings['end_date']) ? date('Y-m-d H:i:s', strtotime('+100 years')) : $settings['end_date'],
                'status' => $settings['status'] ? 'active' : 'inactive',
                'template' => $template,
                'Short_Code' => '',
                'color' => $settings['color'],
                'bgcolor' => $settings['bgcolor'],
                'sharing' => $settings['sharing'] ? 'true' : 'false',
                'real_time_result_text' => $settings['real_time_check'] ? '' : $settings['real_time_result_text'],
                'min_votes' => $settings['min_votes']
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
                    'question_text' => $pollCard['questionTitle'],
                );
                $wpdb->insert($wpdb->prefix . 'polls_psx_survey_questions', $question_data);
                $question_id = $wpdb->insert_id;

                // Insert data into polls_psx_survey_answers table
                foreach ($pollCard['options'] as $option) {
                    $answer_data = array(
                        'poll_id' => $poll_id,
                        'question_id' => $question_id,
                        'answer_text' => $option,
                    );
                    $wpdb->insert($wpdb->prefix . 'polls_psx_survey_answers', $answer_data);
                }
            }
        }
        wp_die();
    }
    public function save_poll_rating_data()
    {
        global $wpdb;
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["poll_data"])) {
            $poll_data_array = json_decode(stripslashes($_POST["poll_data"]), true);

            // Extract necessary data from $poll_data_array
            $surveyTitle = $poll_data_array['surveyTitle'];
            $questions = $poll_data_array['questions'];
            $ratesArray = $poll_data_array['ratesArray'];
            $settings = $poll_data_array['settings'];
            $template = $poll_data_array['template'];

            // Insert data into polls_psx_polls table
            $poll_data_array_insert = array(
                'title' => $surveyTitle,
                'cta_Text' => $settings['cta_Text'],
                'start_date' => empty($settings['start_date']) ? current_time('mysql') : $settings['start_date'],
                'end_date' => empty($settings['end_date']) ? date('Y-m-d H:i:s', strtotime('+100 years')) : $settings['end_date'],
                'status' => 'archived',
                'template' => $template,
                'Short_Code' => '',
                'color' => $settings['color'],
                'bgcolor' => $settings['bgcolor'],
                'sharing' => $settings['sharing'] ? 'true' : 'false',
                'real_time_result_text' => $settings['real_time_check'] ? '' : $settings['real_time_result_text'],
                'min_votes' => $settings['min_votes']
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
                    'question_text' => $question['questionTitle'],
                );
                $wpdb->insert($wpdb->prefix . 'polls_psx_survey_questions', $question_data);
                $question_id = $wpdb->insert_id;

                // Insert data into polls_psx_survey_answers table
                foreach ($ratesArray as $otion) {
                    $answer_data = array(
                        'poll_id' => $poll_id,
                        'question_id' => $question_id,
                        'answer_text' => $otion,
                    );
                    $wpdb->insert($wpdb->prefix . 'polls_psx_survey_answers', $answer_data);
                }
            }
        }
        wp_die();
    }
    public function save_poll_open_ended_data()
    {
        global $wpdb;
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["poll_data"])) {
            $poll_data_array = json_decode(stripslashes($_POST["poll_data"]), true);

            // Extract necessary data from $poll_data_array
            $surveyTitle = $poll_data_array['surveyTitle'];
            $questions = $poll_data_array['questions'];
            $settings = $poll_data_array['settings'];
            $template = $poll_data_array['template'];

            // Insert data into polls_psx_polls table
            $poll_data_array_insert = array(
                'title' => $surveyTitle,
                'cta_Text' => $settings['cta_Text'],
                'start_date' => empty($settings['start_date']) ? current_time('mysql') : $settings['start_date'],
                'end_date' => empty($settings['end_date']) ? date('Y-m-d H:i:s', strtotime('+100 years')) : $settings['end_date'],
                'status' => $settings['status'] ? 'active' : 'inactive',
                'template' => $template,
                'Short_Code' => '',
                'color' => $settings['color'],
                'bgcolor' => $settings['bgcolor'],
                'sharing' => $settings['sharing'] ? 'true' : 'false',
                'real_time_result_text' => $settings['real_time_check'] ? '' : $settings['real_time_result_text'],
                'min_votes' => $settings['min_votes']
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
                    'question_text' => $question['questionTitle'],
                );
                $wpdb->insert($wpdb->prefix . 'polls_psx_survey_questions', $question_data);
            }
        }
        wp_die();
    }
    public function archive_poll()
    {
        if (isset($_POST["poll_id"])) {
            $poll_id = intval($_POST["poll_id"]);

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
    public function restore_poll()
    {
        if (isset($_POST["poll_id"])) {
            $poll_id = intval($_POST["poll_id"]);

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
    public function permenant_delete()
    {
        if (isset($_POST["poll_id"])) {
            $poll_id = intval($_POST["poll_id"]);

            global $wpdb;
            $table_polls = $wpdb->prefix . "polls_psx_polls";
            $table_survey_questions = $wpdb->prefix . "polls_psx_survey_questions";
            $table_survey_answers = $wpdb->prefix . "polls_psx_survey_answers";
            $table_survey_responses = $wpdb->prefix . "polls_psx_survey_responses";

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

    // Add shortcode
    public function poll_shortcode_handler($atts)
    {
        global $wpdb;
        var_dump(
            $atts[0] . "<br>" . 'fafasfasfa'
        );
        // Extract the poll ID from the shortcode
        $components = explode("_", $atts[0]);
        $poll_id = $components[2];

        // Query the database
        $table_name = $wpdb->prefix . 'polls_psx_polls';
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
        $poll_data = $wpdb->get_results($query, ARRAY_A);

        // Process and display the poll data
        $output = '<div class="poll">';
        if ($poll_data) {
            $output .= '<h2>' . $poll_data[0]['title'] . '</h2>';
            // Add other poll data here
        } else {
            $output .= '<p>Poll not found.</p>';
        }
        $output .= '</div>';
        return $output;
    }
}
$survey_plugin = new PollSurveyXpress();
