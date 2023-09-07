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

$end_date = current_time('mysql'); // Get the current date and time in MySQL format
$start_date = date('Y-m-d H:i:s', strtotime('-7 days', strtotime($end_date))); // Calculate the start date as 7 days ago

// Create an array of all dates in the last week
$all_dates = [];
$current_date = strtotime($start_date);
$end_timestamp = strtotime($end_date);

while ($current_date <= $end_timestamp) {
    $all_dates[] = date('Y-m-d', $current_date);
    $current_date = strtotime('+1 day', $current_date);
}

// Create a query to retrieve the vote counts for each date
$votes_query = $wpdb->prepare("
    SELECT DATE(answerd_at) AS date, COUNT(*) AS vote_count
    FROM {$wpdb->prefix}polls_psx_survey_responses
    WHERE poll_id = %d
    AND answerd_at BETWEEN %s AND %s
    GROUP BY DATE(answerd_at)
", $poll_id, $start_date, $end_date);

$votes_data = $wpdb->get_results($votes_query);

// Create an associative array to store the vote counts for each date
$vote_counts = [];
foreach ($votes_data as $row) {
    $vote_counts[$row->date] = $row->vote_count;
}

// Fill in missing dates with zero votes
$result_data = [];
foreach ($all_dates as $date) {
    $vote_count = isset($vote_counts[$date]) ? $vote_counts[$date] : 0;
    $result_data[] = ['date' => $date, 'vote_count' => $vote_count];
}
$poll_data_json = json_encode($poll_data);
$questions_json = json_encode($questions);

$result_data_json = json_encode($result_data);
$jsonDataEncoded = htmlspecialchars($result_data_json, ENT_QUOTES, 'UTF-8');

// Query to fetch question answers data to analyze

// Create an array to store the questions and their answers
$questionAnswers = array();

// Step 1: Get the questions for the given poll
$questions_query = $wpdb->prepare("
    SELECT * FROM {$wpdb->prefix}polls_psx_survey_questions
    WHERE poll_id = %d
", $poll_id);

$questions = $wpdb->get_results($questions_query);

// Step 2: Loop through the questions and retrieve their corresponding answers
foreach ($questions as $question) {
    $question_id = $question->question_id;

    // Query to get the answer text for each question
    $answers_query = $wpdb->prepare("
        SELECT srd.open_text_response
        FROM {$wpdb->prefix}polls_psx_survey_responses_data AS srd
        JOIN {$wpdb->prefix}polls_psx_survey_responses AS sr
        ON srd.response_id = sr.response_id
        WHERE srd.question_id = %d
        AND sr.poll_id = %d
    ", $question_id, $poll_id);

    $answers = $wpdb->get_results($answers_query);

    // Create an array to store the answers for the current question
    $questionAnswers[$question_id] = array();

    foreach ($answers as $answer) {
        // Add the answer to the array
        $questionAnswers[$question_id][] = $answer->open_text_response;
    }
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
    <main class="container-fluid main-content position-relative max-height-vh-100 h-100">

        <div class="d-flex align-items-center gap-2 mt-4">
            <a href="<?php echo admin_url('admin.php?page=poll-survey-xpress-surveys'); ?>" class="m-0 text-dark"><?php _e('Home', 'psx-poll-survey-plugin'); ?></a>
            <i class="fas fa-angle-right"></i>
            <h6 class="font-weight-bolder mb-0 p-0 "><?php _e('Open-ended Survey View', 'psx-poll-survey-plugin'); ?></h6>
        </div>


        <div id="chart-container" class="bg-white mt-4 border rounded-3 col-lg-9 col-md-10 col-12" data-json-data="<?php echo $jsonDataEncoded  ?>">
            <div class="card-header pb-0 mt-3">
                <h6 class="mb-2 mb-0 text-lg fw-bolder"><?php _e('Survey Analysis', 'psx-poll-survey-plugin'); ?></h6>
            </div>

            <div class="card-body p-3">
                <div class="bg-gradient-dark border-radius-lg py-3 mb-3">
                    <div class="chart">
                        <canvas id="chart-bars" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>


                <div class="d-flex align-items-center justify-content-between flex-wrap mt-4 col-10">
                    <div class="d-flex flex-column align-items-center justify-content-center gap-2 mb-2">
                        <p class="text-xs mt-1 mb-0 font-weight-bold"><?php _e('Questions', 'psx-poll-survey-plugin'); ?></p>
                        <h2 class="font-weight-bolder"> <?php echo ($poll_num_of_questions) ?></h2>
                    </div>

                    <div class="d-flex flex-column align-items-center justify-content-center gap-2 mb-2">
                        <p class="text-xs mt-1 mb-0 font-weight-bold"><?php _e('Votes', 'psx-poll-survey-plugin'); ?></p>
                        <h2 class="font-weight-bolder"> +<?php echo ($poll_num_of_votes) ?></h2>
                    </div>

                    <div class="d-flex flex-column align-items-center justify-content-center gap-2 mb-2">
                        <p class="text-xs mt-1 mb-0 font-weight-bold"><?php _e('Signed votes', 'psx-poll-survey-plugin'); ?></p>
                        <h2 class="font-weight-bolder"> +<?php echo ($poll_num_of_signed_votes) ?> </h2>
                    </div>

                    <div class="d-flex flex-column align-items-center justify-content-center gap-2 mb-2">
                        <p class="text-xs mt-1 mb-0 font-weight-bold"><?php _e('Unsigned votes', 'psx-poll-survey-plugin'); ?></p>
                        <h2 class="font-weight-bolder"> <?php echo ($poll_num_of_unsigned_votes) ?></h2>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 mt-4">
                    <button data-bs-toggle="modal" data-bs-target="#deleteModal" type="button" class="align-self-start text-white shadow-none btn bg-danger col-lg-4 col-md-5 col-5 text-sm font-weight-bold m-0">
                        <?php _e('Reset votes', 'psx-poll-survey-plugin'); ?> <i class="fas fa-trash text-white fa-md ms-2"></i>
                    </button>
                    <span style="font-size: 11px;">(Be careful!, This action will reset the votes for the current survey)</span>
                </div>

            </div>
        </div>

        <div class="mt-6 p-0 pb-4">
            <h4 class="mb-4 m-0 align-self-start p-0">
                <?php echo ((json_decode(($poll_data_json), true)['title'])); ?></h4>

            <div class="row row-cols-1 row-cols-lg-2 g-2">

                <?php
                // Decode the JSON back to a PHP array
                $questions_decoded = json_decode(($questions_json), true);

                // Check if decoding was successful
                if ($questions_decoded !== null) {
                    foreach ($questions_decoded as $index => $question) {
                        $answers_for_question = $questionAnswers[$question['question_id']];
                ?>
                        <div class="col">
                            <div class="poll-card d-flex flex-column m-0 p-4 rounded-3 border bg-white">
                                <h6 class="mt-2">
                                    <?php echo $index + 1 . ") " . $question['question_text']; ?>
                                </h6>

                                <button title="Show answers" class="btn btn-white text-primary shadow-none m-0 border mt-2 mb-4 col-lg-4 col-md-5 col-4" id="toggle_button"><?php _e('Show answers', 'psx-poll-survey-plugin'); ?></button>


                                <div id="answers_container" class="d-none flex-column gap-3">
                                    <?php foreach ($answers_for_question as $index => $anwser) { ?>
                                        <p class="m-0"><?php echo  $index + 1 . ") " . $anwser; ?></p>
                                    <?php
                                    } ?>
                                </div>

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

        <!-- Reset votes Modal -->
        <div class="modal fade" id="deleteModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-0">
                    <!-- Modal body -->
                    <div class="modal-body">
                        <p class="p-2 m-0">
                            <?php _e('Are you sure you want reset votes for this survey?', 'psx-poll-survey-plugin'); ?>
                        </p>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer d-flex justify-content-start">
                        <button id="confirm_delete" type="button" class="btn btn-danger text-white" data-bs-dismiss="modal" data-poll-id="<?php echo $poll_id ?>">
                            <?php _e('Reset', 'psx-poll-survey-plugin'); ?>

                            <i class="fas fa-trash text-xs text-white m-1"></i>
                        </button>
                        <button type="button" class="btn bg-transparent text-danger border-danger shadow-none border" data-bs-dismiss="modal">
                            <?php _e('Cancel', 'psx-poll-survey-plugin'); ?>

                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const chart_container = document.getElementById("chart-container")
        const totalPercentages_json = JSON.parse(chart_container.getAttribute("data-json-data"));


        const datesArray = totalPercentages_json.map(item => item.date);

        const dayStrings = datesArray.map(dateString => {
            const date = new Date(dateString);
            // Use the toLocaleDateString method to get the day string
            return date.toLocaleDateString('en-US', {
                weekday: 'long'
            }); // Adjust the locale as needed
        });

        // Extract 'vote_count' values into a separate array
        const voteCountArray = totalPercentages_json.map(item => parseInt(item.vote_count));

        var ctx = document.getElementById("chart-bars").getContext("2d");

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: dayStrings,
                datasets: [{
                    label: "Votes",
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    backgroundColor: "#fff",
                    data: voteCountArray,
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
                            drawTicks: true,
                        },
                        ticks: {
                            suggestedMin: 1,
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
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                        },
                        ticks: {
                            display: true,
                            color: "#fff",

                        },
                    },
                },
            },
        });



        const confirm_delete = document.getElementById("confirm_delete");

        confirm_delete.addEventListener("click", () => {
            jQuery.ajax({
                url: my_ajax_object.ajaxurl,
                type: "POST",
                data: {
                    action: "PSX_delete_poll_response",
                    poll_id: confirm_delete.getAttribute("data-poll-id"),
                },
                success: function() {
                    setTimeout(() => {
                        window.location.reload()
                    }, 500)
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });

        // toggle answers

        // Get references to the button and the answers container
        const pollCards = document.querySelectorAll('.poll-card');

        pollCards.forEach((card) => {
            const toggleButton = card.querySelector('#toggle_button');
            const answersContainer = card.querySelector('#answers_container');

            // Add a click event listener to the button
            toggleButton.addEventListener('click', function() {
                // Toggle the 'hidden-answers' class on the answers container to show/hide it
                answersContainer.classList.toggle('hidden-answers');
            });
        });
    </script>



</body>

</html>