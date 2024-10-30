<?php

function jlt_page_post_resume_login_check( $action = '' ) {
	if ( ! jlt_is_logged_in() ) {
		do_action( 'jlt_page_post_resume_not_login', $action );
		switch ( $action ) {
			case 'login':
				break;
			default:
				jlt_force_redirect( esc_url_raw( add_query_arg( 'action', 'login' ) ) );
				break;
		}
	} elseif ( ! jlt_is_candidate() ) {
		do_action( 'jlt_page_post_resume_not_candidate', $action );
		jlt_message_add( __( 'You can not post resume', 'job-listings-resume' ), 'error' );
		wp_safe_redirect( JLT_Member::get_member_page_url() );
		exit;
	}
}

function jlt_get_page_post_resume_steps() {
	$steps = array(
		'login'             => jlt_get_page_post_resume_login_step(),
		'candidate_package' => jlt_get_page_post_candidate_package_step(),
		'resume_post'       => jlt_get_page_post_resume_post_step(),
		'resume_preview'    => jlt_get_page_post_resume_preview_step(),
	);

	if ( ! jlt_is_woo_resume_posting() ) {
		unset( $steps[ 'candidate_package' ] );
	}

	return apply_filters( 'jlt_page_post_resume_steps_list', $steps );
}

function jlt_get_page_post_resume_login_step() {
	$title = __( 'Login', 'job-listings-resume' );

	return apply_filters( 'jlt_page_post_resume_login_step', array(
		'actions' => array( 'login', 'register' ),
		'title'   => $title,
		'link'    => 'javascript:void(0);',
	) );
}

function jlt_get_page_post_candidate_package_step() {
	$title = __( 'Package', 'job-listings-resume' );
	if ( jlt_is_woo_resume_posting() && ! isset( $_REQUEST[ 'package_id' ] ) ) {
		$link = esc_url( remove_query_arg( 'package_id', add_query_arg( 'action', 'candidate_package' ) ) );
	} else {
		$link = 'javascript:void(0);';
	}

	return apply_filters( 'jlt_page_post_candidate_package_step', array(
		'actions' => array( 'candidate_package' ),
		'title'   => $title,
		'link'    => $link,
	) );
}

function jlt_get_page_post_resume_post_step() {
	$title     = __( 'Resume Details', 'job-listings-resume' );
	$link_args = array( 'action' => 'resume_post' );
	$resume_id = isset( $_GET[ 'resume_id' ] ) ? absint( $_GET[ 'resume_id' ] ) : 0;
	if ( $resume_id ) {
		$link_args[ 'resume_id' ] = $resume_id;
	}
	$link = esc_url( add_query_arg( $link_args ) );

	return apply_filters( 'jlt_page_post_resume_post_step', array(
		'actions' => array( 'resume_post' ),
		'title'   => $title,
		'link'    => $link,
	) );
}

function jlt_get_page_post_resume_preview_step() {
	$title = __( 'Preview and Submit', 'job-listings-resume' );

	return apply_filters( 'jlt_get_page_post_resume_preview_step', array(
		'actions' => array( 'resume_preview' ),
		'title'   => $title,
		'link'    => 'javascript:void(0);',
	) );
}
