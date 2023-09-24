<?php
global $wpdb;
$isItEditPage = false;
if (isset($_GET['action']) && ($_GET['action'] == 'edit')) {
    $isItEditPage = true;
    $poll_id = $_GET['poll_id']; // Get the poll ID from the URL parameter

    // Query to fetch poll data
    $query = $wpdb->prepare("
                SELECT * FROM {$wpdb->prefix}polls_psx_polls
                WHERE poll_id = %d
            ", $poll_id);
    $poll_data = $wpdb->get_results($query);
    if (!$poll_data) {
        echo "Poll not found";
    }

    $questions_query = $wpdb->prepare("
                SELECT * FROM {$wpdb->prefix}polls_psx_survey_questions
                WHERE poll_id = %d
            ", $poll_id);

    $questions = $wpdb->get_results($questions_query);


    $questions_with_answers = array();

    foreach ($questions as &$question) {
        $answers_query = $wpdb->prepare("
                SELECT * FROM {$wpdb->prefix}polls_psx_survey_answers
                WHERE question_id = %d and poll_id = %d
                ", $question->question_id, $poll_id);

        $question->answers = $wpdb->get_results($answers_query);
        $questions_with_answers[] = $question;
    }
    $poll_data_json = json_encode($poll_data);
    $questions_json = json_encode($questions);
    $jsonDataEncoded = htmlspecialchars($questions_json, ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Open-ended Template</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</head>


<body>
    <main class="col-lg-6 col-md-8 col-10 mx-auto main-content position-relative max-height-vh-100 h-100 mt-4 border-radius-lg">
        <div class="d-flex align-items-center gap-2 my-4">
            <a href="<?php echo admin_url('admin.php?page=poll-survey-xpress-surveys'); ?>" class="m-0 text-dark"><?php _e('Home', 'psx-poll-survey-plugin'); ?></a>
            <i class="fas fa-angle-right"></i>
            <?php if ($isItEditPage) { ?>
                <a href="<?php echo admin_url('admin.php?page=poll-survey-xpress-surveys'); ?>" class="m-0 text-dark"><?php _e('Surveys', 'psx-poll-survey-plugin'); ?></a>
                <i class="fas fa-angle-right"></i>
                <h6 class="font-weight-bolder mb-0 p-0 "><?php _e('Open Ended Survey Edit', 'psx-poll-survey-plugin'); ?></h6>
            <?php } else { ?>
                <a href="<?php echo admin_url('admin.php?page=poll-survey-xpress-add'); ?>" class="m-0 text-dark"><?php _e('Templates', 'psx-poll-survey-plugin'); ?></a>
                <i class="fas fa-angle-right"></i>
                <h6 class="font-weight-bolder mb-0 p-0 "><?php _e('Open Ended Survey Add', 'psx-poll-survey-plugin'); ?></h6>
            <?php } ?>
        </div>



        <div class="d-flex flex-column align-items-start my-3 p-4 rounded-3 border bg-white">
            <input data-json-data="<?php echo ($isItEditPage ? $jsonDataEncoded : '') ?>" type="text" class="w-100 border text-lg rounded-1 p-1 rounded-1 bg-white mb-3" placeholder="Poll/Survey title" id="surveyTitle" value="<?php echo ($isItEditPage ? $poll_data[0]->title : '') ?>" data-type="<?php echo ($isItEditPage ? "Edit" : "Add"); ?>" data-form-id="<?php echo ($isItEditPage ? $poll_id : null); ?>" />
            <input type="hidden" id="my-ajax-nonce" value="<?php echo wp_create_nonce('my_ajax_nonce'); ?>" />

            <div class="d-flex w-100 flex-column gap-2 rounded-3 bg-white mt-2 mb-2">
                <div class="mb-2">
                    <label for="surveyTitle" class="form-label"><?php _e('Add new question', 'psx-poll-survey-plugin'); ?></label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control p-2 border rounded-1" id="questionInput" placeholder="Question title" />
                        <button id="addQuestion" class="text-primary p-0 border btn text-sm font-weight-bold mb-0 shadow-none d-flex justify-content-center align-items-center p-3 rounded-1">
                            <i class="fas fa-plus text-sm p-0"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div id="questionsGroup" class="flex flex-column gap-2 w-100 mt-2">
                <?php
                if ($isItEditPage) {
                    foreach (json_decode($questions_json) as $index => $question) {
                ?>
                        <div data-card-id="<?php echo $question->question_id ?>" class="question-container d-flex align-items-center w-100 gap-3 mb-3">
                            <i data-card-id="<?php echo $question->question_id ?>" style="cursor: pointer" class="fas fa-minus text-danger"></i>
                            <textarea class="form-control" id="questionTitle_<?php echo $index; ?>" placeholder="Edit question title"><?php echo $question->question_text ?></textarea>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>

        <button disabled id="save_button" type="button" class="align-self-start text-white btn bg-primary col-lg-3 col-md-4 col-4 text-sm font-weight-bold mb-0 mb-5 mt-2">
            <?php _e('Save', 'psx-poll-survey-plugin'); ?>
        </button>

    </main>

    <!-- Fixed plugin settings ICON -->
    <div title="Edit survey settings" id="settings_icon" data-bs-toggle="modal" data-bs-target="#settingsModal" style="cursor: pointer" class="position-fixed bottom-4 end-2 px-3 py-2 bg-primary shadow-sm rounded-circle text-white border">
        <i class="fa fa-cog py-2"> </i>
    </div>


    <!-- Fixed Plugin settings -->
    <div class="modal fade" id="settingsModal">
        <div class="modal-dialog modal-dialog-centered rounded-3">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title"> <?php _e('Survey Settings', 'psx-poll-survey-plugin'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div onsubmit="(e) => e.preventDefault()" class="modal-body card rounded">
                    <input type="hidden" id="my-ajax-nonce" value="<?php echo wp_create_nonce('my_ajax_nonce'); ?>" />
                    <div>
                        <label><?php _e('Change plugin Theme', 'psx-poll-survey-plugin'); ?></label>

                        <div class="d-flex align-items-center px-2 gap-2">
                            <span class="text-sm fw-bold"><?php _e('Bg color', 'psx-poll-survey-plugin'); ?> </span>
                            <input type="color" class="form-control form-control-color border-0 p-0 w-10 me-2" id="bg_color" value="<?php echo $isItEditPage ? $poll_data[0]->bgcolor : "#f8f9fa"; ?>" />
                            <span class="text-sm fw-bold"><?php _e('Text color', 'psx-poll-survey-plugin'); ?> </span>
                            <input type="color" class="form-control form-control-color border-0 p-0 w-10 me-2" id="text_color" value="<?php echo $isItEditPage ? $poll_data[0]->color : "#344767"; ?>" />
                            <span class="text-sm fw-bold"><?php _e('Button color', 'psx-poll-survey-plugin'); ?> </span>
                            <input type="color" class="form-control form-control-color border-0 p-0 w-10 me-2" id="button_color" value="<?php echo $isItEditPage ? $poll_data[0]->button_color : "#cb0c9f"; ?>" />
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 mt-2 mb-2 px-2">
                        <div>
                            <label class="m-0"><?php _e('Starts', 'psx-poll-survey-plugin'); ?></label>
                            <input type="datetime-local" class="form-control" id="start_date" placeholder="Select a date" value="<?php echo $isItEditPage ? $poll_data[0]->start_date : date('Y-m-d\TH:i'); ?>" />
                        </div>

                        <div>
                            <label class="m-0" <?php _e('Ends', 'psx-poll-survey-plugin'); ?>></label>

                            <input type="datetime-local" class="form-control" id="end_date" placeholder="Select a date" value="<?php echo $isItEditPage ? $poll_data[0]->end_date : date('Y-m-d\TH:i', strtotime('+1 year')); ?>" />
                        </div>
                    </div>

                    <div class="card-body pt-sm-3 p-0">
                        <div class="form-group d-flex flex-column">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="active_plugin" <?php echo ($isItEditPage ? $poll_data[0]->status === 'active' ? 'checked' : '' : ''); ?> />
                                <label class="form-check-label" for="active_plugin">
                                    <?php _e('Activate the survey', 'psx-poll-survey-plugin'); ?>
                                </label>
                            </div>

                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="show_results" <?php echo ($isItEditPage ? empty($poll_data[0]->real_time_result_text) ? "checked" : "" : ''); ?> onchange="toggleInputState()" />
                                    <label class="form-check-label" for="show_results">
                                        <?php _e('Show real-time results', 'psx-poll-survey-plugin'); ?>
                                    </label>
                                </div>
                                <input type="text" class="form-control border rounded-1 p-1 mt-2" placeholder="Add Thank Meesage" value="<?php echo ($isItEditPage ? $poll_data[0]->real_time_result_text : ''); ?>" id="show_results_input" <?php echo ($isItEditPage ? !empty($poll_data->real_time_result_text) ? 'disabled' : '' : ''); ?> />
                            </div>

                            <div class="d-flex align-items-center justify-content-start gap-2 mt-3">
                                <label class="form-check-label w-45">
                                    <?php _e('Show results after', 'psx-poll-survey-plugin'); ?>
                                </label>
                                <input type="number" class="form-control border rounded-1 p-1 w-55" placeholder="Number of votes" id="min_votes_input" value="<?php echo ($isItEditPage ? $poll_data[0]->min_votes : ''); ?>" />
                            </div>

                            <div class="w-100 d-flex flex-column align-items-start mt-2 gap-2">
                                <input type="text" class="form-control border rounded-1 p-1" placeholder="Add CTA button title" id="cta_input" value="<?php echo ($isItEditPage ? $poll_data[0]->cta_Text  : ''); ?>" />
                                <button onclick="(e)=> e.preventDefault();" id="cta_button" type="button" class="btn btn-dark m-0 mt-1">
                                    <?php echo ($isItEditPage ? ($poll_data[0]->cta_Text != '' ? $poll_data[0]->cta_Text : "Do The Survey Now!") : ''); ?>
                                </button>
                                <p class="m-0 mb-2" style="font-size:10px"><?php _e('(This button is a preview for a cta button in the modal view)', 'psx-poll-survey-plugin'); ?> </p>
                            </div>
                        </div>
                    </div>
                    <button id="save_settings_button" onclick="(e)=> e.preventDefault();" class="btn btn-primary w-100" data-bs-dismiss="modal"><?php _e('Continue to save', 'psx-poll-survey-plugin'); ?></button>
                </div>
            </div>
        </div>
    </div>


    <script>
        const save_button = document.getElementById("save_button");
        const pullTitle = document.getElementById("surveyTitle");
        const addQuestionButton = document.getElementById("addQuestion");
        const questionInput = document.getElementById("questionInput");
        const questionsGroup = document.getElementById("questionsGroup");
        const questionsArray = [];
        let counter = 1;
        let finalObj = {};

        // Plugin Settings variables
        const bg_color = document.getElementById("bg_color");
        const text_color = document.getElementById("text_color");
        const start_date = document.getElementById("start_date");
        const end_date = document.getElementById("end_date");
        const active_plugin = document.getElementById("active_plugin");
        const show_results = document.getElementById("show_results");
        const show_results_input = document.getElementById("show_results_input");
        const min_votes_input = document.getElementById("min_votes_input");
        const cta_input = document.getElementById("cta_input");
        const button_color = document.getElementById("button_color");
        var nonce = jQuery('#my-ajax-nonce').val();

        const save_settings_button = document.getElementById("save_settings_button")

        save_settings_button.addEventListener("click", () => {
            save_button.scrollIntoView({
                behavior: "smooth"
            })
            save_button.classList.add("pulse")
        })


        // Update save_button status
        function updateButtonStates() {
            if (questionsGroup.childElementCount <= 0) {
                save_button.disabled = true
            } else {
                save_button.disabled = false
            }
        }

        // Event listener for removing a poll question
        document.addEventListener("click", function(event) {
            if (event.target.classList.contains("fa-minus")) {
                const cardId = event.target.getAttribute("data-card-id");

                const card = document.querySelector(`[data-card-id="${cardId}"]`);
                if (card) {
                    card.remove();
                    updateButtonStates();
                }
            }
        });


        function addQuestion() {
            const questionTitle = questionInput.value.trim();
            if (questionTitle !== "") {
                questionInput.value = "";

                // Create the new HTML element
                const newQuestionDiv = document.createElement("div");
                newQuestionDiv.className =
                    "question-container d-flex justify-content-between align-items-center w-100 mb-3 gap-3";
                const UID = new Date().toISOString();

                newQuestionDiv.setAttribute("data-card-id", UID);

                newQuestionDiv.innerHTML = `
                <i data-card-id="${UID}" style="cursor: pointer"class="fas fa-minus text-danger"></i>
                <textarea
                  class="form-control"
                  id="questionTitle_${counter}"
                  placeholder="Edit question title"
                  value="${questionTitle}"
                >${questionTitle}</textarea>
          `;

                questionsGroup.appendChild(newQuestionDiv);
                counter++;
                updateButtonStates();
            }
        }

        updateButtonStates()

        // Add option on button click
        addQuestionButton.addEventListener("click", addQuestion);

        // Add option on Enter key press in questionInput
        questionInput.addEventListener("keydown", function(event) {
            if (event.keyCode === 13) {
                addQuestion();
            }
        });

        let flag = false;
        const settings_icon = document.getElementById("settings_icon");

        save_button.addEventListener("click", () => {
            // Set the questions fields
            const pollCards = document.querySelectorAll(".question-container");
            const data = {
                pollCards: []
            };

            pollCards.forEach((card) => {
                const questionText = card.querySelector("textarea").value;
                data.pollCards.push({
                    question_text: questionText,
                });
            });

            const textFields = document.querySelectorAll(".question-container textarea")

            // Initialize a flag to track if any empty field is found
            let isEmptyField = false;

            textFields.forEach((input, index) => {
                if (input.value == "") {
                    input.style.cssText = "border: 1px solid red !important";
                    isEmptyField = true;
                } else {
                    input.style.border = "none"; // Remove the red border if the field is not empty
                }
            });

            if (pullTitle.value.trim() == "") {
                pullTitle.style.cssText = "border: 1px solid red !important";
                pullTitle.scrollIntoView({
                    behavior: "smooth"
                });
                return;
            } else {
                pullTitle.style.border = "none"; // Remove the red border if the field is not empty
            }

            if (isEmptyField) {
                // If any empty field is found, scroll to the first empty field
                textFields.forEach((input, index) => {
                    if (input.value == "") {
                        input.scrollIntoView({
                            behavior: "smooth"
                        });
                        return; // Exit the loop after scrolling to the first empty field
                    }
                });
            } else {

                if (!localStorage.getItem("isFreshUser")) {
                    const popoverContent = document.createElement("div");
                    popoverContent.style.cssText = "bottom:20px; right:20px";
                    popoverContent.innerHTML = "Click here to edit the survey settings";


                    const popoverInstance = new bootstrap.Popover(settings_icon, {
                        content: popoverContent,
                        trigger: "focus",
                        html: true, // Enable HTML content in the popover
                    });

                    settings_icon.classList.add("shake");
                    if (!flag) {
                        popoverInstance.show(); // Show the popover
                        localStorage.setItem("isFreshUser", true);

                    }
                    flag = true

                    settings_icon.addEventListener("click", () => {
                        settings_icon.classList.remove("shake");
                        localStorage.setItem("isFreshUser", false);
                        popoverInstance.hide(); // Show the popover
                    })
                } else {
                    save_button.disabled = true;
                    save_button.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    settingObj = {
                        cta_Text: cta_input.value,
                        start_date: start_date.value,
                        end_date: end_date.value,
                        status: active_plugin.checked,
                        color: text_color.value,
                        bgcolor: bg_color.value,
                        real_time_result_text: show_results_input.value,
                        min_votes: min_votes_input.value,
                        button_color: button_color.value,
                    };

                    finalObj = {
                        surveyTitle: pullTitle.value,
                        questions: data.pollCards,
                        settings: settingObj,
                        template: "Open Ended",
                        type: pullTitle.getAttribute("data-type"),
                        poll_id: pullTitle.getAttribute("data-form-id") != null ? pullTitle.getAttribute("data-form-id") : null,

                    };

                    jQuery.ajax({
                        type: "POST",
                        url: my_ajax_object.ajaxurl,
                        data: {
                            action: "PSX_save_poll_open_ended_data",
                            poll_data: JSON.stringify(finalObj),
                            nonce: nonce,
                        },
                        success: function(url) {
                            save_button.textContent = "Save";
                            save_button.disabled = false;

                            // Create a new toast element
                            var toast = document.createElement("div");
                            toast.style = "z-index:1000; right: 10px; bottom: 10px";
                            toast.className = "position-fixed p-2 px-4 bg-success border rounded-2";
                            toast.innerHTML = `
                            <p class="m-0 fw-bold text-xs text-white">
                            New survey has been added successfully!
                            </p>
                        `;
                            // Append the toast to the document
                            document.body.appendChild(toast);

                            // Initialize the Bootstrap toast with custom options
                            var bootstrapToast = new bootstrap.Toast(toast, {
                                autohide: true, // Set to true to enable automatic hiding
                                delay: 2000,
                            });
                            bootstrapToast.show();

                            setTimeout(() => {
                                window.location.href = url;
                            }, 500)

                        },
                        error: function(error) {
                            console.error("Error:", error);
                            save_button.textContent = "Save";
                            save_button.disabled = false;
                        },
                    });
                }
            }
        })
    </script>

    <!-- CTA button preview -->
    <script>
        const ctaInput = document.getElementById("cta_input");
        const ctaButton = document.getElementById("cta_button");

        ctaInput.addEventListener("keyup", () => {
            if (ctaInput.value == "") {
                ctaButton.innerText = "Do The Survey Now!";
            } else {
                ctaButton.innerText = ctaInput.value;
            }
        });
    </script>
</body>

</html>