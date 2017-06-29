<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<?php wp_head(); ?>
	<!--[if lt IE 9]>
	  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body <?php body_class(); ?>>
	<div id="page" class="site">
		<div class="container site-header">
			<div class="row head-info">
				<div class="col-4 first-column">
				<?php
					do_action( 'first_head_column' );
				?>
				</div>
				<div class="col-4 second-column">
					<?php do_action( 'second_head_column' ); ?>
				</div>
				<div class="col-4 third-column">
					<?php do_action( 'third_head_column' ); ?>
				</div>
			</div><!--.row head-info-->
		</div>
		<?php default_theme_nav(); ?>
		<div id="content" class="site-content">