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
				<div class="col-4 logotype"><?php the_custom_logo(); ?></div>
				<div class="col-4">
					<?php echo do_shortcode('[our_address]'); ?>
				</div>
				<div class="col-4">
					<?php // echo do_shortcode('[our_numbers]'); ?>
					<?php // echo do_shortcode('[our_email]'); ?>
					<?php // echo do_shortcode('[our_time_work]'); ?>
					<?php // echo do_shortcode('[our_socials]'); ?>
					<?php // echo get_company_first_number(); ?>
				</div>
			</div><!--.row head-info-->
		</div>
		<?php default_theme_nav(); ?>
		<div id="content" class="site-content">