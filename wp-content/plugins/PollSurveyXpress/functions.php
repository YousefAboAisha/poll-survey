<?php


class PollSurveyXpress
{

    // Constructor for class includes all hooks
    public function __construct()
    {

        add_action('admin_enqueue_scripts', array($this, 'PSX_enqueue_admin_scripts'));
        add_action('admin_menu', array($this, 'PSX_add_admin_menu_link'));
        add_action('admin_bar_menu', array($this, 'PSX_toolbar_link'), 99);
        add_action('wp_enqueue_scripts', array($this, 'PSX_enqueue_frontend_scripts'));



        add_action('wp_ajax_PSX_save_poll_Multiple_data', array($this, 'PSX_save_poll_Multiple_data'));
        add_action('wp_ajax_nopriv_PSX_save_poll_Multiple_data', array($this, 'PSX_save_poll_Multiple_data'));
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
        add_shortcode('poll_psx', array($this, 'PSX_poll_shortcode_handler'));
        add_action("wp_ajax_PSX_update_poll_settings", array($this, "PSX_update_poll_settings"));
        add_action("wp_ajax_nopriv_PSX_update_poll_settings", array($this, "PSX_update_poll_settings")); // For non-logged-in users
        add_action('wp_ajax_PSX_save_poll_response', array($this, 'PSX_save_poll_response'));
        add_action('wp_ajax_nopriv_PSX_save_poll_response', array($this, 'PSX_save_poll_response')); // For non-logged-in users
        add_action('wp_ajax_PSX_save_changes_settings', array($this, 'PSX_save_changes_settings'));
        add_action('wp_ajax_nopriv_PSX_save_changes_settings', array($this, 'PSX_save_changes_settings')); // For non-logged-in users
        add_action('wp_ajax_PSX_delete_poll_response', array($this, 'PSX_delete_poll_response'));
        add_action('wp_ajax_nopriv_PSX_delete_poll_response', array($this, 'PSX_delete_poll_response')); // For non-logged-in users

    }

