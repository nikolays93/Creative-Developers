<?php
/**
 * Получить стандартные классы ячейки bootstrap сетки
 * Исключить аттрибуты width и height из тега img
 * Шаблон вывода главного меню
 */
if ( ! defined( 'ABSPATH' ) )    exit; // Exit if accessed directly

/**
 * Получить стандартные классы ячейки bootstrap сетки
 */
function get_default_bs_columns($columns_count="4", $non_responsive=true){
  switch ($columns_count) {
    case '1': $col = 'col-12'; break;
    case '2': $col = (!$non_responsive) ? 'col-6 col-sm-6 col-md-6 col-lg-6' : 'col-6'; break;
    case '3': $col = (!$non_responsive) ? 'col-12 col-sm-6 col-md-4 col-lg-4' : 'col-4'; break;
    case '4': $col = (!$non_responsive) ? 'col-6 col-sm-4 col-md-3 col-lg-3' : 'col-3'; break;
    case '5': $col = (!$non_responsive) ? 'col-12 col-sm-6 col-md-2-4 col-lg-2-4' : 'col-2-4'; break; // be careful
    case '6': $col = (!$non_responsive) ? 'col-6 col-sm-4 col-md-2 col-lg-2' : 'col-2'; break;
    case '12': $col= (!$non_responsive) ? 'col-4 col-sm-3 col-md-1 col-lg-1' : 'col-1'; break;

    default: $col = false; break;
  }
  return $col;
}

/**
 * Исключить аттрибуты width и height из тега img при выводе thumbnail'а
 */
function wp_bootstrap_remove_thumbnail_dimensions( $html ) {
  $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
  return $html;
  }
  add_filter( 'post_thumbnail_html', 'wp_bootstrap_remove_thumbnail_dimensions', 10 );
  add_filter( 'image_send_to_editor', 'wp_bootstrap_remove_thumbnail_dimensions', 10 );

/**
 * Добавить все главным меню элементам li.nav-item и li.nav-item.active активному элементу
 */
function wp_add_bootstrap_class($classes, $item) {
  if( $item->menu_item_parent == 0 && in_array('current-menu-item', $classes) )
    $classes[] = "active";

  if($item->menu_item_parent == 0)
    $classes[] = "nav-item";

  return $classes;
  }
  add_filter('nav_menu_css_class', 'wp_add_bootstrap_class', 10, 2 );

/**
 * Шаблон вывода главного меню
 */
class Bootstrap_walker extends Walker_Nav_Menu{

  function start_el(&$output, $object, $depth = 0, $args = Array(), $current_object_id = 0){
  if( is_object($args) ):
   global $wp_query;
   $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

   $class_names = $value = '';

    // If the item has children, add the dropdown class for bootstrap


    if ( $args->has_children ) {
      $class_names = "dropdown ";
    }

    $classes = empty( $object->classes ) ? array() : (array) $object->classes;

    $class_names .= join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $object ) );
    $class_names = ' class="'. esc_attr( $class_names ) . '"';

    $output .= $indent . '<li id="menu-item-'. $object->ID . '"' . $value . $class_names .'>';

    $attributes  = ! empty( $object->attr_title ) ? ' title="'  . esc_attr( $object->attr_title ) .'"' : '';
    $attributes .= ! empty( $object->target )     ? ' target="' . esc_attr( $object->target     ) .'"' : '';
    $attributes .= ! empty( $object->xfn )        ? ' rel="'    . esc_attr( $object->xfn        ) .'"' : '';
    $attributes .= ! empty( $object->url )        ? ' href="'   . esc_attr( $object->url        ) .'"' : '';

    // if the item has children add these two attributes to the anchor tag
    if ( $args->has_children ) {
      if($depth == 0){
        if(empty($args->allow_click))
          $attributes .= ' class="nav-link dropdown-toggle" data-toggle="dropdown"';
        else
          $attributes .= ' class="nav-link dropdown-toggle"';
      } else {
        $attributes .= ' class="dropdown-item"';
      }
    // not has children
    } else {
      if($depth == 0)
        $attributes .= ' class="nav-link"';
      else
        $attributes .= ' class="dropdown-item"';
    }

    $item_output = $args->before;
    $item_output .= '<a'. $attributes .'>';
    $item_output .= $args->link_before .apply_filters( 'the_title', $object->title, $object->ID );
    $item_output .= $args->link_after;
    $item_output .= '</a>';
    $item_output .= $args->after;

    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $object, $depth, $args );
  endif; // is_object
  } // end start_el function

  function start_lvl(&$output, $depth = 0, $args = Array()) {
    $indent = str_repeat("\t", $depth);
    $output .= "\n$indent<ul class=\"dropdown-menu\">\n";
  }

  function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ){
    $id_field = $this->db_fields['id'];
    if ( is_object( $args[0] ) ) {
        $args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
    }
    return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
  }
}