<?php
/*
 * Добавление поддержки функций
 * Добавление областей 'primary', 'footer'
 * Регистрация Сайдбара: Архивы и записи
 * Фильтры шаблона
 */

/**
 * Include required files
 */
define('THEME', get_template_directory());
define('TPL', get_template_directory_uri());

if( ! class_exists('scssc') ) {
  include THEME . '/includes/scss.inc.php';
}

require_once THEME . '/includes/admin.php';
require_once THEME . '/includes/tpl.php';
require_once THEME . '/includes/tpl-bootstrap.php';  // * Вспомагателные bootstrap функции
require_once THEME . '/includes/tpl-gallery.php';    // * Шаблон встроенной галереи wordpress
require_once THEME . '/includes/tpl-navigation.php'; // * Шаблон навигации

if( class_exists('woocommerce') ) {
  require_once THEME . '/includes/woocommerce.php';
  require_once THEME . '/includes/wc-customizer.php';
}

function theme_setup() {
  // load_theme_textdomain( 'seo18theme', get_template_directory() . '/assets/languages' );

  add_theme_support( 'custom-logo' );
  add_theme_support( 'title-tag' );
  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'html5', array(
    'search-form',
    'comment-form',
    'comment-list',
    'gallery',
    'caption',
  ) );

  register_nav_menus( array(
    'primary' => 'Главное меню',
    'footer' => 'Меню в подвале',
  ) );
}
add_action( 'after_setup_theme', 'theme_setup' );

function archive_widgets_init(){
  register_sidebar( array(
    'name'          => 'Архивы и записи',
    'id'            => 'archive',
    'description'   => 'Эти виджеты показываются в архивах и остальных страницах', 
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h3 class="widget-title">',
    'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'archive_widgets_init' );

function update_theme_styles( &$filemtime, $option_name, $is_compressed ) {
    $filename = THEME . '/style.scss';
    if( current_user_can( 'administrator' ) &&  filemtime($filename) != $filemtime ) {
        $scss = new scssc();

        if ( $is_compressed ) {
            $scss->setFormatter( 'scss_formatter_compressed' );
        }

        $scss->addImportPath( THEME );

        $cyrilic = "/[\x{0410}-\x{042F}]+.*[\x{0410}-\x{042F}]+/iu";
        $excluded_cyr = preg_replace( $cyrilic, "", file_get_contents($filename) );

        file_put_contents( str_replace('.scss', '.css', $filename), $scss->compile( $excluded_cyr ) );
        $filemtime = filemtime($filename);
        update_option( $option_name, $filemtime );
    }
}

function _theme_styles_and_scripts() {
    $is_compressed = ! defined('SCRIPT_DEBUG') || SCRIPT_DEBUG === false;
    $minify = $is_compressed ? '.min' : '';

    $option_name = 'stylesheet_cache';
    $filemtime = get_option( $option_name );
    update_theme_styles($filemtime, $option_name, $is_compressed);

    wp_enqueue_style( 'style', TPL . '/style.css', array(), $filemtime, 'all' );
    wp_enqueue_style( 'bootload', TPL . '/assets/scss/bootload/source/bootload'.$minify.'.css', array(), '1.0' );

    // wp_deregister_script( 'jquery' );
    // wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js');
    wp_enqueue_script('jquery');
    wp_enqueue_script('script', TPL . '/assets/script.js', array('jquery'), '1.0', true);
}
add_action( 'wp_enqueue_scripts', '_theme_styles_and_scripts', 999 );

/**
 * Template Filtes
 */
// add_filter( 'archive_reviews_title', function($t){ return 'Отзывы наших покупателей'; } );

// add_action( 'theme_after_title', '_after_title' );
// function _after_title(){}

add_filter( 'content_columns', 'content_columns_default', 10, 1 );
function content_columns_default($columns){
  if( is_singular() )
    $columns = 1;

  return $columns;
}
