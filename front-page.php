<?php
	get_header();
?>
<div class="container">
	<div class="row">
		<div id="primary" class="<?php echo ( is_show_sidebar() ) ? "col-9" : "col-12"; ?>">
			<main id="main" class="main content" role="main">
			<?php
				get_tpl_content();
			?>
			</main><!-- #main -->

		<?php get_sidebar(); ?>
		</div><!-- .col -->
	</div><!-- .row -->
</div><!-- .container -->
<?php
get_footer();