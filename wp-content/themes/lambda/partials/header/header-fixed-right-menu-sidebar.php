<?php
/**
 * Creates a boostrap menu
 *
 * @package Lambda
 * @subpackage Admin
 * @since 0.1
 *
 * @copyright (c) 2015 Oxygenna.com
 * @license **LICENSE**
 * @version 1.53.1
 */
global $oxy_theme;
$header_caps = $oxy_theme->get_option('header_capitalization');
?>
<div id="masthead" class="menu header-fixed-right-menu-sidebar navbar navbar-static-top oxy-mega-menu <?php echo esc_attr(implode(' ', $classes)); ?>" role="banner">
    <div class="logo-navbar container-logo">
        <div class="<?php echo esc_attr(implode(' ', $container_classes)); ?>">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".main-navbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <?php include(locate_template('partials/header/logo.php')); ?>
            </div>
        </div>
    </div>
</div>

<nav id="navbar-slide" class="collapse navbar-collapse main-navbar menu <?php echo esc_attr($header_caps); ?>" role="navigation">
    <?php include(locate_template('partials/header/logo.php')); ?>
    <?php include(locate_template('partials/header/nav.php')); ?>
</nav>
