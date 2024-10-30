<?php
/**
 * Display candidate avatar
 *
 * This template can be overridden by copying it to yourtheme/job-listings/resume/candidate-avatar.php.
 *
 * HOWEVER, on occasion NooTheme will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author      NooTheme
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $post;
$candidate_id = $post->post_author;
?>
<div class="jlt-col-30">
	<div class="candidate-avatar">
		<?php echo jlt_get_avatar( $candidate_id, 200 ); ?>
	</div>
</div>
