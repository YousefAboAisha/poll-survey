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

<body class="g-sidenav-show bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <div class="container-fluid py-4">
            <div class="py-4">
                <div class="row">
                    <div class="col-12">
                            <h6 class="fw-bolder w-50 m-0 p-0 mb-4">Recent Surveys</h6>

                        <div class="p-0 pt-0 border rounded-3">
                        <div class="table-responsive p-0 bg-white rounded-3">
                                <table class="table align-items-center mb-0 m-0">
                                <thead>
                                            <tr>
                                                <th
                                                    class="text-uppercase text-center text-secondary text-xxs font-weight-bolder ">
                                                    ID
                                                </th>

                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder">
                                                    Title
                                                </th>

                                                <th
                                                    class="text-uppercase text-center text-secondary text-xxs font-weight-bolder">
                                                    Status
                                                </th>


                                                <th
                                                    class=" text-uppercase text-center text-secondary text-xxs font-weight-bolder">
                                                    End Date
                                                </th>

                                                <th
                                                    class=" text-center text-uppercase text-center text-secondary text-xxs font-weight-bolder">
                                                    Template
                                                </th>
                                                <th
                                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder p-0">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>

                                    <tbody>
                                        <?php foreach ($polls as $poll) { ?>
                                        <tr id="survey_data" data-card-id=<?php echo $poll->poll_id; ?>>
                                            <td class="align-middle text-center"><?php echo $poll->poll_id; ?></td>

                                            <td>
                                                <p class="text-xs font-weight-bold mb-0 m-0">
                                                    <?php echo $poll->title; ?>
                                                </p>
                                            </td>

                                            <td class="align-middle text-sm text-center">
                                                    <p
                                                        class="badge badge-sm m-0 bg-gradient-warning">
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
                                                <i id="restore_btn" class="restoreButton fas fa-undo text-sm text-dark"  
                                                    style="cursor: pointer"></i>

                                                <i id="delete_btn" style="cursor: pointer" class="deleteButton fas fa-trash text-sm text-danger" data-card-id=<?php echo $poll->poll_id; ?>
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"></i>
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
                    <button type="button" class="btn bg-transparent text-danger border-danger shadow-none border"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const restoreButtons = document.querySelectorAll(".restoreButton");
        const deleteButtons = document.querySelectorAll(".deleteButton");
        const confirm_delete = document.getElementById("confirm_delete");
        
        restoreButtons.forEach(button => {
            button.addEventListener("click", function (event) {
                const row = event.target.closest("tr");
                const dataCardId = row.getAttribute("data-card-id");
                console.log("Restore data-card-id:", dataCardId);
                
                // Handle restore action here
            });
        });
        
        let id;
        deleteButtons.forEach(button => {
            button.addEventListener("click", function (event) {
                const dataCardId = button.getAttribute("data-card-id");
                console.log("Delete data-card-id:", dataCardId);
                id = dataCardId;
                // Handle delete action here
            });
        });

            confirm_delete.addEventListener("click",()=>{
                console.log(id);
            }) 
        
    });
</script>

   
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
</body>

</html>