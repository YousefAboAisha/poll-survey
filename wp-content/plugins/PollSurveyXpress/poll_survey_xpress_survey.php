    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'polls_psx_polls';
    $statuses = array('active', 'inactive'); // List of statuses to display
    $polls = $wpdb->get_results("SELECT * FROM $table_name WHERE status IN ('" . implode("','", $statuses) . "')");
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Survey</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
        <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    </head>


    <body class="g-sidenav-show bg-gray-100">

        <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
            <!-- Navbar -->

            <!-- End Navbar -->
            <div class="container-fluid py-4">
                <div class="py-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-header w-100 pb-0 d-flex align-items-center justify-content-between mb-6">
                                <h6 class="fw-bolder col-4 m-0 p-0">Recent Surveys</h6>
                                <div class="d-flex gap-2 align-items-center m-0 p-0">

                                    <a href="<?php echo admin_url('admin.php?page=poll-survey-xpress-add'); ?>" class="btn btn-dark m-0">New Survey
                                        <i style="cursor: pointer" class="fas fa-add text-white ms-2 text-lg"></i>
                                    </a>
                                    <a href="<?php echo (admin_url('admin.php?page=poll-survey-xpress-recycle')); ?>" class="btn btn-danger m-0">Recycle Bin <i style="cursor: pointer" class="fas fa-trash text-white ms-2"></i></a>
                                </div>
                            </div>

                            <div class="p-0 pt-0 border rounded-3">
                                <div class="table-responsive p-0 bg-white rounded-3">
                                    <table class="table align-items-center mb-0 w-100 rounded-3">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7 w-fit">
                                                    ID
                                                </th>

                                                <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                    Title
                                                </th>

                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Status
                                                </th>

                                                <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Shortcode
                                                </th>

                                                <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    End Date
                                                </th>

                                                <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Template
                                                </th>
                                                <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 p-0">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($polls as $poll) { ?>
                                                <tr id="survey_data" data-card-id=<?php echo $poll->poll_id; ?>>
                                                    <td>
                                                        <p class="text-xs mb-0 m-0 text-center align-middle ">
                                                            <?php echo $poll->poll_id; ?>
                                                        </p>

                                                    </td>

                                                    <td class="align-middle">
                                                        <p class="text-xs mb-0">
                                                            <?php echo $poll->title; ?>
                                                        </p>
                                                    </td>

                                                    <td class="align-middle">
                                                        <span class="badge badge-sm bg-gradient-<?php echo ($poll->status == 'active') ? 'success' : 'danger'; ?>">
                                                            <?php echo ucfirst($poll->status); ?>
                                                        </span>
                                                    </td>

                                                    <td class="align-middle">
                                                        <p class="text-xs mb-0">
                                                            <?php echo $poll->Short_Code; ?>
                                                        </p>
                                                    </td>

                                                    <td class="align-middle">
                                                        <p class="text-xs mb-0">
                                                            <?php echo $poll->end_date; ?>
                                                        </p>
                                                    </td>

                                                    <td class="align-middle">
                                                        <p class="text-xs mb-0">
                                                            <?php echo $poll->template; ?>
                                                        </p>
                                                    </td>
                                                    <!-- Other dynamic data columns here -->

                                                    <td class="d-flex align-items-center px-0 p-4 gap-lg-3 gap-md-2 gap-1">
                                                        <a href="<?php echo admin_url('admin.php?page=show_template_page&template=' . $poll->template . '&poll_id=' . $poll->poll_id); ?>">
                                                            <i class="fas fa-eye text-sm text-dark" aria-hidden="true" style="cursor: pointer"></i>
                                                        </a>
                                                        <a href="<?php echo admin_url('admin.php?page=edit_template_page&template=' . $poll->template . '&poll_id=' . $poll->poll_id); ?>">
                                                            <i class="fas fa-pen text-sm text-dark" aria-hidden="true" style="cursor: pointer"></i>
                                                        </a>

                                                        <i style="cursor: pointer" class="fas fa-trash text-sm text-danger archiveButton" aria-hidden="true" data-bs-toggle="modal" data-bs-target="#deleteModal" data-poll-id="<?php echo $poll->poll_id; ?>"></i>

                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-0">
                    <!-- Modal body -->
                    <div class="modal-body">
                        <p class="p-2 m-0">
                            Are you sure you want to move this survey to trash?
                        </p>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer d-flex justify-content-start">
                        <button id="confirm_delete" type="button" class="btn btn-danger text-white" data-bs-dismiss="modal" id="moveButton">
                            Move
                            <i class="fas fa-trash text-xs text-white m-1"></i>
                        </button>
                        <button type="button" class="btn bg-transparent text-danger border-danger shadow-none border" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const archiveButtons = document.querySelectorAll(".archiveButton");
                let id;
                archiveButtons.forEach(button => {
                    button.addEventListener("click", function(event) {
                        const row = event.target.closest("tr"); // Find the closest row element
                        const dataCardId = row.getAttribute("data-card-id");
                        id = dataCardId;
                    });
                });

                const confirm_delete = document.getElementById("confirm_delete")
                // Delete button
                confirm_delete.addEventListener("click", () => {
                    jQuery.ajax({
                        url: my_ajax_object.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'archive_poll',
                            poll_id: id
                        },
                        success: function() {
                            const archivedPollId = parseInt(
                                id); // Parse the poll_id from the response
                            const rowToRemove = document.querySelector(
                                `tr[data-card-id="${archivedPollId}"]`);

                            if (rowToRemove) {
                                rowToRemove.remove(); // Remove the row from the table
                            } else {
                                console.log(
                                    `Row with data-card-id ${archivedPollId} not found.`);
                            }
                        },
                        error: function(errorThrown) {
                            console.log(errorThrown);
                        }
                    });
                })
            });
        </script>



    </body>



    </html>