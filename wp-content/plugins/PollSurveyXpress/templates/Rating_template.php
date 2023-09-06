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
    $questions_with_answers_json = json_encode($questions_with_answers);
    $jsonDataEncoded = htmlspecialchars($questions_with_answers_json, ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="en">


<head>
    <title>Rating Template</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</head>


<body>
    <main class="col-lg-8 col-md-9 col-10 mx-auto main-content position-relative max-height-vh-100 h-100 mt-4 border-radius-lg">

        <div class="d-flex align-items-center gap-2 my-4">
            <a href="<?php echo admin_url('admin.php?page=poll-survey-xpress-surveys'); ?>" class="m-0 text-dark"><?php _e('Home', 'psx-poll-survey-plugin'); ?></a>
            <i class="fas fa-angle-right"></i>
            <?php if ($isItEditPage) { ?>
                <a href="<?php echo admin_url('admin.php?page=poll-survey-xpress-surveys'); ?>" class="m-0 text-dark"><?php _e('Surveys', 'psx-poll-survey-plugin'); ?></a>
                <i class="fas fa-angle-right"></i>
                <h6 class="font-weight-bolder mb-0 p-0 "><?php _e('Rating Survey Edit', 'psx-poll-survey-plugin'); ?></h6>
            <?php } else { ?>
                <a href="<?php echo admin_url('admin.php?page=poll-survey-xpress-add'); ?>" class="m-0 text-dark"><?php _e('Templates', 'psx-poll-survey-plugin'); ?></a>
                <i class="fas fa-angle-right"></i>
                <h6 class="font-weight-bolder mb-0 p-0 "><?php _e('Rating Survey Add', 'psx-poll-survey-plugin'); ?></h6>
            <?php } ?>
        </div>

        <!-- Final output Survey -->
        <div class="d-flex flex-column align-items-start my-3 p-4 rounded-3 border bg-white">
            <div class="d-flex justify-content-between align-items-center w-100 mb-4">
                <input data-json-data="<?php echo $jsonDataEncoded ?>" type="text" class="w-100 border text-lg rounded-1 p-1 rounded-1 bg-white" placeholder="Poll/Survey title" id="surveyTitle" value="<?php echo $poll_data[0]->title ?>" data-type="<?php echo ($isItEditPage ? "Edit" : "Add"); ?>" data-form-id="<?php echo ($isItEditPage ? $poll_id : null); ?>" />
                <div id="rateInputs" class="form-check d-flex justify-content-around align-items-center col-8 gap-2">

                    <?php if (!$isItEditPage) { ?>
                        <input type="text" id="rateInput1" class="w-100 text-lgs p-2 px-0 bg-white border-0 rounded-1" placeholder="Rate #1" value="Rate #1" />
                        <input type="text" id="rateInput2" class="w-100 text-lgs p-2 px-0 bg-white border-0 rounded-1" placeholder="Rate #2" value="Rate #2" />
                        <input type="text" id="rateInput3" class="w-100 text-lgs p-2 px-0 bg-white border-0 rounded-1" placeholder="Rate #3" value="Rate #3" />
                        <input type="text" id="rateInput4" class="w-100 text-lgs p-2 px-0 bg-white border-0 rounded-1" placeholder="Rate #4" value="Rate #4" />
                        <input type="text" id="rateInput5" class="w-100 text-lgs p-2 px-0 bg-white border-0 rounded-1" placeholder="Rate #5" value="Rate #5" />

                    <?php } else { ?>
                        <?php
                        $table_name = $wpdb->prefix . 'polls_psx_survey_answers';
                        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE poll_id = %d", $poll_id);
                        $ratings = $wpdb->get_results($query, ARRAY_A);

                        $flag = false;
                        $flag_text = $ratings[0]["answer_text"];

                        foreach ($ratings as $index => $rating) {
                            if ($flag_text === $rating["answer_text"] && $flag) {
                                break;
                            }
                        ?>
                            <input type="text" id="rateInput2_<?php echo $index; ?>" class="w-100 text-lgs p-2 px-0 bg-white border-0 rounded-1" placeholder="Rate_<?php echo $index + 1; ?>" value="<?php echo $rating["answer_text"]; ?>" />
                        <?php
                            $flag = true;
                        }
                        ?>
                    <?php } ?>
                </div>
            </div>

            <div class="d-flex w-100 flex-column gap-2 rounded-3 bg-white mb-4">
                <div class="mb-2">
                    <label for="surveyTitle" class="form-label"><?php _e('Add new question', 'psx-poll-survey-plugin'); ?></label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control p-2 border rounded-1" id="questionInput" placeholder="Question title" />
                        <button id="addQuestion" class="text-primary p-0 border btn text-sm font-weight-bold mb-0 shadow-none d-flex justify-content-center align-items-center p-3 rounded-1">
                            <i class="fas fa-plus text-sm p-0" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>


            <div id="questionsGroup" class="flex flex-column gap-2 w-100">

                <?php
                if ($isItEditPage) {

                    // Check if decoding was successful
                    if ($questions_with_answers !== null) {
                        foreach ($questions_with_answers as $index => $question) {
                ?>
                            <div class="poll-card d-flex justify-content-between align-items-center w-100 mb-3" data-card-id="<?php echo $question->question_id ?>">
                                <div class="question-container d-flex align-items-center w-100 gap-3">
                                    <i data-card-id="<?php echo $question->question_id ?>" style="cursor: pointer" class="fas fa-minus text-danger" aria-hidden="true"></i>
                                    <input type="text" class="question-text form-control border p-2" placeholder="Edit question title" value="<?php echo $question->question_text; ?>">
                                </div>

                                <div class="form-check d-flex justify-content-around align-items-center col-8">
                                    <input class="form-check-input border" type="radio" name="radioGroup_5" id="radio1">
                                    <input class="form-check-input border" type="radio" name="radioGroup_5" id="radio2">
                                    <input class="form-check-input border" type="radio" name="radioGroup_5" id="radio3">
                                    <input class="form-check-input border" type="radio" name="radioGroup_5" id="radio4">
                                    <input class="form-check-input border" type="radio" name="radioGroup_5" id="radio5">
                                </div>
                            </div>
                <?php
                        }
                    } else {
                        echo "Error decoding JSON.";
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
                                <input class="form-check-input" type="checkbox" id="active_plugin" <?php echo $poll_data[0]->status === 'active' ? 'checked' : ''; ?> />
                                <label class="form-check-label" for="active_plugin">
                                    <?php _e('Activate the survey', 'psx-poll-survey-plugin'); ?>
                                </label>
                            </div>

                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="show_results" <?php echo empty($poll_data[0]->real_time_result_text) ? "checked" : ""; ?> onchange="toggleInputState()" />
                                    <label class="form-check-label" for="show_results">
                                        <?php _e('Show real-time results', 'psx-poll-survey-plugin'); ?>
                                    </label>
                                </div>
                                <input type="text" class="form-control border rounded-1 p-1 mt-2" placeholder="Add Thank Meesage" value="<?php echo $poll_data[0]->real_time_result_text; ?>" id="show_results_input" <?php echo !empty($poll_data->real_time_result_text) ? 'disabled' : ''; ?> />
                            </div>

                            <div class="d-flex align-items-center justify-content-start gap-2 mt-3">
                                <label class="form-check-label w-45">
                                    <?php _e('Show results after', 'psx-poll-survey-plugin'); ?>
                                </label>
                                <input type="number" class="form-control border rounded-1 p-1 w-55" placeholder="Number of votes" id="min_votes_input" value="<?php echo $poll_data[0]->min_votes; ?>" />
                            </div>

                            <div class="w-100 d-flex flex-column align-items-start mt-2 gap-2">
                                <input type="text" class="form-control border rounded-1 p-1" placeholder="Add CTA button title" id="cta_input" value="<?php echo $poll_data[0]->cta_Text; ?>" />
                                <button onclick="(e)=> e.preventDefault();" id="cta_button" type="button" class="btn btn-dark m-0 mt-1">
                                    <?php echo ($poll_data[0]->cta_Text != '' ? $poll_data[0]->cta_Text : "Do The Survey Now!"); ?>
                                </button>
                                <p class="m-0 mb-2" style="font-size:10px"><?php _e('(This button is a preview for a cta button in the modal view)', 'psx-poll-survey-plugin'); ?> </p>
                            </div>
                        </div>
                    </div>
                    <button id="save_settings_button" onclick="(e)=> e.preventDefault();" class="btn btn-primary w-100" data-bs-dismiss="modal"><?php _e('SAVE', 'psx-poll-survey-plugin'); ?></button>
                </div>
            </div>
        </div>
    </div>

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

    <script>
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

        const save_button = document.getElementById("save_button");
        const pullTitle = document.getElementById("surveyTitle");
        const addOptionButton = document.getElementById("addQuestion");
        const optionInput = document.getElementById("questionInput");
        const questionsGroup = document.getElementById("questionsGroup");
        const button_color = document.getElementById("button_color");

        let questionsArray = [];
        let counter = 1;
        let finalObj = {};

        // const cards_array = JSON.parse(surveyTitle.getAttribute("data-json-data"));

        // jQuery(document).ready(function(jQuery) {
        //     console.log(cards_array);
        // })


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
        var nonce = jQuery('#my-ajax-nonce').val();

        const save_settings_button = document.getElementById("save_settings_button")

        save_settings_button.addEventListener("click", () => {
            save_button.scrollIntoView({
                behavior: "smooth"
            })
            save_button.classList.add("pulse")
        })

        // Rates data
        const rateInputs = document.querySelectorAll("#rateInputs input");
        let ratesArray = [];

        rateInputs.forEach((input, index) => {
            ratesArray[index] = input.value;
            input.addEventListener("input", (event) => {
                const inputValue = event.target.value;
                ratesArray[index] = inputValue;
            });
        });

        function addOption() {
            const questionTitle = optionInput.value.trim();
            if (questionTitle !== "") {
                questionsArray.push({
                    questionTitle: questionTitle,
                    id: counter,
                    answers: ratesArray
                });
                optionInput.value = "";

                // Create the new HTML element
                const newQuestionDiv = document.createElement("div");
                newQuestionDiv.className =
                    "poll-card d-flex justify-content-between align-items-center w-100 mb-3";
                const UID = new Date().toISOString();
                newQuestionDiv.setAttribute("data-card-id", UID);

                newQuestionDiv.innerHTML = `
                <div class="question-container d-flex align-items-center w-100 gap-3" >
                <i data-card-id="${UID}" style="cursor: pointer" class="fas fa-minus text-danger" ></i>
                <input
                    type="text"
                    class="question-text form-control border p-2"
                    id="questionTitle_${counter}"
                    placeholder="Edit question title"
                    value="${questionTitle}"
                    data-card-id="${counter}"
                    />
                </div>

                <div
                    class="form-check d-flex justify-content-around align-items-center col-8"
                >
                    <input
                    class="form-check-input border"
                    type="radio"
                    name="radioGroup_${counter}"
                    id="radio1"
                    />
                    <input
                    class="form-check-input border"
                    type="radio"
                    name="radioGroup_${counter}"
                    id="radio2"
                    />
                    <input
                    class="form-check-input border"
                    type="radio"
                    name="radioGroup_${counter}"
                    id="radio3"
                    />
                    <input
                    class="form-check-input border"
                    type="radio"
                    name="radioGroup_${counter}"
                    id="radio4"
                    />
                    <input
                    class="form-check-input border"
                    type="radio"
                    name="radioGroup_${counter}"
                    id="radio5"
                    />
            `;
                questionsGroup.appendChild(newQuestionDiv);
                counter++;
            }

            if (questionsArray.length > 0) {
                save_button.disabled = false
            }
        }

        addOptionButton.addEventListener("click", addOption);

        // Add option on Enter key press in optionInput
        optionInput.addEventListener("keydown", function(event) {
            if (event.keyCode === 13) {
                addOption();
            }
        });



        questionsGroup.addEventListener("input", function(event) {
            const inputElement = event.target;

            if (inputElement.tagName === "INPUT" && inputElement.dataset.cardId) {
                const cardId = parseInt(inputElement.dataset.cardId, 10);
                const questionIndex = questionsArray.findIndex(question => question.id === cardId);

                if (questionIndex !== -1) {
                    questionsArray[questionIndex].questionTitle = inputElement.value;
                }
            }
            updateButtonStates()
        });

        updateButtonStates()

        let is_first_button_click = true;


        save_button.addEventListener("click", () => {
            const textFields = document.querySelectorAll(".question-container input")

            // Set the questions fields
            const pollCards = document.querySelectorAll(".poll-card");
            const data = {
                pollCards: []
            };

            pollCards.forEach((card) => {
                const questionText = card.querySelector(".question-text").value;

                data.pollCards.push({
                    question_text: questionText,
                });
            });

            // Set the ratings text
            rateInputs.forEach((input, index) => {
                ratesArray[index] = input.value;
                input.addEventListener("input", (event) => {
                    const inputValue = event.target.value;
                    ratesArray[index] = inputValue;
                });
            });

            // Initialize a flag to track if any empty field is found
            let isEmptyField_question = false;
            let isEmptyField_rate = false;


            // Questions fields validation
            textFields.forEach((input, index) => {
                if (input.value == "") {
                    input.style.cssText = "border: 1px solid red !important";
                    isEmptyField_question = true;
                } else {
                    input.style.border = "none"; // Remove the red border if the field is not empty
                }
            });

            rateInputs.forEach((input, index) => {
                if (input.value == "") {
                    input.style.cssText = "border: 1px solid red !important";
                    isEmptyField_rate = true;
                } else {
                    input.style.border = "none"; // Remove the red border if the field is not empty
                }
            });



            if (pullTitle.value.trim() == "") {
                pullTitle.style.cssText = "border: 1px solid red !important";
                pullTitle.scrollIntoView({
                    behavior: "smooth",
                });
                return;
            } else {
                pullTitle.style.border = "none"; // Remove the red border if the field is not empty
            }

            if (isEmptyField_question) {
                // If any empty field is found, scroll to the first empty field
                textFields.forEach((input, index) => {
                    if (input.value == "") {
                        input.scrollIntoView({
                            behavior: "smooth"
                        });
                        return; // Exit the loop after scrolling to the first empty field
                    }
                });
            } else if (isEmptyField_rate) {
                // If any empty field is found, scroll to the first empty field
                rateInputs.forEach((input, index) => {
                    if (input.value == "") {
                        input.scrollIntoView({
                            behavior: "smooth"
                        });
                        return; // Exit the loop after scrolling to the first empty field
                    }
                });
            } else {
                if (is_first_button_click) {
                    settings_icon.classList.add("shake");
                    var popoverContent = document.createElement("div");
                    popoverContent.style.cssText = "bottom:20px; right:20px";
                    popoverContent.innerHTML = "Click here to edit the survey settings";

                    const popoverInstance = new bootstrap.Popover(settings_icon, {
                        content: popoverContent,
                        trigger: "focus",
                        html: true, // Enable HTML content in the popover
                    });
                    popoverInstance.show(); // Show the popover

                    settings_icon.addEventListener("click", () => {
                        settings_icon.classList.remove("shake");
                        is_first_button_click = false
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
                        real_time_check: show_results.checked,
                        min_votes: min_votes_input.value,
                        button_color: button_color.value,
                    };

                    finalObj = {
                        surveyTitle: pullTitle.value,
                        questions: data.pollCards,
                        ratesArray: ratesArray,
                        settings: settingObj,
                        template: "Rating",
                        type: pullTitle.getAttribute("data-type"),
                        poll_id: pullTitle.getAttribute("data-form-id") != null ? pullTitle.getAttribute("data-form-id") : null,
                    };

                    console.log(finalObj);

                    jQuery.ajax({
                        type: "POST",
                        url: my_ajax_object.ajaxurl,
                        data: {
                            action: "PSX_save_poll_rating_data",
                            poll_data: JSON.stringify(finalObj),
                            nonce: nonce,
                        },
                        success: function(url) {
                            console.log("Done");
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

        const voteCheckbox = document.getElementById("show_results");
        const limitsInput = document.getElementById("show_results_input");
        voteCheckbox.addEventListener("change", function() {
            if (!voteCheckbox.checked) {
                limitsInput.disabled = false;
            } else {
                limitsInput.disabled = true;
            }
        });
    </script>
</body>

</html>