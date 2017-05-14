				<article id="post-<?php the_ID(); ?>" <?php post_class('media col-12'); ?>>
					<a class="media-left" href="<?php the_permalink();?>"><?php the_post_thumbnail( 'thumbnail', array('class' => 'al') ); ?></a>
					<div class="media-body">
						<?php the_advanced_title(); ?>
						<?php the_content('<span class="more meta-nav">Подробнее</span>'); ?>
					</div>
				</article>
