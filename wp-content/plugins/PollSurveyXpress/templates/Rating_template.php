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
        <!-- Navbar -->
        <nav class="px-0 mb-4 shadow-none border-radius-xl" navbar-scroll="true">
            <nav aria-label="breadcrumb">
                <h6 class="font-weight-bolder mb-0">Rating Template</h6>
            </nav>
        </nav>

        <div class="d-flex flex-column justify-content-center align-items-center gap-3">
            <div class="d-flex w-100 flex-column gap-2 border rounded-3 bg-white">
                <div class="p-4 pt-3">
                    <div class="mb-2">
                        <label for="surveyTitle" class="form-label">Add new question</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" class="form-control p-2 border rounded-1" id="questionInput" placeholder="Question title" />
                            <button id="addQuestion" class="text-primary p-0 border btn text-sm font-weight-bold mb-0 shadow-none d-flex justify-content-center align-items-center p-3 rounded-1">
                                <i class="fas fa-plus text-sm p-0" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Final output Survey -->
        <div class="d-flex flex-column align-items-start my-3 p-4 rounded-3 border bg-white">

            <div class="d-flex justify-content-between align-items-center w-100 mb-3">
                <input type="text" class="w-100 text-lgs p-2 px-0 bg-white border-0 rounded-1 mb-4" placeholder="Pull/Survey title" id="pullTitle" value="Pull/Survey title" />

                <div id="rateInputs" class="form-check d-flex justify-content-around align-items-center col-8 gap-2">
                    <input type="text" id="rateInput1" class="w-100 text-lgs p-2 px-0 bg-white border-0 rounded-1 mb-4" placeholder="Rate #1" value="Rate #1" />
                    <input type="text" id="rateInput2" class="w-100 text-lgs p-2 px-0 bg-white border-0 rounded-1 mb-4" placeholder="Rate #2" value="Rate #2" />
                    <input type="text" id="rateInput3" class="w-100 text-lgs p-2 px-0 bg-white border-0 rounded-1 mb-4" placeholder="Rate #3" value="Rate #3" />
                    <input type="text" id="rateInput4" class="w-100 text-lgs p-2 px-0 bg-white border-0 rounded-1 mb-4" placeholder="Rate #4" value="Rate #4" />
                    <input type="text" id="rateInput5" class="w-100 text-lgs p-2 px-0 bg-white border-0 rounded-1 mb-4" placeholder="Rate #5" value="Rate #5" />
                </div>
            </div>


            <div id="questionsGroup" class="flex flex-column gap-2 w-100">
                <div class="d-flex justify-content-between align-items-center w-100 mb-1">

                </div>
            </div>
        </div>

        <button disabled id="save_button" type="button" class="align-self-start text-white btn bg-primary col-lg-3 col-md-4 col-4 text-sm font-weight-bold mb-0 mb-5 mt-2">
            Save
        </button>

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
                    <h5 class="modal-title">Survey Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <form class="modal-body card">
                    <div>
                        <label>Change plugin Theme</label>

                        <div class="d-flex align-items-center px-2 gap-2">
                            <span class="text-sm fw-bold"> Bg color</span>
                            <input type="color" class="form-control form-control-color border-0 p-0 w-10 me-2" id="bg_color" value="#F00" />
                            <span class="text-sm fw-bold"> Text color </span>
                            <input type="color" class="form-control form-control-color border-0 p-0 w-10" id="text_color" value="#006600" />
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 mt-2 mb-2 px-2">
                        <div>
                            <label class="m-0">Starts</label>
                            <input type="datetime-local" class="form-control" id="start_date" placeholder="Select a date" />
                        </div>

                        <div>
                            <label class="m-0">Ends</label>

                            <input type="datetime-local" class="form-control" id="end_date" placeholder="Select a date" />
                        </div>
                    </div>

                    <div class="card-body pt-sm-3 p-0">
                        <div class="form-group d-flex flex-column">
                            <div class="form-check">
                                <input class="form-check-input border" type="checkbox" id="active_plugin" />
                                <label class="form-check-label" for="active_plugin">
                                    Activate the survey
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input border" type="checkbox" id="share_plugin" />
                                <label class="form-check-label" for="share_plugin">
                                    Share with my friends
                                </label>
                            </div>

                            <div>
                                <div class="form-check">
                                    <input class="form-check-input border" type="checkbox" id="show_results" />
                                    <label class="form-check-label" for="show_results">
                                        Show real-time results
                                    </label>
                                </div>

                                <input type="text" class="form-control mt-2" placeholder="Add your results title" id="show_results_input" />
                            </div>

                            <div class="d-flex align-items-center justify-content-start gap-2 mt-3">
                                <label class="form-check-label w-45">
                                    Show results after</label>
                                <input type="number" class="form-control w-55" placeholder="Number of votes" id="min_votes_input" value="10" />
                            </div>

                            <div class="w-100 d-flex flex-column align-items-start mt-2 gap-2">
                                <input type="text" class="form-control" placeholder="Add CTA button title" id="cta_input" value="CTA title" />
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


    <script>
        function deleteQuestion(cardId) {
            const index = questionsArray.findIndex(question => question.id === cardId);
            if (index !== -1) {
                questionsArray.splice(index, 1);
                const questionElement = document.querySelector(`[data-card-id="${cardId}"]`);
                if (questionElement) {
                    questionElement.remove();
                }
            }
            // finalObj = {
            //   surveyTitle: pullTitle.value,
            //   questions: questionsArray,
            // };
        }
        const save_button = document.getElementById("save_button");
        const pullTitle = document.getElementById("pullTitle");
        const addOptionButton = document.getElementById("addQuestion");
        const optionInput = document.getElementById("questionInput");
        const questionsGroup = document.getElementById("questionsGroup");
        let questionsArray = [];
        let counter = 1;
        let finalObj = {};

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

        // Rates data
        const rateInputs = document.querySelectorAll("#rateInputs input");
        const ratesArray = [];

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
                    "d-flex justify-content-between align-items-center w-100 mb-3";
                newQuestionDiv.setAttribute("data-card-id", counter);

                newQuestionDiv.innerHTML = `
                <div class="d-flex align-items-center w-100 gap-3">
                <i onclick="deleteQuestion(${counter})" style="cursor: pointer" class="fas fa-trash text-danger"></i>
                <input
                    type="text"
                    class="form-control border p-2"
                    id="questionTitle_${counter}"
                    placeholder="Edit Question title"
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

            if (questionsArray.length <= 0) {
                save_button.disabled = true
            }
        });


        save_button.addEventListener("click", () => {
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
                sharing: share_plugin.checked,
                real_time_result_text: show_results_input.value,
                real_time_check: show_results.checked,
                min_votes: min_votes_input.value,
            };

            finalObj = {
                surveyTitle: pullTitle.value,
                questions: questionsArray,
                ratesArray: ratesArray,
                settings: settingObj,
                template: "Rating",
            };
            jQuery.ajax({
                type: "POST",
                url: my_ajax_object.ajaxurl,
                data: {
                    action: "PSX_save_poll_rating_data",
                    poll_data: JSON.stringify(finalObj),
                },
                success: function(shortcode) {
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

                    // Initialize the Bootstrap toast
                    // Initialize the Bootstrap toast with custom options
                    var bootstrapToast = new bootstrap.Toast(toast, {
                        autohide: true, // Set to true to enable automatic hiding
                        delay: 2000,
                    });
                    bootstrapToast.show();

                    window.location.reload();

                },
                error: function(error) {
                    console.error("Error:", error);
                    save_button.textContent = "Save";
                    save_button.disabled = false;
                },
            });
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
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
</body>

</html>