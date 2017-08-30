<?php

if ( ! defined( 'ABSPATH' ) )
  exit; // You shall not pass

/**
 * Utilites
 * Bread Crumbs
 * Navigation
 * Title Templates
 * Thumbnail
 * Content Template
 */

/******************************************** Utilites ********************************************/
/**
 * Показывать sidebar или нет
 * @return boolean
 */
function is_show_sidebar(){
  $post_type = get_post_type();
  $enable_types = apply_filters( 'sidebar_archive_enable_on_type', array('post', 'page') );

  $show_sidebar = false;
  if( function_exists('is_woocommerce') ){
    if( is_woocommerce() || is_shop() && is_active_sidebar('woocommerce')  )
       $show_sidebar = 'woocommerce';
    if( is_cart() || is_checkout() || is_account_page())
      $show_sidebar = false;
    elseif( is_active_sidebar('archive') && in_array($post_type, $enable_types) )
      $show_sidebar = 'archive';
  }
  else {
    if( is_active_sidebar('archive') && in_array($post_type, $enable_types) )
      $show_sidebar = 'archive';
  }

  return apply_filters( 'enable_sidebar', $show_sidebar );
}

/**
 * Наличие подкатегорий (подтерминов)
 * @return boolean
 */
function has_children_terms($hide_empty=true, &$children=array()){
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

  if(sizeof($children) >= 1) {
    return true;
  }
  return false;
}

/**
 * Получить ID самой родительской страницы (после "главной")
 * @return absolute Int
 */
function get_parent_page_id($post) {
  if ($post->post_parent)  {
    $ancestors=get_post_ancestors($post->ID);
    $root=count($ancestors)-1;
    $parent = $ancestors[$root];
  } else {
    $parent = $post->ID;
  }
  return absint($parent);
}

/****************************************** Bread Crumbs ******************************************/
/**
 * yoast крошки ( Для активации установить/активировать плагин, дополнительно => breadcrumbs => enable )
 */
add_action( 'template_before_main_content', 'breadcrumbs_from_yoast', 10 );
add_action( 'woocommerce_before_main_content', 'breadcrumbs_from_yoast', 25 );
function breadcrumbs_from_yoast(){
  if ( function_exists('yoast_breadcrumb') && !is_front_page() ) {
    yoast_breadcrumb('<p id="breadcrumbs">','</p>');
  }
}
/******************************************* Navigation *******************************************/
function default_theme_nav( $args = array() ){
  $args = wp_parse_args( $args, array(
    'menu' => 'main_nav',
    'menu_class' => 'nav navbar-nav',
    'container_class' => 'container',
    'theme_location' => 'primary',
    'walker' => new Bootstrap_walker(),
    'allow_click' => get_theme_mod( 'allow_click', false ),
    'toggler' => '',
    ) );
  $brand = apply_filters( 'set_custom_brand', get_bloginfo("name"), 'navbar-brand hidden-lg-up text-center text-primary', get_bloginfo("description") );

  $container = array( '<nav class="navbar navbar-default non-responsive">', '</nav>' );
  if( get_theme_mod( 'responsive' ) ){
    $container = array( '<section class="navbar-default"><nav class="container navbar navbar-toggleable-md">', '</nav></section>' );
    $args['container_class'] = 'collapse navbar-collapse navbar-responsive-collapse';
    $args['container_id'] = 'default-collapse';
    $args['toggler'] = '
    <button class="navbar-toggler navbar-toggler-left" type="button" data-hide="#default-collapse">
      <span class="navbar-toggler-icon"></span>
    </button>';
  }

  echo $container[0], $args['toggler'], $brand, wp_nav_menu( $args ), $container[1];
}

function wp_footer_links( $args = array() ) {
  $args = wp_parse_args( $args, array(
      'menu' => 'footer_links',
      'theme_location' => 'footer',
      'container_class' => 'footer clearfix',
    ) );
  wp_nav_menu($args);
}

/***************************************** Title Templates ****************************************/
function get_advanced_title( $post_id = null, $args = array() ){
  $singular = ( isset($args['force_single']) || is_singular() ) ? true : false;
  $defaults = array(
    'tag'    => $singular ? 'h1' : 'h2',
    'class'  => 'post-title',
    'clear'  => false,
    'before' => '',
    'after'  => '',
    );
  $args = wp_parse_args( $args, $defaults );

  if(is_404()){
    return sprintf( '<%1$s class="%2$s error_not_found"> Ошибка #404: страница не найдена. </%1$s>',
      esc_attr($args['tag']),
      esc_attr($args['class']) );
  }

  if( $title = get_the_title($post_id) ){
    /**
     * Get Edit Post Icon
     */
    $edit_link = get_edit_post_link($post_id);
    $edit_tpl = '';
    if( $edit_link ){
      $edit_attrs = ' class=\'dashicons dashicons-welcome-write-blog no-underline\'';
      $edit_tpl = "<object><a href='{$edit_link}'{$edit_attrs}></a></object>";
    }

    /**
     * Get Title Template
     */
    if($args['clear'])
      return $title . $edit_tpl;

    $link = ( $singular ) ? array('', '') : array('<a href="'.get_permalink( $post_id ).'">', '</a>');

    return sprintf( $link[0].'<%1$s class="%2$s">%4$s %3$s %5$s %6$s</%1$s>'.$link[1],
      esc_attr($args['tag']),
      esc_attr($args['class']),
      $title,
      $args['before'],
      $args['after'],
      $edit_tpl
      );
  }

  // Title Not Found
  return false;
}

