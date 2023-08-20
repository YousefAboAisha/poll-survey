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
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">Settings</h6>
                </nav>
            </div>
        </nav>

        <div class="container-fluid py-1 px-4 d-flex flex-column gap-3">
            <form class="card-body pt-sm-3 pt-0" method="post">
                <div class="form-group d-flex flex-column">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="email" name="email" <?php if (get_checkbox_value('email') === '1') echo 'checked'; ?> />
                        <label class="form-check-label" for="email">
                            Email on survey deactivation
                        </label>
                    </div>

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

                <button type="submit" name="save_changes" class="btn btn-primary mt-4">
                    Save changes
                </button>
            </form>
        </div>
    </main>
</body>

</html>