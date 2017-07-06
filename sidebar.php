<?php
if ( !is_active_sidebar('archive') && !is_active_sidebar('woocommerce') )
	return;

if( !is_show_sidebar() )
	return;

function aside_start(){
	echo '</div>';
	echo '<div id="secondary" class="col-3">';
	echo '	<aside class="widget-area" role="complementary">';
}

function aside_end(){
	echo '	</aside>';
}

add_action( 'before_sidebar', 'aside_start', 10 );
add_action( 'after_sidebar',  'aside_end', 10 );

do_action('before_sidebar');

dynamic_sidebar( is_show_sidebar() );

do_action('after_sidebar');