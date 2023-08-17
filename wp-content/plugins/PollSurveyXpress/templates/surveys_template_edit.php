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

$poll_data_json = json_encode($poll_data);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</head>


<body class="g-sidenav-show bg-gray-100">
    <main class="col-lg-6 col-md-8 col-10 mx-auto main-content position-relative max-height-vh-100 h-100 mt-4 border-radius-lg">
        <nav aria-label="breadcrumb">
            <h6 class="font-weight-bolder mb-0">Template Settings</h6>
        </nav>


        <form id="update_form" data-form-id=<?php echo ($poll_id) ?> class="d-flex flex-column card p-4 rounded-3 border">
            <div>
                <label>Change plugin Theme</label>

                <div class="d-flex align-items-center px-2 gap-2">
                    <span class="text-sm fw-bold"> Bg color</span>
                    <input type="color" class="form-control form-control-color border-0 p-0 w-10 me-2" id="bg_color" value="<?php echo $poll_data->bgcolor; ?>" />
                    <span class="text-sm fw-bold"> Text color </span>
                    <input type="color" class="form-control form-control-color border-0 p-0 w-10" id="text_color" value="<?php echo $poll_data->color; ?>" />
                </div>
            </div>

            <div class="d-flex flex-column gap-2 mt-2 mb-2 px-2">
                <div>
                    <label class="m-0">Starts</label>
                    <input type="datetime-local" class="form-control border rounded-1 p-1" id="start_date" placeholder="Select a date" value="<?php echo $poll_data->start_date; ?>" />
                </div>

                <div>
                    <label class="m-0">Ends</label>

                    <input type="datetime-local" class="form-control border rounded-1 p-1" id="end_date" placeholder="Select a date" value="<?php echo $poll_data->end_date; ?>" />
                </div>
            </div>

            <div class="px-2">
                <div class="form-group d-flex flex-column">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="active_plugin" <?php echo $poll_data->status === 'active' ? 'checked' : ''; ?> />
                        <label class="form-check-label" for="active_plugin">
                            Activate the survey
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="share_plugin" <?php echo $poll_data->sharing ? 'checked' : ''; ?> />
                        <label class="form-check-label" for="share_plugin">
                            Share with my friends
                        </label>
                    </div>

                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="show_results" <?php echo empty($poll_data->real_time_result_text) ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="show_results">
                                Show real-time results
                            </label>
                        </div>

                        <input type="text" class="form-control border rounded-1 p-1 mt-2" placeholder="Add Thank Meesage" value="<?php echo $poll_data->real_time_result_text; ?>" id="show_results_input" />
                    </div>

                    <div class="d-flex align-items-center justify-content-start gap-2 mt-3">
                        <label class="form-check-label w-45">
                            Show results after</label>
                        <input type="number" class="form-control border rounded-1 p-1 w-55" placeholder="Number of votes" id="min_votes_input" value="<?php echo $poll_data->min_votes; ?>" />
                    </div>

                    <div class="w-100 d-flex flex-column align-items-start mt-2 gap-2">
                        <input type="text" class="form-control border rounded-1 p-1" placeholder="Add CTA button title" id="cta_input" value="<?php echo $poll_data->cta_Text; ?>" />
                        <button id="cta_button" type="button" class="btn btn-dark m-0 mt-1">
                            <?php echo $poll_data->cta_Text; ?>
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" id="save_button" class="text-white btn bg-primary col-12 mx-auto text-sm font-weight-bold m-0 mt-3">
                Save
            </button>
        </form>
    </main>


    <script>
        // Plugin Settings variables
        const update_form_id = document.getElementById("update_form").getAttribute("data-form-id");
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
        let settingObj = {}

        save_button.addEventListener("click", (e) => {
            e.preventDefault();
            settingObj = {
                poll_id: update_form_id,
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
                sharing: share_plugin.checked,
                real_time_result_text: show_results_input.value,
                real_time_check: show_results.checked,
                min_votes: min_votes_input.value,
            };

            console.log(settingObj);

            // if (settingObj != {} || !settingObj) {
            //     jQuery.ajax({
            //         type: "POST",
            //         url: my_ajax_object.ajaxurl,
            //         data: {
            //             action: "save_poll_Multiple_data",
            //             poll_data: JSON.stringify(settingObj),
            //         },
            //         success: function(shortcode) {
            //             console.log("Done");
            //             // window.location.reload();
            //         },
            //         error: function(error) {
            //             console.error("Error:", error);
            //         },
            //     });
            // }
        });
    </script>

</body>