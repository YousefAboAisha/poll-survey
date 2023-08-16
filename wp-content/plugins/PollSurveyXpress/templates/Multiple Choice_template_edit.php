<?php 
    global $wpdb;

    $poll_id = $_GET['poll_id']; // Get the poll ID from the URL parameter

    // Query to fetch poll data
    $query = $wpdb->prepare("
        SELECT * FROM {$wpdb->prefix}polls_psx_polls
        WHERE poll_id = %d
    ", $poll_id);

    $poll_data = $wpdb->get_row($query);
    if (!$poll_data) {
        echo "Poll not found";
        return;
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
        ", $question->question_id , $poll_id);
        
        $question->answers = $wpdb->get_results($answers_query);
        $questions_with_answers[] = $question;
    }

    $poll_data_json = json_encode($poll_data);
    $questions_json = json_encode($questions);
    $questions_with_answers_json = json_encode($questions_with_answers);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Multiple Choice Template</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</head>


<body class="g-sidenav-show bg-gray-100">
    <main
        class="col-lg-6 col-md-8 col-10 mx-auto main-content position-relative max-height-vh-100 h-100 mt-4 border-radius-lg">
        <!-- Navbar -->
        <nav class="px-0 mb-6 shadow-none border-radius-xl" navbar-scroll="true">
            <h6 class="font-weight-bolder mb-0">Multiple Choice Edit</h6>
        </nav>

        <div class="d-flex flex-column justify-content-center align-items-center gap-3">
            <input type="text" class="w-100 border text-lg rounded-1 p-2 rounded-3 bg-white"
                placeholder="Pull/Survey title" id="surveyTitleValue" value="Pull/Survey title" />

            <div class="d-flex w-100 flex-column gap-2 border rounded-3 bg-white">
                <div class="p-4 pt-3">
                    <label for="surveyTitle" class="form-label">Add question title</label>
                    <input type="text" class="form-control mb-2" placeholder="Question title" id="questionTitle" />

                    <div class="mb-2">
                        <label for="surveyTitle" class="form-label">Add new option</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" class="form-control" placeholder="Option title" id="optionInput" />
                            <button id="addOption"
                                class="text-primary border col-1 btn text-sm font-weight-bold mb-0 shadow-none d-flex justify-content-center align-items-center p-2">
                                <i class="fas fa-plus text-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Radio Buttons group -->
                    <div id="optionsGroup"></div>

                    <button disabled id="createPoll"
                        class="text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold mb-0 mt-3">
                        Create
                        <i class="fas fa-pen text-sm ms-1" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <!-- Final output cards -->
            <div id="cardsContainer" class="w-100 d-flex flex-column gap-3 my-5" style="min-height: 300px">
                <!-- Cards will be rendered here -->
            </div>

            <button type="submit"
                class="align-self-start text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold mb-0 mb-5">
                Update
            </button>
        </div>
    </main>

    <!-- Fixed plugin settings ICON -->
    <div data-bs-toggle="modal" data-bs-target="#settingsModal" style="cursor: pointer"
        class="position-fixed bottom-4 end-2 px-3 py-2 bg-white shadow-sm rounded-circle text-dark">
        <i class="fa fa-cog py-2"> </i>
    </div>
    <!-- Fixed Plugin settings -->
    <div class="modal fade" id="settingsModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Plugin Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <form class="modal-body card">
                    <div>
                        <label>Change plugin Theme</label>

                        <div class="d-flex align-items-center px-2 gap-2">
                            <span class="text-sm fw-bold"> Bg color</span>
                            <input type="color" class="form-control form-control-color border-0 p-0 w-10 me-2"
                                id="bg_color" value="#F00" />
                            <span class="text-sm fw-bold"> Text color </span>
                            <input type="color" class="form-control form-control-color border-0 p-0 w-10"
                                id="text_color" value="#006600" />
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 mt-2 mb-2 px-2">
                        <div>
                            <label class="m-0">Starts</label>
                            <input type="datetime-local" class="form-control" id="start_date"
                                placeholder="Select a date" />
                        </div>

                        <div>
                            <label class="m-0">Ends</label>

                            <input type="datetime-local" class="form-control" id="end_date"
                                placeholder="Select a date" />
                        </div>
                    </div>

                    <div class="card-body pt-sm-3 p-0">
                        <div class="form-group d-flex flex-column">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="active_plugin" />
                                <label class="form-check-label" for="active_plugin">
                                    Activate the survey
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="share_plugin" />
                                <label class="form-check-label" for="share_plugin">
                                    Share with my friends
                                </label>
                            </div>

                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="show_results" />
                                    <label class="form-check-label" for="show_results">
                                        Show real-time results
                                    </label>
                                </div>

                                <input type="text" class="form-control mt-2" placeholder="Add your results title"
                                    id="show_results_input" disabled />
                            </div>

                            <div class="d-flex align-items-center justify-content-start gap-2 mt-3">
                                <label class="form-check-label w-45">
                                    Show results after</label>
                                <input type="number" class="form-control w-55" placeholder="Number of votes"
                                    id="min_votes_input" value="10" />
                            </div>

                            <div class="w-100 d-flex flex-column align-items-start mt-2 gap-2">
                                <input type="text" class="form-control" placeholder="Add CTA button title"
                                    id="cta_input" value="CTA title" />
                                <button id="cta_button" type="button" class="btn btn-dark m-0 mt-1">
                                    CTA Title
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Add the CTA button title dynamically -->
    <script>
    const ctaInput = document.getElementById("cta_input");
    const ctaButton = document.getElementById("cta_button");

    ctaInput.addEventListener("keyup", () => {
        if (ctaInput.value == "") {
            ctaButton.innerText = "CTA Title";
        } else {
            ctaButton.innerText = ctaInput.value;
        }
    });
    </script>

    <!-- Disable vote Input -->
    <script>
    const voteCheckbox = document.getElementById("show_results");
    const limitsInput = document.getElementById("show_results_input");
    voteCheckbox.addEventListener("change", function() {
        if (voteCheckbox.checked) {
            limitsInput.disabled = false;
        } else {
            limitsInput.disabled = true;
        }
    });
    </script>

    <!-- Add the pull card -->
    <script>
    console.log(<?php echo (json_decode($poll_data_json)); ?>);
    // Add Pol cards variables
    let addOptionButton = document.getElementById("addOption");
    let optionInput = document.getElementById("optionInput");
    let optionsGroup = document.getElementById("optionsGroup");
    let createPollButton = document.getElementById("createPoll");
    let questionTitle = document.getElementById("questionTitle");
    let cardsContainer = document.getElementById("cardsContainer");
    let surveyTitleValue = document.getElementById("surveyTitleValue");
    let pollCardCounter = 1;
    let optionsHTMLArray = [];
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
    const share_plugin = document.getElementById("share_plugin");
    const show_results = document.getElementById("show_results");
    const show_results_input = document.getElementById("show_results_input");
    const min_votes_input = document.getElementById("min_votes_input");
    const cta_input = document.getElementById("cta_input");

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

    // Function to enable or disable the createPollButton
    function updateCreatePollButton() {
        const questionTitleValue = questionTitle.value.trim();

        const enableButton =
            questionTitleValue !== "" && optionsHTMLArray.length >= 2;
        createPollButton.disabled = !enableButton;
    }

    // Event listener for questionTitle change
    questionTitle.addEventListener("change", updateCreatePollButton);

    function addOption() {
        const optionTitle = optionInput.value.trim();
        if (optionTitle !== "") {
            createOption(optionTitle);
            optionInput.value = "";
            updateCreatePollButton();
        }
    }

    optionInput.addEventListener("keydown", function(event) {
        if (event.keyCode === 13) {
            addOption();
        }
    });

    // Event listener for addOptionButton click
    addOptionButton.addEventListener("click", addOption);
    const questionTitleValue = questionTitle.value.trim();

    const enableButton =
        questionTitleValue !== "" && optionsHTMLArray.length >= 2;
    createPollButton.disabled = !enableButton;

    // Event listener for questionTitle change
    questionTitle.addEventListener("keyup", updateCreatePollButton);

    // Event listener for addOptionButton click
    addOptionButton.addEventListener("click", function() {
        const optionTitle = optionInput.value.trim();
        if (optionTitle !== "") {
            createOption(optionTitle);
            optionInput.value = "";
            updateCreatePollButton();
        }
    });

    function createPollCard() {
        const optionTitle = optionInput.value.trim();

        const newPollCard = document.createElement("div");
        newPollCard.className =
            "position-relative flex-column flex-wrap gap-2 border rounded-3 bg-white p-4";

        const optionsContainer = document.createElement("div");
        optionsContainer.className = "d-flex flex-column gap-1";

        optionsArray.forEach((option, index) => {
            const newInput = document.createElement("div");
            newInput.className = "d-flex align-items-center";
            const inputElement = document.createElement("input");
            inputElement.type = "text";
            inputElement.className = "form-control mb-2 border-0 w-100";
            inputElement.id = `surveyTitle_${index}`;
            inputElement.placeholder = `Add option #${index + 1}`;
            inputElement.value = option;

            inputElement.addEventListener("input", (event) => {
                const parentElement = event.target.parentNode.parentNode.parentNode;
                const cardId = parentElement.getAttribute("data-card-id");

                const targetCardIndex = finalObj.pollCards.findIndex(
                    (elem) => elem.id == cardId
                );

                if (targetCardIndex !== -1) {
                    // Create a copy of the options array for this specific poll card
                    const newOptionsArray = [
                        ...finalObj.pollCards[targetCardIndex].options,
                    ];
                    newOptionsArray[index] = event.target.value; // Update the corresponding option
                    finalObj.pollCards[targetCardIndex].options = newOptionsArray;
                    console.log(finalObj);
                }
            });

            newInput.appendChild(inputElement);
            optionsContainer.appendChild(newInput);
        });

        // Append the icons div to the newPollCard
        const iconsDiv = document.createElement("div");
        iconsDiv.className = "position-absolute bottom-4 end-2 p-0";

        const deleteIcon = document.createElement("i");
        deleteIcon.style.cursor = "pointer";
        deleteIcon.className = "fas fa-trash text-sm ms-1 text-danger";
        deleteIcon.setAttribute("aria-hidden", "true");
        deleteIcon.setAttribute("data-bs-toggle", "modal");
        deleteIcon.setAttribute("data-bs-target", "#deleteModal");

        iconsDiv.appendChild(deleteIcon);
        newPollCard.appendChild(iconsDiv);

        // Add the title
        const pollTitle = document.createElement("textarea");
        pollTitle.className = "form-control mb-2 w-100 border-0 fw-bolder";
        pollTitle.placeholder = "Edit the poll question title";

        pollTitle.addEventListener("input", (e) => {
            const parentElement = event.target.parentNode;
            const cardId = parentElement.getAttribute("data-card-id");
            const targetCardIndex = finalObj.pollCards.findIndex(
                (elem) => elem.id == cardId
            );
            console.log(targetCardIndex);
            console.log(finalObj);

            if (targetCardIndex !== -1) {
                finalObj.pollCards[targetCardIndex].questionTitle = e.target.value;
                console.log(finalObj);
            }
        });

        pollTitle.value = `${questionTitle.value.trim()}`;
        newPollCard.appendChild(pollTitle);

        // Append the optionsContainer
        newPollCard.appendChild(optionsContainer);

        const newPollCardObj = {
            id: new Date().toString(),
            questionTitle: questionTitle.value.trim(),
            options: optionsArray,
        };

        deleteIcon.setAttribute("data-card-id", newPollCardObj.id);

        deleteIcon.addEventListener("click", (event) => {
            const cardId = event.target.getAttribute("data-card-id");

            // Remove the card from the DOM
            const cardToRemove = document.querySelector(
                `[data-card-id="${cardId}"]`
            );

            if (cardToRemove) {
                cardToRemove.remove();

                // Find the index of the card in the pollsCardsArray
                const cardIndex = pollsCardsArray.findIndex(
                    (card) => card.id === cardId
                );

                // If the card was found in the array, remove it
                if (cardIndex !== -1) {
                    pollsCardsArray.splice(cardIndex, 1);
                    console.log(pollsCardsArray);
                }
            }
        });

        // Append the new poll card to the document
        newPollCard.setAttribute("data-card-id", newPollCardObj.id);
        cardsContainer.appendChild(newPollCard);
        pollsCardsArray.push(newPollCardObj);
        console.log(pollsCardsArray);

        settingObj = {
            cta_Text: cta_input.value,
            start_date: start_date.value,
            end_date: end_date.value,
            status: active_plugin.checked,
            color: text_color.value,
            bgcolor: bg_color.value,
            sharing: share_plugin.checked,
            real_time_result_text: show_results.checked,
            min_votes: min_votes_input.value,
        };

        finalObj = {
            surveyTitle: surveyTitleValue.value,
            pollCards: pollsCardsArray,
            settings: settingObj,
        };

        // Reset Input fields
        optionsHTMLArray = [];
        optionsArray = [];
        optionsGroup.innerHTML = "";
        questionTitle.value = "";

        // Increment the poll card counter for the next card
        pollCardCounter++;
    }

    createPollButton.addEventListener("click", () => {
        if (questionTitle.value !== "" && optionsHTMLArray.length >= 2) {
            createPollCard();
            console.log("Final Object", finalObj);
        }
    });
    </script>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
</body>

</html>