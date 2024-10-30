<?php
/**
 * resume-dashboard.php
 *
 * @package:
 * @since  : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function jlt_member_manage_resume() {

	$multiple_resume = jlt_resume_check_multiple();

	if ( $multiple_resume ) {

		$paged = jlt_member_get_paged();

		$current_user = wp_get_current_user();

		$args = array(
			'post_type'   => 'resume',
			'paged'       => $paged,
			'post_status' => array( 'publish', 'pending', 'pending_payment' ),
			'author'      => $current_user->ID,
		);

		$list_resumes = new WP_Query( $args );

		$args = array(
			'list_resumes' => $list_resumes,
			'current_user' => $current_user,
		);

		jlt_get_template( 'member/manage-resume.php', $args, '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
		wp_reset_query();
	} else {
		jlt_force_redirect( jlt_resume_edit_url() );
	}
}

add_action( 'jlt_account_manage-resume_endpoint', 'jlt_member_manage_resume' );

function jlt_resume_edit_shortcode() {
	return JLT_Resume_Form_Hander::step_edit();
}

add_action( 'jlt_account_edit-resume_endpoint', 'jlt_resume_edit_shortcode' );

//Employer Viewed Resume

function jlt_member_viewed_resume() {

	$paged = jlt_member_get_paged();

	$viewed_resumes = jlt_get_viewed_resumes();

	$args           = array(
		'post_type'   => 'resume',
		'paged'       => $paged,
		'post_status' => array( 'publish' ),
		'post__in'    => array_merge( $viewed_resumes, array( 0 ) ),
	);
	$viewed_resumes = new WP_Query( $args );
	wp_reset_query();

	$args = array(
		'viewed_resumes' => $viewed_resumes,
	);

	jlt_get_template( 'member/manage-viewed-resume.php', $args, '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
}

add_action( 'jlt_account_viewed-resume_endpoint', 'jlt_member_viewed_resume' );