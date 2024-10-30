<?php

function jlt_resume_admin_init() {
	register_setting( 'jlt_resume', 'jlt_resume' );
	register_setting( 'jlt_resume_custom_field', 'jlt_resume_custom_field' );
}

add_filter( 'admin_init', 'jlt_resume_admin_init' );

function jlt_resume_settings_tabs( $tabs = array() ) {
	$temp1 = array_slice( $tabs, 0, 1 );
	$temp2 = array_slice( $tabs, 1 );

	$resume_tab = array( 'resume' => __( 'Resumes', 'job-listings-resume' ) );

	return array_merge( $temp1, $resume_tab, $temp2 );
}

add_filter( 'jlt_admin_settings_tabs_array', 'jlt_resume_settings_tabs', 11 );

function jlt_resume_setting_form() {

	if ( isset( $_GET[ 'settings-updated' ] ) && $_GET[ 'settings-updated' ] ) {
		flush_rewrite_rules();
	}

	// Resume display setting fields
	$fields = array(
		array(
			'id'      => 'multiple_resume',
			'label'   => __( 'Multiple Resumes Mode', 'job-listings-resume' ),
			'type'    => 'checkbox',
			'default' => '1',
		),
		array(
			'id'      => 'archive_slug',
			'label'   => __( 'Resume Archive base (slug)', 'job-listings-resume' ),
			'type'    => 'text',
			'default' => 'resumes',
		),
		array(
			'id'      => 'apply_with_resume',
			'label'   => __( 'Apply With Resume', 'job-listings-resume' ),
			'type'    => 'checkbox',
			'default' => '1',
		),
		array(
			'id'      => 'apply_required_resume',
			'label'   => __( 'Apply Required Resume', 'job-listings-resume' ),
			'type'    => 'checkbox',
			'default' => '1',
		),
	);

	jlt_render_setting_form( apply_filters( 'jlt_resume_setting_display_fields', $fields ), 'jlt_resume', __( 'Resume Displaying', 'job-listings-resume' ) );
	echo '<hr/>';

	$fields = array(
		array(
			'id'      => 'resume_post_page',
			'label'   => __( 'Resume Post Page', 'job-listings-resume' ),
			'desc'    => __( 'Each newly submitted resume needs the manual approval of Admin.', 'job-listings-resume' ),
			'type'    => 'select',
			'default' => '',
			'class'   => 'jlt-admin-chosen',
			'options' => jlt_list_pages(),
		),
		array(
			'id'      => 'resume_approve',
			'label'   => __( 'Resume Approval', 'job-listings-resume' ),
			'desc'    => __( 'Each newly submitted resume needs the manual approval of Admin.', 'job-listings-resume' ),
			'type'    => 'checkbox',
			'default' => '1',
		),
		array(
			'id'      => 'enable_upload_resume',
			'label'   => __( 'Enable Upload CV', 'job-listings-resume' ),
			'desc'    => '',
			'type'    => 'checkbox',
			'default' => '1',
		),
		array(
			'id'      => 'enable_education',
			'label'   => __( 'Enable Education', 'job-listings-resume' ),
			'desc'    => '',
			'type'    => 'checkbox',
			'default' => '1',
		),
		array(
			'id'      => 'enable_experience',
			'label'   => __( 'Enable Experience', 'job-listings-resume' ),
			'desc'    => '',
			'type'    => 'checkbox',
			'default' => '1',
		),
		array(
			'id'      => 'enable_skill',
			'label'   => __( 'Enable Skill', 'job-listings-resume' ),
			'desc'    => '',
			'type'    => 'checkbox',
			'default' => '1',
		),
	);

	jlt_render_setting_form( $fields, 'jlt_resume', __( 'Resume Posting', 'job-listings-resume' ) );
}

add_action( 'jlt_admin_setting_resume', 'jlt_resume_setting_form' );

/**
 * Addons Settings: Action Control
 */

function jlt_resume_action_list( $actions ) {
	$resume_action = array(
		'post_resume'            => array(
			'label'   => __( 'Post Resume', 'job-listings-resume' ),
			'default' => 'candidate',
			'options' => apply_filters( 'jlt_post_resume_action_options', array(
				'candidate' => __( 'Candidates', 'job-listings-resume' ),
			) ),
		),
		'view_resume'            => array(
			'label'   => __( 'View Resume', 'job-listings-resume' ),
			'default' => 'public',
			'options' => apply_filters( 'jlt_view_resume_action_options', array(
				'public'   => __( 'Public', 'job-listings-resume' ),
				'user'     => __( 'Logged-in Users', 'job-listings-resume' ),
				'employer' => __( 'Employers', 'job-listings-resume' ),
			) ),

		),
		'view_candidate_contact' => array(
			'label'   => __( 'View Candidate Contact', 'job-listings-resume' ),
			'default' => '',
			'options' => apply_filters( 'jlt_view_candidate_contact_action_options', array(
				'public'   => __( 'Public', 'job-listings-resume' ),
				'employer' => __( 'Employers', 'job-listings-resume' ),
				'private'  => __( 'Private', 'job-listings-resume' ),
			) ),
			'desc'    => __( 'In any case, employers who received resumes from applications can view contact information.', 'job-listings-resume' ),
		),
	);
	$list_action   = array_merge( $actions, $resume_action );

	return $list_action;
}

add_filter( 'jlt_action_control_list', 'jlt_resume_action_list' );