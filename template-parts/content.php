<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php the_thumbnail(); ?>
    <div class="media-body article-content">
        <?php the_advanced_title(); ?>
        <?php the_content('<span class="more meta-nav">Подробнее</span>'); ?>
    </div>
</article>