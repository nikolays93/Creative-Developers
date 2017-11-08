<?php

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

if( ! defined('TPL_RESPONSIVE') ) {
    define('TPL_RESPONSIVE', false);
}

define('TPL_VIEWPORT', 1170);
define('TPL_PADDINGS', 15);

add_action('wp_head', 'template_viewport_html');
function template_viewport_html() {
    if( TPL_RESPONSIVE ) {
        echo '
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">';
    }
    else {
        $max_width = TPL_VIEWPORT - ( TPL_PADDINGS * 2 );

        echo '
        <meta name="viewport" content="width='.TPL_VIEWPORT.'">
        <style type="text/css">
            .container {
                max-width: '.$max_width.'px !important;
                width: '.$max_width.'px !important;
            }
        </style>';
    }
}

/**
 * Мякиш от yoast SEO ( установить/активировать плагин, дополнительно => breadcrumbs )
 *
 * @link https://wordpress.org/plugins/wordpress-seo/
 */
function breadcrumbs_from_yoast(){
    if ( function_exists('yoast_breadcrumb') ) {
        echo "<div class='container'>";
        yoast_breadcrumb('<p id="breadcrumbs">','</p>');
        echo "</div>";
    }
}

/**
 * Шаблон заголовка
 */
function get_advanced_title( $post_id = null, $args = array() ){
    $args = wp_parse_args( $args, array(
        'title_tag' => '',
        'title_class' => 'post-title',
        'clear' => false,
        'force' => false, // multiple | single
        ) );

    switch ( $args['force'] ) {
        case 'single':
            $is_singular = true;
            break;
        case 'multiple':
            $is_singular = false;
            break;
        default:
            $is_singular = is_singular();
            break;
    }

    if( ! $args['title_tag'] ) {
        $args['title_tag'] = $is_singular ? 'h1' : 'h2';
    }

    if( is_404() ) {
        return sprintf( '<%1$s class="%2$s error_not_found"> Ошибка #404: страница не найдена. </%1$s>',
            esc_html( $args['title_tag'] ),
            esc_attr( $args['title_class'] )
            );
    }

    /**
     * Get Title
     */
    if( ! $title = get_the_title( $post_id ) ) {
        // Title Not Found
        return false;
    }

    /**
     * Get Edit Post Icon
     */
    $edit_tpl = sprintf('<object><a href="%s" class="%s"></a></object>',
        get_edit_post_link( $post_id ),
        'dashicons dashicons-welcome-write-blog no-underline'
        );

    if( $args['clear'] ) {
        return $title . ' ' . $edit_tpl;
    }

    $result = array();

    if( ! $is_singular ) $result[] = sprintf('<a href="%s">', get_permalink( $post_id ));

    $result[] = "\t" . sprintf('<%1$s class="%2$s">%3$s %4$s</%1$s>',
        esc_html( $args['title_tag'] ),
        esc_attr( $args['title_class'] ),
        $title,
        $edit_tpl
        );

    if( ! $is_singular ) $result[] = '</a>';

    return implode("\r\n", $result);
}

function the_advanced_title( $post_id = null, $args = array() ){
    $args = wp_parse_args( $args, array(
        'before' => '',
        'after'  => '',
        ) );

    if( $title = get_advanced_title($post_id, $args) ) {
        echo $args['before'] . $title . $args['after'];
    }

    do_action( 'theme_after_title', $title );
}

add_filter( 'get_the_archive_title', 'theme_archive_title_filter', 10, 1 );
function theme_archive_title_filter( $title ) {
    $title = preg_replace("/[\w]+: /ui", "", $title);

    return $title;
}

/**
 * Добавить ссылку на превью
 *
 * @param html $thumbnail HTML Код превью
 * @param int  $post_id   ИД записи превью которой добавляем ссылку
 */
function add_thumbnail_link( $thumbnail, $post_id ) {
    if( ! $thumbnail ) return '';
    $link = get_permalink( absint($post_id) );
    $thumbnail_html = sprintf('<a class="media-left" href="%s">%s</a>',
        esc_url( $link ),
        $thumbnail);

    return $thumbnail_html;
}

