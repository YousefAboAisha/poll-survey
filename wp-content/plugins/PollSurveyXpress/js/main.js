// Multiple Choice Template Add Poll
jQuery(document).ready(function (jQuery) {
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
  voteCheckbox.addEventListener("change", function () {
    if (voteCheckbox.checked) {
      limitsInput.disabled = true;
    } else {
      limitsInput.disabled = false;
    }
  });

  // Add Pol cards variables
  let addOptionButton = document.getElementById("addOption");
  let optionInput = document.getElementById("optionInput");
  let optionsGroup = document.getElementById("optionsGroup");
  let createPollButton = document.getElementById("createPoll");
  let questionTitle = document.getElementById("questionTitle");
  let cardsContainer = document.getElementById("cardsContainer");
  let surveyTitleValue = document.getElementById("surveyTitleValue");
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
  const save_button = document.getElementById("save_button");

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

  optionInput.addEventListener("keydown", function (event) {
    if (event.key === 13) {
      createOption();
    }
  });

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

  optionInput.addEventListener("keydown", function (event) {
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
  addOptionButton.addEventListener("click", function () {
    const optionTitle = optionInput.value.trim();
    if (optionTitle !== "") {
      createOption(optionTitle);
      optionInput.value = "";
      updateCreatePollButton();
    }
  });

  function createPollCard() {
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

      if (targetCardIndex !== -1) {
        finalObj.pollCards[targetCardIndex].questionTitle = e.target.value;
      }
      if (pollsCardsArray.length <= 0) {
        save_button.disabled = true;
        createPollButton.disabled = true;
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
      const cardToRemove = document.querySelector(`[data-card-id="${cardId}"]`);

      if (cardToRemove) {
        cardToRemove.remove();

        // Find the index of the card in the pollsCardsArray
        const cardIndex = pollsCardsArray.findIndex(
          (card) => card.id === cardId
        );

        // If the card was found in the array, remove it
        if (cardIndex !== -1) {
          pollsCardsArray.splice(cardIndex, 1);
        }
      }

      // Disable button if there is no cards to send
      if (pollsCardsArray.length <= 0) {
        save_button.disabled = true;
        createPollButton.disabled = true;
      }
    });

    // Append the new poll card to the document
    newPollCard.setAttribute("data-card-id", newPollCardObj.id);
    cardsContainer.appendChild(newPollCard);
    pollsCardsArray.push(newPollCardObj);
    if (pollsCardsArray.length > 0) {
      save_button.disabled = false;
    }

    // Reset Input fields
    optionsHTMLArray = [];
    optionsArray = [];
    optionsGroup.innerHTML = "";
    questionTitle.value = "";
  }

  createPollButton.addEventListener("click", () => {
    if (questionTitle.value !== "" && optionsHTMLArray.length >= 2) {
      createPollCard();
    }
  });

  save_button.addEventListener("click", () => {
    save_button.disabled = true;
    save_button.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    settingObj = {
      cta_Text: cta_input.value,
      start_date: start_date.value || new Date().toISOString(),
      end_date:
        end_date.value ||
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
      sharing: share_plugin.checked,
      real_time_result_text: show_results_input.value,
      real_time_check: show_results.checked,
      min_votes: min_votes_input.value,
    };

    finalObj = {
      surveyTitle: surveyTitleValue.value,
      pollCards: pollsCardsArray,
      settings: settingObj,
      template: "Multiple Choice",
    };

    console.log(finalObj);

    if (pollsCardsArray.length > 0) {
      jQuery.ajax({
        type: "POST",
        url: my_ajax_object.ajaxurl,
        data: {
          action: "PSX_save_poll_Multiple_data",
          poll_data: JSON.stringify(finalObj),
        },
        success: function () {
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
          // Initialize the Bootstrap toast with custom options
          var bootstrapToast = new bootstrap.Toast(toast, {
            autohide: true, // Set to true to enable automatic hiding
            delay: 2000,
          });
          bootstrapToast.show();

          window.location.reload();
        },
        error: function (error) {
          console.error("Error:", error);
          save_button.textContent = "Save";
          save_button.disabled = false;
        },
      });
    }
  });
});

// Archive Poll
jQuery(document).ready(function (jQuery) {
  const archiveButtons = document.querySelectorAll(".archiveButton");

  // Add a click event listener to each archive button
  archiveButtons.forEach((button) => {
    const pollId = button.getAttribute("data-poll-id");
    const moveButton = document.getElementById("moveButton");

    moveButton.addEventListener("click", () => deleteRow(pollId));
  });

  function deleteRow(id) {
    jQuery.ajax({
      type: "POST",
      url: my_ajax_object.ajaxurl,
      data: {
        action: "PSX_archive_poll",
        poll_id: id,
      },
      success: function (response) {
        console.log("Archived"); // Check if this message appears in the console

        // Hide the modal
        const modal = new bootstrap.Modal(
          document.getElementById("deleteModal")
        );
        modal.hide();
      },
      error: function (error) {
        console.error("Error:", error);
      },
    });
  }
});

// Multiple Choice Question collect handle
jQuery(document).ready(function (jQuery) {
  const poll_id = document
    .getElementById("poll_card")
    .getAttribute("data-card-id");

  const save_button = document.getElementById("mcq_save_button");

  save_button.addEventListener("click", function (event) {
    event.preventDefault();

    // Disable the button and add spinner/loading text
    save_button.disabled = true;
    save_button.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

    const responses_arr = [];
    const questions = document.querySelectorAll(".poll-answer-radio");

    Array.from(questions).forEach(function (question) {
      const question_id = question.getAttribute("data-question-id");
      const answer_id = question.getAttribute("data-answer-id");

      if (question.checked) {
        responses_arr.push({
          question_id: question_id,
          answer_id: answer_id,
          answer_text: "",
        });
      }
    });

    const finalObj = {
      poll_id: poll_id,
      responses: responses_arr,
    };

    const show_results_buttons = document.querySelectorAll(
      "#percentage_result_btn"
    );
    const radio_buttons = document.querySelectorAll(".poll-answer-radio");

    jQuery.ajax({
      type: "POST",
      url: my_ajax_object.ajaxurl,
      data: {
        action: "PSX_save_poll_response",
        poll_response: JSON.stringify(finalObj),
      },
      success: function (response) {
        const jsonData = JSON.parse(JSON.parse(response));
        // Access the 'percentages' and 'isSessionSaved' properties
        const percentages = jsonData.percentages;
        const isSessionSaved = jsonData.isSessionSaved;

        save_button.textContent = "Done!";
        save_button.disabled = jsonData.isSessionSaved;

        // Now you can work with the decoded data
        console.log("JSON data:", jsonData);
        console.log("Percentages:", percentages);
        console.log("Is Session Saved:", isSessionSaved);

        show_results_buttons.forEach((button) => {
          button.disabled = false;
          var popoverInstance = null; // Initialize the popover instance

          button.addEventListener("click", function () {
            var questionId = button.getAttribute("data-question-id");

            if (popoverInstance && popoverInstance._popper) {
              popoverInstance.hide(); // Hide the popover if it's already open
              popoverInstance = null; // Reset the popover instance
            } else {
              // Customize the popover content with a <div>
              var question_data = percentages[questionId];
              console.log("Question Data", question_data);

              var popoverContent = document.createElement("div");
              popoverContent.className =
                "position-relative d-flex flex-column gap-2 "; // Customize the class

              // Iterate over the questionData object and create HTML elements
              for (var key in question_data) {
                if (question_data.hasOwnProperty(key)) {
                  var percentageValue = question_data[key];

                  var element = document.createElement("div");
                  element.className =
                    "d-flex align-items-center justify-content-between gap-2 w-100";
                  element.style.cssText = "min-width:200px";
                  element.innerHTML = `
                  <p style="width:${percentageValue}%; height:2px" class="m-0 bg-primary text-primary rounded-2"></p>
                  <p style="font-size:10px" class="text-primary m-0 fw-bolder">${percentageValue}%</p>
                `;
                  popoverContent.appendChild(element);
                }
              }

              popoverInstance = new bootstrap.Popover(this, {
                content: popoverContent,
                trigger: "focus",
                html: true, // Enable HTML content in the popover
              });

              popoverInstance.show(); // Show the popover
            }
          });
        });
        // window.location.reload();
        radio_buttons.forEach((radio) => {
          radio.disabled = true;
        });
      },
      error: function (error) {
        console.error("Error:", error);
        save_button.textContent = "Save";
        save_button.disabled = false;
      },
    });
  });
});

// Open Ended collect data
jQuery(document).ready(function (jQuery) {
  const poll_id = document
    .getElementById("poll_card")
    .getAttribute("data-card-id");
  // Assume you have a button with ID "get_values_button" to trigger the action

  const save_button = document.getElementById("open_ended_save_button");

  save_button.addEventListener("click", function (event) {
    event.preventDefault(); // Prevent the default form submission behavior

    // Disable the button and add spinner/loading text
    save_button.disabled = true;
    save_button.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

    const textareas = document.querySelectorAll("textarea[data-question-id]"); // Select textareas with the data-question-id attribute
    // Loop through all questions
    let finalObj = {};
    const responses_arr = [];

    textareas.forEach(function (elem) {
      let response_obj = {};
      const question_id = elem.getAttribute("data-question-id");

      // if (elem.value != "") {
      response_obj = {
        question_id: question_id, // Use the question_id
        answer_id: question_id, // Use the answer_id for the selected answer
        answer_text: elem.value,
      };
      responses_arr.push(response_obj);
      // }
    });

    finalObj = {
      poll_id: poll_id,
      responses: responses_arr,
    };

    console.log(finalObj);
    jQuery.ajax({
      type: "POST",
      url: my_ajax_object.ajaxurl,
      data: {
        action: "PSX_save_poll_response",
        poll_response: JSON.stringify(finalObj),
      },
      success: function (response) {
        save_button.textContent = "Save";
        save_button.disabled = false;

        console.log("Done");
        // window.location.reload();
      },
      error: function (error) {
        console.error("Error:", error);
        save_button.textContent = "Save";
        save_button.disabled = false;
      },
      complete: function () {
        // Reset button state whether the request succeeded or failed
        save_button.innerHTML = "Save";
        save_button.disabled = false;
      },
    });
  });
});

// Rating collect data
jQuery(document).ready(function (jQuery) {
  const poll_id = document
    .getElementById("poll_card")
    .getAttribute("data-card-id");

  const save_button = document.getElementById("rating_save_button");
  // Disable the button and add spinner/loading text

  save_button.addEventListener("click", function (event) {
    event.preventDefault(); // Prevent the default form submission behavior
    // Get the user ID if logged in
    save_button.disabled = true;
    save_button.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

    // Get the session ID from the browser
    const session_id = sessionStorage.getItem("my_session_id");
    // Loop through all questions
    let finalObj = {};
    const responses_arr = [];
    var questions = document.querySelectorAll(".poll-answer-radio");

    questions.forEach(function (question) {
      let response_obj = {};
      const question_id = question.getAttribute("data-question-id");
      const answer_id = question.getAttribute("data-answer-id");

      if (question.checked) {
        response_obj = {
          question_id: question_id, // Use the question_id
          answer_id: answer_id, // Use the answer_id for the selected answer
          answer_text: "",
        };
        responses_arr.push(response_obj);
      }
    });

    finalObj = {
      poll_id: poll_id,
      responses: responses_arr,
    };
    console.log(finalObj);

    jQuery.ajax({
      type: "POST",
      url: my_ajax_object.ajaxurl,
      data: {
        action: "PSX_save_poll_response",
        poll_response: JSON.stringify(finalObj),
      },
      success: function (response) {
        save_button.textContent = "Save";
        save_button.disabled = false;
      },
      error: function (error) {
        console.error("Error:", error);
        save_button.textContent = "Save";
        save_button.disabled = false;
      },
      complete: function () {
        // Reset button state whether the request succeeded or failed
        save_button.innerHTML = "Save";
        save_button.disabled = false;
      },
    });
  });
});

// Validate that all questions have been answered for the Rating template
jQuery(document).ready(function (jQuery) {
  const saveButton = document.getElementById("rating_save_button");
  const questionContainers = document.querySelectorAll(".poll_card");

  // Function to check if all questions have a radio button selected
  function areAllQuestionsAnswered() {
    for (const questionContainer of questionContainers) {
      const questionRadioButtons =
        questionContainer.querySelectorAll(".poll-answer-radio");
      let answered = false;
      for (const radioButton of questionRadioButtons) {
        if (radioButton.checked) {
          answered = true;
          break;
        }
      }
      if (!answered) {
        return false;
      }
    }
    return true;
  }

  // Update "Save" button state when radio buttons change
  for (const questionContainer of questionContainers) {
    const questionRadioButtons =
      questionContainer.querySelectorAll(".poll-answer-radio");
    for (const radioButton of questionRadioButtons) {
      radioButton.addEventListener("change", function () {
        saveButton.disabled = !areAllQuestionsAnswered();
      });
    }
  }
});

// Validate that all questions have been answered for the Open-ended template
jQuery(document).ready(() => {
  const saveButton = document.getElementById("open_ended_save_button");
  const textareas = document.querySelectorAll(".poll-question-textarea");

  // Function to check if all textareas have content
  function areAllTextareasFilled() {
    for (const textarea of textareas) {
      if (textarea.value.trim() === "") {
        return false;
      }
    }
    return true;
  }

  // Update "Save" button state when textareas change
  for (const textarea of textareas) {
    textarea.addEventListener("input", function () {
      saveButton.disabled = !areAllTextareasFilled();
    });
  }
});

// Validate that all questions have been answered for the MCQ template
jQuery(document).ready(() => {
  const saveButton = document.getElementById("mcq_save_button");
  const questionContainers = document.querySelectorAll(
    ".poll-question-container"
  );

  // Function to check if all questions are answered
  function areAllQuestionsAnswered() {
    for (const questionContainer of questionContainers) {
      const questionRadioButtons =
        questionContainer.querySelectorAll(".poll-answer-radio");
      let answered = false;
      for (const radioButton of questionRadioButtons) {
        if (radioButton.checked) {
          answered = true;
          break;
        }
      }
      if (!answered) {
        return false;
      }
    }
    return true;
  }

  // Update "Save" button state when radio buttons change
  for (const questionContainer of questionContainers) {
    const questionRadioButtons =
      questionContainer.querySelectorAll(".poll-answer-radio");
    for (const radioButton of questionRadioButtons) {
      radioButton.addEventListener("change", function () {
        saveButton.disabled = !areAllQuestionsAnswered();
      });
    }
  }
});
