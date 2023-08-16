<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit survey</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</head>


<body class="g-sidenav-show bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 mt-4 border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl"  >
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm">
                            <a class="opacity-5 text-dark"  >Pages</a>
                        </li>
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                            Surveys
                        </li>
                    </ol>
                    <h6 class="font-weight-bolder mb-0">Survey #1</h6>
                </nav>
            </div>
        </nav>

        <div
            class="col-lg-6 col-md-8 col-10 mx-auto d-flex flex-column justify-content-center align-items-center gap-3 my-5">
            <div class="w-100 d-flex justify-content-between align-items-center">
                <h5 id="surveyTitle" class="">Poll/Survey title</h5>
                <button data-bs-toggle="modal" data-bs-target="#editTitleModal"
                    class="btn shadow-none bg-transparent border-0 m-0 p-0">
                    <i class="fas fa-pen text-lg text-dark" aria-hidden="true" title="Edit title"></i>
                </button>
            </div>

            <!-- Final output cards -->
            <div class="d-flex flex-column gap-3">
                <!-- Start of the output card -->
                <div class="position-relative flex-column gap-2 border rounded-3 bg-white shadow-sm p-4">
                    <div
                        class="position-absolute top-0 start-0 bg-dark d-flex justify-content-center align-content-center p-1 col-1 text-white rounded-1">
                        1
                    </div>

                    <div
                        class="position-absolute top-1 end-1 d-flex justify-content-center align-content-center gap-3 p-2 col-2 rounded-1">
                        <i style="cursor: pointer" class="fas fa-pen text-sm ms-1 text-dark" aria-hidden="true"
                            data-bs-toggle="modal" data-bs-target="#editModal"></i>
                        <i style="cursor: pointer" class="fas fa-trash text-sm ms-1 text-danger" aria-hidden="true"
                            data-bs-toggle="modal" data-bs-target="#deleteModal"></i>
                    </div>

                    <p class="mt-4">
                        This modal title This modal title This modal title This modal This
                        modal title
                    </p>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radioGroup" id="radio1" />
                        <label class="form-check-label" for="radio1">
                            Default radio
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radioGroup" id="radio2" />
                        <label class="form-check-label" for="radio2">
                            Default checked radio
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radioGroup" id="radio3" />
                        <label class="form-check-label" for="radio3">
                            Default checked radio
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radioGroup" id="radio4" />
                        <label class="form-check-label" for="radio4">
                            Default checked radio
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit poll/survey title -->
    <div class="modal fade" id="editTitleModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal body -->
                <div class="modal-body d-flex flex-column gap-2">
                    <label class="form-check-label" for="radio1">
                        Edit poll/survey title
                    </label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control" placeholder="Edit poll/survey title"
                            id="surveyTitleValue" />
                        <button type="button"
                            class="btn btn-primary d-flex justify-content-center align-items-center gap-2 m-0"
                            data-bs-dismiss="modal" id="updateButton">
                            Edit
                            <i class="fas fa-pen text-sm" aria-hidden="true" title="Edit title"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit survey/modal modal -->
    <div class="modal fade" id="editModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Poll/Survey title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body d-flex flex-column gap-2">
                    <input type="text" class="form-control mb-2" placeholder="Edit Poll/Survey title" />

                    <div class="form-check d-flex align-items-center">
                        <input type="text" class="form-control" id="surveyTitle" placeholder="Option title #1" />
                    </div>

                    <div class="form-check d-flex align-items-center">
                        <input type="text" class="form-control" placeholder="Option title #2" />
                    </div>

                    <div class="form-check d-flex align-items-center">
                        <input type="text" class="form-control" placeholder="Option title #3" />
                    </div>

                    <div class="form-check d-flex align-items-center">
                        <input type="text" class="form-control" placeholder="Option title #4" />
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer d-flex justify-content-start">
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-2"
                        data-bs-dismiss="modal">
                        Update
                        <i class="fas fa-pen text-sm" aria-hidden="true" title="Get shortlink"></i>
                    </button>
                    <button type="button" class="btn bg-transparent text-dark shadow-none border"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-0">
                <!-- Modal body -->
                <div class="modal-body">
                    <p class="p-2 m-0">
                        Are you sure you want to delete this Question?
                    </p>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer d-flex justify-content-start">
                    <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">
                        Delete
                    </button>
                    <button type="button" class="btn bg-transparent text-danger border-danger shadow-none border"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ShortLink modal -->
    <div class="modal fade" id="linkModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Get Poll/Survey shortlink</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body d-flex flex-column gap-2">
                    <input type="text" class="form-control" placeholder="Option title"
                        value="https://docs.google.com/document/d/1TLqlIy0D1UmX-yUiqAEGKhjmCkCkyT6Cg1vfpV76XRY/edit" />
                </div>

                <!-- Modal footer -->
                <div class="modal-footer d-flex justify-content-start">
                    <button type="button" class="btn btn-primary col-4">
                        Copy link
                    </button>
                    <button type="button" class="btn bg-transparent text-dark shadow-none border"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview survey/modal modal -->
    <div class="modal fade" id="myModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Poll/Survey title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body d-flex flex-column gap-2">
                    <p>
                        This modal title This modal title This modal title This modal This
                        modal title
                    </p>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radioGroup" id="radio1" />
                        <label class="form-check-label" for="radio1">
                            Default radio
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radioGroup" id="radio2" />
                        <label class="form-check-label" for="radio2">
                            Default checked radio
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radioGroup" id="radio3" />
                        <label class="form-check-label" for="radio3">
                            Default checked radio
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radioGroup" id="radio4" />
                        <label class="form-check-label" for="radio4">
                            Default checked radio
                        </label>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer d-flex justify-content-start">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        Confirm
                    </button>
                    <button type="button" class="btn bg-transparent text-dark shadow-none border"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--   Core JS Files   -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/chartjs.min.js"></script>

    <!-- Edit title -->
    <script>
    const surveyTitle = document.getElementById("surveyTitle");
    const updateButton = document.getElementById("updateButton");

    updateButton.addEventListener("click", function() {
        var inputValue = document.getElementById("surveyTitleValue");
        if (inputValue.value.trim() !== "") {
            surveyTitle.innerText = inputValue.value;
        }
        inputValue.value = "";
    });
    </script>

    <!-- Add new option -->
    <script>
    const addOptionButton = document.getElementById("addOption");
    const optionInput = document.getElementById("optionInput");
    const radioGroup = document.getElementById("radioGroup");
    const createPollButton = document.getElementById("createPoll");
    const questionTitle = document.getElementById("questionTitle");

    addOptionButton.addEventListener("click", function() {
        const optionTitle = optionInput.value.trim();

        if (optionTitle !== "") {
            const newOption = document.createElement("div");
            newOption.className = "form-check";

            newOption.innerHTML = `
            <input
              class="form-check-input"
              type="radio"
              name="radioGroup"
              id="${optionTitle}"
            />
            <label class="form-check-label" for="${optionTitle}">
              ${optionTitle}
            </label>
          `;

            radioGroup.appendChild(newOption);
            optionInput.value = "";
        }
    });

    createPollButton.addEventListener(("click", () => {}));
    </script>

    <script>
    var win = navigator.platform.indexOf("Win") > -1;
    if (win && document.querySelector("#sidenav-scrollbar")) {
        var options = {
            damping: "0.5",
        };
        Scrollbar.init(document.querySelector("#sidenav-scrollbar"), options);
    }
    </script>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../assets/js/soft-ui-dashboard.min.js?v=1.0.3"></script>
</body>

</html>