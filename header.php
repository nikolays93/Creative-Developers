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
			<div itemscope itemtype="http://schema.org/LocalBusiness">
				<div class="row head-info">
					<div class="col-4 logotype">
					<?php
						echo do_shortcode('[company field="image"]');
					?>
					</div>
					<div class="col-4 contacts">
						<?php
						 /**
						   * From Organized Contacts Plug-in
						   */
						  if( shortcode_exists( 'company' ) ) {
						  	echo do_shortcode('[company field="name"]');
						    echo do_shortcode('[company field="address"]');
						    echo do_shortcode('[company field="numbers"]');
						    echo do_shortcode('[company field="email"]');
						    echo do_shortcode('[company field="time_work"]');
						    echo do_shortcode('[company field="socials"]');

						    // echo do_shortcode('[phone del="," num="1"]'); // only first phone between ,
						  }
						?>
					</div>
					<div class="col-4 callback">
						<!-- <a href="#" id="get-recall"></a> -->
					</div>
				</div><!--.row head-info-->

				<div class="hidden-xs-up">
					<span itemprop="priceRange">RUB</span>
				</div>
			</div>
		</div>
		<?php default_theme_nav(); ?>
		<div id="content" class="site-content">