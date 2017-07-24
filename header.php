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
				<div class="col-4 logotype">
				<?php
					the_custom_logo();
				?>
				</div>
				<div class="col-4 contacts">
					<?php
					 /**
					   * From Organized Contacts Plug-in
					   */
					  if( shortcode_exists( 'our_address' ) )
					    echo do_shortcode('[our_address]');
					  
					  // if( shortcode_exists( 'our_numbers' ) )
					  //   echo do_shortcode('[our_numbers]');
					  
					  // if( shortcode_exists( 'our_email' ) )
					  //   echo do_shortcode('[our_email]');
					  
					  // if( shortcode_exists( 'our_time_work' ) )
					  //   echo do_shortcode('[our_time_work]');
					  
					  // if( shortcode_exists( 'our_socials' ) )
					  //   echo do_shortcode('[our_socials]');
					  
					  // if( function_exists('get_company_number') )
					  //   echo get_company_number();
					?>
				</div>
				<div class="col-4 callback">
					<!-- <a href="#" id="get-recall"></a> -->
				</div>
			</div><!--.row head-info-->
		</div>
		<?php default_theme_nav(); ?>
		<div id="content" class="site-content">