<?php
//  $columns - colums count from custom-query.php ([query])
	$column_class = get_default_bs_columns(empty($columns) ? 1 : $columns);
	$class= is_singular() ? "content" : "media";
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($column_class); ?>>
	<a class="<?=$class?>-left" href="<?php the_permalink();?>"><?php the_post_thumbnail(); ?></a>
	<div class="<?=$class?>-body">
		<?php the_advanced_title(null, array('title_tag' => 'h3')); ?>
		<?php the_content('<span class="more meta-nav">Подробнее</span>'); ?>
	</div>
</article>