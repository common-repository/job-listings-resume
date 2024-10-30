<?php
/**
 * Display Single Resume
 *
 * This template can be overridden by copying it to yourtheme/job-listings/content-single-resume.php.
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
<div <?php post_class( 'jlt-single single-resume' ); ?> id="resume-<?php the_ID(); ?>">

	<?php do_action( 'jlt_single_resume_before' ); ?>

	<div class="jlt-single-resume-header">

		<?php
		/**
		 * @hook: jlt_single_resume_avatar - 5
		 * @hook: jlt_single_resume_meta - 10
		 */
		do_action( 'jlt_single_resume_header' );

		?>

	</div>

	<div class="jlt-single-content">

		<h3><?php _e( 'About me', 'job-listings-resume' ); ?></h3>

		<?php the_content(); ?>

		<?php do_action( 'jlt_single_resume_content' ); ?>

	</div>

	<?php
	
	/**
	 * @hooked jlt_single_resume_info - 5
	 */
	do_action( 'jlt_single_resume_after' );

	?>
</div>