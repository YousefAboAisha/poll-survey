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

  function generateRandomHexColor() {
    // Generate random values for red, green, and blue components
    const red = Math.floor(Math.random() * 256);
    const green = Math.floor(Math.random() * 256);
    const blue = Math.floor(Math.random() * 256);

    // Convert the values to hexadecimal format
    const hexColor = `#${red.toString(16).padStart(2, "0")}${green
      .toString(16)
      .padStart(2, "0")}${blue.toString(16).padStart(2, "0")}`;

    return hexColor;
  }

  save_button.addEventListener("click", function (event) {
    event.preventDefault();

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
    const show_results_containers =
      document.querySelectorAll("#result-container");
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

        // window.location.reload();
        radio_buttons.forEach((radio) => {
          radio.style.display = "none";
        });

        const jsonData = JSON.parse(JSON.parse(response));
        const percentages = jsonData.percentages;

        const votes = jsonData.min_votes;

        if (
          (poll_results != null && poll_results != "") ||
          votes < poll_count
        ) {
          mcq_container.innerHTML = "";
          mcq_container.style.cssText = "display:none !important";
          message.style.cssText = "display:flex !important";
        } else {
          show_results_containers.forEach((elem) => {
            elem.style.cssText = "display:flex !important";
            var questionId = elem.getAttribute("data-question-id");
            var answerId = elem.getAttribute("data-answer-id");
            const percentage_bar = elem.querySelector(
              ".progress-bar .percentage-bar"
            );
            const percentage_value = elem.querySelector(".percentage-value");

            // Check if the percentages data for this question exists
            if (percentages.hasOwnProperty(questionId)) {
              var question_data = percentages[questionId];
              const randomBgColor = generateRandomHexColor();

              // Check if the percentage data for this answer exists
              if (question_data.hasOwnProperty(answerId)) {
                var percentageValue = parseFloat(question_data[answerId]);
                percentageValue = !isNaN(percentageValue) ? percentageValue : 0;

                // Update the percentage bar and value for this specific answer
                setTimeout(() => {
                  percentage_bar.classList.add("transition-progress-bar");
                  percentage_bar.style.cssText = `width:${percentageValue}% !important; z-index:5; background-color:${randomBgColor} !important;`;
                  percentage_value.textContent = `${percentageValue.toFixed(
                    2
                  )}%`;
                }, 500);
              }
            }
          });
        }
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

      response_obj = {
        question_id: question_id, // Use the question_id
        answer_id: question_id, // Use the answer_id for the selected answer
        answer_text: elem.value,
      };
      responses_arr.push(response_obj);
    });

    finalObj = {
      poll_id: poll_id,
      responses: responses_arr,
    };

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
        save_button.style.display = "none";

        open_ended_container.innerHTML = "";
        open_ended_container.style.cssText = "display:none !important";
        message.style.cssText = "display:flex !important";
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
  const poll_count = document
    .getElementById("Title")
    .getAttribute("data-vote-count");
  const poll_results = document
    .getElementById("Title")
    .getAttribute("data-show-results");
  const message = document.getElementById("message");
  const result_chart = document.getElementById("result_chart");

  const poll_id = document
    .getElementById("poll_card")
    .getAttribute("data-card-id");
  var nonce = jQuery("#my-ajax-nonce").val();

  const save_button = document.getElementById("rating_save_button");
  const rating_container = document.getElementById("rating_container");
  const radio_buttons = document.querySelectorAll(".poll-answer-radio");

  save_button.addEventListener("click", function (event) {
    event.preventDefault(); // Prevent the default form submission behavior
    // Get the user ID if logged in
    save_button.disabled = true;
    save_button.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

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

    jQuery.ajax({
      type: "POST",
      url: my_ajax_object.ajaxurl,
      data: {
        action: "PSX_save_poll_response",
        poll_response: JSON.stringify(finalObj),
        nonce: nonce,
      },
      success: function (response) {
        const jsonData = JSON.parse(JSON.parse(response));
        const percentages = jsonData.percentages;
        const answers_labels = jsonData.answers;

        const answerTextsArray = answers_labels.map(
          (answerObj) => answerObj[0].answer_text
        );

        const reversed_labels_array = answerTextsArray.reverse();

        const finalObjects = Object.values(percentages).map((innerObject) => {
          const newObj = {};
          let letterIndex = 0;
          const alphabet = "abcdefghijklmnopqrstuvwxyz";

          Object.values(innerObject).forEach((value) => {
            const key = alphabet[letterIndex++];
            newObj[key] = parseFloat(value); // Convert the value to a number if needed
          });
          return newObj;
        });

        // Function to calculate percentages for each option and ensure the sum is 100%
        function calculatePercentages(dataArray) {
          const totalVotes = dataArray.reduce((acc, dataObject) => {
            for (const key in dataObject) {
              acc[key] = (acc[key] || 0) + dataObject[key];
            }
            return acc;
          }, {});

          const percentages = {};

          for (const key in totalVotes) {
            percentages[key] = `${(
              (totalVotes[key] / (dataArray.length * 100)) *
              100
            ).toFixed(2)}`;
          }

          return percentages;
        }

        // Calculate percentages for each option and ensure the sum is 100%
        const inputData = calculatePercentages(finalObjects);

        // Cal
        save_button.textContent = "DONE!";
        save_button.style.display = "none";

        radio_buttons.forEach((radio) => {
          radio.disabled = true;
        });
        const votes = jsonData.min_votes;

        if (
          (poll_results != null && poll_results != "") ||
          votes < poll_count
        ) {
          rating_container.innerHTML = "";
          rating_container.style.cssText = "display:none !important";
          message.style.cssText = "display:flex !important";
        } else {
          rating_container.innerHTML = "";
          rating_container.style.cssText = "display:none !important";
          var chart = new CanvasJS.Chart("result_chart", {
            animationEnabled: true,
            title: {
              text: "Survey results",
            },
            data: [
              {
                type: "pie",
                startAngle: 240,
                yValueFormatString: '##0.00"%"',
                indexLabel: "{label}. {y}",
                dataPoints: [
                  {
                    y: parseFloat(inputData["a"]),
                    label: reversed_labels_array[0],
                  },
                  {
                    y: parseFloat(inputData["b"]),
                    label: reversed_labels_array[1],
                  },
                  {
                    y: parseFloat(inputData["c"]),
                    label: reversed_labels_array[2],
                  },
                  {
                    y: parseFloat(inputData["d"]),
                    label: reversed_labels_array[3],
                  },
                  {
                    y: parseFloat(inputData["e"]),
                    label: reversed_labels_array[4],
                  },
                ],
              },
            ],
          });
          chart.render();
          result_chart.style.cssText =
            "display:block !important;height: 300px; width: 100%;";
        }
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
