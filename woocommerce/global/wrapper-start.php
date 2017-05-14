<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$col_class = ( is_active_sidebar('woocommerce') && !is_singular( 'product' ) ) ? 'col-9' : 'col-12';
?>
<div id="container" class="container">
	<div class="row">
		<div id="primary" class="content <?=$col_class;?>" role="main">