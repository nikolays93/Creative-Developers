<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Логотип на форме входа
 */
add_action('login_header', 'login_header_logo_css');
function login_header_logo_css() {
  if( $logo_id = get_theme_mod( 'custom_logo' ) ) {
    $logo_src = wp_get_attachment_image_src( $logo_id, 'full' );
    echo "
    <style type=\"text/css\">
    .login h1 a {
      background: url('$logo_src[0]');
      width: $logo_src[1]px;
      height: $logo_src[2]px;
    }
    </style> \r\n";
  }
}

add_filter( 'login_headerurl', 'home_url', 10, 0 );

/**
 * Добавить ссылку о разработчике в топбар
 */
add_action('admin_bar_menu', 'customize_toolbar_link', 9999);
function customize_toolbar_link( $wp_admin_bar ) {
    if( ! current_user_can( 'edit_pages' ) ) {
        $wp_admin_bar->remove_menu( 'wp-logo' );
    }

    $id = 'Seo18';
    $wp_admin_bar->add_node( array(
        'id' => $id,
        'title' => $id . '.ru',
        'href' => 'http://' . $id . '.ru',
        'meta' => array(
            'title' => 'Перейти на сайт разработчика',
            ),
        ) );
}

/**
 * Сменить строку "Спасибо за творчество с Wordpress"
 */
add_filter('admin_footer_text', 'custom_admin_footer');
function custom_admin_footer() {
    $wp_ver_str = get_bloginfo('version') . '-'. get_bloginfo('charset');

    echo '
    <span id="footer-thankyou">Разработано компанией
    <a href="http://seo18.ru" target="_blank">seo18.ru - создание и продвижение сайтов</a>
    </span>.
    <small> Использована система <a href="wordpress.com">WordPress (' . $wp_ver_str . ')</a>. </small>';
}
