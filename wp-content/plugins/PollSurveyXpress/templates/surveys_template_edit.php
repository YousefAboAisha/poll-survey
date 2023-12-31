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


<body>
    <main class="col-xl-5 col-lg-8 col-md-9 col-10 mx-auto main-content position-relative d-flex flex-column justify-content-center align-items-center max-height-vh-100 h-100 mt-4 border-radius-lg">
        <h6 class="font-weight-bolder mb-4 align-self-start">Survey Settings</h6>


        <form id="update_form" data-form-id=<?php echo ($poll_id) ?> class="d-flex flex-column p-4 rounded-3 border w-100 bg-white">
            <input type="hidden" id="my-ajax-nonce" value="<?php echo wp_create_nonce('my_ajax_nonce'); ?>" />

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
                            <input class="form-check-input" type="checkbox" id="show_results" <?php echo empty($poll_data->real_time_result_text) ? 'checked' : ''; ?> onchange="toggleInputState()" />
                            <label class="form-check-label" for="show_results">
                                Show real-time results
                            </label>
                        </div>

                        <input type="text" class="form-control border rounded-1 p-1 mt-2" placeholder="Add Thank Meesage" value="<?php echo $poll_data->real_time_result_text; ?>" id="show_results_input" <?php echo empty($poll_data->real_time_result_text) ? 'disabled' : ''; ?> />
                    </div>

                    <div class="d-flex align-items-center justify-content-start gap-2 mt-3">
                        <label class="form-check-label w-45">
                            Show results after</label>
                        <input type="number" class="form-control border rounded-1 p-1 w-55" placeholder="Number of votes" id="min_votes_input" value="<?php echo $poll_data->min_votes; ?>" />
                    </div>

                    <div class="w-100 d-flex flex-column align-items-start mt-2 gap-2">
                        <input type="text" class="form-control border rounded-1 p-1" placeholder="Add CTA button title" id="cta_input" value="<?php echo $poll_data->cta_Text; ?>" />
                        <button onclick="(e)=> e.preventDefault();" id="cta_button" type="button" class="btn btn-dark m-0 mt-1">
                            <?php echo $poll_data->cta_Text; ?>
                        </button>
                    </div>
                </div>
            </div>

            <button id="save_button" class="text-white btn bg-primary col-12 mx-auto text-sm font-weight-bold m-0 mt-3">
                Save
            </button>
        </form>
    </main>

    <script>
        // Function to toggle input state based on checkbox status
        function toggleInputState() {
            var inputField = document.getElementById("show_results_input");
            var checkbox = document.getElementById("show_results");

            if (checkbox.checked) {
                inputField.setAttribute("disabled");
            } else {
                inputField.removeAttribute("disabled", "disabled");
            }
        }

        // Call the function initially to set the input state based on checkbox status
        toggleInputState();
    </script>


    <script>
        jQuery(document).ready(function(jQuery) {

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
            var nonce = jQuery('#my-ajax-nonce').val();
            save_button.addEventListener("click", (e) => {
                e.preventDefault();
                save_button.disabled = true;
                save_button.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

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

                if (settingObj != {} || !settingObj) {
                    jQuery.ajax({
                        type: "POST",
                        url: my_ajax_object.ajaxurl,
                        data: {
                            action: "PSX_update_poll_settings",
                            poll_data: JSON.stringify(settingObj),
                            nonce: nonce,
                        },
                        success: function() {
                            console.log("Done");
                            save_button.textContent = "Save";
                            save_button.disabled = false;

                            // Create a new toast element
                            var toast = document.createElement("div");
                            toast.style = "z-index:1000; right: 10px; bottom: 10px";
                            toast.className = "position-fixed p-2 px-4 bg-success border rounded-2";
                            toast.innerHTML = `
                            <p class="m-0 fw-bold text-xs text-white">
                            Updated survey settings successfully!
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