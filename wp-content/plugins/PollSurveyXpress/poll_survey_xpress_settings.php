<?php
function get_checkbox_value($checkbox_id)
{
    return get_option($checkbox_id);
}

// Function to save the checkbox value to wp_options
function update_checkbox_value($checkbox_id, $value)
{
    update_option($checkbox_id, $value);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_changes'])) {
    $checkbox_ids = array('email', 'gdpr', 'clear_data');

    foreach ($checkbox_ids as $checkbox_id) {
        if (isset($_POST[$checkbox_id])) {
            update_checkbox_value($checkbox_id, '1');
        } else {
            update_checkbox_value($checkbox_id, '0');
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Settings</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</head>


<body>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-4 border-radius-lg">
        <div class="container-fluid mx-auto ">
            <div class="w-100 pb-0 d-flex align-items-center">
                <h4 class="fw-bolder m-0 p-0">General settings</h4>
            </div>

            <form class="p-4 d-flex flex-column bg-white mt-4 rounded-3 border" method="post">
                <div class="form-group d-flex flex-column">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="email" name="email" <?php if (get_checkbox_value('email') === '1') echo 'checked'; ?> />
                        <label class="form-check-label" for="email">
                            Email on survey deactivation
                        </label>

                    </div>

                    <input readonly=<?php echo true ?> id="email_input" type="text" class="form-control border rounded-1 w-25 text-dark" value="sadsad dsa dsa d">

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="gdpr" name="gdpr" <?php if (get_checkbox_value('gdpr') === '1') echo 'checked'; ?> />
                        <label class="form-check-label" for="gdpr">
                            General Data Protection Regulations(GDBR) integrity
                        </label>
                    </div>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="clear_data" name="clear_data" <?php if (get_checkbox_value('clear_data') === '1') echo 'checked'; ?> />
                        <label class="form-check-label" for="clear_data">
                            Clear tables data when plugin uninstalled
                        </label>
                    </div>
                </div>

                <button type="submit" name="save_changes" class="align-self-start m-0 text-white btn bg-primary col-lg-2 col-md-4 col-5 text-sm font-weight-bold mt-2">
                    Save changes
                </button>
            </form>
        </div>
    </main>



    <script>
        const emailRadioButton = document.getElementById("email");
        const isEmailChecked = document.getElementById("email").checked;
        const email_input = document.getElementById("email_input");

        // Define the function to handle the initial state
        function setInitialEmailState() {
            if (emailRadioButton.checked) {
                email_input.style.display = "block";
            } else {
                email_input.style.display = "none";
            }
        }

        // Call the function immediately
        setInitialEmailState();

        // Call the function within DOMContentLoaded event
        document.addEventListener('DOMContentLoaded', setInitialEmailState);

        // Event listener for change input state
        emailRadioButton.addEventListener("change", function() {
            if (emailRadioButton.checked) {
                email_input.style.display = "block";
            } else {
                email_input.style.display = "none";
            }
        });
    </script>

</body>


</html>