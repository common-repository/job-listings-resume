<?php
/**
 * functions.php
 *
 * @package:
 * @since  : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function jlt_resume_candidate_endpoint_define() {
	$endpoints   = [ ];
	$endpoints[] = array(
		'key'          => 'manage-resume',
		'value'        => jlt_get_endpoints_setting( 'manage-resume', 'manage-resume' ),
		'text'         => __( 'Resumes', 'job-listings-resume' ),
		'order'        => 5,
		'show_in_menu' => true,
	);
	$endpoints[] = array(
		'key'          => 'edit-resume',
		'value'        => jlt_get_endpoints_setting( 'edit-resume', 'edit-resume' ),
		'text'         => __( 'Edit resume', 'job-listings-resume' ),
		'order'        => 5,
		'show_in_menu' => false,
	);

	return $endpoints;
}

function jlt_resume_candidate_endpoint( $endpoints ) {

	$endpoints = array_merge( $endpoints, jlt_resume_candidate_endpoint_define() );

	return $endpoints;
}

add_filter( 'jlt_list_endpoints_candidate', 'jlt_resume_candidate_endpoint' );

function jlt_resume_employer_endpoint_define() {
	$endpoints   = [ ];
	$endpoints[] = array(
		'key'          => 'viewed-resume',
		'value'        => jlt_get_endpoints_setting( 'viewed-resume', 'viewed-resume' ),
		'text'         => __( 'Viewed Resumes', 'job-listings-resume' ),
		'order'        => 15,
		'show_in_menu' => true,
	);

	return $endpoints;
}

function jlt_resume_employer_endpoint( $endpoints ) {

	$endpoints = array_merge( $endpoints, jlt_resume_employer_endpoint_define() );

	return $endpoints;
}

add_filter( 'jlt_list_endpoints_employer', 'jlt_resume_employer_endpoint' );

function jlt_resume_add_endpoints() {
	foreach ( jlt_resume_candidate_endpoint_define() as $endpoint ) {
		add_rewrite_endpoint( $endpoint[ 'value' ], EP_ROOT | EP_PAGES );
	}
	foreach ( jlt_resume_employer_endpoint_define() as $endpoint ) {
		add_rewrite_endpoint( $endpoint[ 'value' ], EP_ROOT | EP_PAGES );
	}
}

add_action( 'init', 'jlt_resume_add_endpoints' );

function jlt_application_resume_url( $post ) {
	$resume_id = jlt_get_post_meta( $post->ID, '_resume', '' );
	if ( 'publish' == get_post_status( $resume_id ) ) {
		if ( ! empty( $resume_id ) ) {
			$url = add_query_arg( 'application_id', $post->ID, get_permalink( $resume_id ) );
			$url = apply_filters( 'jlt_application_resume_url', $url, $post->ID );

			return $url;
		}
	}

	return;
}