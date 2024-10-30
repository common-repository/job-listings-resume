<?php

function jlt_admin_resumes_page_state( $states = array(), $post = null ) {
	if ( ! empty( $post ) && is_object( $post ) ) {
		$archive_slug = jlt_get_resume_setting( 'archive_slug' );
		if ( ! empty( $archive_slug ) && $archive_slug == $post->post_name ) {
			$states[ 'resume_page' ] = __( 'Resumes Page', 'job-listings-resume' );
		}
	}

	return $states;
}

add_filter( 'display_post_states', 'jlt_admin_resumes_page_state', 10, 2 );

function jlt_admin_resumes_page_notice( $post_type = '', $post = null ) {
	if ( ! empty( $post ) && is_object( $post ) ) {
		$archive_slug = jlt_get_resume_setting( 'archive_slug' );
		if ( ! empty( $archive_slug ) && $archive_slug == $post->post_name && empty( $post->post_content ) ) {
			add_action( 'edit_form_after_title', '_jlt_admin_resumes_page_notice' );
			remove_post_type_support( $post_type, 'editor' );
		}
	}
}

add_action( 'add_meta_boxes', 'jlt_admin_resumes_page_notice', 10, 2 );

function _jlt_admin_resumes_page_notice() {
	echo '<div class="notice notice-warning inline"><p>' . __( 'You are currently editing the page that shows all your resumes.', 'job-listings-resume' ) . '</p></div>';
}
