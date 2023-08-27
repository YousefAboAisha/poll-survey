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
                <input type="hidden" id="my-ajax-nonce" value="<?php echo wp_create_nonce('my_ajax_nonce'); ?>" />
                <div class="form-group d-flex flex-column">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="email" name="email" <?php if (get_option('PSX_email')) echo 'checked'; ?> />
                        <label class="form-check-label" for="email">
                            Email on survey deactivation
                        </label>

                    </div>

                    <input id="email_input" type="text" class="form-control border rounded-1 w-25 text-dark" value="<?php echo get_option('admin_email') ?>">

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="gdpr" name="gdpr" <?php if (get_option('PSX_gdpr')) echo 'checked'; ?> />
                        <label class="form-check-label" for="gdpr">
                            General Data Protection Regulations(GDBR) integrity
                        </label>
                    </div>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="clear_data" name="clear_data" <?php if (get_option('PSX_clear_data')) echo 'checked'; ?> />
                        <label class="form-check-label" for="clear_data">
                            Clear tables data when plugin uninstalled
                        </label>
                    </div>
                </div>

                <button type="button" name="save_changes" id="save_changes" class="align-self-start m-0 text-white btn bg-primary col-lg-2 col-md-4 col-5 text-sm font-weight-bold mt-2">
                    Save changes
                </button>
            </form>


        </div>
    </main>



    <script>
        const nonce = document.getElementById("my-ajax-nonce").value;
        const emailRadioButton = document.getElementById("email");
        const isEmailChecked = document.getElementById("email").checked;
        const email_input = document.getElementById("email_input");
        const save_changes = document.getElementById("save_changes");

        // Define the function to handle the initial state
        // Define the function to handle the initial state
        function setInitialEmailState() {
            if (emailRadioButton.checked) {
                email_input.style.display = "block";
                email_input.disabled = false;
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
                email_input.disabled = false;
            } else {
                email_input.style.display = "none";
                email_input.disabled = true;
            }
        });
        finalObj = {
            email: emailRadioButton.checked,
            gdpr: document.getElementById("gdpr").checked,
            clear_data: document.getElementById("clear_data").checked,
            admin_email: email_input.value,
        }
        save_changes.addEventListener("click", function() {
            save_changes.disabled = true;
            save_changes.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            finalObj = {
                email: emailRadioButton.checked,
                gdpr: document.getElementById("gdpr").checked,
                clear_data: document.getElementById("clear_data").checked,
                admin_email: email_input.value,
            }
            console.log(finalObj);
            jQuery.ajax({
                type: "POST",
                url: my_ajax_object.ajaxurl,
                data: {
                    action: 'PSX_save_changes_settings',
                    settings_data: JSON.stringify(finalObj),
                    nonce: nonce,
                },
                success: function(data) {
                    console.log(data);
                    save_changes.textContent = "Save changes";
                    save_changes.disabled = false;

                    // Create a new toast element
                    var toast = document.createElement("div");
                    toast.style = "z-index:1000; right: 10px; bottom: 10px";
                    toast.className = "position-fixed p-2 px-4 bg-success border rounded-2";
                    toast.innerHTML = `
                            <p class="m-0 fw-bold text-xs text-white">
                                Plugin settings updated successfully!
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
                error: function(error) {
                    console.error("Error:", error);
                    save_changes.textContent = "Save changes";
                    save_changes.disabled = false;
                },
            });
        });
    </script>

</body>


</html>