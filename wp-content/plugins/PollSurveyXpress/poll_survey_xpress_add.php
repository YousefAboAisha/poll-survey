<?php
// Get the absolute path to the plugin directory
$pluginPath = plugin_dir_path(__FILE__);

// Define the path to the templates folder inside the plugin directory
$templateFolder = $pluginPath . 'templates';

// Scan the template folder for preview files
$previewFiles = glob($templateFolder . '/*_template.php');

// Count the number of preview files
$templateCount = count($previewFiles);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-100">

    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" >
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">All Templates</h6>
                </nav>
            </div>
        </nav>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2">
                <?php foreach ($previewFiles as $index => $previewFile) : ?>
                <?php
        // Extract the template name from the filename
        $fileName = basename($previewFile, '_template.php');
        $templateName = str_replace('_', ' ', $fileName);

        // Generate the admin PHP link for each template
        $templateAdminLink = admin_url('admin.php?page=poll-survey-xpress-add&view_template=' . $fileName);
        ?>
                 <div class="col">
                    <div class="card p-4 border-1">
                        <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100"
                            style="background-image: url('../assets/img/ivancik.jpg')">
                            <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-0">
                                <h5 class="text-dark font-weight-bolder mb-4 pt-2">
                                    <?php echo ucwords($templateName); ?>
                                </h5>
                               
                                <a class="text-white btn bg-primary text-sm font-weight-bold mb-0 icon-move-right"
                                    href="<?php echo admin_url('admin.php?page=view_template_page&template=' . $fileName); ?>">
                                    View Template
                                    <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

</body>

</html>