<?php
/**
 * Gallery template
 */
function theme_gallery_callback($output, $attr) {
    global $post;

    if (isset($attr['orderby'])) {
        $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
        if (!$attr['orderby'])
            unset($attr['orderby']);
    }

    extract($attrs = shortcode_atts(array(
    'order'      => 'ASC',
    'orderby'    => 'menu_order ID',
    'gallery_id' => $post->ID,
    'itemtag'    => 'div class="row text-center"', // dl
    'icontag'    => 'div', //'dt',
    'captiontag' => 'p', //'dd',
    'columns'    => 3,
    'size'       => 'thumbnail',
    'include'    => '',
    'exclude'    => ''
    ), $attr));

    $gallery_id = intval($gallery_id);
    if ('RAND' == $order)
      $orderby = 'none';

    if ( $include ) {
      // $include = preg_replace('/[^0-9,]+/', '', $include);
      // $include = array_filter( explode(',', $include), 'intval' );

      $attachments = get_posts(
        array(
          'include'        => $include,
          'post_status'    => 'inherit',
          'post_type'      => 'attachment',
          'post_mime_type' => 'image',
          'order'          => $order,
          'orderby'        => $orderby
          ));
    }

    if (empty($attachments))
      return '';

    $iconclass = get_default_bs_columns($columns);

    $output = "<div class=\"gallery-wrapper\">\n";
    $output .= "<div class=\"preloader\"></div>\n";
    $output .= "<{$itemtag}>\n";

    // Now you loop through each attachment
    foreach ( $attachments as $attachment ) {
      $id = $attachment->ID;

      $url = wp_get_attachment_url( $id );
      $img = wp_get_attachment_image_src($id, $size);

      $output .= "<{$icontag} class='{$iconclass}'>\n";
      $output .= "<a href='{$url}' class='zoom' rel='group-{$gallery_id}'>";
      $output .= "<img src=\"{$img[0]}\" width=\"{$img[1]}\" height=\"{$img[2]}\" alt=\"\" />\n";
      $output .= "<{$captiontag}>{$attachment->post_excerpt}</{$captiontag}>";
      $output .= "</a>\n";
      $output .= "</{$icontag}>\n";
    }

    $output .= "</{$itemtag}>\n";
    $output .= "</div>\n";

    return $output;
}
add_filter('post_gallery', 'theme_gallery_callback', 10, 2);