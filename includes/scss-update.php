<?php

function update_theme_styles( $filemtime, $option_name, $is_compressed ) {
    if( (defined('AUTO_COMPILE') && AUTO_COMPILE) || ! empty( $_GET['scss_upd'] ) ) {
        $filename = THEME . '/style.scss';
        if( current_user_can( 'administrator' ) &&  filemtime($filename) != $filemtime ) {
            $scss = new scssc();

            if ( $is_compressed ) {
                $scss->setFormatter( 'scss_formatter_compressed' );
            }

            $scss->addImportPath( THEME );

            $cyrilic = "/[\x{0410}-\x{042F}]+.*[\x{0410}-\x{042F}]+/iu";
            $excluded_cyr = preg_replace( $cyrilic, "", file_get_contents($filename) );

            file_put_contents( str_replace('.scss', '.css', $filename), $scss->compile( $excluded_cyr ) );
            $filemtime = filemtime($filename);
            update_option( $option_name, $filemtime );
        }

        // if( ! empty( $_GET['scss_upd'] ) ) {
        //     Редирект не сработает, потому что функция сработает в head (что означает что html уже выведен)
        //     wp_redirect( remove_query_arg( 'scss_upd', home_url($_SERVER['REQUEST_URI']) ) );
        //     wp_die('Стили обновлены');
        // }

        return $filename;
    }
}

if( ! defined('AUTO_COMPILE') || ! AUTO_COMPILE ) {
    add_action('admin_bar_menu', 'add_update_style_button', 999);
    function add_update_style_button( $wp_admin_bar ) {
        $wp_admin_bar->add_node( array(
            'id'    => 'scss-update',
            'title' => __('Обновить стили'),
            'href'  => add_query_arg( 'scss_upd', 1 ),
            'meta'  => array(
                'title' => 'Скомпилировать css стили из scss',
                ),
            ) );
    }
}
