<?php
if (!is_active_sidebar('archive') && !is_active_sidebar('woocommerce') )
	return;

function aside_start(){
	echo '</div>';
	echo '<div id="secondary" class="col-3">';
	echo '<aside class="widget-area" role="complementary">';
}

function aside_end(){
	echo '</aside>';
}

$is_commerce = function_exists('is_woocommerce') ?
	(is_woocommerce() || is_shop() || is_cart() || is_checkout() || is_account_page()) : false;

if( !$is_commerce ){
	add_action( 'before_sidebar', 'aside_start', 10 );
	add_action( 'after_sidebar',  'aside_end', 10 );
}

do_action('before_sidebar');

if (  $is_commerce ){
	dynamic_sidebar( 'woocommerce' );
}
else {
	dynamic_sidebar( 'archive' );
}

do_action('after_sidebar');