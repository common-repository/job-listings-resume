<?php

function jlt_register_resume_post_type() {
	if ( post_type_exists( 'resume' ) ) {
		return;
	}

	$archive_slug    = jlt_get_resume_setting( 'archive_slug', 'resumes' );
	$archive_rewrite = $archive_slug ? array(
		'slug'       => sanitize_title( $archive_slug ),
		'with_front' => true,
		'feeds'      => true,
	) : false;

	register_post_type( 'resume', array(
		'labels'           => array(
			'name'               => __( 'Resumes', 'job-listings-resume' ),
			'singular_name'      => __( 'Resume', 'job-listings-resume' ),
			'add_new'            => __( 'Add New Resume', 'job-listings-resume' ),
			'add_new_item'       => __( 'Add Resume', 'job-listings-resume' ),
			'edit'               => __( 'Edit', 'job-listings-resume' ),
			'edit_item'          => __( 'Edit Resume', 'job-listings-resume' ),
			'new_item'           => __( 'New Resume', 'job-listings-resume' ),
			'view'               => __( 'View', 'job-listings-resume' ),
			'view_item'          => __( 'View Resume', 'job-listings-resume' ),
			'view_items'         => __( 'View Resume Listings', 'job-listings-resume' ),
			'search_items'       => __( 'Search Resume', 'job-listings-resume' ),
			'not_found'          => __( 'No Resumes found', 'job-listings-resume' ),
			'not_found_in_trash' => __( 'No Resumes found in Trash', 'job-listings-resume' ),
			'all_items'          => __( 'All Resumes', 'job-listings-resume' ),
		),
		'public'           => true,
		'has_archive'      => true,
		'menu_icon'        => 'dashicons-id-alt',
		'rewrite'          => apply_filters( 'jlt_resume_rewrite', $archive_rewrite ),
		'supports'         => array( 'title', 'editor' ),
		'can_export'       => true,
		'delete_with_user' => true,
	) );
}

add_action( 'init', 'jlt_register_resume_post_type', 0 );
