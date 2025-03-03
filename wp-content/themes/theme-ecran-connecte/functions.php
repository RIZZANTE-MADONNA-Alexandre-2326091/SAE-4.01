<?php

use Models\CodeAde;

include_once 'inc/customizer/custom_colors.php';
include_once 'inc/customizer/custom_sidebar.php';
include_once 'inc/customizer/custom_schedule.php';
include_once 'inc/customizer/custom_footer.php';

add_filter('auto_update_plugin', '__return_true');
add_filter('auto_update_theme', '__return_true');


error_reporting(0);

function add_scripts()
{
    //jQuery
    wp_enqueue_script('jquery_cdn', 'https://code.jquery.com/jquery-3.4.1.slim.min.js');

    //Bootstrap
    wp_enqueue_style('bootstrap_css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap_js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js', array('jquery_cdn'), '', true);

    // CSS
    wp_enqueue_style( 'style_ecran', get_template_directory_uri().'/style.css');
    wp_enqueue_style( 'header_ecran_theme', get_template_directory_uri().'/assets/css/header.css');
    wp_enqueue_style( 'content_ecran_theme', get_template_directory_uri().'/assets/css/content.css');
    wp_enqueue_style( 'sidebar_ecran_theme', get_template_directory_uri().'/assets/css/sidebar.css');
    wp_enqueue_style( 'footer_ecran_theme', get_template_directory_uri().'/assets/css/footer.css');
}
add_action('wp_enqueue_scripts', 'add_scripts');


/**
 * Load all scripts (CSS / JS)
 */
function add_theme_scripts()
{
    wp_enqueue_script( 'theme-jquery', get_template_directory_uri() . '/assets/js/vendor/jquery-3.3.1.min.js', array (), '', false);
    wp_enqueue_script( 'theme-jqueryUI', get_template_directory_uri() . '/assets/js/vendor/jquery-ui.min.js', array ( 'jquery' ), '', false);
}

/**
 * CSS for login page
 */
function my_login_stylesheet()
{
    wp_enqueue_style('login_css', get_stylesheet_directory_uri() . '/assets/css/login.css');
}
add_action('login_enqueue_scripts', 'my_login_stylesheet');

/**
 * Remove the wordpress bar
 */
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar()
{
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

/**
 * Disable the url /wp-admin except for the admin
 */
function wpm_admin_redirection()
{
    if (is_admin() && !current_user_can('administrator')  && !defined('DOING_AJAX')) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('init', 'wpm_admin_redirection');

/**
 * Change the url of the logo
 * @return mixed
 */
function my_login_logo_url()
{
    return $_SERVER['HTTP_HOST'];
}
add_filter('login_headerurl', 'my_login_logo_url');

/**
 * Change the title of the logo
 *
 * @return string
 */
function my_login_logo_url_title()
{
    return get_bloginfo('name');
}
add_filter('login_headertitle', 'my_login_logo_url_title');

// Register a new navigation menu
function add_Main_Nav()
{
    register_nav_menu('header-menu',__('Header Menu'));
}
add_action('init', 'add_Main_Nav');

$args = array(
    'flex-width'    => true,
    'width'         => 500,
    'flex-height'   => true,
    'height'        => 500,
    'default-image' => get_template_directory_uri() . '/assets/media/header.png',
);
add_theme_support('custom-header', $args);

function get_page_by_title_V2(string $title) {
	// Define the arguments what to retrieve.
	$args = array(
		'post_type' => 'page',
		'title' => $title,
		'post_status' => 'publish',
		'posts_per_page' => 1
	);

	// Execute the WP Query.
	$my_query = new WP_Query($args);

	// Start a loop.
	if ( $my_query->have_posts() ) {
		$my_query->the_post();
		return get_post();
	}

	// Reset to the main query (IMPORTANT).
	wp_reset_postdata();
}

//Met la bonne heure
global $wpdb;
date_default_timezone_set('Europe/Paris');
$wpdb->time_zone = 'Europe/Paris';

// All sidebars
if (function_exists('register_sidebar')) {
    register_sidebar(array(
        'name' => 'Header',
        'before_widget' => '<li>',
        'after_widget' => '</li>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));
    register_sidebar(array(
        'name' => 'Footer',
        'before_widget' => '<li>',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
    register_sidebar(array(
        'name' => 'Footer gauche',
        'before_widget' => '<li>',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
    register_sidebar(array(
        'name' => 'Footer droite',
        'before_widget' => '<li>',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
    register_sidebar(array(
        'name' => 'Colonne Gauche',
        'before_widget' => '<li>',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
    register_sidebar(array(
        'name' => 'Colonne Droite',
        'before_widget' => '<li>',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
}

function get_all_codes() {
    $deptId = isset($_POST['deptId']) ? intval($_POST['deptId']) : 0;
    $model = new CodeAde();

    $years = $model->getAllFromType('year');
    $groups = $model->getAllFromType('group');
    $halfGroups = $model->getAllFromType('halfGroup');

    echo '<option value="0">Aucun</option>
      <option value="all">Tous</option>
      <optgroup label="Année">';
    foreach ($years as $year) {
        if($deptId == 0 || $year->getDeptId() == $deptId)
            echo '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option >';
    }
    echo '</optgroup>
     <optgroup label="Groupe">';
    foreach ($groups as $group) {
        if($deptId == 0 || $group->getDeptId() == $deptId)
            echo '<option value="' . $group->getCode() . '">' . $group->getTitle() . '</option>';
    }
    echo '</optgroup>
      <optgroup label="Demi groupe">';
    foreach ($halfGroups as $halfGroup) {
        if($deptId == 0 || $halfGroup->getDeptId() == $deptId)
            echo '<option value="' . $halfGroup->getCode() . '">' . $halfGroup->getTitle() . '</option>';
    }
    echo '</optgroup>';
}
add_action('wp_ajax_get_all_codes', 'get_all_codes');
add_action('wp_ajax_nopriv_get_all_codes', 'get_all_codes');


