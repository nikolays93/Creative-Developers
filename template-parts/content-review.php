<?php

global $post;

$user_data = get_post_meta($post->ID, RQ_META_NAME, true );
$user_name = isset($user_data['your-name']) ? $user_data['your-name'] : '';

$user_link = false;
$user_id = get_post_meta($post->ID, 'user_id', true);
if($user_id){
	$user = get_user_by('ID', (int)$user_id);
	$vk_id = explode('_', $user->user_login);
	if(isset($vk_id['1'])){
		$vk_id = $vk_id['1'];
		$vk_src = get_user_meta( $user_id, 'vkapi_ava', true );
		$user_link = 'http://vk.com/'. $vk_id;
	}
}

$img_class = 'round al ar';
$avatar = '';
if( has_post_thumbnail() )
	$avatar = get_the_post_thumbnail( $post->ID, 'thumbnail', array('class' => $img_class) );
elseif(isset($vk_src))
	$avatar = "<img src='{$vk_src}' alt='{$user->user_nicename}' class='{$img_class} vk-thumbnail'>";
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('media rq'); ?>>
	<?php echo $avatar; ?>
	<div class="media-body">
		<?php echo apply_filters( 'rq_title', $user_name, $user_link ); ?>
		<div class="entry-review-body">
			<?php the_content(); ?>
		</div>
	</div>
</article>