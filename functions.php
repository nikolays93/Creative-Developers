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

require_once THEME . '/inc/debugger.php';       // * Debug функции
require_once THEME . '/inc/template.php';
require_once THEME . '/inc/woocommerce.php';
require_once THEME . '/inc/customizer.php';
require_once THEME . '/inc/bootstrap.php';
require_once THEME . '/inc/gallery.php';    // * Шаблон встроенной галереи wordpress

if( class_exists('woocommerce') )
  require_once THEME . '/inc/functions-woocommerce.php';

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

function _theme_styles_and_scripts() {
  wp_enqueue_style( 'style', get_stylesheet_directory_uri(), array(), '1.0', 'all' );

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
add_filter( 'content_image_html', 'add_thumbnail_link', 10, 2 );
