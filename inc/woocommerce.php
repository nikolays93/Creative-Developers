<?php
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if( ! class_exists('WooCommerce') )
	return;

/**
 * @link * Globals
 * @link * Archive
 * @link * Single Product
 * @link * Order
 * @link * Checkout
 * @link * Account
 * @link * Filters
 */

/********************************** Globals ***********************************/
/**
 * Add Theme WooCommerce Support
 */
add_action( 'after_setup_theme', 'add_custom_theme_woocommerce_support' );
function add_custom_theme_woocommerce_support() {

	add_theme_support( 'woocommerce' );
}

/**
 * Вернет объект таксономии если на странице есть категории товара
 * @param  string $taxonomy название таксаномии (Не уверен что логично изменять)
 * @return array | false 	ids дочерних категорий | не удалось получить
 */
function get_children_product_terms($taxonomy = 'product_cat') {
	$children = array();
	if( is_shop() && !is_search() ){
		$results = get_terms( $taxonomy );
		if(is_wp_error( $results ))
			return false;

		foreach ($results as $term) {
			$children[] = $term->term_id;
		}
	}
	else {
		$current = get_queried_object();
		if( !empty($current->term_id) )
			$children = get_term_children( $current->term_id, $taxonomy );

		if(is_wp_error( $children ))
			return false;
	}

	if( sizeof($children) < 1 )
		return false; // Не удалось получить

	return $children;
}

/**
 * Disable Default WooCommerce Styles
 */
// add_filter( 'woocommerce_enqueue_styles', 'dp_dequeue_styles' );
function dp_dequeue_styles( $enqueue_styles ) {
    unset( $enqueue_styles['woocommerce-general'] );     // Отключение общих стилей
    unset( $enqueue_styles['woocommerce-layout'] );      // Отключение стилей шаблонов
    unset( $enqueue_styles['woocommerce-smallscreen'] ); // Отключение оптимизации для маленьких экранов
    return $enqueue_styles;
}

/**
 * yoast мякиши ( @see установить/активировать плагин, дополнительно => breadcrumbs )
 * @link https://wordpress.org/plugins/wordpress-seo/
 */
add_action( 'woocommerce_before_main_content', 'breadcrumbs_from_yoast', 5 );
function breadcrumbs_from_yoast(){
  if ( function_exists('yoast_breadcrumb') ) {
    echo "<div class='container'>";
    yoast_breadcrumb('<p id="breadcrumbs">','</p>');
    echo "</div>";
  }
}

/**
 * SideBar For WooCommerce
 */
add_action( 'widgets_init', 'init_woocommerce_sidebar' );
function init_woocommerce_sidebar(){
    register_sidebar( array(
        'name'          => 'Витрины магазина',
        'id'            => 'woocommerce',
        'description'   => 'Показываются на витринах магазина WooCommerce',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
        ) );
}

/*********************************** Archive **********************************/
/**
 * Change Default Placeholder
 */
add_filter('woocommerce_placeholder_img_src', 'placeholder_img_src');
function placeholder_img_src( $src ) {
	$ph = '/img/placeholder.png';
	if( is_readable( get_template_directory() . $ph ) )
		$src = get_template_directory_uri() . $ph;

	return $src;
}

/**
 * Используем формат цены вариативного товара WC 2.0 (к пр. "от 30 Р.")
 */
add_filter( 'woocommerce_variable_sale_price_html', 'wc_wc20_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wc_wc20_variation_price_format', 10, 2 );
function wc_wc20_variation_price_format( $price, $product ) {
    $prices = array(
        $product->get_variation_price( 'min', true ),
        $product->get_variation_price( 'max', true )
        );

    $price = wc_price( $prices[0] );
    if($prices[0] !== $prices[1])
        $price = 'от ' . $price;

    $prices = array(
        $product->get_variation_regular_price( 'min', true ),
        $product->get_variation_regular_price( 'max', true )
        );
    $saleprice = wc_price( $prices[0] );
    if( $prices[0] !== $prices[1] )
        $saleprice = 'от ' . $saleprice;

    if ( $price !== $saleprice ) {
        $price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins>';
    }

    return $price;
}

/******************************* Single Product *******************************/
/**
 * Change Default WC Tabs
 */
