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
var_dump($result_data);

//Adding code to get perecentage of each answer


    $poll_questions = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT question_id FROM {$wpdb->prefix}polls_psx_survey_questions WHERE poll_id = %d",
            $poll_id
        )
    );
    $response_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT response_id FROM {$wpdb->prefix}polls_psx_survey_responses WHERE poll_id = %d",
            $poll_id
        )
    );
    $responses_data = array();
    foreach ($response_ids as $id) {
        $response_data = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT question_id, answer_id FROM {$wpdb->prefix}polls_psx_survey_responses_data WHERE response_id = %s",
                $id
            )
        );
        $responses_data[] = $response_data;
    }
    $answers_for_each_question = array();
    foreach ($poll_questions as $question) {
        //get the answers for each question
        $question_id = $question->question_id;
        $question_answers = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT answer_id FROM {$wpdb->prefix}polls_psx_survey_answers WHERE question_id = %d",
                $question_id
            )
        );
        $answers_for_each_question[$question_id] = $question_answers;
    }
    $allAnswerChoices = array();
    foreach ($poll_questions as $question) {
        $questionId = $question->question_id;
        $questionAnswers = $answers_for_each_question[$questionId];
        $allAnswerChoices[$questionId] = array_column($questionAnswers, 'answer_id');
    }
    $chosenAnswerChoices = array();
    foreach ($responses_data as $response) {
        foreach ($response as $answer) {
            $questionId = $answer->question_id;
            $answerId = $answer->answer_id;
            $chosenAnswerChoices[$questionId][$answerId] = true;
        }
    }


    $answerCounts = array();
    $answeredQuestions = array();
    foreach ($responses_data as $response) {
        foreach ($response as $answer) {
            $questionId = $answer->question_id;
            $answerId = $answer->answer_id;

            if (!isset($answerCounts[$questionId])) {
                $answerCounts[$questionId] = array();
                $answeredQuestions[$questionId] = array(); // Keep track of answered questions
            }

            if (!isset($answerCounts[$questionId][$answerId])) {
                $answerCounts[$questionId][$answerId] = 0;
                $answeredQuestions[$questionId][] = $answerId;
            }

            $answerCounts[$questionId][$answerId]++;
        }
    }

    $questions = array();
    foreach ($answerCounts as $questionId => $answerData) {
        if (!isset($questions[$questionId])) {
            $questions[$questionId] = array();
        }

        $answeredAnswers = $answeredQuestions[$questionId]; // Maintain order of creation
        foreach ($answeredAnswers as $answerId) {
            if (!isset($questions[$questionId][$answerId])) {
                $questions[$questionId][$answerId] = 0;
            }

            $questions[$questionId][$answerId] += $answerCounts[$questionId][$answerId];
        }
    }

    // Calculate the total response count
    $totalResponses = count($responses_data);

    // Calculate percentages including unanswered questions
    $percentages = array();
    foreach ($allAnswerChoices as $questionId => $answerChoices) {
        $totalAnswers = count($answerChoices);
        $percentages[$questionId] = array();

        foreach ($answerChoices as $answerId) {
            if (isset($chosenAnswerChoices[$questionId][$answerId])) {
                $count = isset($answerCounts[$questionId][$answerId]) ? $answerCounts[$questionId][$answerId] : 0;
            } else {
                $count = 0;
            }

            $percentage = ($totalResponses > 0) ? (($count / $totalResponses) * 100) : 0;
            $formattedPercentage = number_format($percentage, 2);
            $percentages[$questionId][$answerId] = $formattedPercentage;
        }
    }

    // Calculate the total percentage for each question
    $totalPercentages = array();
    foreach ($percentages as $questionId => $answerData) {
        $totalPercentage = array_sum($answerData);
        $totalPercentages[$questionId] = number_format($totalPercentage, 2);
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

<style>
    .alpha-numeric {
        list-style: upper-alpha;
    }
</style>


<body>
    <main class="container-fluid main-content position-relative max-height-vh-100 h-100">

        <div class="d-flex align-items-center gap-2 my-4">
            <a href="<?php echo admin_url('admin.php?page=poll-survey-xpress-surveys'); ?>" class="m-0 text-dark"><?php _e('Home', 'psx-poll-survey-plugin'); ?></a>
            <i class="fas fa-angle-right"></i>
            <h6 class="font-weight-bolder mb-0 p-0 "><?php _e('Multiple Choice Survey View', 'psx-poll-survey-plugin'); ?></h6>
        </div>

        <div class="bg-white mt-4 border rounded-3">
            <div class="row row-cols-1 row-cols-lg-2 g-4 ">
                <div class="col mb-4">
                    <div class="card-header pb-0">
                        <h6 class="mb-2 mb-0 "><?php _e('Poll/survey Analysis', 'psx-poll-survey-plugin'); ?></h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="bg-gradient-dark border-radius-lg py-3 mb-3">
                            <div class="chart">
                                <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                            </div>
                        </div>


                        <div class="d-flex align-items-center justify-content-between flex-wrap mt-4">
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
                            <button data-bs-toggle="modal" data-bs-target="#deleteModal" type="button" class="align-self-start text-white shadow-none btn bg-danger col-lg-5 col-md-6 col-4 text-sm font-weight-bold m-0">
                                <?php _e('Reset votes', 'psx-poll-survey-plugin'); ?> <i class="fas fa-trash text-white fa-md ms-2"></i>
                            </button>
                            <span style="font-size: 11px;">(Be careful!, This action will reset the votes for the current survey)</span>
                        </div>

                    </div>
                </div>
                <div id="result-container" class="position-absolute align-items-center justify-content-between gap-2 w-75 bottom-0" data-question-id="' . $question['question_id'] . '" data-answer-id="' . $answer['answer_id'] . '">
                    <div class="progress-bar bg-transparent transition-progress-bar">
                        <p style="width: 0%;" class="percentage-bar m-0 bg-primary rounded-2"></p>
                        <p style="width: 100%; background-color: #DDD;" class="m-0 rounded-2"></p>
                    </div>
                    <p style="font-size: 12px" class="percentage-value text-primary m-0 fw-bolder"></p>
                </div>
                <div class="col">
                    <div class="z-index-2">
                        <div class="card-header pb-0">
                            <h6><?php _e('Poll/survey overview', 'psx-poll-survey-plugin'); ?></h6>
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
            <h4 class="mb-4 align-self-start p-0">
                <?php echo ((json_decode(($poll_data_json), true)['title'])); ?></h4>

            <div class="row row-cols-1 row-cols-lg-2 g-2 ">
                <?php
                // Decode the JSON back to a PHP array
                $questions_decoded = json_decode(($questions_json), true);

                // Check if decoding was successful
                if ($questions_decoded !== null) {
                    foreach ($questions_decoded as $index => $question) {
                ?>
                        <div class="col">
                            <div class="position-relative flex-column gap-2 border rounded-3 bg-white m-0 p-4 ">
                                <h6 class="mt-2">
                                    <?php echo $index + 1 . ") " . $question['question_text']; ?>
                                </h6>
                                <?php
                                $answers_query = $wpdb->prepare("
                                SELECT * FROM {$wpdb->prefix}polls_psx_survey_answers
                                WHERE question_id = %d and poll_id = %d
                                ", $question['question_id'], (json_decode(($poll_data_json), true)['poll_id']));


                                $answers = $wpdb->get_results($answers_query);
                                $questions_with_answers_json = json_encode($answers);
                                $answers = json_decode(($questions_with_answers_json), true); ?>

                                <ul>
                                    <?php foreach ($answers as $answer) { ?>
                                        <li class="alpha-numeric m-0 text-sm ">
                                            <?php echo $answer['answer_text'];  ?>
                                        </li>
                                    <?php } ?>
                                </ul>
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
                    console.log('response');

                    setTimeout(() => {
                        window.location.reload()
                    }, 500)
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });
    </script>

    <script> 
    
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
                percentage_bar.style.cssText = `width:${percentageValue}% !important; z-index:5; background-color:${randomBgColor} !important;`;
                percentage_value.textContent = `${percentageValue.toFixed(2)}%`;
              }
            }
        </script>
</body>

</html>