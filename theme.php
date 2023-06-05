<?php global $Wcms; ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php
            echo $Wcms->get('config', 'siteTitle') . ' - ' . $Wcms->page('title');
        ?></title>
        <meta name="description" content="<?php echo $Wcms->page('description') ?>">
        <meta name="keywords" content="<?php echo $Wcms->page('keywords') ?>">
		<meta name="title" content="<?php echo $Wcms->get('config', 'siteTitle') ?> - <?php echo $Wcms->page('title') ?>" />
        <meta property="og:url" content="<?php echo $this->url() ?>" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="<?php echo $Wcms->get('config', 'siteTitle') ?>" />
        <meta property="og:title" content="<?php echo $Wcms->page('title') ?>" />
        <meta name="twitter:site" content="<?php echo $this->url() ?>" />
        <meta name="twitter:title" content="<?php echo $Wcms->get('config', 'siteTitle') ?> - <?php echo $Wcms->page('title') ?>" />
        <meta name="twitter:description" content="<?php echo $Wcms->page('description') ?>" />

        <?php
            // Admin CSS
            echo $Wcms->css();
            // Theme CSS
            echo '<link rel="stylesheet" href="' . $Wcms->asset('css/style.css') . '">';
        ?>

    </head>

    <!-- Disable Bootstrap's "animation" on "collapsing" for the navbar -->
    <style>
        #bs5navBar.collapsing {
            transition-property: none !important;
            transition-duration: 0s !important;
            transition-delay: 0s !important;
        }
    </style>

    <body>
        <div class="container p-1 wcmsbs5-body">

        <?php echo $Wcms->alerts() ?>

        <div class="settings-margin">
            <?php echo $Wcms->settings() ?>
        </div>

        <nav class="navbar navbar-fixed-top mb-5 w-75 wcmsbs5-navbar" role="navigation">
            <div class="container d-flex flex-row text-left">
                <div class="mx-0">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#bs5navBar" aria-controls="bs5navBar" aria-expanded="false" aria-label="Toggle navigation">
                      <small><span class="navbar-toggler-icon"></span></small>
                    </button>
                </div>
                <div class="flex-grow-1 text-truncate ms-3">
                    <a class="navbar-brand" href="<?php echo $Wcms->url(); ?>">
                        <?php echo htmlentities( $Wcms->get( 'config', 'siteTitle' ) ); ?>
                    </a>
                </div>
                <div class="collapse navbar-collapse p-2 mt-2 bg-secondary-subtle rounded-bottom border border-3 border-secondary-subtle w-75" id="bs5navBar">
                    <ul class="navbar-nav ms-1 mt-3 text-truncate">
                        <?php echo $Wcms->menu(); ?>
                    </ul>
                </div>
            </div>
        </nav>

        <?php
            /**
             * Display title of current page, unless we're on a page called
             * "blog" and the plugin "Simple Blog" is installed. I really don't
             * like kludges, but "Simple Blog" is a nice plugin, so we'll be
             * nice. And "Simple Blog" does not like it when we display the
             * actual page('title') since it seems to use that for other
             * things.
             */
            $hideTitle = false;
            $simpleBlogSlug = 'blog';
            $pathTest = $Wcms->currentPageTree;
            if ( array_shift( $pathTest ) === $simpleBlogSlug ) {
                if ( @ file_exists( $Wcms->rootDir . '/plugins/simple-blog/simple-blog.php' ) ) {
                    $hideTitle = true;
                }
            }
            if ( ! $hideTitle ) {
                ?>
                <div class="container wcmsbs5-pagetitle">
                    <h1 class="h2 text-truncate text-body-secondary" title="<?php echo htmlentities( $Wcms->page('title') ); ?>">
                        <?php
                            // The page title
                            // Not sure if I should use $Wcms->currentPage or
                            // page('title') here, but currentPage seems to be
                            // a slug ...
                            echo htmlentities( $Wcms->page('title') );
                        ?>
                    </h1>
                </div>
                <?php
            }
        ?>

        <div class="container wcmsbs5-maincontainer">
            <div class="bg-body-tertiary text-body p-5 rounded wcmsbs5-maincontent">
                <?php
                    // The page text
                    echo $Wcms->page('content');
                ?>
            </div>
        </div>

        <div class="container mt-5 wcmsbs5-subsidecontainer">
            <div class="bg-success-subtle p-5 text-center rounded wcmsbs5-subsidecontent">
                <?php
                    // The page text
                    echo $Wcms->block('subside');
                ?>
            </div>
        </div>

        <footer class="container-fluid mt-5 wcmsbs5-footercontainer">
            <div class="bg-body-secondary container p-2 text-end rounded wcmsbs5-footercontent">
                <?php echo $Wcms->footer() ?>
            </div>
        </footer>

        <?php
            echo $Wcms->js();
        ?>
        </div>
    </body>
</html>
