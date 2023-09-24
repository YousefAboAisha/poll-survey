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

<style>
    .top-neg-20 {
        top: -20px;
    }

    .end-neg-20 {
        right: -20px;
    }
</style>

<?php $cardData = [
    [
        'details' => 'Questions are presented with a set of questions and a predefined list of answer choices for each question. Respondents select one or more answers from the provided options.',
        'icon' => 'fas fa-hashtag fa-md',
    ],
    [
        'details' => 'Questions prompting respondents to provide free-form, open-ended responses. Participants have the freedom to express their thoughts',
        'icon' => 'fas fa-paragraph fa-md',
    ],
    [
        'details' => 'Participants are asked to assign ratings or scores to specific items or statements based on their preferences, opinions, or experiences.',
        'icon' => 'fas fa-percent fa-md',
    ],
]; ?>

<body class="bg-gray-100">


    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">

        <div class="d-flex align-items-center gap-2 my-4 mb-5">
            <a href="<?php echo admin_url('admin.php?page=poll-survey-xpress-surveys'); ?>" class="m-0 text-dark"><?php _e('Home', 'psx-poll-survey-plugin'); ?></a>
            <i class="fas fa-angle-right"></i>
            <h6 class="font-weight-bolder mb-0 p-0 "><?php _e('All Templates', 'psx-poll-survey-plugin'); ?></h6>
        </div>


        <div class="container-fluid p-0">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2 mx-auto">
                <?php foreach ($previewFiles as $index => $previewFile) : ?>
                    <?php
                    // Extract the template name from the filename
                    $fileName = basename($previewFile, '_template.php');
                    $templateName = str_replace('_', ' ', $fileName);
                    $icon = $cardData[$index]['icon'];
                    $details = $cardData[$index]['details'];

                    // Generate the admin PHP link for each template
                    $templateAdminLink = admin_url('admin.php?page=poll-survey-xpress-add&view_template=' . $fileName);
                    ?>

                    <div class="col">
                        <div class="p-4 border position-relative rounded-3 bg-white">
                            <div class="bg-black text-white m-0 p-2 px-3 rounded-3 position-absolute top-3 end-3">
                                <i class="<?php echo ($icon); ?> "></i>
                            </div>
                            <div class="overflow-hidden position-relative h-100">
                                <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-0">
                                    <h5 class="text-dark font-weight-bolder m-0 mt-2">
                                        <?php _e(ucwords($templateName), 'psx-poll-survey-plugin'); ?>
                                    </h5>

                                    <p class="mt-3" style="height: 150px;">
                                        <?php echo ($details); ?>
                                    </p>

                                    <a class="text-white bg-primary text-sm font-weight-bold mb-0 icon-move-right p-2 py-3 rounded-2 text-center fw-bolder" href="<?php echo admin_url('admin.php?page=poll-survey-xpress-add&template=' . $fileName); ?>">
                                        <?php _e('View Template', 'psx-poll-survey-plugin'); ?>
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