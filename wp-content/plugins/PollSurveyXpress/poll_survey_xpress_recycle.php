<?php
global $wpdb;
$table_name = $wpdb->prefix . 'polls_psx_polls';
$statuses = array('archived'); // List of statuses to display
$polls = $wpdb->get_results("SELECT * FROM $table_name WHERE status IN ('" . implode("','", $statuses) . "')");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Recycle pin</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</head>

<style>
    .gray-row:nth-child(even) {
        background-color: rgba(250, 250, 250, 0.9) !important;
    }

    thead {
        background-color: #EEE !important;
    }

    thead tr th {
        font-weight: 900;
        color: #111 !important;
    }
</style>

<body>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <div class="container-fluid py-4">
            <div class="py-4">
                <div class="row">
                    <div class="col-lg-12 col-xxl-10 mx-auto">

                        <div class="w-100 pb-0 d-flex align-items-center justify-content-between mb-6">
                            <h4 class="fw-bolder m-0 p-0">Archived Surveys</h4>

                            <div class="d-flex gap-2 align-items-center m-0 p-0">
                                <a href="<?php echo admin_url('admin.php?page=poll-survey-xpress-surveys'); ?>" class="btn btn-dark m-0">All surveys
                                    <i style="cursor: pointer" class="fas fa-square-poll-vertical text-white ms-2 text-lg"></i>

                                </a>
                            </div>
                        </div>

                        <div class="p-0 pt-0 border rounded-3">
                            <div class="table-responsive p-0 bg-white rounded-3">
                                <table class="table align-items-center mb-0 col-lg-12 col-xxl-10 mx-auto rounded-3">
                                    <thead class="p-4 ">
                                        <tr>
                                            <th class="text-uppercase text-center text-xxs font-weight-bolder opacity-7 p-4 w-fit">
                                                ID
                                            </th>

                                            <th class=" text-uppercase text-xxs font-weight-bolder opacity-7 p-4 ps-2">
                                                Title
                                            </th>

                                            <th class="text-uppercase text-xxs text-center font-weight-bolder opacity-7 p-4">
                                                Status
                                            </th>

                                            <th class=" text-uppercase text-xxs text-center font-weight-bolder opacity-7 p-4">
                                                End Date
                                            </th>

                                            <th class=" text-uppercase text-xxs text-center font-weight-bolder opacity-7 p-4">
                                                Template
                                            </th>
                                            <th class=" text-uppercase text-xxs text-center font-weight-bolder opacity-7 p-4 p-0">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($polls as $poll) { ?>
                                            <tr class="gray-row" id="survey_data" data-card-id=<?php echo $poll->poll_id; ?>>
                                                <td class="align-middle text-center"><?php echo $poll->poll_id; ?></td>

                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0 m-0">
                                                        <?php echo $poll->title; ?>
                                                    </p>
                                                </td>

                                                <td class="align-middle text-sm text-center">
                                                    <p class="badge badge-sm m-0 bg-gradient-warning">
                                                        <?php echo ucfirst($poll->status); ?>
                                                    </p>
                                                </td>

                                                <td class="align-middle text-sm text-center">
                                                    <p class="text-xs font-weight-bold mb-0 m-0">
                                                        <?php echo $poll->end_date; ?>
                                                    </p>
                                                </td>

                                                <td class="align-middle text-sm text-center">
                                                    <p class="text-xs font-weight-bold mb-0 m-0 text-center">
                                                        <?php echo $poll->template; ?>
                                                    </p>
                                                </td>

                                                <td class="text-center d-flex align-items-center justify-content-center px-0 p-4 gap-lg-3 gap-md-2 gap-1">
                                                    <i id="restore_btn" class="restoreButton fas fa-undo text-sm text-dark" style="cursor: pointer"></i>

                                                    <i id="delete_btn" style="cursor: pointer" class="deleteButton fas fa-trash text-sm text-danger" data-card-id=<?php echo $poll->poll_id; ?> data-bs-toggle="modal" data-bs-target="#deleteModal"></i>
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
                        Are you sure you want to permenently delete this survey?
                    </p>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer d-flex justify-content-start">
                    <button id="confirm_delete" type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">
                        Delete
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
            const restoreButtons = document.querySelectorAll(".restoreButton");
            const deleteButtons = document.querySelectorAll(".deleteButton");
            const confirm_delete = document.getElementById("confirm_delete");

            restoreButtons.forEach(button => {
                button.addEventListener("click", function(event) {
                    const row = event.target.closest("tr");
                    const dataCardId = row.getAttribute("data-card-id");
                    jQuery.ajax({
                        url: my_ajax_object.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'PSX_restore_poll',
                            poll_id: dataCardId
                        },
                        success: function() {
                            const restoredPollId = parseInt(dataCardId);
                            const rowToRemove = document.querySelector(
                                `tr[data-card-id="${restoredPollId}"]`);

                            if (rowToRemove) {
                                rowToRemove.remove(); // Remove the row from the table
                            } else {
                                console.log(
                                    `Row with data-card-id ${restoredPollId} not found.`
                                );
                            }
                        },

                    });
                });
            });

            let id;
            deleteButtons.forEach(button => {
                button.addEventListener("click", function(event) {
                    const dataCardId = button.getAttribute("data-card-id");
                    console.log("Delete data-card-id:", dataCardId);
                    id = dataCardId;
                });
            });

            confirm_delete.addEventListener("click", () => {
                const row = event.target.closest("tr");
                jQuery.ajax({
                    url: my_ajax_object.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'PSX_permenant_delete',
                        poll_id: id
                    },
                    success: function() {
                        console.log("Deleted");
                        const deletePollId = parseInt(id);
                        const rowToRemove = document.querySelector(
                            `tr[data-card-id="${deletePollId}"]`);

                        if (rowToRemove) {
                            rowToRemove.remove(); // Remove the row from the table
                        } else {
                            console.log(
                                `Row with data-card-id ${deletePollId} not found.`
                            );
                        }
                    },

                });
            })

        });
    </script>


    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
</body>

</html>