<?php global $Wcms; ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
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

    <script>
        (() => {
            'use strict'

            // Set theme to the user's preferred color scheme
            function updateBootstrapTheme() {
                const colorMode = window.matchMedia("(prefers-color-scheme: dark)").matches ?
                    "dark" :
                    "light";
                document.querySelector("html").setAttribute("data-bs-theme", colorMode);
            }
            // Submit search form to ourselves
            function wcmsBS5search() {
                let e = document.getElementById("searchtext");
                if (e && ! e.value.length ) {
                    e.focus();
                } else {
                    e = document.getElementById("searchform");
                    if (e) {
                        e.submit();
                    }
                }
            }
            function wcmsBS5setup() {
                // Update theme when the preferred scheme changes
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateBootstrapTheme);
                // Setup button click handler for search form
                let searchButton = document.getElementById("searchbutton");
                if (searchButton) {
                    searchButton.addEventListener("click", wcmsBS5search);
                }
                // Setup search field handler for ENTER key
                let e = document.getElementById("searchtext");
                if (e) {
                    if (searchButton) {
                        e.addEventListener("keydown", function(event) {
                            if (event.keyCode === 13) {
                                event.preventDefault();
                                searchButton.click();
                            }
                        });
                    }
                }
            }

            if (document.readyState === "complete" || (document.readyState !== "loading" && !document.documentElement.doScroll)) {
                wcmsBS5setup();
            } else {
                document.addEventListener("DOMContentLoaded", wcmsBS5setup);
            }
        })()
    </script>

    <!-- Disable Bootstrap's "animation" on "collapsing" for the navbar -->
    <style>
        #bs5navBar.collapsing {
            transition-property: none !important;
            transition-duration: 0s !important;
            transition-delay: 0s !important;
        }
    </style>

    <body>
        <script>
            <?php
            /**
             * It's not always best practice to put a script tag here since it
             * will block rendering of the page marginally, but this is a very
             * minor script block that is used to set the correct Bootstrap 5
             * "color mode" so it matches the visitor browser's setting (i.e.
             * "Dark mode" or "Light mode") without getting a brief flash
             * effect every time the page loads
             **/
            ?>
            (() => {
            'use strict'
                const colorMode = window.matchMedia("(prefers-color-scheme: dark)").matches ?
                    "dark" :
                    "light";
                document.querySelector("html").setAttribute("data-bs-theme", colorMode);
            })()
        </script>
        <div class="container p-1 wcmsbs5-body">

        <?php echo $Wcms->alerts() ?>

        <div class="settings-margin">
            <?php echo $Wcms->settings() ?>
        </div>

        <?php
        /**
         * Deconstruct possible tokens
         */
        if ( ! empty( $_POST['token'] ) ) {
            $thisToken = $_POST['token'];
        } else {
            $thisToken = '';
        }
        /**
         * Deconstruct search parameter, if there is one.
         */
        if ( ! empty( $_POST['searchtext'] ) ) {
            $searchString = trim( urldecode( mb_substr( $_POST['searchtext'], 0, 64 ) ) );
        } else {
            $searchString = '';
        }
        /**
         * Make sure we have a valid token if there's a search parameter
         */
        if ( ! empty( $searchString ) && ! $Wcms->hashVerify( $thisToken ) ) {
            // No match, ignore search
            $searchString = '';
        }
        /**
         * Perform search, after pre-conditioning the pages in question
         */
            // ----
            // Walk through menu items, excluding empty/hidden/invisible items
            function iterateItems( $theItems ) {
                $items = array();

                foreach( $theItems as $item ) {
                    if ( empty( $item->visibility ) || $item->visibility != 'show' ) {
                        return( $items );
                    }
                    if ( empty ( $item->slug ) ) {
                        return( $items );
                    }
                    $items[$item->slug] = $item->slug;
                    $subpages = $item->subpages;
                    $visibleSubpage = $subpages && in_array( 'show', array_column( (array)$subpages, 'visibility' ) );
                    if ( $visibleSubpage ) {
                        $items['subpages'] = iterateItems( $subpages, $items );
                    }
                }
                return( $items );
            }
            // ----

        // Find menu items
        $allItems = $Wcms->db->config->menuItems;
        $ourItems = iterateItems( $allItems );
        // Find corresponding pages
        $allPages = (array)$Wcms->db->pages;
        $ourPages =  array_intersect_key( $allPages, $ourItems );

        $GLOBALS['searchpages'] = array();

            // ----
            // Walk through visible pages, process search string and slugs
            // The use of $GLOBALS[] isn't pretty, duly noted :-)

            function pageWalk( $item, $key, $slug  ) {
                if ( isset( $item->content ) ) {
                    // Remove HTML, etc
                    $content = preg_replace( '/<(|\/)(?!\?).*?(|\/)>/', '', $item->content );
                } else {
                    $content = '';
                }
                $GLOBALS['searchpages'][] = array(
                    'slug' => $slug . '/' . $key,
                    'title' => $item->title,
                    'content' => $content,
                );
                if ( $item->subpages ) {
                    array_walk_recursive( $item->subpages, 'pageWalk', $slug . '/' . $key );
                }
            }
            // ----

        $matchingContent = array();

        if ( ! empty( $searchString ) ) {

            // Flatten or findings
            if ( array_walk_recursive( $ourPages, 'pageWalk', '' ) ) {
                /*
                error_log('Final result-------------------------------------');
                error_log(print_r($GLOBALS['searchpages'] , true));
                */

                foreach( $GLOBALS['searchpages'] as $page ) {
                    if ( mb_stristr( $page['title'], $searchString ) !== false ) {
                        $matchingContent[] = array(
                            'slug' => $page['slug'],
                            'title' => $page['title'],
                            'content' => '',
                        );
                    } else {
                        $match = mb_stristr( $page['content'], $searchString );
                        if ( $match !== false ) {
                            $matchingContent[] = array(
                                'slug' => $page['slug'],
                                'title' => $page['title'],
                                'content' => '<mark>' . $searchString . '</mark>' .
                                             mb_substr( $match, mb_strlen( $searchString ), 128 ),
                            );
                        }
                    }
                }// foreach
            } else {
                // Something didn't work out
                error_log( __FILE__ . '(' . __LINE__ . '): Unable to process pages' );
            }

            /**
             * See if the SimpleBlog plugin is present, in which case we need to search there too
             */
            $simpleBlogData = $Wcms->dataPath . '/simpleblog.json';
            if ( file_exists( $Wcms->rootDir . '/plugins/simple-blog/simple-blog.php' ) ) {
                if ( file_exists( $simpleBlogData ) ) {
                    $blogPosts = json_decode( file_get_contents( $simpleBlogData ), true );
                    if ( ! empty( $blogPosts['posts'] ) && is_array( $blogPosts['posts'] ) ) {
                        foreach( $blogPosts['posts'] as $slug => $post ) {
                            if ( mb_stristr( $post['title'], $searchString ) !== false ) {
                                $matchingContent[] = array(
                                    'slug' => '/blog/' . $slug,
                                    'title' => $post['title'],
                                    'content' => '',
                                );
                            } else {
                                // Remove HTML, etc
                                $content = preg_replace( '/<(|\/)(?!\?).*?(|\/)>/', '', $post['body'] );
                                $match = mb_stristr( $content, $searchString );
                                if ( $match !== false ) {
                                    $matchingContent[] = array(
                                        'slug' => '/blog/' . $slug,
                                        'title' => $post['title'],
                                        'content' => '<mark>' . $searchString . '</mark>' .
                                                     mb_substr( $match, mb_strlen( $searchString ), 128 ),
                                    );
                                } elseif ( ! empty( $post['description'] ) ) {
                                    // Also check "description" part of post
                                    $content = preg_replace( '/<(|\/)(?!\?).*?(|\/)>/', '', $post['description'] );
                                    $match = mb_stristr( $content, $searchString );
                                    if ( $match !== false ) {
                                        $matchingContent[] = array(
                                            'slug' => '/blog/' . $slug,
                                            'title' => $post['title'],
                                            'content' => '<mark>' . $searchString . '</mark>' .
                                                         mb_substr( $match, mb_strlen( $searchString ), 128 ),
                                        );
                                    }
                                }
                            }
                        }// foreach
                    } else {
                        // Something didn't work out
                        error_log( __FILE__ . '(' . __LINE__ . '): Unable to process SimpleBlog data in "' . $simpleBlogData . '"' );
                    }
                }
            }
        } // ! empty( $searchString )

        ?>

        <div class="container sticky-top pb-5">
            <nav class="navbar wcmsbs5-navbar justify-content-start align-items-start" role="navigation">
                <div class="mx-0">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#bs5navBar" aria-controls="bs5navBar" aria-expanded="false" aria-label="Toggle navigation">
                      <small><span class="navbar-toggler-icon"></span></small>
                    </button>
                </div>
                <div class="navbar-text text-truncate ms-1 flex-fill w-50 p-1 wcmsbs5-navbar-site-title">
                    <a class="h4 text-decoration-none wcmsbs5-navbar-site-title-text"
                       href="<?php echo $Wcms->url(); ?>"
                       title="<?php echo htmlentities( $Wcms->get( 'config', 'siteTitle' ) ); ?>" >
                        <?php echo htmlentities( $Wcms->get( 'config', 'siteTitle' ) ); ?>
                    </a>
                </div>
                <div class="navbar-text text-right ms-1 w-25 p-0 align-middle wcmsbs5-navbar-search">
                    <form method="post" name="searchform" id="searchform" role="search" action="<?php echo htmlentities( $Wcms->getCurrentPageUrl() ); ?>">
                        <input type="hidden" name="token" value="<?php echo htmlentities( $Wcms->getToken() ); ?>" />
                        <div class="input-group d-flex justify-content-end w-100">
                            <input class="form-control p-1" name="searchtext" id="searchtext" type="search" aria-label="Search" maxlength="200" value="<?php echo htmlentities( $searchString ); ?>" />
                            <button class="btn btn btn-outline-secondary btn-sm d-none d-lg-inline-block" name="searchbutton" id="searchbutton" type="button">Search</button>
                        </div>
                    </form>
                </div>
                <div class="collapse navbar-collapse p-2 mt-2 bg-secondary-subtle rounded-bottom border border-3 border-secondary-subtle w-75" id="bs5navBar">
                    <ul class="navbar-nav ms-1 mt-3 text-truncate">
                        <?php echo $Wcms->menu(); ?>
                    </ul>
                </div>
            </nav>
        </div>

        <?php
            /**
             * If we're searching, display result of search
             */
            if ( ! empty( $searchString ) ) {
                ?>
                <div class="container wcmsbs5-maincontainer">
                    <div class="bg-body-tertiary text-body p-5 rounded wcmsbs5-maincontent">
                <?php
                    if ( empty( $matchingContent ) ) {
                        echo '<p class="wcmsbs5-notfound">' .
                             'No content matches the search criteria' . '&nbsp;&#x1F914;' .
                             '</p>';
                    } else {
                        foreach( $matchingContent as $m ) {
                            echo '<a class="text-decoration-none" href="' . $Wcms->url( ltrim( $m['slug'], '/' ) ) . '">';
                            echo '<div class="mt-4 wcmsbs5-match text-wrap">'.
                                 '<h5 class="wcmsbs5-match-title">&#x27A1;&#xFE0F;&nbsp;'.
                                 htmlentities( $m['title'] ) .
                                 '</h5>';
                            if ( ! empty( $m['content'] ) ) {
                                echo '<p class="wcmsbs5-match-content text-wrap">'.
                                     $m['content'] .
                                     '</p>';
                            }
                            echo '</div>';
                            echo '</a>';
                        }// foreach
                    }
                ?>
                    </div>
                </div>
                <?php

            } else {
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
                    if ( file_exists( $Wcms->rootDir . '/plugins/simple-blog/simple-blog.php' ) ) {
                        $hideTitle = true;
                    }
                }
                if ( ! $hideTitle ) {
                    ?>
                    <div class="container wcmsbs5-pagetitle">
                        <h1 class="h2 text-body-secondary" title="<?php echo htmlentities( $Wcms->page('title') ); ?>">
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
                        /**
                         * This hack will refrain us from outputting our "better" blog post
                         * cards if we're currently logged in as the Simple Blog plugin
                         * makes some assumptions regarding what it can modify and output.
                         */
                        if ( $hideTitle && ! $Wcms->loggedIn ) {
                            echo '<div class="wcmsbs5-blogcards">';
                        }
                        // The page text
                        echo $Wcms->page('content');
                        // Closing the "hack" (see above)
                        if ( $hideTitle && ! $Wcms->loggedIn ) {
                            echo '</div">';
                        }
                    ?>
                    </div>
                </div>
<?php
            }
?>

        <div class="container mt-5 wcmsbs5-subsidecontainer">
            <div class="bg-success-subtle p-5 text-center rounded wcmsbs5-subsidecontent">
                <?php
                    // The page text
                    echo $Wcms->block('subside');
                ?>
            </div>
        </div>

        <footer class="container-fluid mt-5 wcmsbs5-footercontainer">
            <div class="bg-body-secondary container p-2 text-center rounded wcmsbs5-footercontent">
                <?php echo $Wcms->footer() ?>
            </div>
        </footer>

        <?php
            echo $Wcms->js();
        ?>
        </div>
    </body>

</html>
