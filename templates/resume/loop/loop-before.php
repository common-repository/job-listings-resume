<?php
/**
 * Display before resume loop
 *
 * This template can be overridden by copying it to yourtheme/job-listings/resume/loop/loop-before.php.
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

$resume_count_founds = $resume_query->found_posts;

?>

<?php do_action( 'jlt_before_resume_loop' ); ?>

<div class="jlt-listing-before jlt-resumes-listing-before">
	<span><?php echo sprintf( _n( '%s resume', '%s resumes', $resume_count_founds, 'job-listings-resume' ), $resume_count_founds ) ?></span>
	<?php do_action( 'jlt_before_resume_loop_content' ); ?>
</div>

<ul class="jlt-resumes-listing">