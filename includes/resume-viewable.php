<?php

//Resume view check

function jlt_can_view_resumes_list() {

	if ( 'administrator' == JLT_Member::get_user_role( get_current_user_id() ) ) {
		return true;
	}

	$can_view_resume_setting = jlt_get_action_control( 'view_resume', 'public' );

	switch ( $can_view_resume_setting ) {
		case 'public':
			$can_view_resume = true;
			break;
		case 'user':
			$can_view_resume = jlt_is_logged_in();
			break;
		case 'employer':
			$can_view_resume = jlt_is_employer();
			break;
		default:
			$can_view_resume = true;
			break;
	}

	return apply_filters( 'jlt_can_view_resumes_list', $can_view_resume );
}

function jlt_can_view_single_resume( $resume_id = null ) {

	if ( empty( $resume_id ) ) {
		return false;
	}

	// Resume's author can view his/her resume
	$candidate_id = get_post_field( 'post_author', $resume_id );

	if ( $candidate_id == get_current_user_id() ) {
		return true;
	}

	$can_view_resume = false;

	// Administrator can view all resumes
	if ( 'administrator' == JLT_Member::get_user_role( get_current_user_id() ) ) {
		return true;
	} elseif ( isset( $_GET[ 'application_id' ] ) && ! empty( $_GET[ 'application_id' ] ) ) {
		// Employers can view resumes from their applications

		$job_id = get_post_field( 'post_parent', $_GET[ 'application_id' ] );

		$employer_id = get_post_field( 'post_author', $job_id );
		if ( $employer_id == get_current_user_id() ) {
			$attachement_resume_id = jlt_get_post_meta( $_GET[ 'application_id' ], '_resume', '' );
			$can_view_resume       = $resume_id == $attachement_resume_id;
		}
	}

	$can_view_resume_setting = jlt_get_action_control( 'view_resume', 'public' );

	switch ( $can_view_resume_setting ) {
		case 'public':
			$can_view_resume = true;
			break;
		case 'user':
			$can_view_resume = jlt_is_logged_in();
			break;
		case 'employer':
			$can_view_resume = jlt_is_employer();
			break;
		default:
			$can_view_resume = $can_view_resume;
			break;
	}

	return apply_filters( 'jlt_can_view_single_resume', $can_view_resume, $resume_id );
}

function jlt_can_view_resume( $resume_id = null, $is_loop = false ) {
	if ( $is_loop ) {
		$can_view_resume = jlt_can_view_resumes_list();
	} else {
		$can_view_resume = jlt_can_view_single_resume( $resume_id );
	}

	return apply_filters( 'jlt_can_view_resume', $can_view_resume, $resume_id, $is_loop );
}

function jlt_resume_not_view_html( $resume_id = null ) {

	$title                   = '';
	$link                    = '';
	$login_link              = JLT_Member::get_login_url();
	$logout_link             = JLT_Member::get_logout_url();
	$can_view_resume_setting = jlt_get_action_control( 'view_resume', 'public' );

	switch ( $can_view_resume_setting ) {
		case 'public':
			$title = __( 'There\'s an unknown error. Please retry or contact Administrator.', 'job-listings-resume' );
			break;
		case 'user':

			$title = __( 'Only logged in users can view resumes.', 'job-listings-resume' );

			if ( ! jlt_is_logged_in() ) {
				$link = $login_link;
				$link = '<a href="' . esc_url( $link ) . '" class="jlt-btn">' . __( 'Login', 'job-listings-resume' ) . '</a>';
			}

			break;
		case 'employer':
			$title = __( 'Only employer can view resumes.', 'job-listings-resume' );
			if ( ! jlt_is_logged_in() ) {
				$link = $login_link;
				$link = '<a href="' . esc_url( $link ) . '" class="jlt-btn">' . __( 'Login as Employer', 'job-listings-resume' ) . '</a>';
			} elseif ( ! jlt_is_employer() ) {
				$link = $logout_link;
				$link = '<a href="' . esc_url( $link ) . '" class="jlt-btn">' . __( 'Logout', 'job-listings-resume' ) . '</a>';
			}

			break;
	}
	$result = array( 'title' => $title, 'link' => $link );

	return apply_filters( 'jlt_resume_not_view_html', $result, $can_view_resume_setting, $resume_id );
}