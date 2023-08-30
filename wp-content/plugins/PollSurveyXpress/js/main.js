// Archive Poll
jQuery(document).ready(function (jQuery) {
  const archiveButtons = document.querySelectorAll(".archiveButton");

  // Add a click event listener to each archive button
  archiveButtons.forEach((button) => {
    const pollId = button.getAttribute("data-poll-id");
    const moveButton = document.getElementById("moveButton");

    moveButton.addEventListener("click", deleteRow(pollId));
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
  const poll_count = document
    .getElementById("Title")
    .getAttribute("data-vote-count");
  const poll_results = document
    .getElementById("Title")
    .getAttribute("data-show-results");
  const poll_id = document
    .getElementById("poll_card")
    .getAttribute("data-card-id");
  const message = document.getElementById("message");
  const mcq_container = document.getElementById("mcq_container");

  const save_button = document.getElementById("mcq_save_button");
  var nonce = jQuery("#my-ajax-nonce").val();

  save_button.addEventListener("click", function (event) {
    console.log(poll_count);

    event.preventDefault();
    console.log(nonce);

    // Disable the button and add spinner/loading text
    save_button.disabled = true;
    save_button.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

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
        nonce: nonce, // Pass the nonce
      },
      success: function (response) {
        save_button.textContent = "DONE!";
        save_button.style.display = "none";

        const jsonData = JSON.parse(JSON.parse(response));
        const percentages = jsonData.percentages;

        if (poll_results != null && poll_results != "") {
          mcq_container.innerHTML = ``;
          mcq_container.style.cssText = "display:none !important";
          message.style.cssText = "display:flex !important";

        }

        // Now you can work with the decoded data
        console.log("JSON data:", jsonData);
        console.log("Percentages:", percentages);

        // Create a new toast element
        var toast = document.createElement("div");
        toast.style = "z-index:1000; right: 10px; bottom: 10px";
        toast.className = "position-fixed p-2 px-4 bg-success border rounded-2";
        toast.innerHTML = `
        <p class="m-0 fw-bold text-xs text-white">
          You have successfully submitted your votes!
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

                  // Check if the percentageValue is defined (not null or undefined)
                  // If it's not defined, set it to 0
                  percentageValue =
                    percentageValue !== null && percentageValue !== undefined
                      ? percentageValue
                      : "0";

                  console.log(percentageValue);

                  var element = document.createElement("div");
                  element.className =
                    "d-flex align-items-center justify-content-between gap-2 w-100";
                  element.style.cssText = "min-width:200px";

                  element.innerHTML = `
                    <div class="progress-bar bg-transparent">
                        <p style="width:${percentageValue}%; z-index:5" class="m-0 bg-primary rounded-2"></p>
                        <p style="width:100%; background-color:#DDD;" class="m-0 rounded-2"></p>
                    </div>                    
                        <p style="font-size:12px" class="text-primary m-0 fw-bolder">${percentageValue}%</p>
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
  const message = document.getElementById("message");
  const save_button = document.getElementById("open_ended_save_button");
  const open_ended_container = document.getElementById("open_ended_container");

  save_button.addEventListener("click", function (event) {
    event.preventDefault(); // Prevent the default form submission behavior

    console.log(open_ended_container);

    // Disable the button and add spinner/loading text
    save_button.disabled = true;
    save_button.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

    const textareas = document.querySelectorAll("textarea[data-question-id]"); // Select textareas with the data-question-id attribute
    // Loop through all questions
    let finalObj = {};
    const responses_arr = [];
    var nonce = jQuery("#my-ajax-nonce").val();
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
        nonce: nonce,
      },
      success: function (response) {
        save_button.textContent = "DONE!";
        save_button.disabled = true;
        open_ended_container.innerHTML = ``;
        open_ended_container.style.cssText = "display:none !important";
        message.style.cssText = "display:flex !important";
        save_button.style.display = "none";

        // Create a new toast element
        var toast = document.createElement("div");
        toast.style = "z-index:1000; right: 10px; bottom: 10px";
        toast.className = "position-fixed p-2 px-4 bg-success border rounded-2";
        toast.innerHTML = `
        <p class="m-0 fw-bold text-xs text-white">
          You have successfully submitted your votes!
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
      },
      error: function (error) {
        console.error("Error:", error);
        save_button.textContent = "Save";
        save_button.disabled = false;
      },
    });
  });
});

// Rating collect data
jQuery(document).ready(function (jQuery) {
  const poll_results = document
    .getElementById("Title")
    .getAttribute("data-show-results");
  const message = document.getElementById("message");

  const poll_id = document
    .getElementById("poll_card")
    .getAttribute("data-card-id");
  var nonce = jQuery("#my-ajax-nonce").val();

  const save_button = document.getElementById("rating_save_button");
  const rating_container = document.getElementById("rating_container");
  // Disable the button and add spinner/loading text

  save_button.addEventListener("click", function (event) {
    event.preventDefault(); // Prevent the default form submission behavior
    // Get the user ID if logged in
    save_button.disabled = true;
    save_button.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

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
        nonce: nonce,
      },
      success: function (response) {
        save_button.textContent = "DONE!";
        save_button.disabled = true;
        if (poll_results != null && poll_results != "") {
          rating_container.innerHTML = ``;
          rating_container.style.cssText = "display:none !important";
          message.style.cssText = "display:flex !important";
        }

        save_button.style.display = "none";

        // Create a new toast element
        var toast = document.createElement("div");
        toast.style = "z-index:1000; right: 10px; bottom: 10px";
        toast.className = "position-fixed p-2 px-4 bg-success border rounded-2";
        toast.innerHTML = `
        <p class="m-0 fw-bold text-xs text-white">
          You have successfully submitted your votes!
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
      },
      error: function (error) {
        console.error("Error:", error);
        save_button.textContent = "Save";
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
