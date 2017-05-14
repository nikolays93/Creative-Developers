<?php
if ( ! defined( 'ABSPATH' ) )
  exit; // Exit if accessed directly

// [our_address], [our_numbers], [our_email], [our_time_work], [our_socials] - for easy use
function get_company_info( $field, $filter = 'the_content' ){
  $info = get_theme_mod( 'company_' . $field );
  if($filter == 'the_content')
    return str_replace( ']]>', ']]&gt;', wpautop(wptexturize( $info )) );
  elseif( $filter )
    return apply_filters( $filter, $info );

  return $info;
}
function get_company_address(){ return get_company_info('address'); }
function get_company_numbers(){ return get_company_info('numbers'); }
function get_company_time_work(){ return get_company_info('time_work'); }
function get_company_email(){ return get_company_info('email'); }
function get_company_socials(){ return get_company_info('socials'); }
function get_company_first_number( $del = ',', $num=0, $filter = 'the_content' ) {
  // for shortcode
  if(! $del) $del = ',';
  if(! $num) $num = 0;
  if(! $filter || $filter != false) $filter = 'the_content';

  $numbers = get_company_info('numbers', false);
  $info = explode($del, $numbers);
  
  if($filter == 'the_content')
    return str_replace( ']]>', ']]&gt;', wpautop(wptexturize( $info[$num] )) );
  elseif( $filter )
    return apply_filters( $filter, $info[$num] );

  return $info[$num];
}
add_shortcode('our_address', 'get_company_address');
add_shortcode('our_numbers', 'get_company_numbers');
add_shortcode('our_first_number', 'get_company_first_number');
add_shortcode('our_time_work', 'get_company_time_work');
add_shortcode('our_email', 'get_company_email');
add_shortcode('our_socials', 'get_company_socials');

function company_display_settings($wp_customize){
  $wp_customize->add_section('company_options', array(
    'title'     => 'Информация о компании',
    'priority'  => 60,
    'description' => 'Добавьте информации о своей организации'
    )
  );

  $wp_customize->add_setting('company_address');
  $wp_customize->add_control('company_address',
    array(
      'type'     => 'textarea',
      'label'    => 'Адрес',
      'section'  => 'company_options',
      )
    );

  $wp_customize->add_setting('company_numbers');
  $wp_customize->add_control('company_numbers',
    array(
      'type'     => 'textarea',
      'label'    => 'Номера телефонов',
      'section'  => 'company_options',
      )
    );

  $wp_customize->add_setting('company_email');
  $wp_customize->add_control('company_email',
    array(
      'type'     => 'text',
      'label'    => 'Email адрес',
      'section'  => 'company_options',
      )
    );

  $wp_customize->add_setting('company_time_work');
  $wp_customize->add_control('company_time_work',
    array(
      'type'     => 'textarea',
      'label'    => 'Режим работы',
      'section'  => 'company_options',
      )
    );

  $wp_customize->add_setting('company_socials');
  $wp_customize->add_control('company_socials',
    array(
      'type'     => 'textarea',
      'label'    => 'Социальные сети',
      'section'  => 'company_options',
      )
    );
}
add_action( 'customize_register', 'company_display_settings' );