add_action('theme_render_title', 'add_advanced_title', 10, 2);
function add_advanced_title( $post_id, $args ){
  if( $title = get_advanced_title($post_id, $args) )
    echo $title;
}

function the_advanced_title( $post_id = null, $args = array() ){

  do_action( 'theme_render_title', $post_id, $args );
}

/**
 * Получить заголовок архива (отличается от стандартной функции отсутствием мультиязычности
 * и не выводит "Категория:", "Ярлык:", "Автор:", "Архивы:" )
 */
function get_advanced_archive_title() {
  if ( is_category() ) {
    $title = single_cat_title( '', false );
  } elseif ( is_tag() ) {
    $title = single_tag_title( '', false );
  } elseif ( is_author() ) {
    $title = '<span class="vcard">' . get_the_author() . '</span>';
  } elseif ( is_year() ) {
    $title = sprintf( 'Записи за %s год', get_the_date( _x( 'Y', 'yearly archives date format' ) ) );
  } elseif ( is_month() ) {
    $title = sprintf( 'Записи за %s месяц', get_the_date( _x( 'F Y', 'monthly archives date format' ) ) );
  } elseif ( is_day() ) {
    $title = sprintf( 'Записи за %s день', get_the_date( _x( 'F j, Y', 'daily archives date format' ) ) );
  } elseif ( is_tax( 'post_format' ) ) {
    if ( is_tax( 'post_format', 'post-format-aside' ) ) {
      $title = _x( 'Asides', 'post format archive title' );
    } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
      $title = _x( 'Galleries', 'post format archive title' );
    } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
      $title = _x( 'Images', 'post format archive title' );
    } elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
      $title = _x( 'Videos', 'post format archive title' );
    } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
      $title = _x( 'Quotes', 'post format archive title' );
    } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
      $title = _x( 'Links', 'post format archive title' );
    } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
      $title = _x( 'Statuses', 'post format archive title' );
    } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
      $title = _x( 'Audio', 'post format archive title' );
    } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
      $title = _x( 'Chats', 'post format archive title' );
    }
  } elseif ( is_post_type_archive() ) {
    $title = apply_filters( 'archive_'.get_post_type().'_title', post_type_archive_title( '', false ) );
  } elseif ( is_tax() ) {
    $title = single_term_title( '', false );
  } else {
    $title = __( 'Archives' );
  }

  return $title;
}

function the_advanced_archive_title(
  $before='',
  $after='') {

  $title = get_advanced_archive_title();
  if(!empty($title)){
    echo $before .'<h1 class="archive-title">'. $title .'</h1>'. $after;
    return true;
  }
  return false;
}

/******************************************** Thumbnail *******************************************/
add_filter('content_thumbnail_html', 'add_thumbnail_link', 10, 2);
function add_thumbnail_link($thumbnail, $post_id = false){
  $link = get_permalink($post_id);
  return "<a class='media-left' href='{$link}'>{$thumbnail}</a>";
}

function the_thumbnail( $post_id = false ){
  if( !$post_id ) $post_id = get_the_id();

  if( is_singular() ){
    $thumbnail = get_the_post_thumbnail(
      $post_id,
      apply_filters( 'content_full_image_size', 'medium' ),
      apply_filters( 'content_full_image_args', array('class' => 'al') )
    );
  }
  else {
    $thumbnail = get_the_post_thumbnail(
      $post_id,
      apply_filters( 'content_thumbnail_size', 'thumbnail' ),
      apply_filters( 'content_thumbnail_args', array('class' => 'al') )
    );
  }

  echo apply_filters( 'content_thumbnail_html', $thumbnail, $post_id );
}

/**************************************** Content Template ****************************************/
function get_tpl_content( $affix, $return = false ){
  if($return)
    ob_start();

  if( ! is_front_page() && is_archive() && !is_search() ){
    the_advanced_archive_title();
    the_archive_description( '<div class="taxonomy-description">', '</div>' );
  }

  echo "<div class='row'>";

  while ( have_posts() ){
    the_post();

    // need for search
    if( ! $affix )
      $affix = get_post_type();

    if( $affix != 'product' )
      get_template_part( 'template-parts/content', $affix );
  }

  echo "</div>";

  if($return)
    return ob_get_clean();
}
function get_tpl_search_content( $return = false ){
  ob_start();
  while ( have_posts() ){
    the_post();

    if( get_post_type() == 'product' )
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


/********************* Pagination - Принятые настройки постраничной навигации *********************/
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

/**
 * Добавить ссылку о разработчике в топбар
 */
add_action('admin_bar_menu', 'customize_toolbar_link', 999);
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

/**
 * Сменить строку "Спасибо за творчество с Wordpress"
 */
add_filter('admin_footer_text', 'custom_admin_footer');
function custom_admin_footer() {
  $ver = get_bloginfo('version');
  $char = get_bloginfo('charset');
  $wp_ver_str = $ver.'-'.$char;

  echo '<span id="footer-thankyou">Разработано компанией <a href="http://seo18.ru" target="_blank">seo18.ru - создание и продвижение сайтов</a></span>.
  <small> Использована система <a href="wordpress.com">WordPress ('.$wp_ver_str.')</a>. </small>';
}
