<?php

function jlt_get_resume_setting( $id = null, $default = null ) {
	return jlt_get_setting( 'jlt_resume', $id, $default );
}

function jlt_get_resume_status() {
	return apply_filters( 'jlt_resume_status', array(
		'draft'           => _x( 'Draft', 'Job status', 'job-listings-resume' ),
		'pending'         => _x( 'Pending Approval', 'Job status', 'job-listings-resume' ),
		'pending_payment' => _x( 'Pending Payment', 'Job status', 'job-listings-resume' ),
		'publish'         => _x( 'Published', 'Job status', 'job-listings-resume' ),
	) );
}

function jlt_resume_status( WP_Post $post ) {
	$status      = $status_class = $post->post_status;
	$statuses    = jlt_get_resume_status();
	$status_text = '';
	if ( isset( $statuses[ $status ] ) ) {
		$status_text = $statuses[ $status ];
	} else {
		$status_text  = __( 'Inactive', 'job-listings-resume' );
		$status_class = 'inactive';
	}
	$rs_status[ 'text' ]  = $status_text;
	$rs_status[ 'class' ] = $status_class;

	return $rs_status;
}

function jlt_resume_edit_url() {
	$slug = jlt_get_endpoints_setting( 'edit-resume', 'edit-resume' );

	return esc_url_raw( add_query_arg( 'resume_id', get_the_ID(), jlt_get_member_endpoint_url( $slug ) ) );
}

function jlt_resume_delete_url() {
	return wp_nonce_url( add_query_arg( array(
		'action'    => 'delete',
		'resume_id' => get_the_ID(),
	) ), 'resume-manage-action' );
}

function jlt_resume_preview_url() {
	$slug = jlt_get_endpoints_setting( 'edit-resume', 'edit-resume' );

	return esc_url_raw( add_query_arg( 'resume_id', get_the_ID(), jlt_get_member_endpoint_url( $slug ) ) );
}

function jlt_get_candidate_resume( $candidate_id = '' ) {
	if ( empty( $candidate_id ) ) {
		$candidate_id = get_current_user_id();
	}

	return get_user_meta( $candidate_id, 'candidate_resume', true );
}

function jlt_resume_check_multiple() {

	$multiple_resume = jlt_get_resume_setting( 'multiple_resume', 1 );

	return apply_filters( 'jlt_multiple_resume', $multiple_resume );
}

function jlt_resume_quick_setup_page( $list_pages ) {
	$list_pages[] = array(
		'title'         => __( 'Post Resume', 'job-listings-resume' ),
		'content'       => '[resume_submit_form]',
		'shortcode'     => '[resume_submit_form]',
		'page_template' => '',
		'help'          => __( 'The page for Resume posting', 'job-listings-resume' ),
		'setting'       => array(
			'group' => 'jlt_resume',
			'key'   => 'resume_post_page',
			'url'   => jlt_admin_setting_page_url( 'resume' ),
		),
	);

	return $list_pages;
}

add_filter( 'jlt_setup_page', 'jlt_resume_quick_setup_page' );

function jlt_resume_is_owner( $user_id = 0, $resume_id = 0 ) {
	$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;

	if ( empty( $user_id ) || empty( $resume_id ) ) {
		return false;
	}

	$candidate_id = get_post_field( 'post_author', $resume_id );

	return $candidate_id == $user_id;
}