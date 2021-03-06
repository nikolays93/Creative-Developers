<?php
function default_theme_nav(
  $args = array( 'container_class' => 'container' ),
  $container = array( '<nav class="navbar navbar-default non-responsive">', '</nav>' ),
  $toggler = '' ){

  if( TPL_RESPONSIVE ){
    $container = array( '<section class="navbar-default"><nav class="navbar navbar-expand-lg navbar-light bg-light"><div class="container">', '</div></nav></section>' );
    $args['container_class'] = 'collapse navbar-collapse navbar-responsive-collapse';
    $args['container_id'] = 'default-collapse';
    $toggler = '
    <button class="navbar-toggler navbar-toggler-left" type="button" data-hide="#default-collapse">
      <span class="navbar-toggler-icon"></span>
    </button>';
  }

  $brand = apply_filters( 'set_custom_brand', get_bloginfo("name"), 'navbar-brand hidden-lg-up text-center text-primary', get_bloginfo("description") );

  echo $container[0];
  echo $toggler;
  echo $brand;
  echo wp_bootstrap_nav( $args );
  echo $container[1];
}

function wp_bootstrap_nav( $args = array() ) {
  $defaults = array(
    'menu' => 'main_nav',
    'menu_class' => 'nav navbar-nav',
    'theme_location' => 'primary',
    'walker' => new Bootstrap_walker(),
    'allow_click' => get_theme_mod( 'allow_click', false )
    );

  $args = array_merge($defaults, $args);
  wp_nav_menu( $args );
}

function wp_footer_links() {
  wp_nav_menu(
    array(
      'menu' => 'footer_links', /* menu name */
      'theme_location' => 'footer', /* where in the theme it's assigned */
      'container_class' => 'footer clearfix', /* container class */
    )
  );
}

/**
 * Принятые настройки постраничной навигации
 */
function the_template_pagination( $echo = true ) {
    $args = apply_filters( 'theme_template_pagination', array(
        'show_all'     => false,
        'end_size'     => 1,
        'mid_size'     => 1,
        'prev_next'    => true,
        'prev_text'    => '« Пред.',
        'next_text'    => 'След. »',
        'add_args'     => false,
        ) );

    if( ! $echo ) {
        return get_the_posts_pagination($args);
    }

    the_posts_pagination($args);
}