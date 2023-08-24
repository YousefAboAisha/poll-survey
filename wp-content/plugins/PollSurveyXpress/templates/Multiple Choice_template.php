<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Soft UI Dashboard by Creative Tim</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

</head>

<body>
    <main class="col-lg-6 col-md-8 col-10 mx-auto main-content position-relative max-height-vh-100 h-100 mt-4 border-radius-lg">
        <!-- Navbar -->
        <nav class="px-0 mb-4" navbar-scroll="true">
            <nav aria-label="breadcrumb">
                <h6 class="font-weight-bolder mb-0">Multiple Choice Template
                </h6>
            </nav>
        </nav>

        <div class="d-flex flex-column justify-content-center align-items-center">
            <input type="text" class="w-100 border text-lg rounded-1 p-1 rounded-1 bg-white mb-3" placeholder="Pull/Survey title" id="surveyTitleValue" value="Pull/Survey title" />

            <div class="d-flex w-100 flex-column border rounded-3 bg-white p-4">
                <label for="surveyTitle" class="form-label">Add question title</label>
                <input type="text" class="form-control mb-2 border p-2" placeholder="Question title" id="questionTitle" />

                <div class="mt-2 mb-2">
                    <label for="surveyTitle" class="form-label">Add new option</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control border p-2" placeholder="Option title" id="optionInput" />
                        <button id="addOption" class="text-primary border btn text-sm font-weight-bold mb-0 shadow-none d-flex justify-content-center align-items-center p-3 rounded-1">
                            <i class="fas fa-plus text-sm" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <!-- Radio Buttons group -->
                <div id="optionsGroup"></div>

                <button disabled id="createPoll" class="text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold mb-0 mt-4">
                    Create
                    <i class="fas fa-plus text-sm ms-1" aria-hidden="true"></i>
                </button>
            </div>

            <!-- Final output cards -->
            <div id="cardsContainer" class="w-100 d-flex flex-column gap-3 my-5">
                <!-- Cards will be rendered here -->
            </div>

            <button type="submit" id="save_button" disabled class="align-self-start text-white btn bg-primary col-lg-4 col-md-6 col-7 text-sm font-weight-bold mb-5">
                Save
            </button>
        </div>
    </main>




    <!-- Fixed plugin settings ICON -->
    <div data-bs-toggle="modal" data-bs-target="#settingsModal" style="cursor: pointer" class="position-fixed bottom-4 end-2 px-3 py-2 bg-white shadow-sm rounded-circle text-dark">
        <i class="fa fa-cog py-2"> </i>
    </div>

    <!-- Fixed Plugin settings -->
    <div class="modal fade" id="settingsModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Survey Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <form class="modal-body card">
                    <div>
                        <label>Change plugin Theme</label>

                        <div class="d-flex align-items-center px-2 gap-2">
                            <span class="text-sm fw-bold"> Bg color</span>
                            <input type="color" class="form-control form-control-color border-0 p-0 w-10 me-2" id="bg_color" value="#F00" />
                            <span class="text-sm fw-bold"> Text color </span>
                            <input type="color" class="form-control form-control-color border-0 p-0 w-10" id="text_color" value="#006600" />
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 mt-2 mb-2 px-2">
                        <div>
                            <label class="m-0">Starts</label>
                            <input type="datetime-local" class="form-control" id="start_date" placeholder="Select a date" />
                        </div>

                        <div>
                            <label class="m-0">Ends</label>

                            <input type="datetime-local" class="form-control" id="end_date" placeholder="Select a date" />
                        </div>
                    </div>

                    <div class="card-body pt-sm-3 p-0">
                        <div class="form-group d-flex flex-column">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="active_plugin" />
                                <label class="form-check-label" for="active_plugin">
                                    Activate the survey
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="share_plugin" />
                                <label class="form-check-label" for="share_plugin">
                                    Share with my friends
                                </label>
                            </div>

                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="show_results" />
                                    <label class="form-check-label" for="show_results">
                                        Show real-time results
                                    </label>
                                </div>

                                <input type="text" class="form-control mt-2" placeholder="Add Thank Meesage" value="Thank You!" id="show_results_input" />
                            </div>

                            <div class="d-flex align-items-center justify-content-start gap-2 mt-3">
                                <label class="form-check-label w-45">
                                    Show results after</label>
                                <input type="number" class="form-control w-55" placeholder="Number of votes" id="min_votes_input" value="10" />
                            </div>

                            <div class="w-100 d-flex flex-column align-items-start mt-2 gap-2">
                                <input type="text" class="form-control" placeholder="Add CTA button title" id="cta_input" value="CTA title" />
                                <button id="cta_button" type="button" class="btn btn-dark m-0 mt-1">
                                    CTA Title
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>