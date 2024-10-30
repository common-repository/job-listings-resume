<?php
/**
 * Display resume meta
 *
 * This template can be overridden by copying it to yourtheme/job-listings/resume/resume-meta.php.
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

global $post, $resume;

$resume_id = $resume->id;
$candidate_id = $resume->candidate_id();

?>
<div class="jlt-col-70">

	<div class="resume-meta">

		<div class="resume-title"><?php the_title(); ?></div>
		<h3><?php echo esc_html( $resume->candidate_name()); ?></h3>

		<?php
		/**
		 * @hook: jlt_single_company_social_list - 10
		 */
		do_action( 'jlt_single_resume_meta' );
		?>

	</div>

</div>
