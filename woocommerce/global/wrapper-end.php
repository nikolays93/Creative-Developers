<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( is_active_sidebar('woocommerce') && apply_filters( 'sidebar_on_single', !is_singular( 'product' ) ) )
	do_action( 'woocommerce_sidebar' );
?>
		</div><!-- .col -->
	</div><!-- .row -->
</div><!-- .container -->