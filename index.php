<?php
	get_header();

	$primary_class = "col-12";
	$type = $affix = get_post_type();

	if($type == 'post')
		$affix = get_post_format();
?>

<div class="container">
	<?php if ( !is_front_page() ) breadcrumbs_from_yoast(); ?>
	<div class="row">
		<?php
		if ( $type == 'post' && is_active_sidebar( 'archive' ) ){
			$primary_class = "col-9";
			
			get_sidebar();
		}
		?>
		<div id="primary" class="<?php echo $primary_class; ?>">
			<main id="main" class="main content" role="main">
			<?php
				if ( have_posts() ){
					if( is_search() ){
						echo'
						<header class="archive-header">
							<h1>Результаты поиска: '. get_search_query().'</h1>
						</header>';

						get_tpl_search_content();
					}
					else {
						if( ! is_front_page() ){
							the_advanced_archive_title();
							the_archive_description( '<div class="taxonomy-description">', '</div>' );
						}

						get_tpl_content( $affix );

					}

					the_template_pagination();
				}
				else {
					if( ! is_front_page() )
						get_template_part( 'template-parts/content', 'none' );
				}
			?>	
			</main><!-- #main -->
		</div><!-- #primary -->
	</div><!-- .row -->
</div><!-- .container -->
<?php
get_footer();