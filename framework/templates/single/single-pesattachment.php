<?php
/**
 * @package pes
 */
?>
<?php global $smof_data; ?>
<?php 
$attachment_id = get_the_ID();
$attachment = get_post($attachment_id);
$metadata = wp_get_attachment_metadata($attachment_id);
$large_image = wp_get_attachment_image_src($attachment_id, 'large');
$uploads = wp_upload_dir();
//echo '<pre>LARGE attachment'.print_r($attachment,1).'</pre>';

$caption = get_the_excerpt();
if (!$caption) {
  $caption = ($metadata['image_meta']['caption']) ? $metadata['image_meta']['caption'] : '<em>No caption</em>';
}

// Calculate dimension, size and download URL.
$filepath = $uploads['basedir'] . '/' . $metadata['file'];
$filebase = dirname($filepath);
$web_size = pes_formatBytes(filesize($filebase .'/'. $metadata['sizes']['large']['file']), 0);
$hq_size = pes_formatBytes(filesize($filepath), 0);

$web_dimension = $metadata['sizes']['large']['width'] .' x '. $metadata['sizes']['large']['height'];
$hq_dimension = $metadata['width'] .' x '. $metadata['height'];

//$web_id = pes_hash_create($large_image[0]);
//$hq_id = pes_hash_create($attachment->guid);
//echo da_download_attachment_url($attachment_id);
$web_id = $hq_id = 'asd';

$parent = get_post($attachment->post_parent);
$parent_gallery = get_field('pes_gallery', $attachment->post_parent);
$total_images = count($parent_gallery);
$i = 0;
$prev_img = $next_img = 0;
$prev_img_url = $next_img_url = '';
$image_number = 1;
foreach ($parent_gallery as $image) {
  if ($image['id'] == $attachment_id) {
    $image_number = $i + 1;
    $prev_img = $i - 1;
    $next_img = $i + 1;
  }
  $i++;
}

if (array_key_exists($prev_img, $parent_gallery)) {
  $prev_img_url = '<a id="previous-url" href="'. get_attachment_link($parent_gallery[$prev_img]['id']) .'">&laquo; Previous</a>';
}
if (array_key_exists($next_img, $parent_gallery)) {
  $next_img_url = '<a id="next-url" href="'. get_attachment_link($parent_gallery[$next_img]['id']) .'">Next &raquo;</a>';
}
$both_images = $prev_img_url && $next_img_url;
$prev_next = $prev_img_url . (($both_images) ? ' | ' : ' ') . $next_img_url;