// add_filter( 'woocommerce_product_tabs', 'woo_change_tabs', 98 );
function woo_change_tabs( $tabs ) {
	global $post;

	if(isset($post->post_content) && strlen($post->post_content) < 55){
		unset($tabs['description']);
	} else {
		if(isset($tabs['description']))
			$tabs['description']['title'] = 'Описание товара';
	}

	if(isset($tabs['reviews']))
		unset( $tabs['reviews'] );

	if(isset($tabs['additional_information']))
		unset( $tabs['additional_information'] );

	return $tabs;
}

/************************************ Order ***********************************/
/**
 * Change Default WC Currency (to FA or 'P.')
 */
add_filter('woocommerce_currency_symbol', 'change_currency_symbol', 10, 2);
function change_currency_symbol( $currency_symbol, $currency ) {
    if( $currency == 'RUB' && !is_admin() ) {
        if( class_exists('DevelopersTools') && ! empty(DevelopersTools::$settings['FontAwesome']) ) {
                return '<i class="fa fa-rub"></i>';
        }
        $currency_symbol = 'Р.';
    }
    return $currency_symbol;
}

// add_filter( 'wc_order_statuses', 'change_wc_order_statuses' );
function change_wc_order_statuses( $order_statuses ) {
    // $order_statuses = array(
    //  'wc-pending'    => _x( 'Pending payment', 'Order status', 'woocommerce' ),
    //  'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
    //  'wc-on-hold'    => _x( 'On hold', 'Order status', 'woocommerce' ),
    //  'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
    //  'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
    //  'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
    //  'wc-failed'     => _x( 'Failed', 'Order status', 'woocommerce' ),
    //  );

    if( isset($order_statuses['wc-completed']) )
        $order_statuses['wc-completed'] = 'Оплачен';

    return $order_statuses;
}

/********************************** Checkout **********************************/
add_filter( 'woocommerce_default_address_fields', 'change_wc_default_address_fields', 20, 1 );
function change_wc_default_address_fields($fields){
    // $fields['first_name']['priority'] = 10;
    // $fields['last_name']['priority'] = 20;
    // $fields['company']['priority'] = 30;
    // $fields['country']['priority'] = 40;
    $fields['address_1']['priority'] = 70;
    unset( $fields['address_2'] );
    $fields['city']['priority'] = 60;
    $fields['state']['priority'] = 50;
    unset( $fields['postcode'] );

    $fields['last_name']['required'] = false;
    $fields['address_1']['required'] = false;

    foreach ($fields as $field) {
        $field['input_class'][] = 'form-control'; // add class for bootstrap
    }

    return $fields;
}

add_filter( 'woocommerce_checkout_fields' , 'change_woocommerce_checkout_fields', 15, 1 );
function change_woocommerce_checkout_fields( $fields ){
    $fields['billing']['billing_phone']['priority'] = 22;
    $fields['billing']['billing_email']['priority'] = 24;

    foreach (array('billing', 'shipping', 'account', 'order') as $field_key) {
        foreach ($fields[$field_key] as $key => &$field) {
            $field['input_class'][] = 'form-control'; // add class for bootstrap
        }
    }

    return $fields;
}

/*********************************** Account **********************************/
// add_filter ( 'woocommerce_account_menu_items', 'change_account_menu_items' );
function change_account_menu_items( $items ) {
    // $items = array(
    //  'dashboard' => __( 'Dashboard', 'woocommerce' ),
    //  'orders' => __( 'Orders', 'woocommerce' ),
    //  'downloads' => __( 'Downloads', 'woocommerce' ),
    //  'edit-address' => __( 'Addresses', 'woocommerce' ),
    //  'payment-methods' => __( 'Payment methods', 'woocommerce' ),
    //  'edit-account' => __( 'Account details', 'woocommerce' ),
    //  'customer-logout' => __( 'Logout', 'woocommerce' ),
    //  );

    unset( $items['downloads'] );

    return $items;
}

// add_filter( 'woocommerce_save_account_details_required_fields', 'change_account_required_inputs' );
function change_account_required_inputs( $required_fields ){
    $required_fields = array(
        // 'account_first_name' => __( 'First name', 'woocommerce' ),
        // 'account_last_name'  => __( 'Last name', 'woocommerce' ),
        'account_email'      => __( 'Email address', 'woocommerce' ),
        );
    return $required_fields;
}

/**
 * Do Not Log in after registration user.
 */
add_action('woocommerce_registration_redirect', 'logout_after_registration_redirect', 2);
function logout_after_registration_redirect() {
    wp_logout();
    return home_url('/my-account/?register_success=1&action=login');
}

/*********************************** Filters **********************************/
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );