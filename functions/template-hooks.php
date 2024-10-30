<?php
/**
 * template-hooks.php
 *
 * @package:
 * @since  : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Body Class
 */
add_filter( 'body_class', 'jlt_resume_body_class' );

/**
 * Single Resume
 */

add_action( 'jlt_single_resume_header', 'jlt_single_resume_avatar', 5 );
add_action( 'jlt_single_resume_header', 'jlt_single_resume_meta', 10 );

add_action( 'jlt_single_resume_meta', 'jlt_single_resume_candidate_info', 5 );
add_action( 'jlt_single_resume_meta', 'jlt_single_resume_candidate_social', 10 );

add_action( 'jlt_single_resume_after', 'jlt_single_resume_info', 5 );
add_action( 'jlt_single_resume_after', 'jlt_single_resume_detail', 10 );

add_action( 'jlt_single_resume_detail', 'jlt_single_resume_education', 5 );
add_action( 'jlt_single_resume_detail', 'jlt_single_resume_experience', 10 );
add_action( 'jlt_single_resume_detail', 'jlt_single_resume_skill', 15 );

/**
 * Resume Submit Details
 */

add_action( 'jlt_after_resume_post_form', 'jlt_resume_submit_detail', 5 );

add_action( 'jlt_resume_submit_detail', 'jlt_resume_submit_education', 5 );
add_action( 'jlt_resume_submit_detail', 'jlt_resume_submit_experience', 10 );
add_action( 'jlt_resume_submit_detail', 'jlt_resume_submit_skill', 15 );

/**
 * Manage Applications Page
 */

add_action( 'jlt_manage_application_attachment', 'jlt_resume_attachment' );

/**
 * Add resume field Apply Form
 */

add_action('after_apply_job_form', 'jlt_resume_apply_field');