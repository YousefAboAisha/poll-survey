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
            ", $question->question_id, $poll_id);

    $question->answers = $wpdb->get_results($answers_query);
    $questions_with_answers[] = $question;
}

$poll_data_json = json_encode($poll_data);
$questions_json = json_encode($questions);
$questions_with_answers_json = json_encode($questions_with_answers);


// Query to fetch poll responses data to analyze
$poll_num_of_questions = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT count(question_id) FROM {$wpdb->prefix}polls_psx_survey_questions WHERE poll_id = %d",
        $poll_id
    )
);

$poll_num_of_votes = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT count(response_id) FROM {$wpdb->prefix}polls_psx_survey_responses WHERE poll_id = %d",
        $poll_id
    )
);

$poll_num_of_signed_votes = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT count(response_id) FROM {$wpdb->prefix}polls_psx_survey_responses WHERE poll_id = %d AND user_id != 0",
        $poll_id
    )
);

$poll_num_of_unsigned_votes = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT count(response_id) FROM {$wpdb->prefix}polls_psx_survey_responses WHERE poll_id = %d AND user_id = 0",
        $poll_id
    )
);
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
    <main class="container-fluid main-content position-relative max-height-vh-100 h-100">
        <div class="d-flex align-items-center gap-2 mt-4">
            <a href="<?php echo admin_url('admin.php?page=poll-survey-xpress-surveys'); ?>" class="m-0 text-dark">Home</a>
            <i class="fas fa-angle-right"></i>
            <h6 class="font-weight-bolder mb-0 p-0 ">Rating Survey View</h6>
        </div>

        <div class="bg-white mt-4 border rounded-3">
            <div class="row row-cols-1 row-cols-lg-2 g-4 ">
                <div class="col mb-4">
                    <div class="card-body p-3">
                        <div class="bg-gradient-dark border-radius-lg py-3 mb-3">
                            <div class="chart">
                                <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                            </div>
                        </div>
                        <h6 class="ms-2 mt-4 mb-0">Poll/survey Analysis</h6>

                        <div class="d-flex align-items-center justify-content-between flex-wrap mt-4">
                            <div class="d-flex flex-column align-items-center justify-content-center gap-2 mb-2">
                                <p class="text-xs mt-1 mb-0 font-weight-bold">Questions</p>
                                <h2 class="font-weight-bolder"> <?php echo ($poll_num_of_questions) ?></h2>
                            </div>

                            <div class="d-flex flex-column align-items-center justify-content-center gap-2 mb-2">
                                <p class="text-xs mt-1 mb-0 font-weight-bold">Votes</p>
                                <h2 class="font-weight-bolder"> +<?php echo ($poll_num_of_votes) ?></h2>
                            </div>

                            <div class="d-flex flex-column align-items-center justify-content-center gap-2 mb-2">
                                <p class="text-xs mt-1 mb-0 font-weight-bold">Signed votes</p>
                                <h2 class="font-weight-bolder"> +<?php echo ($poll_num_of_signed_votes) ?> </h2>
                            </div>

                            <div class="d-flex flex-column align-items-center justify-content-center gap-2 mb-2">
                                <p class="text-xs mt-1 mb-0 font-weight-bold">Unsigned votes</p>
                                <h2 class="font-weight-bolder"> <?php echo ($poll_num_of_unsigned_votes) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="z-index-2">
                        <div class="card-header pb-0">
                            <h6>Poll/survey overview</h6>
                            <p class="text-sm">
                                <i class="fa fa-arrow-up text-success"></i>
                                <span class="font-weight-bold">4% more</span> in 2021
                            </p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 p-0 pb-4">
            <h4 class="mb-4 align-self-start p-0 w-75">
                <?php echo ((json_decode(($poll_data_json), true)['title'])); ?></h4>
            <div class="d-flex flex-column align-items-start mb-4 gap-3">
                <?php
                // Decode the JSON back to a PHP array
                $questions_decoded = json_decode(($questions_json), true);

                // Check if decoding was successful
                if ($questions_decoded !== null) {
                    foreach ($questions_decoded as $index => $question) {
                ?>
                        <div class="d-flex justify-content-between align-items-center w-100 rounded-3 border bg-white p-4">
                            <h6 class="mt-2 ">
                                <?php echo $index + 1 . ") " . $question['question_text']; ?>
                            </h6>

                            <div class="d-flex justify-content-around align-items-center col-8 gap-2">
                                <?php
                                $answers_query = $wpdb->prepare("
                                SELECT * FROM {$wpdb->prefix}polls_psx_survey_answers
                                WHERE question_id = %d and poll_id = %d
                                ", $question['question_id'], (json_decode(($poll_data_json), true)['poll_id']));

                                $answers = $wpdb->get_results($answers_query);
                                $questions_with_answers_json = json_encode($answers);
                                $answers = json_decode(($questions_with_answers_json), true); ?>
                                <?php foreach ($answers as $answer) { ?>
                                    <p class="m-0 text-sm ">
                                        <?php echo $answer['answer_text']; ?>
                                    </p>
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
        </div>
    </main>

    <!-- Edit title -->
    <script>
        const surveyTitle = document.getElementById("surveyTitle");
        const updateButton = document.getElementById("updateButton");

        updateButton.addEventListener("click", function() {
            var inputValue = document.getElementById("surveyTitleValue");
            if (inputValue.value.trim() !== "") {
                surveyTitle.innerText = inputValue.value;
            }
            inputValue.value = "";
        });
    </script>

    <!-- Add new option -->
    <script>
        const addOptionButton = document.getElementById("addOption");
        const optionInput = document.getElementById("optionInput");
        const radioGroup = document.getElementById("radioGroup");
        const createPollButton = document.getElementById("createPoll");
        const questionTitle = document.getElementById("questionTitle");

        addOptionButton.addEventListener("click", function() {
            const optionTitle = optionInput.value.trim();

            if (optionTitle !== "") {
                const newOption = document.createElement("div");
                newOption.className = "form-check";

                newOption.innerHTML = `
            <input
              class="form-check-input m-0"
              type="radio"
              name="radioGroup"
              id="${optionTitle}"
            />
            <label class="form-check-label" for="${optionTitle}">
              ${optionTitle}
            </label>
          `;

                radioGroup.appendChild(newOption);
                optionInput.value = "";
            }
        });
    </script>

    <script>
        var ctx = document.getElementById("chart-bars").getContext("2d");

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: [
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                    "Aug",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dec",
                ],
                datasets: [{
                    label: "Sales",
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    backgroundColor: "#fff",
                    data: [450, 200, 100, 220, 500, 100, 400, 230, 500],
                    maxBarThickness: 6,
                }, ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                interaction: {
                    intersect: false,
                    mode: "index",
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: 500,
                            beginAtZero: true,
                            padding: 15,
                            font: {
                                size: 14,
                                family: "Open Sans",
                                style: "normal",
                                lineHeight: 2,
                            },
                            color: "#fff",
                        },
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            display: false,
                        },
                    },
                },
            },
        });

        var ctx2 = document.getElementById("chart-line").getContext("2d");

        var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

        gradientStroke1.addColorStop(1, "rgba(203,12,159,0.2)");
        gradientStroke1.addColorStop(0.2, "rgba(72,72,176,0.0)");
        gradientStroke1.addColorStop(0, "rgba(203,12,159,0)"); //purple colors

        var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);

        gradientStroke2.addColorStop(1, "rgba(20,23,39,0.2)");
        gradientStroke2.addColorStop(0.2, "rgba(72,72,176,0.0)");
        gradientStroke2.addColorStop(0, "rgba(20,23,39,0)"); //purple colors

        new Chart(ctx2, {
            type: "line",
            data: {
                labels: [
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                    "Aug",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dec",
                ],
                datasets: [{
                        label: "Mobile apps",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#cb0c9f",
                        borderWidth: 3,
                        backgroundColor: gradientStroke1,
                        fill: true,
                        data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
                        maxBarThickness: 6,
                    },
                    {
                        label: "Websites",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#3A416F",
                        borderWidth: 3,
                        backgroundColor: gradientStroke2,
                        fill: true,
                        data: [30, 90, 40, 140, 290, 290, 340, 230, 400],
                        maxBarThickness: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                interaction: {
                    intersect: false,
                    mode: "index",
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5],
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: "#b2b9bf",
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: "normal",
                                lineHeight: 2,
                            },
                        },
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [5, 5],
                        },
                        ticks: {
                            display: true,
                            color: "#b2b9bf",
                            padding: 20,
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: "normal",
                                lineHeight: 2,
                            },
                        },
                    },
                },
            },
        });
    </script>

    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../assets/js/soft-ui-dashboard.min.js?v=1.0.3"></script>
</body>

</html>