    // Enqueue scripts and styles for the frontend
    public function PSX_enqueue_frontend_scripts()
    {

        wp_enqueue_script('jquery');
        wp_enqueue_script('Piechart', plugin_dir_url(__FILE__) . 'js/Piechart.js');
        wp_enqueue_script('plugin-custom', plugin_dir_url(__FILE__) . '/js/main.js', array('jquery'), '1.8', true);
        wp_enqueue_script('bootstrap-min-script', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), false, true);
        wp_enqueue_script('popper-extension-script', plugin_dir_url(__FILE__) . 'js/popper.min.js');
        wp_localize_script('plugin-custom', 'my_ajax_object', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('my_ajax_nonce'),
        ));
        wp_enqueue_script('canvasjs', 'https://cdn.canvasjs.com/canvasjs.min.js', array(), null, true);


        //enqueue Style files
        wp_enqueue_style('bootstrap-style', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css');
        wp_enqueue_style('soft-style', plugin_dir_url(__FILE__) . 'css/soft-ui-dashboard.css');
        wp_enqueue_style('dashboard-styles', plugin_dir_url(__FILE__) . 'css/custom-styles.css', array(), "2.0");
    }

    // Enqueue scripts and styles for the admin area
    public function PSX_enqueue_admin_scripts()
    {
        //enqueue Script files
        if (isset($_GET['page']) && (($_GET['page'] === 'poll-survey-xpress-surveys' || $_GET['page'] === 'poll-survey-xpress-recycle' || $_GET['page'] === 'poll-survey-xpress-add' || $_GET['page'] === 'poll-survey-xpress-settings' || $_GET['page'] === 'view_template_page' || $_GET['page'] === 'edit_template_page' || $_GET['page'] === 'poll-survey-xpress-recycle' || $_GET['page']
            === 'show_template_page'))) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('plugin-custom', plugin_dir_url(__FILE__) . '/js/main.js', array('jquery'), '2.1', true);
            wp_enqueue_script('bootstrap-script', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), false, true);
            wp_enqueue_script('bootstrap-min-script', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), false, true);
            wp_enqueue_script('popper-extension-script', plugin_dir_url(__FILE__) . 'js/popper.min.js');
            wp_enqueue_script('chartjs-extension-script', plugin_dir_url(__FILE__) . 'js/chartjs.min.js');

            wp_localize_script('plugin-custom', 'my_ajax_object', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('my_ajax_nonce'),
            ));
            wp_enqueue_script('canvasjs', 'https://cdn.canvasjs.com/canvasjs.min.js', array(), null, true);


            //enqueue Style files
            wp_enqueue_style('bootstrap-style', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css');
            wp_enqueue_style('soft-style', plugin_dir_url(__FILE__) . 'css/soft-ui-dashboard.css');
            wp_enqueue_style('dashboard-styles', plugin_dir_url(__FILE__) . 'css/custom-styles.css', array(), "1.7");
        }
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
        if (!isset($_GET['poll_id'])) {
            include 'poll_survey_xpress_survey.php';
        } elseif (isset($_GET['poll_id']) && !isset($_GET['action'])) {
            $template = $_GET['template'];
            $templatePath = dirname(__FILE__) . '/templates/' . $template . '_template_view.php';
            include($templatePath);
        } else {
            $templatePath = dirname(__FILE__) . '/templates/' . 'surveys_template_edit.php';
            include($templatePath);
        }
    }

    // Callback method for the Add page and templates (Add New)
    public function PSX_poll_survey_xpress_add_callback()
    {
        if (!isset($_GET['template'])) {
            include 'poll_survey_xpress_add.php';
        } else {
            $template = $_GET['template'];
            $templatePath = dirname(__FILE__) . '/templates/' . $template . '_template.php';

            if (file_exists($templatePath)) {
                include $templatePath;
            } else {
                echo "Template not found.";
            }
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

    //Function to change settings values in database
    public function PSX_save_changes_settings()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce')) {
            wp_send_json_error('Invalid nonce.');
        }

        // Decode the JSON string into an associative array
        $settings_data = json_decode(stripslashes($_POST['settings_data']), true);
        update_option('PSX_gdpr', $settings_data['gdpr']);
        update_option('PSX_response_email', $settings_data['response_email']);

        update_option('PSX_clear_data', $settings_data['clear_data']);
        update_option('PSX_email', $settings_data['email']);
        update_option('PSX_expire_message', $settings_data['expire_message'] != '' ? $settings_data['expire_message'] : "Your survey has expired.");
        update_option('PSX_status_message', $settings_data['status_message'] != '' ? $settings_data['status_message'] : "This survey is expired.");

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
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce')) {
            wp_send_json_error('Invalid nonce.');
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["poll_data"])) {
            $poll_data_array = json_decode(stripslashes($_POST["poll_data"]), true);
            // Extract necessary data from $poll_data_array
            $form_type = $poll_data_array['type'];

            if ($form_type == 'Edit') {

                $poll_id = sanitize_text_field($poll_data_array['poll_id']);
                $table_survey_questions = $wpdb->prefix . "polls_psx_survey_questions";
                $table_survey_answers = $wpdb->prefix . "polls_psx_survey_answers";
                $table_survey_responses = $wpdb->prefix . "polls_psx_survey_responses";
                $table_survey_responses_data = $wpdb->prefix . "polls_psx_survey_responses_data";


                $responses_id = $wpdb->get_results("SELECT response_id FROM {$table_survey_responses} WHERE poll_id = $poll_id", ARRAY_A);

                foreach ($responses_id as $response) {
                    $response_id = $response['response_id'];
                    $wpdb->delete($table_survey_responses_data, array("response_id" => $response_id));
                }
                $wpdb->delete($table_survey_responses, array("poll_id" => $poll_id));

                // Delete from survey answers
                $wpdb->delete($table_survey_answers, array("poll_id" => $poll_id));

                // Delete from survey questions
                $wpdb->delete($table_survey_questions, array("poll_id" => $poll_id));
            }
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
            $real_time_result_text = $settings['real_time_check'] ? '' : sanitize_text_field($settings['real_time_result_text']);
            $min_votes = absint($settings['min_votes']);
            $button_color = sanitize_hex_color($settings['button_color']);
            // Insert data into polls_psx_polls table
            $poll_data_array_insert = array(
                'title' => $surveyTitle,
                'cta_Text' => $cta_Text,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => $status,
                'template' => $template,
                'button_color' => $button_color,
                'Short_Code' => '',
                'color' => $color,
                'bgcolor' => $bgcolor,
                'sharing' => 'false',
                'real_time_result_text' => $real_time_result_text,
                'min_votes' => $min_votes
            );
            if ($form_type == 'Edit') {
                $poll_data_array_insert['poll_id'] = $poll_id;
                $wpdb->update($wpdb->prefix . 'polls_psx_polls', $poll_data_array_insert, array('poll_id' => $poll_id));
            } else {
                $wpdb->insert($wpdb->prefix . 'polls_psx_polls', $poll_data_array_insert);
                $poll_id = $wpdb->insert_id;
            }

            // Generate the shortcode based on title and ID
            $shortcode = 'poll_psx ' . $poll_id;

            // Update the Short_Code field in polls_psx_polls table
            $wpdb->update(
                $wpdb->prefix . 'polls_psx_polls',
                array('Short_Code' => $shortcode),
                array('poll_id' => $poll_id)
            );

            foreach ($pollCards as $pollCard) {
                $question_data = array(
                    'poll_id' => $poll_id,
                    'question_text' => sanitize_text_field($pollCard['question_text']),
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
        $url = admin_url('admin.php?page=poll-survey-xpress-add&template=Multiple+Choice&poll_id=' . $poll_id . '&action=edit');
        echo ($url);
        wp_die();
    }

    //Method to save poll (Rating) data
    public function PSX_save_poll_rating_data()
    {
        global $wpdb;
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce')) {
            wp_send_json_error('Invalid nonce.');
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["poll_data"])) {
            $poll_data_array = json_decode(stripslashes($_POST["poll_data"]), true);
            $form_type = sanitize_text_field($poll_data_array['type']);

            if ($form_type == 'Edit') {
                $poll_id = sanitize_text_field($poll_data_array['poll_id']);
                $table_survey_questions = $wpdb->prefix . "polls_psx_survey_questions";
                $table_survey_answers = $wpdb->prefix . "polls_psx_survey_answers";
                $table_survey_responses = $wpdb->prefix . "polls_psx_survey_responses";
                $table_survey_responses_data = $wpdb->prefix . "polls_psx_survey_responses_data";

                $responses_id = $wpdb->get_results("SELECT response_id FROM $table_survey_responses  WHERE poll_id = $poll_id", ARRAY_A);

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
            }
            // Extract necessary data from $poll_data_array
            $surveyTitle = sanitize_text_field($poll_data_array['surveyTitle']);
            $questions = $poll_data_array['questions'];
            $ratesArray = $poll_data_array['ratesArray'];
            $settings = $poll_data_array['settings'];
            $template = sanitize_text_field($poll_data_array['template']);
            $button_color = sanitize_hex_color($settings['button_color']);
            // Sanitize and validate date inputs
            $start_date = empty($settings['start_date']) ? current_time('mysql') : sanitize_text_field($settings['start_date']);
            $end_date = empty($settings['end_date']) ? date('Y-m-d H:i:s', strtotime('+100 years')) : sanitize_text_field($settings['end_date']);

            // Sanitize other settings
            $cta_Text = sanitize_text_field($settings['cta_Text']);
            $status = $settings['status'] ? 'active' : 'inactive';
            $color = sanitize_hex_color($settings['color']);
            $bgcolor = sanitize_hex_color($settings['bgcolor']);
            $real_time_result_text = $settings['real_time_check'] ? '' : sanitize_text_field($settings['real_time_result_text']);
            $min_votes = absint($settings['min_votes']);

            // Insert data into polls_psx_polls table
            $poll_data_array_insert = array(
                'title' => $surveyTitle,
                'cta_Text' => $cta_Text,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => $status,
                'button_color' => $button_color,
                'template' => $template,
                'Short_Code' => '',
                'color' => $color,
                'bgcolor' => $bgcolor,
                'sharing' => 'false',
                'real_time_result_text' => $real_time_result_text,
                'min_votes' => $min_votes
            );
            if ($form_type == 'Edit') {
                $poll_data_array_insert['poll_id'] = $poll_id;
                $wpdb->update($wpdb->prefix . 'polls_psx_polls', $poll_data_array_insert, array('poll_id' => $poll_id));
            } else {
                $wpdb->insert($wpdb->prefix . 'polls_psx_polls', $poll_data_array_insert);
                $poll_id = $wpdb->insert_id;
            }


            // Generate the shortcode based on title and ID
            $shortcode = 'poll_psx ' . $poll_id;

            // Update the Short_Code field in polls_psx_polls table
            $wpdb->update(
                $wpdb->prefix . 'polls_psx_polls',
                array('Short_Code' => $shortcode),
                array('poll_id' => $poll_id)
            );

            foreach ($questions as $question) {
                $question_data = array(
                    'poll_id' => $poll_id,
                    'question_text' => sanitize_text_field($question['question_text']),
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
        $url = admin_url('admin.php?page=poll-survey-xpress-add&template=Rating&poll_id=' . $poll_id . '&action=edit');
        echo ($url);
        wp_die();
    }


    //Method to save poll (Open Ended) data
    public function PSX_save_poll_open_ended_data()
    {
        global $wpdb;
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce')) {
            wp_send_json_error('Invalid nonce.');
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["poll_data"])) {
            $poll_data_array = json_decode(stripslashes($_POST["poll_data"]), true);
            $form_type = sanitize_text_field($poll_data_array['type']);

            if ($form_type == 'Edit') {
                $poll_id = sanitize_text_field($poll_data_array['poll_id']);
                $table_survey_questions = $wpdb->prefix . "polls_psx_survey_questions";
                $table_survey_answers = $wpdb->prefix . "polls_psx_survey_answers";
                $table_survey_responses = $wpdb->prefix . "polls_psx_survey_responses";
                $table_survey_responses_data = $wpdb->prefix . "polls_psx_survey_responses_data";

                $responses_id = $wpdb->get_results("SELECT response_id FROM $table_survey_responses  WHERE poll_id = $poll_id", ARRAY_A);

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
            }
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
            $real_time_result_text = $settings['real_time_check'] ? '' : sanitize_text_field($settings['real_time_result_text']);
            $min_votes = absint($settings['min_votes']);
            $button_color = sanitize_hex_color($settings['button_color']);

            // Insert data into polls_psx_polls table
            $poll_data_array_insert = array(
                'title' => $surveyTitle,
                'cta_Text' => $cta_Text,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => $status,
                'template' => $template,
                'button_color' => $button_color,
                'Short_Code' => '',
                'color' => $color,
                'bgcolor' => $bgcolor,
                'sharing' => 'false',
                'real_time_result_text' => $real_time_result_text,
                'min_votes' => $min_votes
            );
            if ($form_type == 'Edit') {
                $poll_data_array_insert['poll_id'] = $poll_id;
                $wpdb->update($wpdb->prefix . 'polls_psx_polls', $poll_data_array_insert, array('poll_id' => $poll_id));
            } else {
                $wpdb->insert($wpdb->prefix . 'polls_psx_polls', $poll_data_array_insert);
                $poll_id = $wpdb->insert_id;
            }

            // Generate the shortcode based on title and ID
            $shortcode = 'poll_psx ' . $poll_id;

            // Update the Short_Code field in polls_psx_polls table
            $wpdb->update(
                $wpdb->prefix . 'polls_psx_polls',
                array('Short_Code' => $shortcode),
                array('poll_id' => $poll_id)
            );

            foreach ($questions as $question) {
                $question_data = array(
                    'poll_id' => $poll_id,
                    'question_text' => sanitize_text_field($question['question_text']),
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
        $url = admin_url('admin.php?page=poll-survey-xpress-add&template=Open+ended&poll_id=' . $poll_id . '&action=edit');
        echo ($url);
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
            $table_survey_responses_data = $wpdb->prefix . "polls_psx_survey_responses_data";

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

    // Method to add shortcode form to the frontend of the website
    public function PSX_poll_shortcode_handler($atts)
    {
        global $wpdb;

        $length = count($atts);
        // Sanitize the shortcode attributes
        $atts = array_map('sanitize_text_field', $atts);

        // Extract the poll ID from the shortcode
        $components = explode("_", $atts[0]);
        $poll_id = absint($components[0]);

        // Query the database
        $table_name = $wpdb->prefix . 'polls_psx_polls';
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
        $poll_data = $wpdb->get_results($query, ARRAY_A);

        if ($poll_data) {
            if ($poll_data[0]['status'] === 'active') {
                if ($poll_data[0]['end_date'] < date("Y-m-d", strtotime("+1 day"))) {
                    $output = '<p>' . get_option('PSX_expire_message') . '</p>';
                } else {
                    // Sanitize the template type
                    $template_type = sanitize_text_field($poll_data[0]['template']);

                    $user_id = is_user_logged_in() ? get_current_user_id() : 0;
                    $isUserVoted = false;
                    $table_name = $wpdb->prefix . 'polls_psx_survey_responses';
                    if ($user_id != '0') {

                        $query = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE poll_id = %d AND user_id = %s", $poll_id, $user_id);
                        $votesCount = $wpdb->get_var($query);
                        $isUserVoted = ($votesCount > 0); // Convert the result to a boolean value
                    }

                    // Make a unique fingerprint for the user
                    $userAgent = $_SERVER['HTTP_USER_AGENT'];
                    $ipAddress = $_SERVER['REMOTE_ADDR'];
                    $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
                    $encoding = $_SERVER['HTTP_ACCEPT_ENCODING'];

                    // Concatenate and hash the attributes to create a unique fingerprint
                    $check = sha1($userAgent . $ipAddress . $acceptLanguage . $encoding);

                    $table_name = $wpdb->prefix . 'polls_psx_survey_responses';

                    //check if the session ID is already voted for this poll
                    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d AND session_id = %s", $poll_id, $check);
                    $count = $wpdb->get_var($query);
                    $output = '<div>';

                    // If the count is greater than ), the session ID is found in the table
                    if (!($count > 0 || $isUserVoted)) {
                        $output = '<div>';
                        $output .= '<div class="d-flex flex-column justify-content-center align-items-center gap-3 rounded-3 p-5 col-11 mx-auto modal-content" id="already_vote_message">  
                            <p class="m-0 mb-3" style="font-size: 60px; max-height:60px">✅</p> 
                            <h3 class="m-0 text-dark fw-bolder p-0 text-center">You Can`t Vote another Time</h3>
                            <p class="m-0 text-center" style="font-size: 13px;">You voted before this time</p>
                            </div>
                        ';
                        $output .= '</div>';
                    } elseif ($length > 1) {
                        $output = '<div>';

                        $output = '<div>';

                        if ($template_type === 'Multiple Choice') {

                            $output .= '<button type="button" class="btn btn-primary mx-auto" data-bs-toggle="modal" data-bs-target="#mcq_data">' . $poll_data[0]['cta_Text'] . '</button>';

                            $output .= '<div class="modal fade" id="mcq_data" tabindex="-1" role="dialog" aria-hidden="true">';

                            $output .= '<div class="modal-dialog modal-dialog-centered">';

                            $output .= '<div class="d-none flex-column justify-content-center align-items-center gap-3 rounded-3 p-5 col-11 mx-auto modal-content" id="message">  
                            <p class="m-0 mb-3" style="font-size: 60px; max-height:60px">✅</p> 
                            <h3 class="m-0 text-dark fw-bolder p-0 text-center">' . $poll_data[0]['real_time_result_text'] .  '</h3>
                            <p class="m-0 text-center" style="font-size: 13px;">You have successfully added your votes</p>
                            </div>
                            ';


                            $output .= '<div class="modal-content" style="background-color:' . $poll_data[0]['bgcolor'] . ' !important;" >';
                            $output .= '<div id="mcq_container"  class="modal-body">';

                            $output .= '<input type="hidden" id="my-ajax-nonce" value="' . wp_create_nonce('my_ajax_nonce') . '"/>';
                            // Start generating the poll structure
                            // Fetch questions from the database
                            $table_name = $wpdb->prefix . 'polls_psx_survey_questions';
                            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
                            $questions = $wpdb->get_results($query, ARRAY_A);

                            $output .= '<h4 class="mb-3" id="Title" style="color:' . $poll_data[0]['color'] . ' !important;" data-vote-count="' . $poll_data[0]['min_votes'] . '" data-show-results="' . $poll_data[0]['real_time_result_text'] . '">' . $poll_data[0]['title'] . '</h4>';
                            $output .= '<div class="col">';
                            foreach ($questions as $index => $question) {
                                $output .= '<div id="poll_card" data-card-id="' . $poll_id . '" class="poll-question-container position-relative flex-column gap-2 border border-dark rounded-3 p-4 m-0 mt-3">';


                                // Poll title     
                                $output .= '<h6 class="mb-4" style="color:' . $poll_data[0]['color'] . ' !important;">' . ($index + 1) . ") " . $question['question_text'] . '</h6>';

                                // Fetch answers for each question
                                $table_name = $wpdb->prefix . 'polls_psx_survey_answers';
                                $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d and question_id = %d", $poll_id, $question['question_id']);
                                $answers = $wpdb->get_results($query, ARRAY_A);

                                foreach ($answers as $answer) {
                                    $output .= '<div class="poll-answer position-relative d-flex align-items-center mb-2 py-2 gap-3">';
                                    $output .= '<div class="d-flex align-items-center justify-content-center">';
                                    $output .= '<input data-question-id="' . $question['question_id'] . '" data-answer-id="' . $answer['answer_id'] . '" type="radio" class="poll-answer-radio m-0"  name="poll_answers_' . $question['question_id'] . '" value="' . $answer['answer_id'] . '" id="poll_answer_' . $question['question_id'] . '_' . $answer['answer_id'] . '">';
                                    $output .= '</div>';
                                    $output .= '<label class="m-0" for="poll_answer_' . $question['question_id'] . '_' . $answer['answer_id'] . '" class="m-0" style="color:' . $poll_data[0]['color'] . ' !important;">' . $answer['answer_text'] .  '</label>';

                                    $output .= '<div id="result-container" class="position-absolute d-none align-items-center justify-content-between gap-2 w-100" data-question-id="' . $question['question_id'] . '" data-answer-id="' . $answer['answer_id'] . '">
                                        <div class="progress-bar bg-transparent transition-progress-bar">
                                            <p style="width: 0%;" class="percentage-bar m-0 bg-primary rounded-2"></p>
                                            <p style="width: 100%;   background-color: #DDD;" class="m-0 rounded-2"></p>
                                        </div>
                                        <p style="font-size: 12px; width:50px" class="percentage-value text-primary m-0 fw-bolder"></p>
                                    </div>';

                                    $output .= '</div>';
                                }

                                $output .= '</div>'; // Close the poll structure div
                                $output .= '<div class="spinner-border text-primary d-none" role="status">
                            </div>'; // Close the poll structure div
                            }
                            $output .= '</div>'; // Close the col div

                            $output .= '<button id="mcq_save_button" disabled
                            class="btn align-self-start text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold mb-0 mt-4" style="background-color:' . $poll_data[0]['button_color'] . ' !important;" >
                            Save
                        </button>';
                            $output .= '</div>'; // Close the Modal body
                            $output .= '</div>'; // Close the Modal content
                            $output .= '</div>'; // Close the container div
                            $output .= '</div>'; // Close the modal div

                            // Fetch questions from the database
                        } else if ($poll_data[0]['template'] === 'Open ended') {

                            $output .= '<button type="button" class="btn btn-primary mx-auto" data-bs-toggle="modal" data-bs-target="#open_ended_data">' . $poll_data[0]['cta_Text'] . '</button>';

                            $output .= '<div class="modal fade" id="open_ended_data" tabindex="-1" role="dialog" aria-hidden="true">';

                            $output .= '<div class="modal-dialog modal-dialog-centered">';

                            $output .= '<div id="message" class="d-none flex-column justify-content-center align-items-center gap-3 rounded-3 p-5 col-11 mx-auto modal-content" >  
                                <p class="m-0 mb-3" style="font-size: 60px; max-height:60px">✅</p> 
                                <h3 class="m-0 text-dark fw-bolder p-0 text-center">' . ($poll_data[0]['real_time_result_text'] ? $poll_data[0]['real_time_result_text'] : "Thx for submitting!")
                                .  '</h3>
                                <p class="m-0 text-center" style="font-size: 13px;">You have successfully added your votes</p>
                                </div>
                                ';

                            $output .= '<div class="modal-content" style="background-color:"' . $poll_data[0]['bgcolor'] . '">';
                            $output .= '<div id="open_ended_container"  class="modal-body">';

                            // Start generating the poll structure
                            $table_name = $wpdb->prefix . 'polls_psx_survey_questions';
                            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
                            $questions = $wpdb->get_results($query, ARRAY_A);

                            $output .= '<div class="mt-4 container-fluid bg-transparent">';
                            $output .= '<input type="hidden" id="my-ajax-nonce" value="' . wp_create_nonce('my_ajax_nonce') . '"/>';

                            $output .= '<h4 class="mb-3" style="color:' . $poll_data[0]['color'] . ' !important;" id="Title" data-vote-count="' . $poll_data[0]['min_votes'] . '" data-show-results="' . $poll_data[0]['real_time_result_text'] . '">' . $poll_data[0]['title'] . '</h4>';

                            $output .= '<div class="col">';
                            foreach ($questions as $index => $question) {
                                $output .= '<div id="poll_card" data-card-id="' . $poll_id . '" class="position-relative flex-column gap-2 border border-dark rounded-3 p-4 m-0 mt-3">';
                                $output .= '<h6 class="mb-3" style="color:' . $poll_data[0]['color'] . ' !important;">' . ($index + 1) . ") " . $question['question_text'] . '</h6>';
                                $output .= '<textarea data-question-id="' . $question['question_id'] . '" class="poll-question-textarea form-control mb-2 w-100 border rounded-1" placeholder="Add your answer"></textarea>';
                                $output .= '</div>'; // Close the poll structure div
                            }

                            $output .= '</div>'; // Close the col div

                            $output .= '<button disabled id="open_ended_save_button"
                        class="align-self-start text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold mb-0 mt-4" style="background-color:' . $poll_data[0]['button_color'] . ' !important;">
                        Save
                        </button>';

                            $output .= '</div>'; // Close the Modal body
                            $output .= '</div>'; // Close the Modal content
                            $output .= '</div>'; // Close the container div
                            $output .= '</div>'; // Close the modal div


                        } else if ($poll_data[0]['template'] === 'Rating') {
                            $output .= '<button type="button" class="btn btn-primary mx-auto" data-bs-toggle="modal" data-bs-target="#rating_data">' . $poll_data[0]['cta_Text'] . '</button>';

                            $output .= '<div class="modal fade" id="rating_data" tabindex="-1" role="dialog" aria-hidden="true">
                        ';
                            // Thanking message
                            $output .= '<div class="modal-dialog modal-dialog-centered">';
                            $output .= '<div id="message" class="d-none flex-column justify-content-center align-items-center gap-3 rounded-3 p-5 col-11 mx-auto modal-content">  
                                <p class="m-0 mb-3" style="font-size: 60px; max-height:60px">✅</p> 
                                <h3 class="m-0 text-dark fw-bolder p-0 text-center">' . $poll_data[0]['real_time_result_text'] .  '</h3>
                                <p class="m-0 text-center" style="font-size: 13px;">You have successfully added your votes</p>
                                </div>
                                ';

                            // Show results container    
                            $output .= '<div id="result_chart" style="height: auto; width: 100%;" class="d-none"></div>';

                            $output .= '<div class="modal-content" style="background-color:"' . $poll_data[0]['bgcolor'] . '">';
                            $output .= '<div id="rating_container" class="modal-body">';

                            // Code for the 'Rating' template
                            $table_name = $wpdb->prefix . 'polls_psx_survey_questions';
                            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
                            $questions = $wpdb->get_results($query, ARRAY_A);

                            $output .= '<div class="position-relative w-100 col-12 mt-4 bg-white border">';

                            $output .= '<input type="hidden" id="my-ajax-nonce" value="' . wp_create_nonce('my_ajax_nonce') . '"/>';

                            $output .= '<div style="background-color: #EEE;" class="d-flex justify-content-between align-items-center p-3 ">';

                            $output .= '<h4 class="mb-3" style="color:' . $poll_data[0]['color'] . ' !important;" id="Title" data-vote-count="' . $poll_data[0]['min_votes'] . '" data-show-results="' . $poll_data[0]['real_time_result_text'] . '">' . $poll_data[0]['title'] . '</h4>';

                            $table_name = $wpdb->prefix . 'polls_psx_survey_answers';
                            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
                            $ratings = $wpdb->get_results($query, ARRAY_A);

                            // Start of rating buttons div
                            $output .= '<div class="d-flex justify-content-around align-items-center col-6 gap-2">';
                            $flag = false;
                            $flag_text = $ratings[0]["answer_text"];

                            foreach ($ratings as $rating) {
                                if ($flag_text === $rating["answer_text"] && $flag) {
                                    break;
                                }
                                $output .= '<p class="m-0 text-sm" style="color:' . $poll_data[0]['color'] . ' !important;">' . $rating["answer_text"] . '</p>';
                                $flag = true;
                            }

                            $output .= '</div>';

                            $output .= '</div>'; // Close the Rating space-between div

                            $output .= '<div class="p-4" style="background-color:' . $poll_data[0]['bgcolor'] . ' !important;">';
                            foreach ($questions as $index => $question) {
                                // Start of the rating card
                                $output .= '<div id="poll_card" data-card-id="' . $poll_id . '" class="poll_card d-flex justify-content-between align-items-center mb-4">';
                                $output .= '<h6 class="m-0 text-break" style="color:' . $poll_data[0]['color'] . ' !important;" data-question-id="' . $question['question_id'] . '">' . ($index + 1) . ") " . $question['question_text'] . '</h6>';

                                // Fetch answers for each question
                                $table_name = $wpdb->prefix . 'polls_psx_survey_answers';
                                $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d and question_id = %d", $poll_id, $question['question_id']);
                                $answers = $wpdb->get_results($query, ARRAY_A);

                                $output .= '<div class="d-flex justify-content-around align-items-center col-6 gap-2">'; // Start the answers container

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
                            $output .= '<button disabled type="submit" id="rating_save_button" style="background-color:' . $poll_data[0]['button_color'] . ' !important;"
                        class="text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold m-0 mt-4">
                        Save
                        </button>';

                            $output .= '</div>'; // Close the Modal body
                            $output .= '</div>'; // Close the Modal content
                            $output .= '</div>'; // Close the container div
                            $output .= '</div>'; // Close the modal div
                        }
                        $output .= '</div>';  // Close the body tag

                    } else {
                        $output = '<div>';

                        if ($template_type === 'Multiple Choice') {
                            $output .= '<div id="message" class="d-none flex-column justify-content-center align-items-center gap-3 rounded-3 p-5 col-11 mx-auto modal-content" >  
                            <p class="m-0 mb-3" style="font-size: 60px; max-height:60px">✅</p> 
                            <h3 class="m-0 text-dark fw-bolder p-0 text-center">' . $poll_data[0]['real_time_result_text'] .  '</h3>
                            <p class="m-0 text-center" style="font-size: 13px;">You have successfully added your votes</p>
                            </div>
                            ';

                            $output .= '<div class="mt-4 container-fluid bg-transparent" id="mcq_container">';

                            $output .= '<input type="hidden" id="my-ajax-nonce" value="' . wp_create_nonce('my_ajax_nonce') . '"/>';
                            // Start generating the poll structure
                            // Fetch questions from the database
                            $table_name = $wpdb->prefix . 'polls_psx_survey_questions';
                            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
                            $questions = $wpdb->get_results($query, ARRAY_A);

                            $output .= '<h4 class="mb-3" style="color:' . $poll_data[0]['color'] . ' !important;" id="Title" data-vote-count="' . $poll_data[0]['min_votes'] . '" data-show-results="' . $poll_data[0]['real_time_result_text'] . '">' . $poll_data[0]['title'] . '</h4>';

                            $output .= '<div class="col" style="background-color:' . $poll_data[0]['bgcolor'] . ' !important;">';
                            foreach ($questions as $index => $question) {
                                $output .= '<div id="poll_card" data-card-id="' . $poll_id . '" class="poll-question-container position-relative flex-column gap-2 border border-dark rounded-3 p-4 m-0 mt-3">';

                                // Poll title     
                                $output .= '<h6 class="mb-4" style="color:' . $poll_data[0]['color'] . ' !important;">' . ($index + 1) . ") " . $question['question_text'] . '</h6>';

                                // Fetch answers for each question
                                $table_name = $wpdb->prefix . 'polls_psx_survey_answers';
                                $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d and question_id = %d", $poll_id, $question['question_id']);
                                $answers = $wpdb->get_results($query, ARRAY_A);

                                foreach ($answers as $answer) {
                                    $output .= '<div class="poll-answer position-relative d-flex align-items-center mb-2 py-2 gap-3">';
                                    $output .= '<div class="d-flex align-items-center justify-content-center">';
                                    $output .= '<input data-question-id="' . $question['question_id'] . '" data-answer-id="' . $answer['answer_id'] . '" type="radio" class="poll-answer-radio m-0"  name="poll_answers_' . $question['question_id'] . '" value="' . $answer['answer_id'] . '" id="poll_answer_' . $question['question_id'] . '_' . $answer['answer_id'] . '">';
                                    $output .= '</div>';
                                    $output .= '<label class="m-0" for="poll_answer_' . $question['question_id'] . '_' . $answer['answer_id'] . '" class="m-0" style="color:' . $poll_data[0]['color'] . ' !important;">' . $answer['answer_text'] .  '</label>';

                                    $output .= '<div id="result-container" class="position-absolute d-none align-items-center justify-content-between gap-2 w-100 bottom-0" data-question-id="' . $question['question_id'] . '" data-answer-id="' . $answer['answer_id'] . '">
                                    <div class="progress-bar bg-transparent transition-progress-bar">
                                        <p style="width: 0%;" class="percentage-bar m-0 bg-primary rounded-2"></p>
                                        <p style="width: 100%; background-color: #DDD;" class="m-0 rounded-2"></p>
                                    </div>
                                    <p style="font-size: 12px; width:50px" class="percentage-value text-primary m-0 fw-bolder"></p>
                                </div>';

                                    $output .= '</div>';
                                }

                                $output .= '</div>'; // Close the poll structure div
                                $output .= '<div class="spinner-border text-primary d-none" role="status">
                                </div>'; // Close the poll structure div
                            }
                            $output .= '</div>'; // Close the col div

                            $output .= '<button id="mcq_save_button" disabled
                            class="btn align-self-start text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold mb-0 mt-4" style="background-color:' . $poll_data[0]['button_color'] . ' !important;">
                            Save
                        </button>';
                            $output .= '</div>'; // Close the container-fluid div

                            // Fetch questions from the database
                        } else if ($poll_data[0]['template'] === 'Open ended') {

                            $output .= '<div id="message" class="d-none flex-column justify-content-center align-items-center gap-3 rounded-3 p-5 col-11 mx-auto modal-content" >  
                                <p class="m-0 mb-3" style="font-size: 60px; max-height:60px">✅</p> 
                                <h3 class="m-0 text-dark fw-bolder p-0 text-center">' . ($poll_data[0]['real_time_result_text'] ? $poll_data[0]['real_time_result_text'] : "Thx for submitting!")
                                .  '</h3>
                                <p class="m-0 text-center" style="font-size: 13px;">You have successfully added your votes</p>
                                </div>
                                ';

                            $output .= '<div class="mt-4 container-fluid bg-transparent" id="open_ended_container">';

                            // Start generating the poll structure
                            $table_name = $wpdb->prefix . 'polls_psx_survey_questions';
                            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
                            $questions = $wpdb->get_results($query, ARRAY_A);

                            $output .= '<input type="hidden" id="my-ajax-nonce" value="' . wp_create_nonce('my_ajax_nonce') . '"/>';

                            $output .= '<h4 class="mb-3" style="color:' . $poll_data[0]['color'] . ' !important;" id="Title" data-vote-count="' . $poll_data[0]['min_votes'] . '" data-show-results="' . $poll_data[0]['real_time_result_text'] . '">' . $poll_data[0]['title'] . '</h4>';

                            $output .= '<div class="col" style="background-color:' . $poll_data[0]['bgcolor'] . ' !important;">';
                            foreach ($questions as $index => $question) {
                                $output .= '<div id="poll_card"  data-card-id="' . $poll_id . '" class="position-relative flex-column gap-2 border border-dark rounded-3 p-4 m-0 mt-3">';
                                $output .= '<h6 class="mb-3" style="color:' . $poll_data[0]['color'] . ' !important;">' . ($index + 1) . ") " . $question['question_text'] . '</h6>';
                                $output .= '<textarea data-question-id="' . $question['question_id'] . '" class="poll-question-textarea form-control mb-2 w-100 border rounded-1" placeholder="Add your answer"></textarea>';
                                $output .= '</div>'; // Close the poll structure div
                            }

                            $output .= '</div>'; // Close the col div

                            $output .= '<button disabled id="open_ended_save_button"
                        class="align-self-start text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold mb-0 mt-4" style="background-color:' . $poll_data[0]['button_color'] . ' !important;">
                        Save
                    </button>';
                            $output .= '</div>'; // Close the container-fluid div

                        } else if ($poll_data[0]['template'] === 'Rating') {

                            // Thanking message
                            $output .= '<div id="message" class="d-none flex-column justify-content-center align-items-center gap-3 rounded-3 p-5 col-11 mx-auto modal-content">  
                                    <p class="m-0 mb-3" style="font-size: 60px; max-height:60px">✅</p> 
                                    <h3 class="m-0 text-dark fw-bolder p-0 text-center">' . $poll_data[0]['real_time_result_text'] .  '</h3>
                                    <p class="m-0 text-center" style="font-size: 13px;">You have successfully added your votes</p>
                                    </div>
                                    ';

                            // Results container
                            $output .= '<div id="result_chart" style="height: auto; width: 100%;" class="d-none"></div>';


                            $output .= '<div class="mt-4 container-fluid bg-transparent" id="rating_container">';

                            // Code for the 'Rating' template
                            $table_name = $wpdb->prefix . 'polls_psx_survey_questions';
                            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
                            $questions = $wpdb->get_results($query, ARRAY_A);

                            $output .= '<div class="position-relative w-100 col-12 mt-4 bg-white border">';

                            $output .= '<input type="hidden" id="my-ajax-nonce" value="' . wp_create_nonce('my_ajax_nonce') . '"/>';


                            $output .= '<div style="background-color: #EEE;" class="d-flex justify-content-between align-items-center mb-1 p-4 ">';

                            $output .= '<h4 class="mb-3" style="color:' . $poll_data[0]['color'] . ' !important;" id="Title" data-vote-count="' . $poll_data[0]['min_votes'] . '" data-show-results="' . $poll_data[0]['real_time_result_text'] . '">' . $poll_data[0]['title'] . '</h4>';
                            $table_name = $wpdb->prefix . 'polls_psx_survey_answers';
                            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
                            $ratings = $wpdb->get_results($query, ARRAY_A);

                            // Start of rating buttons div
                            $output .= '<div class="d-flex justify-content-around align-items-center col-6 gap-2">';
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

                            $output .= '<div class="p-4" style="background-color:' . $poll_data[0]['bgcolor'] . ' !important;">';
                            foreach ($questions as $index => $question) {
                                // Start of the rating card
                                $output .= '<div id="poll_card" data-card-id="' . $poll_id . '" class="poll_card d-flex justify-content-between align-items-center mb-4">';
                                $output .= '<h6 class="m-0 text-break" style="color:' . $poll_data[0]['color'] . ' !important;" data-question-id="' . ($index + 1) . ") " . $question['question_id'] . '">' . $question['question_text'] . '</h6>';

                                // Fetch answers for each question
                                $table_name = $wpdb->prefix . 'polls_psx_survey_answers';
                                $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d and question_id = %d", $poll_id, $question['question_id']);
                                $answers = $wpdb->get_results($query, ARRAY_A);

                                $output .= '<div class="d-flex justify-content-around align-items-center col-6 gap-2">'; // Start the answers container

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
                        class="text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold m-0 mt-4" style="background-color:' . $poll_data[0]['button_color'] . ' !important;">
                        Save
                        </button>';
                            $output .= '</div>';
                        }
                        $output .= '</div>';
                        $output .= '</div>';
                    }
                }
            } else {
                $output = '<p>' . get_option('PSX_status_message') . '</p>';
            }
        }
        return $output;
    }

    // Method to update poll settings                                                                            
    public function PSX_update_poll_settings()
    {
        global $wpdb;
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce')) {
            wp_send_json_error('Invalid nonce.');
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["poll_data"])) {
            $poll_data_array = json_decode(stripslashes($_POST["poll_data"]), true);
            // Sanitize inputs
            $poll_id = absint($poll_data_array["poll_id"]);


            $start_date = sanitize_text_field($poll_data_array["start_date"]);
            $end_date = sanitize_text_field($poll_data_array["end_date"]);
            $status = $poll_data_array["status"] ?  true : false;
            $color = sanitize_text_field($poll_data_array["color"]);
            $bgcolor = sanitize_text_field($poll_data_array["bgcolor"]);
            $button_color = sanitize_text_field($poll_data_array["button_color"]);

            $real_time_check = $poll_data_array["real_time_check"];
            $real_time_result_text = !$real_time_check ? sanitize_text_field($poll_data_array["real_time_result_text"]) : '';
            $min_votes = absint($poll_data_array["min_votes"]);
            $cta_text = sanitize_text_field($poll_data_array["cta_Text"]);
            $table_name = $wpdb->prefix . "polls_psx_polls";
            $old_status_result = $wpdb->get_results($wpdb->prepare("SELECT status FROM $table_name WHERE poll_id = %d", $poll_id));
            $title = $wpdb->get_results($wpdb->prepare("SELECT title , template FROM $table_name WHERE poll_id = %d", $poll_id));


            if (get_option('PSX_email') != '') {

                if (!empty($old_status_result)) {
                    $old_status = $old_status_result[0]->status; // Access the 'status' property

                    if ($old_status === 'active' && ($status === false)) {
                        if (get_option('PSX_survey_email') != '') {
                            $to = get_option('PSX_survey_email');
                        } else {
                            $to = get_option('admin_email');
                        }
                    }
                    $site_name = get_bloginfo('name'); // Get the name of your WordPress site
                    $plugin_name = 'Poll Survey Xpress'; // Replace with the actual name of your plugin

                    $current_user = wp_get_current_user();

                    if ($current_user->ID !== 0) {
                        $user_name = $current_user->display_name; // Get the display name of the logged-in user
                    } else {
                        $user_name = 'User'; // Default to 'User' if no user is logged in
                    }

                    $subject = 'Poll Deactivation Notification';
                    $body = 'Dear ' . $user_name . ',

                    This is to inform you that the poll "' . $title[0]->title . '" (ID: ' . $poll_id . ') on ' . $site_name . ' Site has been deactivated.
                    Thank you for using ' . $plugin_name . '.

                    Sincerely,
                    [Poll Survey Xpress Plugin]'; // Replace [Your Name] with your name or the site's administrator name

                    wp_mail($to, $subject, $body);
                }
            }

            $wpdb->update(
                $table_name,
                array(
                    "start_date" => $start_date,
                    "end_date" => $end_date,
                    "status" => $status ? 'active' : 'inactive',
                    "color" => $color,
                    "bgcolor" => $bgcolor,
                    'button_color' => $button_color,
                    "sharing" => 'false',
                    "real_time_result_text" => $real_time_check ? '' : $real_time_result_text,
                    "min_votes" => $min_votes,
                    "cta_Text" => $cta_text
                ),
                array("poll_id" => $poll_id)
            );
        }
    }

    //Method to save poll response
    public function PSX_save_poll_response()
    {
        global $wpdb;
        if (!isset($_POST['nonce'])  || !wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce')) {
            wp_send_json_error('Invalid nonce.');
        }
        if (isset($_POST['poll_response'])) {
            $poll_response = json_decode(stripslashes($_POST['poll_response']), true);
            // Extract data from the poll_response object
            $poll_id = $poll_response['poll_id'];
            $user_id = is_user_logged_in() ? get_current_user_id() : 0;

            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                // Check if multiple IP addresses are provided via proxies
                $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $userIP = $ipList[0];
            } else {
                $userIP = $_SERVER['REMOTE_ADDR'];
            }

            if (!(get_option('PSX_gdpr') === '')) {
                $userIP = '';
            }

            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $encoding = $_SERVER['HTTP_ACCEPT_ENCODING'];

            $session_id = sha1($userAgent . $ipAddress . $acceptLanguage . $encoding);


            $responses = $poll_response['responses'];

            // Insert poll response data into the database
            $response_table = $wpdb->prefix . 'polls_psx_survey_responses';
            $wpdb->insert($response_table, array(
                'poll_id' => $poll_id,
                'ip_address' => $userIP,
                'user_id' => $user_id,
                'session_id' => $session_id,
                'answerd_at' => date('Y-m-d H:i:s'),
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

            if (get_option('PSX_response_email') != '') {
                if (get_option('PSX_survey_email') != '') {
                    $to = get_option('PSX_survey_email');
                } else {
                    $to = get_option('admin_email');
                }
                $site_name = get_bloginfo('name'); // Get the name of your WordPress site
                $plugin_name = 'Poll Survey Xpress'; // Replace with the actual name of your plugin
                $current_user = wp_get_current_user();

                if ($current_user->ID !== 0) {
                    $user_name = $current_user->display_name; // Get the display name of the logged-in user
                } else {
                    $user_name = 'User'; // Default to 'User' if no user is logged in
                }
                $title = $wpdb->get_results($wpdb->prepare("SELECT title,template FROM {$wpdb->prefix}polls_psx_polls WHERE poll_id = %d", $poll_id));
                $subject = 'Poll Response Notification';

                // Modify the template name to replace spaces with '+'
                $template_name = str_replace(' ', '+', $title[0]->template);

                // Generate the link
                $link = admin_url('admin.php?page=poll-survey-xpress-surveys&template=' . $template_name . '&poll_id=' . $poll_id);

                $body = 'Dear ' . $user_name . ',
            
                This is to inform you that the poll "' . $title[0]->title . '" (ID: ' . $poll_id . ') on ' . $site_name . ' Site got a response.
            
                You can view the poll and its responses by clicking on the following link:
                ' . $link . '
            
                Thank you for using ' . $plugin_name . '.' .
                    'Sincerely,
                [Poll Survey Xpress Plugin]'; // Replace [Your Name] with your name or the site's administrator name

                wp_mail($to, $subject, $body);
            }


            $poll_questions = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT question_id FROM {$wpdb->prefix}polls_psx_survey_questions WHERE poll_id = %d",
                    $poll_id
                )
            );
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
                        "SELECT question_id, answer_id  FROM {$wpdb->prefix}polls_psx_survey_responses_data WHERE response_id = %s",
                        $id
                    )
                );
                $responses_data[] = $response_data;
            }
            $answers_for_each_question = array();
            foreach ($poll_questions as $question) {
                //get the answers for each question
                $question_id = $question->question_id;
                $question_answers = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT answer_id FROM {$wpdb->prefix}polls_psx_survey_answers WHERE question_id = %d",
                        $question_id
                    )
                );
                $answers_for_each_question[$question_id] = $question_answers;
            }
            $allAnswerChoices = array();
            foreach ($poll_questions as $question) {
                $questionId = $question->question_id;
                $questionAnswers = $answers_for_each_question[$questionId];
                $allAnswerChoices[$questionId] = array_column($questionAnswers, 'answer_id');
            }
            $chosenAnswerChoices = array();
            foreach ($responses_data as $response) {
                foreach ($response as $answer) {
                    $questionId = $answer->question_id;
                    $answerId = $answer->answer_id;
                    $chosenAnswerChoices[$questionId][$answerId] = true;
                }
            }

            $flag = false;
            $all_answers = array();
            foreach ($allAnswerChoices as $answer) {
                foreach ($answer as $answer_text) {
                    $question_answers = $wpdb->get_results(
                        $wpdb->prepare(
                            "SELECT answer_text FROM {$wpdb->prefix}polls_psx_survey_answers WHERE answer_id = %d",
                            $answer_text
                        )
                    );
                    $all_answers[] = $question_answers;
                    $flag = true;
                }
                if ($flag) {
                    break;
                }
            }


            $answerCounts = array();
            $answeredQuestions = array();
            foreach ($responses_data as $response) {
                foreach ($response as $answer) {
                    $questionId = $answer->question_id;
                    $answerId = $answer->answer_id;

                    if (!isset($answerCounts[$questionId])) {
                        $answerCounts[$questionId] = array();
                        $answeredQuestions[$questionId] = array(); // Keep track of answered questions
                    }

                    if (!isset($answerCounts[$questionId][$answerId])) {
                        $answerCounts[$questionId][$answerId] = 0;
                        $answeredQuestions[$questionId][] = $answerId;
                    }

                    $answerCounts[$questionId][$answerId]++;
                }
            }

            $questions = array();
            foreach ($answerCounts as $questionId => $answerData) {
                if (!isset($questions[$questionId])) {
                    $questions[$questionId] = array();
                }

                $answeredAnswers = $answeredQuestions[$questionId]; // Maintain order of creation
                foreach ($answeredAnswers as $answerId) {
                    if (!isset($questions[$questionId][$answerId])) {
                        $questions[$questionId][$answerId] = 0;
                    }

                    $questions[$questionId][$answerId] += $answerCounts[$questionId][$answerId];
                }
            }

            // Calculate the total response count
            $totalResponses = count($responses_data);

            // Calculate percentages including unanswered questions
            $percentages = array();
            foreach ($allAnswerChoices as $questionId => $answerChoices) {
                $totalAnswers = count($answerChoices);
                $percentages[$questionId] = array();

                foreach ($answerChoices as $answerId) {
                    if (isset($chosenAnswerChoices[$questionId][$answerId])) {
                        $count = isset($answerCounts[$questionId][$answerId]) ? $answerCounts[$questionId][$answerId] : 0;
                    } else {
                        $count = 0;
                    }

                    $percentage = ($totalResponses > 0) ? (($count / $totalResponses) * 100) : 0;
                    $formattedPercentage = number_format($percentage, 2);
                    $percentages[$questionId][$answerId] = $formattedPercentage;
                }
            }

            // Calculate the total percentage for each question
            $totalPercentages = array();
            foreach ($percentages as $questionId => $answerData) {
                $totalPercentage = array_sum($answerData);
                $totalPercentages[$questionId] = number_format($totalPercentage, 2);
            }


            $table_name = $wpdb->prefix . 'polls_psx_survey_responses'; // Replace 'prefix' with your database table prefix
            $query = $wpdb->prepare("SELECT count(*) FROM $table_name WHERE poll_id = %s", $poll_id);
            $votes = $wpdb->get_var($query);

            // If the count is greater than 0, the session ID is found in the table

            $jsonResponse = '{"percentages":' . json_encode($percentages) . ',"min_votes":' . json_encode($votes) . ',  "answers":' . json_encode($all_answers) . '}';

            echo json_encode($jsonResponse);
        }
        wp_die();
    }

    // Method to delete a poll response for a given poll
    public function PSX_delete_poll_response()
    {
        global $wpdb;
        $poll_id = intval($_POST['poll_id']); // Use intval to ensure it's treated as an integer


        $table_survey_responses = $wpdb->prefix . "polls_psx_survey_responses";
        $table_survey_responses_data = $wpdb->prefix . "polls_psx_survey_responses_data";

        $responses_id = $wpdb->get_results("SELECT response_id FROM $table_survey_responses  WHERE poll_id = $poll_id", ARRAY_A);

        foreach ($responses_id as $response) {
            $response_id = $response['response_id'];
            $wpdb->delete($table_survey_responses_data, array("response_id" => $response_id));
        }

        // Delete from survey responses
        $wpdb->delete($table_survey_responses, array("poll_id" => $poll_id));

        wp_die();
    }
}
$survey_plugin = new PollSurveyXpress();
