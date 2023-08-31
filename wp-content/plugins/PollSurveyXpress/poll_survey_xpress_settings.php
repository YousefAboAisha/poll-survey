<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php _e('Settings', 'psx-poll-survey-plugin'); ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</head>


<body>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-4 border-radius-lg">
        <div class="container-fluid mx-auto ">
            <div class="w-100 pb-0 d-flex align-items-center">
                <h4 class="fw-bolder m-0 p-0"><?php _e('General settings', 'psx-poll-survey-plugin'); ?></h4>
            </div>

            <form class="p-4 d-flex flex-column bg-white mt-4 rounded-3 border" method="post">
                <input type="hidden" id="my-ajax-nonce" value="<?php echo wp_create_nonce('my_ajax_nonce'); ?>" />
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="email" name="email" <?php echo get_option('PSX_email') ?  "checked" : '' ?>>
                        <label class="form-check-label" for="email">
                            <?php _e('Email on survey deactivation', 'psx-poll-survey-plugin'); ?>
                        </label>
                    </div>

                    <div class="col-12 col-sm-8 col-md-6 col-lg-5 ">
                        <input id="email_input" type="text" class="form-control border rounded-1 text-dark mt-2 mb-4 p-2" value="ashrafweb@gmail.com" placeholder="Enter your email" disabled="" style="display: none;">
                    </div>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="gdpr" name="gdpr" <?php echo get_option('PSX_gdpr') ? "checked" : '' ?>>
                        <label class="form-check-label" for="gdpr">
                            <?php _e('General Data Protection Regulations(GDBR) integrity', 'psx-poll-survey-plugin'); ?>
                        </label>
                    </div>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="clear_data" name="clear_data" <?php echo get_option('PSX_clear_data') ? "checked" : '' ?>>
                        <label class="form-check-label" for="clear_data">
                            <?php _e('Clear tables data when plugin uninstalled', 'psx-poll-survey-plugin'); ?>
                        </label>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 mt-2">
                    <h4><?php _e('Front-end messages', 'psx-poll-survey-plugin'); ?></h4>

                    <div class="col-12 col-sm-8 col-md-6 col-lg-5 ">
                        <p class="m-0"><?php _e('When poll status is (inactive)', 'psx-poll-survey-plugin'); ?></p>
                        <p class="m-0 mb-2" style="font-size:10px"><?php _e('(This message will be shown to the user, when the poll is inactive) ', 'psx-poll-survey-plugin'); ?></p>
                        <input id="status_message" type="text" class="form-control border rounded-1 text-dark mb-2 p-2" placeholder="Enter inactive message..." value="<?php echo get_option('PSX_status_message') ?>">
                    </div>

                    <div class="col-12 col-sm-8 col-md-6 col-lg-5 ">
                        <p class="m-0"><?php _e('When poll is (expired)', 'psx-poll-survey-plugin'); ?></p>
                        <p class="m-0 mb-2" style="font-size:10px"><?php _e('(This message will be shown to the user, when the poll is date expired)', 'psx-poll-survey-plugin'); ?> </p>
                        <input id="expire_message" type="text" class="form-control border rounded-1 text-dark mb-2 p-2" value="<?php echo get_option('PSX_expire_message') ?>" placeholder="Enter expire message...">
                    </div>

                </div>

                <button type="button" name="save_changes" id="save_changes" class="align-self-start m-0 text-white btn bg-primary col-lg-2 col-md-4 col-5 text-sm font-weight-bold mt-4">
                    <?php _e('Save changes', 'psx-poll-survey-plugin'); ?>

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
        const status_message = document.getElementById("status_message");
        const expire_message = document.getElementById("expire_message");

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
        save_changes.addEventListener("click", function() {
            save_changes.disabled = true;
            save_changes.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            finalObj = {
                email: emailRadioButton.checked,
                gdpr: document.getElementById("gdpr").checked,
                clear_data: document.getElementById("clear_data").checked,
                admin_email: email_input.value,
                expire_message: expire_message.value,
                status_message: status_message.value,
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