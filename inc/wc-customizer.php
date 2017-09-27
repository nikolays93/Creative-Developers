<?php

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

add_action( 'customize_register', 'print_wc_settings' );
function print_wc_settings( $wp_customize ){
    $section = 'display_wc_options';
    $wp_customize->add_section(
        $section,
        array(
            'title'     => 'Настройки WooCommerce',
            'priority'  => 60,
            'description' => 'Настройки шаблона WooCommerce'
            )
        );

    /**
     * @see wp_woo_shop_columns()
     */
    $wp_customize->add_setting( 'woo_product_columns', array('default' => '4') );
    $wp_customize->add_control(
        'woo_product_columns',
        array(
            'section'     => $section,
            'label'       => '',
            'description' => 'Колличество товара в строке',
            'type'        => 'number',
            )
        );

    /**
     * @see wp_woo_shop_columns()
     */
    $wp_customize->add_setting( 'woo_product_cat_columns', array('default' => '4') );
    $wp_customize->add_control(
        'woo_product_cat_columns',
        array(
            'section'     => $section,
            'label'       => '',
            'description' => 'Колличество категорий в строке',
            'type'        => 'number',
            )
        );

    /**
     * @see customize_per_page()
     */
    $wp_customize->add_setting( 'woo_item_count', array('default' => '16') );
    $wp_customize->add_control(
        'woo_item_count',
        array(
            'section'     => $section,
            'label'       => '',
            'description' => 'Товаров на странице',
            'type'        => 'number',
            )
        );

    /**
     * @see customize_per_page()
     */
    $wp_customize->add_setting( 'woo_item_count_mobile', array('default' => '8') );
    $wp_customize->add_control(
        'woo_item_count_mobile',
        array(
            'section'     => $section,
            'label'       => '',
            'description' => 'Товаров на странице (Для мал. экранов)',
            'type'        => 'number',
            )
        );

    /**
     * @see replace_cat_description_to_bottom()
     */
    $wp_customize->add_setting( 'archive_description_bottom', array('default' => 'on') );
    $wp_customize->add_control(
        'archive_description_bottom',
        array(
            'section'     => $section,
            'label'       => 'Описание таксаномий снизу',
            'description' => 'Показывать описание к странице снизу',
            'type'        => 'checkbox',
            )
        );

    /**
     * @see change_product_labels() AND change_wc_menu_labels()
     */
    $wp_customize->add_setting( 'woo_product_label', array('default' => '') );
    $wp_customize->add_control(
        'woo_product_label',
        array(
            'section'     => $section,
            'label'       => '',
            'description' => 'Заменить "Товары" на..',
            'type'        => 'text',
            )
        );

    /**
     * @see woo_remove_category_products_count()
     */
    $wp_customize->add_setting( 'woo_show_tax_count', array('default' => '') );
    $wp_customize->add_control(
        'woo_show_tax_count',
        array(
            'section'     => $section,
            'label'       => 'Показывать колличество товара таксономии в скобках',
            'description' => '',
            'type'        => 'checkbox',
            )
        );
}

// Определяем сетку вывода товара
add_filter( 'loop_shop_columns', 'wp_woo_shop_columns' );
function wp_woo_shop_columns( $columns, $is_tax=false ) {
    if( $is_tax && get_children_product_terms() !== false ){
        $columns = (int)get_theme_mod( 'woo_product_cat_columns', 4 );
        return ( $columns < 1) ? 4 : $columns;
    }

    $columns = (int)get_theme_mod( 'woo_product_columns', 4 );
    return ( $columns < 1) ? 4 : $columns;
}

// Количество товаров на странице
add_filter( 'loop_shop_per_page', 'customize_per_page', 20 );
function customize_per_page($cols){
    if(wp_is_mobile())
        return get_theme_mod( 'woo_item_count_mobile', 8 );

    return get_theme_mod( 'woo_item_count', 16 );
}

if( get_theme_mod( 'woo_product_label' ) ) replace_cat_description_to_bottom();
function replace_cat_description_to_bottom() {
    remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
    remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
    add_action( 'woocommerce_after_main_content', 'woocommerce_taxonomy_archive_description', 5 );
    add_action( 'woocommerce_after_main_content', 'woocommerce_product_archive_description', 5 );
}

// Заменить "Товары" на..
add_action( 'init', 'change_product_labels' );
function change_product_labels() {
    global $wp_post_types;

    $label = $wp_post_types['product']->label = get_theme_mod( 'woo_product_label', 'Каталог' );
    $wp_post_types['product']->labels->name      = __( $label );
    $wp_post_types['product']->labels->all_items = __( $label );
    $wp_post_types['product']->labels->archives  = __( $label );
    $wp_post_types['product']->labels->menu_name = __( $label );
}

add_action( 'admin_menu', 'change_wc_menu_labels' );
function change_wc_menu_labels() {
    global $menu;

    foreach ($menu as $key => $value) {
        if($value[0] == 'WooCommerce')
            $menu[$key][0] = 'Магазин';

        if($value[0] == 'Товары')
            $menu[$key][0] = get_theme_mod( 'woo_product_label', 'Каталог' );
    }
}

/**
 * Не показывать количество товаров в категории
 */
add_filter( 'woocommerce_subcategory_count_html', 'woo_remove_category_products_count' );
function woo_remove_category_products_count( $count_html ) {
    return ( get_theme_mod( 'woo_show_tax_count', false ) ) ? $count_html : false;
}
