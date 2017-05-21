<?php

if ( ! defined( 'ABSPATH' ) )
  exit; // Exit if accessed directly

function get_tpl_content( $affix, $return = false ){
  $singular = is_singular() ? '-single' : '';

  if($return)
    ob_start();

  if( ! is_front_page() && is_archive() && !is_search() ){
    the_advanced_archive_title();
    the_archive_description( '<div class="taxonomy-description">', '</div>' );
  }

  if (!$singular)
    echo "<div class='row'>";

  while ( have_posts() ){
    the_post();
    
    // need for search
    if( ! $affix )
      $affix = get_post_type();

    if( $affix != 'product' )
      get_template_part( 'template-parts/content' . $singular, $affix );
  }

  if (!$singular)
    echo "</div>";

  if($return)
    return ob_get_clean();
}
function get_tpl_search_content( $return = false ){
  ob_start();
  while ( have_posts() ){
    the_post();

    if( get_post_type() == 'product')
      wc_get_template_part( 'content', 'product' );
  }
  $products = ob_get_clean();
  $content = get_tpl_content( false, true );

  if( $return ){
    return $products . $content;
  }
  else {
    if($products)
      echo "<ul class='products row'>" . $products . "</ul>";
    echo $content;
  }
}

add_filter( 'set_custom_brand', 'add_custom_brand', 10, 3 );
function add_custom_brand($brand, $brand_class, $brand_title){
  $brand = '<a class="'.$brand_class.'" title="'.$brand_title.'" href="'.get_home_url().'">'.$brand.'</a>';
  return $brand;
}

/**
 * yoast крошки ( Для активации установить/активировать плагин, дополнительно => breadcrumbs => enable )
 */
function breadcrumbs_from_yoast(){
  if ( function_exists('yoast_breadcrumb') ) {
    yoast_breadcrumb('<p id="breadcrumbs">','</p>');
  }
}
add_action( 'woocommerce_before_main_content', 'breadcrumbs_from_yoast', 25 );

/**
 * Принятые настройки постраничной навигации
 */
function the_template_pagination($echo=true){
  $args = array(
    'show_all'     => false,
    'end_size'     => 1,    
    'mid_size'     => 1,     
    'prev_next'    => true,  
    'prev_text'    => '« Пред.',
    'next_text'    => 'След. »',
    'add_args'     => false,
    );

  if($echo){
    the_posts_pagination($args);
    return true;
  }
  else {
    return get_the_posts_pagination($args);
  }    
}

function has_children_terms($hide_empty=true){
  $o = get_queried_object();
  if(!empty($o->has_archive) && $o->has_archive==true){
    $tax = $o->taxonomies[0];
    $parent = 0;
  }

  if( !empty($o->term_id) ){
    $tax = $o->taxonomy;
    $parent = $o->term_id;
  }

  $children = get_terms( array(
    'taxanomy'  => $tax,
    'parent'    => $parent,
    'hide_empty' => $hide_empty
    ) );

  if($children) {
    return true;
  }
  return false;
}
/**
 * Получить ID самой родительской страницы (после "главной")
 */
function get_parent_page_id($post) {
  if ($post->post_parent)  {
    $ancestors=get_post_ancestors($post->ID);
    $root=count($ancestors)-1;
    $parent = $ancestors[$root];
  } else {
    $parent = $post->ID;
  }
  return $parent;
}

/**
 * Отчистить мета теги
 */
function seo18_head_cleanup() {
  remove_action( 'wp_head', 'feed_links_extra', 3 );                    // Category Feeds
  remove_action( 'wp_head', 'feed_links', 2 );                          // Post and Comment Feeds
  remove_action( 'wp_head', 'rsd_link' );                               // EditURI link
  remove_action( 'wp_head', 'wlwmanifest_link' );                       // Windows Live Writer
  remove_action( 'wp_head', 'index_rel_link' );                         // index link
  remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );            // previous link
  remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );             // start link
  remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // Links for Adjacent Posts
  remove_action( 'wp_head', 'wp_generator' );                           // WP version
  }
  add_action( 'init', 'seo18_head_cleanup' );

/**
 * Добавить ссылку о разработчике в топбар
 */
function customize_toolbar_link($wp_admin_bar) {
  $wp_admin_bar->add_node(array(
    'id' => 'seo',
    'title' => 'Seo18.ru',
    'href' => 'http://seo18.ru',
    'meta' => array(
      'title' => 'Перейти на сайт разработчика'
      )
    ));
  }
  add_action('admin_bar_menu', 'customize_toolbar_link', 999);

/**
 * Сменить строку "Спасибо за творчество с Wordpress"
 */
function custom_admin_footer() {
  echo '<span id="footer-thankyou">Разработано компанией <a href="http://seo18.ru" target="_blank">seo18.ru - создание и продвижение сайтов</a></span>.
  <small> Использована система <a href="wordpress.com">WordPress</a>. </small>';
  }
  add_filter('admin_footer_text', 'custom_admin_footer');