function the_thumbnail( $post_id = false, $add_link = false ) {
    if( 0 >= $post_id = absint($post_id) ) {
        $post_id = get_the_id();
    }

    if( is_singular() ) {
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

    if( $add_link ) {
        $thumbnail = add_thumbnail_link( $thumbnail, $post_id );
    }

    $thumbnail_html = apply_filters( 'content_image_html', $thumbnail, $post_id, $add_link );

    echo $thumbnail_html;
}

/**
 * Шаблон вывода записей
 *
 * @param  string  $affix  post_type
 * @param  boolean $return print or return
 * @return html
 */
function get_tpl_content( $affix = false, $return = false, $container = 'row', $query = null ) {
    $templates = array();
    $slug = 'template-parts/content';

    if( ! $affix ) {
        $type = $affix = get_post_type();

        if($type == 'post')
            $affix = get_post_format();
    }

    if( $query && ! $query instanceof WP_Query ) {
      return false;
    }

    if( $return ) ob_start();

    if( $container ) {
        echo sprintf('<div class="%s">', esc_attr( $container ));
    }

    while ( $query ? $query->have_posts() : have_posts() ){
        $query ? $query->the_post() : the_post();

        // need for search
        if( $affix === false ) {
            $affix = get_post_type();
        }

        if( 'product' !== $affix ) {
            if( is_single() ) {
                $templates[] = "{$slug}-{$affix}-single.php";
                $templates[] = "{$slug}-single.php";
            }
            elseif ( '' !== $affix ) {
                $templates[] = "{$slug}-{$affix}.php";
            }

            $templates[] = "{$slug}.php";

            locate_template($templates, true, false);
        }
    }

    if( $container ) echo '</div>';

    wp_reset_postdata();

    if( $return ) return ob_get_clean();
}

function get_tpl_search_content( $return = false ) {
    ob_start();

    while ( have_posts() ) {
        the_post();

        if( 'product' === get_post_type() ) {
            wc_get_template_part( 'content', 'product' );
        }
    }

    $products = ob_get_clean();
    $content = get_tpl_content( false, true );

    if ( $products ) {
        $products = "<ul class='products row'>" . $products . "</ul>";
    }

    if( $return ){
        return $products . $content;
    }

    echo $products . $content;
}

/**
 * Показывать sidebar или нет
 *
 * @return boolean / (string) sidebar name
 */
function is_show_sidebar() {
    $show_sidebar = false;

    if( ! is_singular() ) {
        $post_type = get_post_type();
        $enable_types = apply_filters( 'sidebar_archive_enable_on_type', array('post', 'page') );

        if( function_exists('is_woocommerce') ){
            if( (is_woocommerce() || is_shop()) && is_active_sidebar('woocommerce') ) {
                $show_sidebar = 'woocommerce';
            }
            if( is_cart() || is_checkout() || is_account_page() ) {
                $show_sidebar = false;
            }
            elseif( is_active_sidebar('archive') && in_array($post_type, $enable_types) ) {
                $show_sidebar = 'archive';
            }
        }
        else {
            if( is_active_sidebar('archive') && in_array($post_type, $enable_types) ) {
                $show_sidebar = 'archive';
            }
        }
    }

    return apply_filters( 'enable_sidebar', $show_sidebar );
}

/**
 * Наличие подкатегорий (подтерминов)
 */
function has_children_terms( $hide_empty = true ) {
    $o = get_queried_object();
    if( ! empty( $o->has_archive ) && $o->has_archive == true ) {
        $tax = $o->taxonomies[0];
        $parent = 0;
    }

    if( ! empty( $o->term_id ) ) {
        $tax = $o->taxonomy;
        $parent = $o->term_id;
    }

    $children = get_terms( array(
        'taxanomy'   => $tax,
        'parent'     => $parent,
        'hide_empty' => $hide_empty,
        'number'     => 1,
        ) );

    if( $children ) {
        return true;
    }

    return false;
}

/**
 * Получить ID самой родительской страницы (после "главной")
 */
function get_parent_page_id( $post ) {
    if ($post->post_parent)  {
        $ancestors = get_post_ancestors( $post->ID );
        $parent = $ancestors[ count($ancestors) - 1 ];
    } else {
        $parent = $post->ID;
    }

    return $parent;
}

/*******************************************************************************
 * Template Filters and Actions
 */
add_filter( 'post_class', 'add_theme_post_class', 10, 3 );
function add_theme_post_class($classes, $class, $post_id) {
    if( 'product' !== get_post_type() ) {
        if( is_singular() ) {
            $columns = apply_filters( 'single_content_columns', 1 );
        }
        else {
            $columns = apply_filters( 'content_columns', 1 );
        }

        $classes[] = get_default_bs_columns( (int)$columns );
    }

    return $classes;
}

/**
 * Логотип
 */
add_filter( 'set_custom_brand', 'add_custom_brand', 10, 3 );
function add_custom_brand( $brand, $brand_class, $brand_title ) {
    $home_url = get_home_url();

    $brand = sprintf('<a class="%1$s" title="%2$s" href="%3$s">%4$s</a>',
        esc_attr( $brand_class ),
        esc_attr( $brand_title ),
        esc_url( $home_url ),
        $brand );

    return $brand;
}

/**
 * Русскоязычная дата
 */
add_filter('the_time', 'the_russian_date');
add_filter('get_the_time', 'the_russian_date');
add_filter('the_date', 'the_russian_date');
add_filter('get_the_date', 'the_russian_date');
add_filter('the_modified_time', 'the_russian_date');
add_filter('get_the_modified_date', 'the_russian_date');
add_filter('get_post_time', 'the_russian_date');
add_filter('get_comment_date', 'the_russian_date');
function the_russian_date( $tdate = '' ) {
    if ( substr_count($tdate , '---') > 0 ) {
        return str_replace('---', '', $tdate);
    }

    $treplace = array (
        "Январь" => "января",
        "Февраль" => "февраля",
        "Март" => "марта",
        "Апрель" => "апреля",
        "Май" => "мая",
        "Июнь" => "июня",
        "Июль" => "июля",
        "Август" => "августа",
        "Сентябрь" => "сентября",
        "Октябрь" => "октября",
        "Ноябрь" => "ноября",
        "Декабрь" => "декабря",

        "January" => "января",
        "February" => "февраля",
        "March" => "марта",
        "April" => "апреля",
        "May" => "мая",
        "June" => "июня",
        "July" => "июля",
        "August" => "августа",
        "September" => "сентября",
        "October" => "октября",
        "November" => "ноября",
        "December" => "декабря",

        "Sunday" => "воскресенье",
        "Monday" => "понедельник",
        "Tuesday" => "вторник",
        "Wednesday" => "среда",
        "Thursday" => "четверг",
        "Friday" => "пятница",
        "Saturday" => "суббота",

        "Sun" => "воскресенье",
        "Mon" => "понедельник",
        "Tue" => "вторник",
        "Wed" => "среда",
        "Thu" => "четверг",
        "Fri" => "пятница",
        "Sat" => "суббота",

        "th" => "",
        "st" => "",
        "nd" => "",
        "rd" => ""
        );
    return strtr($tdate, $treplace);
}

/**
 * Отчистить мета теги
 */
add_action( 'init', 'template_head_cleanup' );
function template_head_cleanup() {
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
