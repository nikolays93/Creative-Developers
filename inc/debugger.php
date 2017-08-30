<?php
if(!function_exists('is_wp_debug')){
  function is_wp_debug(){
    if( WP_DEBUG ){
      if( defined('WP_DEBUG_DISPLAY') && false === WP_DEBUG_DISPLAY){
        return false;
      }
      return true;
    }
    return false;
  }
}
if(!function_exists('_debug')){
  if( is_wp_debug() || current_user_can( 'activate_plugins' ) ){
    function _debug( &$var, $var_dump = false ){
      echo "<pre>";
      if($var_dump)
        print_r( $var );
      else
        var_dump( $var );
      echo "</pre>";
    }
  } else {
    function _debug( &$var, $var_dump = false ){
      return false;
    }
  }
}
if(!function_exists('_dump')){
  function _dump(&$var, $var_dump = false){
    _debug($var, $var_dump);
  }
}