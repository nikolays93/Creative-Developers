<?php
	$post_class = ( is_singular() ) ? '' : 'media ';
	$post_class .= get_default_bs_columns(apply_filters( 'content_columns', 2 ));
?>
			<article <?php post_class($post_class); ?>>
				<?php the_thumbnail(); ?>
				<div class="media-body article-content">
					<?php the_advanced_title(); ?>
					<?php the_content('<span class="more meta-nav">Подробнее</span>'); ?>
				</div>
			</article>