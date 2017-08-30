<?php

if ( ! defined( 'ABSPATH' ) )
  exit; // Exit if accessed directly

class templateCustomizer {
  const VIEWPORT = 1170;
  const PADDINGS = 15;

  function __construct(){
    add_action( 'customize_register', array(__CLASS__, 'print_settings') );
    add_action( 'wp_head', array(__CLASS__, 'set_dp_format') );
    add_filter( 'set_custom_brand', array(__CLASS__, 'add_custom_brand'), 10, 3 );

    if (get_theme_mod( 'allow_click', false ))
      add_action( 'wp_head', array(__CLASS__, 'allow_dropdown_click') );
  }

  /**
   * Настройки отображения
   */
  static function print_settings( $wp_customize ) {
    $wp_customize->add_section('display_options', array(
        'title'     => 'Настройки отображения',
        'priority'  => 50,
        'description' => 'Настройте внешний вид вашего сайта'
        ) );

    $wp_customize->add_setting('responsive', array('default'   => false));
    $wp_customize->add_control('responsive', array(
      'section'  => 'display_options',
      'label'    => 'Адаптивный шаблон',
      'description' => 'Если ваш шаблон адаптивный, включите это.',
      'type'     => 'checkbox'
      ) );

    $wp_customize->add_setting('allow_click', array('default'   => ''));
    $wp_customize->add_control('allow_click', array(
      'section'  => 'display_options',
      'label'    => 'Разрешить переход по ссылке выпадающего меню',
      'description' => '',
      'type'     => 'checkbox',
      ) );

    $wp_customize->add_setting('custom_body_font', array('default'   => ''));
    $wp_customize->add_control('custom_body_font', array(
      'section'  => 'display_options',
      'label'    => 'Шрифт',
      'type'     => 'select',
      'choices'  => $this->fonts_exist
      ) );

    $wp_customize->add_setting('custom_headlines_font', array('default'   => ''));
    $wp_customize->add_control('custom_headlines_font', array(
      'section'  => 'display_options',
      'label'    => 'Шрифт заголовков',
      'type'     => 'select',
      'choices'  => $this->fonts_exist
      ) );
  }

  static function set_dp_format(){
    if( get_theme_mod( 'responsive' ) ){
      $meta = '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">';
    } else {
      $w_container = self::VIEWPORT - ( self::PADDINGS * 2 );
      $meta = '<meta name="viewport" content="width='.self::VIEWPORT.'">' . "\r\n";
      $meta.='<style>.container {max-width: '.$w_container.'px !important;width: '.$w_container.'px !important;}</style>';
    }
    echo $meta;
  }

  /**
   * Логотип
   */
  static function add_custom_brand($brand, $brand_class, $brand_title){
    return sprintf("<a class='%2$s' title='%3$s' href='%4$s'>%1$s</a>",
      $brand,
      $brand_class,
      $brand_title,
      get_home_url()
      );
  }

  static function allow_dropdown_click(){

    echo '<style>.navbar-default .navbar-nav .nav-item:hover > .dropdown-menu { display: block; }</style>';
  }
}
new templateCustomizer();
