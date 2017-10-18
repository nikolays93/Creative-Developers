<?php
	get_header();
?>
<div class="container">
	<?php
		if ( !is_front_page() )
			breadcrumbs_from_yoast();
	?>
	<div class="row">
		<div id="primary" class="<?php echo ( is_show_sidebar() ) ? "col-9" : "col-12"; ?>">
			<main id="main" class="main content" role="main">
			<?php
				if ( have_posts() ){
					if( is_search() ){
						echo sprintf('<header class="archive-header"><h1>%s %s</h1></header>',
							'Результаты поиска:',
							get_search_query()
							);

						get_tpl_search_content();
					}
					else {
						if( ! is_front_page() && is_archive() ){
							the_archive_title('<h1 class="taxanomy-title">', '</h1>');
							the_archive_description( '<div class="taxonomy-description">', '</div>' );
						}

						get_tpl_content();
					}

					the_template_pagination();
				}
				else {
					if( ! is_front_page() )
						get_template_part( 'template-parts/content', 'none' );
				}
			?>
			</main><!-- #main -->

		<?php get_sidebar(); ?>
		</div><!-- .col -->
	</div><!-- .row -->
</div><!-- .container -->
<?php
get_footer();