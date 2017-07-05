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
$tpl_uri = get_template_directory();

require_once $tpl_uri . '/inc/debugger.php';       // * Debug функции
require_once $tpl_uri . '/inc/tpl-view-settings.php';
// require_once $tpl_uri . '/inc/tpl-company-info.php';

require_once $tpl_uri . '/inc/tpl.php';
require_once $tpl_uri . '/inc/tpl-titles.php';     // * Шаблоны заголовков
require_once $tpl_uri . '/inc/tpl-bootstrap.php';  // * Вспомагателные bootstrap функции
require_once $tpl_uri . '/inc/tpl-gallery.php';    // * Шаблон встроенной галереи wordpress
require_once $tpl_uri . '/inc/tpl-navigation.php'; // * Шаблон навигации

if(function_exists('is_woocommerce'))
  require_once $tpl_uri . '/inc/functions-woocommerce.php';

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
  $tpl_uri = get_template_directory_uri();
  $suffix = (!is_wp_debug()) ? '.min' : ''; 

  /**
   * Enqueue Style CSS or SASS/SCSS (if exists)
   */
  $ver = get_option('scss_cache') ? get_option('scss_cache') : '1.0';
  $styles = array('/style'.$suffix.'.css', '/style.css');
  foreach ($styles as $style) {
    if( is_readable(get_template_directory() . $style) ){
      wp_enqueue_style( 'style', $tpl_uri . $style, array(), $ver, 'all' );
      break;
    }
  }

  // wp_deregister_script( 'jquery' );
  // wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js');
  wp_enqueue_script('jquery');
  wp_enqueue_script('script', $tpl_uri . '/assets/script.js', array('jquery'), '1.0', true);
}
add_action( 'wp_enqueue_scripts', '_theme_styles_and_scripts', 999 );

/**
 * Template Filtes
 */
// add_filter( 'archive_reviews_title', function($t){ return 'Отзывы наших покупателей'; } );

// if(class_exists('WPAdvancedPostType')){
//  $types = new WPAdvancedPostType();
//  $types -> add_type( 'enty', 'Entity', 'Entities', array('public'=>false) );
//  $types -> add_type( 'news', 'News');
//  $types -> reg_types();
// }

add_filter( 'content_columns', 'content_columns_default', 10, 1 );
function content_columns_default($columns){
  return is_singular() ? 1 : 2;
}