// For logged in users.
$user_ID = get_current_user_id();
$login_attributes = array(
  'profile_link' => false,
  'template' => 'default',
  'registration' => true,
  'redirect' => false,
  'remember' => true
);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('single-post-inner'); ?>>
	<div class="cs-blog cs-blog-item">
		<div class="cs-blog-content">
          <div class="pes-image-header">
            <?php if ($both_images) ?>
            <span id="allow-arrows"></span>
            <?php ?>
            <div class="pes-image-header-container"><a href="<?php echo get_post_permalink($attachment->post_parent); ?>"><?php echo '&laquo; Back to '. get_the_title($attachment->post_parent) .' gallery' ?></a></div>
            <div class="pes-image-header-container">
              <span><?php echo $prev_next; ?><a id="hideShowDetails" href="javascript:void(0)">Less info</a></span><br />
              <span class="smaller">Use arrows ( &larr; or &rarr; ) on your keyboard to navigate the gallery</span>
            </div>
          </div>
          <div class="pes-image-content">
            <div class="pes-image col-xs-12 col-sm-12 col-md-9">
              <img src="<?php echo $large_image[0] ?>"/>
            </div>
            <div class="pes-image-data col-xs-12 col-sm-12 col-md-3">
              <div class="img-det">
                <p><?php echo "Image {$image_number} of {$total_images}"; ?></p>
                <p><?php echo the_title(); ?></p>
                <?php if ($caption): ?>
                  <p><?php echo $caption; ?></p>
                <?php endif; ?>
                <?php if ($metadata['image_meta']['created_timestamp']): ?>
                  <p><?php echo date('F d, Y H:i', $metadata['image_meta']['created_timestamp']); ?></p>
                <?php endif; ?>
              </div>

              <div class="img-download">
                <?php if (!$user_ID): ?>
                <div class="tdyml">
                  <p><strong>To download this photo you must be logged in.</strong></p>
                </div>
                <?php if (function_exists('login_with_ajax')) login_with_ajax($login_attributes); ?>
                <?php else: ?>
                <div class="size-btn">
                  <div id="img-box" class="btn-container" href="javascript:void(0)" 
                       data-size="<?php echo $web_size ?>"
                       data-dimension="<?php echo $web_dimension ?>" 
                       data-id="<?php echo $web_id ?>">
                    WEB
                  </div>
                  <div id="img-box" class="btn-container pressed" href="javascript:void(0)" 
                       data-size="<?php echo $hq_size ?>"
                       data-dimension="<?php echo $hq_dimension ?>" 
                       data-id="<?php echo $hq_id ?>">
                    Hi-res
                  </div>
                </div>
                <div id="img-attr" class="img-attr"><?php echo $hq_dimension .' / '. $hq_size ?></div>
                <button id="img-download-btn" class="pes-btn"><i class="fa fa-download"></i>Download this photo</button>
                <?php endif; ?>
              </div>
            </div>
            <div class="clearfix visible-md visible-lg"></div>
          </div>
          
          <?php get_template_part('framework/templates/single/pes', 'tag'); ?>
          
          
          
          

			<?php
          /*
          
          
				//the_content();
          
          
          echo '<pre>Parent '.print_r($parent,1).'</pre>';
          
          
          
          
              $attachment_id = get_the_ID();
              $uploads = wp_upload_dir();
          $options = get_option( 'download_attachments_general' );
          $attachment = get_post_meta( $attachment_id, '_wp_attached_file', true );
              
          echo '<pre>Attachment'.print_r($attachment,1).'</pre>';
          
              echo '<pre>Uploads'.print_r($uploads,1).'</pre>';
              echo '<pre>Options '.print_r($options,1).'</pre>';
          
          $filepath = apply_filters( 'da_download_attachment_filepath', $uploads['basedir'] . '/' . $attachment, $attachment_id );
          echo '<br/>Filepath: '.$filepath;
          
          // file exists?
			if ( ! file_exists( $filepath ) || ! is_readable( $filepath ) )
				echo 'FILE DOES NOT EXIST!!!!';

			// if filename contains folders
			if ( ( $position = strrpos( $attachment, '/', 0 ) ) !== false )
				$filename = substr( $attachment, $position + 1 );
			else
				$filename = $attachment;
          
          echo '<br/>The filename: '. $filename;
          
          echo '<br />rawurlencode: '.rawurldecode( $filename );
          
          $url = da_download_attachment_url($attachment_id);
          
          echo '<br />URL: '.$url;
          
          $link = da_download_attachment_link($attachment_id);
          echo '<br />LINK: '.$link;
              
          
              $attc = da_get_download_attachment(get_the_ID());
              echo '<pre>DOWNLOAD ATTACHMENT'.print_r($attc,1).'</pre>';
              
              
              $post_id = get_the_ID();
				$post = get_post($post_id);
				echo '<pre>POOOOST attachment'.print_r($post,1).'</pre>';

				$meta = get_post_meta($post_id);
				echo '<pre>META attachment'.print_r($meta,1).'</pre>';

				$meta = wp_get_attachment_metadata($post_id);
				echo '<pre>METADATA attachment'.print_r($meta,1).'</pre>';
          
          
          
          //dpes_download_attachment($attachment_id);*/
			?>

		</div><!-- .entry-content -->
	</div>
</article><!-- #post-## -->