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
    $type = json_encode($isItEditPage);
    $questions_with_answers_json = json_encode($questions_with_answers);
    $jsonDataEncoded = htmlspecialchars($questions_with_answers_json, ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
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
                <h6 class="font-weight-bolder mb-0 p-0 "><?php _e('Multiple Choice Survey Edit', 'psx-poll-survey-plugin'); ?></h6>
            <?php } else { ?>
                <a href="<?php echo admin_url('admin.php?page=poll-survey-xpress-add'); ?>" class="m-0 text-dark"><?php _e('Templates', 'psx-poll-survey-plugin'); ?></a>
                <i class="fas fa-angle-right"></i>
                <h6 class="font-weight-bolder mb-0 p-0 "><?php _e('Multiple Choice Survey Add', 'psx-poll-survey-plugin'); ?></h6>
            <?php } ?>
        </div>

        <div class="d-flex flex-column justify-content-center align-items-center">
            <input type="text" class="w-100 border text-lg rounded-1 p-1 rounded-1 bg-white mb-3" placeholder="Poll/Survey title" id="surveyTitle" value="Poll/Survey title" data-type="<?php echo ($isItEditPage ? "Edit" : "Add"); ?>" data-form-id="<?php echo ($isItEditPage ? $poll_id : null); ?>" data-json-data="<?php echo $jsonDataEncoded ?>" />

            <div class="d-flex w-100 flex-column border rounded-3 bg-white p-4">
                <label class="form-label"><?php _e('Add question title', 'psx-poll-survey-plugin'); ?></label>
                <input type="text" class="form-control mb-2 border p-2" placeholder="Question title" id="questionTitle" />

                <div class="mt-2 mb-2">
                    <label class="form-label"><?php _e('Add new option', 'psx-poll-survey-plugin'); ?></label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control border p-2" placeholder="Option title" id="optionInput" />
                        <button id="addOption" class="text-primary border btn text-sm font-weight-bold mb-0 shadow-none d-flex justify-content-center align-items-center p-3 rounded-1">
                            <i class="fas fa-plus text-sm" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <!-- Radio Buttons group -->
                <div id="optionsGroup"></div>

                <button disabled id="createPoll" class="text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold mb-0 mt-4">
                    <?php _e('Create', 'psx-poll-survey-plugin'); ?>
                    <i class="fas fa-plus text-sm ms-1" aria-hidden="true"></i>
                </button>

                <div class="d-flex flex-column gap-1 mt-4 ">
                    <p style="font-size: 12px;" class="m-0 text-dark">- You must add add at least one question</p>
                    <p style="font-size: 12px;" class="m-0 text-dark">- Each question must contain at least two options</p>
                </div>
            </div>

            <!-- Final output cards -->
            <div id="cardsContainer" class="w-100 d-flex flex-column gap-3 my-5">

                <?php
                // Check if decoding was successful
                if ($questions_with_answers !== null) {
                    foreach ($questions_with_answers as $index => $question) {
                ?>
                        <div data-card-id="<?php echo $question->question_id ?>" class="poll-card position-relative flex-column flex-wrap gap-2 border rounded-3 bg-white p-4">
                            <textarea class="question-text form-control mb-2 w-100 border-0 fw-bolder" placeholder="Edit the poll question title"><?php echo $question->question_text; ?></textarea>

                            <div class="position-absolute bottom-4 end-2 p-0">
                                <i data-card-id="<?php echo $question->question_id ?>" class="delete-card fas fa-trash text-sm ms-1 text-danger " aria-hidden="true" style="cursor: pointer;"></i>
                            </div>

                            <div class="options-container d-flex flex-column gap-1">
                                <?php foreach ($question->answers as $index => $answer) { ?>
                                    <div data-card-id="<?php echo $answer->question_id ?>" class="option-container d-flex justify-content-between align-items-center w-100 mb-3 gap-3">
                                        <i id="delete_option" data-deleteOption-id="<?php echo $answer->answer_id ?>" class="fas fa-minus text-danger" style="cursor: pointer"></i>
                                        <input type="text" class="form-control border-0 w-100 p-2" id="surveyTitle_<?php echo $answer->answer_id ?>" placeholder="Edit option" value="<?php echo $answer->answer_text; ?>">
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "Error decoding JSON.";
                }
                ?>

            </div>
            <button type="submit" id="save_button" disabled class="align-self-start text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold mb-5">
                <?php _e('Save', 'psx-poll-survey-plugin'); ?>
            </button>
        </div>
    </main>




    <!-- Fixed plugin settings ICON -->
    <div data-bs-toggle="modal" data-bs-target="#settingsModal" style="cursor: pointer" class="position-fixed bottom-4 end-2 px-3 py-2 bg-white shadow-sm rounded-circle text-dark">
        <i class="fa fa-cog py-2"> </i>
    </div>

    <!-- Fixed Plugin settings -->
    <div class="modal fade" id="settingsModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title"> <?php _e('Survey Settings', 'psx-poll-survey-plugin'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <form class="modal-body card">
                    <input type="hidden" id="my-ajax-nonce" value="<?php echo wp_create_nonce('my_ajax_nonce'); ?>" />
                    <div>
                        <label><?php _e('Change plugin Theme', 'psx-poll-survey-plugin'); ?></label>

                        <div class="d-flex align-items-center px-2 gap-2">
                            <span class="text-sm fw-bold"><?php _e('Bg color', 'psx-poll-survey-plugin'); ?> </span>
                            <input type="color" class="form-control form-control-color border-0 p-0 w-10 me-2" id="bg_color" value="<?php echo $isItEditPage ? $poll_data[0]->bgcolor : "#f8f9fa"; ?>" />
                            <span class="text-sm fw-bold"><?php _e('Text color', 'psx-poll-survey-plugin'); ?> </span>
                            <input type="color" class="form-control form-control-color border-0 p-0 w-10 me-2" id="text_color" value="<?php echo $isItEditPage ? $poll_data[0]->color : "#344767"; ?>" />
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
                                    <input class="form-check-input" type="checkbox" id="show_results" <?php echo empty($poll_data[0]->real_time_result_text) ? 'checked' : ''; ?>onchange="toggleInputState()" />
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
                                    <?php echo $poll_data[0]->cta_Text; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Multiple Choice Template Add Poll
        jQuery(document).ready(function(jQuery) {
            const ctaInput = document.getElementById("cta_input");
            const ctaButton = document.getElementById("cta_button");

            ctaInput.addEventListener("keyup", () => {
                if (ctaInput.value == "") {
                    ctaButton.innerText = "CTA Title";
                } else {
                    ctaButton.innerText = ctaInput.value;
                }
            });

            const voteCheckbox = document.getElementById("show_results");
            const limitsInput = document.getElementById("show_results_input");
            voteCheckbox.addEventListener("change", function() {
                if (voteCheckbox.checked) {
                    limitsInput.disabled = true;
                } else {
                    limitsInput.disabled = false;
                }
            });

            // Add Poll cards variables
            let addOptionButton = document.getElementById("addOption");
            let optionInput = document.getElementById("optionInput");
            let optionsGroup = document.getElementById("optionsGroup");
            let createPollButton = document.getElementById("createPoll");
            let questionTitle = document.getElementById("questionTitle");
            let cardsContainer = document.getElementById("cardsContainer");
            let surveyTitle = document.getElementById("surveyTitle");
            let optionsHTMLArray = [];
            const cards_array = JSON.parse(surveyTitle.getAttribute("data-json-data"));

            jQuery(document).ready(function(jQuery) {
                console.log(cards_array);
            })

            // Data will be sent
            let optionsArray = [];
            let pollsCardsArray = [];
            // Final object data
            let finalObj = {};

            let settingObj = {};

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
            const save_button = document.getElementById("save_button");
            const nonce = jQuery("#my-ajax-nonce").val();

            if (cardsContainer.childElementCount > 0) {
                save_button.disabled = false
            }


            function createOption(optionTitle) {
                const newOption = document.createElement("div");
                newOption.innerHTML = `
                    <span>
                    ${optionTitle}
                    </span>
                `;
                optionsGroup.appendChild(newOption);
                optionsArray.push(optionTitle);
                console.log(optionsArray);
                optionsHTMLArray.push(newOption);
            }

            function updateButtonStates() {
                // Check if any card has no options or insufficient options
                const cards = document.querySelectorAll('.poll-card');
                let enableButton = true
                cards.forEach(card => {
                    const options = card.querySelectorAll('.option-container');
                    if (options.length < 2) {
                        enableButton = false;
                        return; // No need to check other cards, button should be disabled
                    }
                });

                // Check if there are no cards
                const noCards = cardsContainer.childElementCount <= 0;

                // Update the save button
                save_button.disabled = !enableButton || noCards;

                // Update the create poll button
                const enableCreatePollButton = optionsHTMLArray.length >= 2;
                createPollButton.disabled = !enableCreatePollButton;
            }

            updateButtonStates();

            function addOption() {
                const optionTitle = optionInput.value.trim();
                if (optionTitle !== "") {
                    createOption(optionTitle);
                    optionInput.value = "";
                    updateButtonStates();
                }
            }

            optionInput.addEventListener("keydown", function(event) {
                if (event.key === "Enter" && event.target === optionInput) {
                    addOption();
                }
            });

            addOptionButton.addEventListener("click", addOption);


            function createPollCard() {
                const newPollCard = document.createElement("div");
                newPollCard.className =
                    "poll-card position-relative flex-column flex-wrap gap-2 border rounded-3 bg-white p-4";
                newPollCard.setAttribute("data-card-id", new Date().toString());

                const optionsContainer = document.createElement("div");
                optionsContainer.className = "options-container d-flex flex-column gap-1";

                const newQuestionDivs = optionsArray.map((option, index) => {
                    const newQuestionDiv = document.createElement("div");
                    newQuestionDiv.className =
                        "option-container d-flex justify-content-between align-items-center w-100 mb-3 gap-3";
                    newQuestionDiv.setAttribute("data-card-id", index);

                    newQuestionDiv.innerHTML = `
                        <i id="delete_option" data-deleteOption-id="${index}" style="cursor: pointer" class="fas fa-minus text-danger"></i>

                        <input 
                            type="text" 
                            class="form-control border-0 w-100 p-2" 
                            id="surveyTitle_${index}" 
                            placeholder="Add option #${index + 1}" 
                            value="${option}"
                        >
                    `;

                    return newQuestionDiv;
                });


                newQuestionDivs.forEach((newQuestionDiv) => {
                    optionsContainer.appendChild(newQuestionDiv);
                });

                // Append the icons div to the newPollCard
                const delete_poll_icon = document.createElement("div");
                delete_poll_icon.className = "position-absolute bottom-4 end-2 p-0";

                const delete_option_icon = document.createElement("i");
                delete_option_icon.style.cursor = "pointer";
                delete_option_icon.className = "fas fa-trash text-sm ms-1 text-danger";

                delete_poll_icon.appendChild(delete_option_icon);
                newPollCard.appendChild(delete_poll_icon);

                // Add the title
                const pollTitle = document.createElement("textarea");
                pollTitle.className = "question-text form-control mb-2 w-100 border-0 fw-bolder";
                pollTitle.placeholder = "Edit the poll question title";

                pollTitle.value = `${questionTitle.value.trim()}`;
                newPollCard.appendChild(pollTitle);

                // Append the optionsContainer
                newPollCard.appendChild(optionsContainer);

                const newPollCardObj = {
                    id: new Date().toString(),
                    questionTitle: questionTitle.value.trim(),
                    options: optionsArray,
                };


                delete_option_icon.setAttribute("data-card-id", newPollCardObj.id);


                // Append the new poll card to the document
                newPollCard.setAttribute("data-card-id", newPollCardObj.id);
                cardsContainer.appendChild(newPollCard);
                pollsCardsArray.push(newPollCardObj);
                updateSaveButtonState();

                // Reset Input fields
                optionsHTMLArray = [];
                optionsArray = [];
                optionsGroup.innerHTML = "";
                questionTitle.value = "";
            }

            // Create Poll card
            createPollButton.addEventListener("click", () => {
                if (questionTitle.value !== "" && optionsHTMLArray.length >= 2) {
                    createPollCard();
                }
            });

            // Update save_button status
            function updateSaveButtonState() {
                const emptyCardExists = pollsCardsArray.some(card => card.options.length === 0);
                const insufficientOptions = pollsCardsArray.some(card => card.options.length < 2);

                if (emptyCardExists || insufficientOptions || cardsContainer.childElementCount <= 0) {
                    save_button.disabled = true;
                } else {
                    save_button.disabled = false;
                }
            }

            // Event listener for removing a poll card
            document.addEventListener("click", function(event) {
                if (event.target.classList.contains("fa-trash")) {
                    const cardId = event.target.getAttribute("data-card-id");

                    const card = document.querySelector(`[data-card-id="${cardId}"]`);
                    if (card) {
                        card.remove();
                        updateButtonStates();
                    }
                }
            });

            // Event listener for removing an option container
            document.addEventListener("click", function(event) {
                if (event.target.classList.contains("fa-minus")) {
                    const optionId = event.target.getAttribute("data-deleteOption-id");

                    const optionContainer = document.querySelector(`[data-deleteOption-id="${optionId}"]`);
                    if (optionContainer) {
                        optionContainer.parentNode.remove();
                        updateButtonStates();
                    }

                    // Prevent the click event from propagating to the card deletion event listener
                    event.stopPropagation();
                }
            });


            save_button.addEventListener("click", () => {
                // Select all input fields within .options-container
                let inputs = document.querySelectorAll(".option-container input");

                const pollCards = document.querySelectorAll(".poll-card");
                const data = {
                    pollCards: []
                };

                pollCards.forEach((card) => {
                    const questionText = card.querySelector(".question-text").value;
                    const options = Array.from(card.querySelectorAll("input"))
                        .map(option => option.value)
                        .filter(option => option.trim() !== "");

                    data.pollCards.push({
                        question_text: questionText,
                        options: options
                    });
                });

                // console.log("Final Data", data); // Output the JSON data to the console


                // Initialize a flag to track if any empty field is found
                let isEmptyField = false;

                inputs.forEach((input, index) => {
                    if (input.value == "") {
                        input.style.cssText = "border: 1px solid red !important";
                        isEmptyField = true;
                    } else {
                        input.style.border = "none"; // Remove the red border if the field is not empty
                    }
                });

                if (surveyTitle.value.trim() == "") {
                    surveyTitle.style.cssText = "border: 1px solid red !important";
                    surveyTitle.scrollIntoView({
                        behavior: "smooth"
                    });

                    return;
                } else {
                    surveyTitle.style.border = "none"; // Remove the red border if the field is not empty
                }

                if (isEmptyField) {
                    // If any empty field is found, scroll to the first empty field
                    inputs.forEach((input, index) => {
                        if (input.value == "") {
                            input.scrollIntoView({
                                behavior: "smooth"
                            });
                            return; // Exit the loop after scrolling to the first empty field
                        }
                    });
                } else {
                    save_button.disabled = true;
                    save_button.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    settingObj = {
                        cta_Text: cta_input.value,
                        start_date: start_date.value || new Date().toISOString(),
                        end_date: end_date.value ||
                            new Date(
                                new Date().getFullYear() + 100,
                                11,
                                31,
                                23,
                                59,
                                59
                            ).toISOString(),
                        status: active_plugin.checked,
                        color: text_color.value,
                        bgcolor: bg_color.value,
                        real_time_result_text: show_results_input.value,
                        real_time_check: show_results.checked,
                        min_votes: min_votes_input.value,
                    };

                    finalObj = {
                        surveyTitle: surveyTitle.value,
                        type: surveyTitle.getAttribute("data-type"),
                        poll_id: surveyTitle.getAttribute("data-form-id") != null ? surveyTitle.getAttribute("data-form-id") : null,
                        pollCards: data.pollCards,
                        settings: settingObj,
                        template: "Multiple Choice",
                    };

                    // console.log("FinalObj", finalObj);

                    jQuery.ajax({
                        type: "POST",
                        url: my_ajax_object.ajaxurl,
                        data: {
                            action: "PSX_save_poll_Multiple_data",
                            nonce: nonce, // Pass the nonce
                            poll_data: JSON.stringify(finalObj),
                        },
                        success: function(shortcode) {
                            console.log("Done");
                            save_button.textContent = "Save";
                            save_button.disabled = false;

                            // Create a new toast element
                            var toast = document.createElement("div");
                            toast.style = "z-index:1000; right: 10px; bottom: 10px";
                            toast.className =
                                "position-fixed p-2 px-4 bg-success border rounded-2";
                            toast.innerHTML = `
                                <p class="m-0 fw-bold text-xs text-white">
                                New survey has been added successfully!
                                </p>
                            `;
                            // Append the toast to the document
                            document.body.appendChild(toast);

                            // Initialize the Bootstrap toast
                            var bootstrapToast = new bootstrap.Toast(toast);
                            bootstrapToast.show();

                            setTimeout(() => {
                                window.location.reload();
                            }, 500)
                        },
                        error: function(error) {
                            console.error("Error:", error);
                            save_button.textContent = "Save";
                            save_button.disabled = false;
                        },
                    });
                }
            });
        });
    </script>

</body>

</html>