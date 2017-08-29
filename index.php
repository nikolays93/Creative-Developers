<?php
	get_header();

	$type = $affix = get_post_type();

	if($type == 'post')
		$affix = get_post_format();
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
						echo'
						<header class="archive-header">
							<h1>Результаты поиска: '. get_search_query().'</h1>
						</header>';

						get_tpl_search_content();
					}
					else {
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

		<?php get_sidebar(); ?>
		</div><!-- .col -->
	</div><!-- .row -->
</div><!-- .container -->
<?php
get_footer();