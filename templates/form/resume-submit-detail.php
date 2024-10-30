<?php
/**
 * Resume Detail Form
 *
 * This template can be overridden by copying it to yourtheme/job-listings/form/resume-submit-detail.php.
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
<?php
/**
 * @hooked: jlt_resume_submit_education - 5
 * @hooked: jlt_resume_submit_experience - 10
 * @hooked: jlt_resume_submit_skill - 15
 */
do_action( 'jlt_resume_submit_detail', $resume_id );
