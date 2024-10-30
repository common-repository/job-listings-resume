<?php
/**
 * Display resume preview submit form
 *
 * This template can be overridden by copying it to yourtheme/job-listings/templates/form/resume-preview.php.
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

?>
<form method="post" id="resume-post" class="jlt-form" autocomplete="on" novalidate="novalidate">
	<h3><?php _e( 'Preview job', 'job-listings-resume' ); ?></h3>
	<h1><?php the_title(); ?></h1>
	<div class="jlt-single-content">
		<?php the_content(); ?>
	</div>
	<input type="hidden" name="action" value="post_resume">
	<input type="hidden" name="resume_id" value="<?php echo esc_attr( $resume_id ); ?>">
	<input type="hidden" name="candidate_id" value="<?php echo esc_attr( $candidate_id ); ?>">
	<?php jlt_form_nonce( 'post-resume' ) ?>
	<a href="<?php echo esc_url( $resume_edit_url ); ?>"
	   class="jlt-btn"><?php echo esc_html( $button_edit_text ); ?></a>
	<button type="submit" class="jlt-btn"><?php echo esc_html( 'Save', 'job-listings-resume' ); ?></button>
</form>