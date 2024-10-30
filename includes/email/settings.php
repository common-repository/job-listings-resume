<?php
/**
 * settings.php
 *
 * @package:
 * @since  : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function jlt_resume_email_template_field( $fields ) {
	$fields_resume = array(
		'admin_resume_submitted'     => array(
			'title'     => __( 'Admin resume submit', 'job-listings-resume' ),
			'desc'      => __( 'Email to admin when resume submit', 'job-listings-resume' ),
			'recipient' => __( 'Administrator', 'job-listings-resume' ),
			'post_type' => 'resume',
			'subject'   => __( '[site_name] New resume posted by [candidate_name]', 'job-listings-resume' ),
			'content'   => __( '[candidate_name] has just submitted a resume:<br/></br><a href="[resume_url]">View Resume</a>.<br/><br/>Best regards,<br/>[site_name]', 'job-listings-resume' ),
			'fields'    => array(
				'[resume_title]'    => __( 'inserting resume title', 'job-listings-resume' ),
				'[resume_url]'      => __( 'inserting resume URL', 'job-listings-resume' ),
				'[resume_category]' => __( 'inserting resume category', 'job-listings-resume' ),
				'[resume_location]' => __( 'inserting resume location', 'job-listings-resume' ),
				'[candidate_name]'  => __( 'inserting candidate name', 'job-listings-resume' ),
			),
		),
		'candidate_resume_submitted' => array(
			'title'     => __( 'Candidate resume submit', 'job-listings-resume' ),
			'desc'      => __( 'Email to candidate when resume submit', 'job-listings-resume' ),
			'recipient' => __( 'Candidate', 'job-listings-resume' ),
			'post_type' => 'resume',
			'subject'   => __( '[site_name] You\'ve posted a resume: [resume_title]', 'job-listings-resume' ),
			'content'   => __( 'Hi [candidate_name],<br/><br/>You\'ve posted a new resume:<br/>Title: [resume_title]<br/>Location: [resume_category]<br/>Category: [resume_location]<br/><br/><br/>You can manage your resumes in <a href="[resume_manage_url]">Manage Resume</a>.<br/><br/>Best regards,<br/>[site_name]', 'job-listings-resume' ),
			'fields'    => array(
				'[resume_title]'      => __( 'inserting resume title', 'job-listings-resume' ),
				'[resume_url]'        => __( 'inserting resume URL', 'job-listings-resume' ),
				'[resume_category]'   => __( 'inserting resume category', 'job-listings-resume' ),
				'[resume_location]'   => __( 'inserting resume location', 'job-listings-resume' ),
				'[candidate_name]'    => __( 'inserting candidate name', 'job-listings-resume' ),
				'[resume_manage_url]' => __( 'inserting application manage Url', 'job-listings-resume' ),
			),
		),
		'candidate_resume_approved'  => array(
			'title'     => __( 'Candidate resume approved', 'job-listings-resume' ),
			'desc'      => __( 'Email to candidate when resume approved by Admin', 'job-listings-resume' ),
			'recipient' => __( 'Candidate', 'job-listings-resume' ),
			'post_type' => 'resume',
			'subject'   => __( '[[site_name]] Your resume [resume_title] has been approved and published', 'job-listings-resume' ),
			'content'   => __( 'Hi [candidate_name],<br/><br/>Your submitted resume [resume_title] has been approved and published now on [site_name]:<br/><a href="[resume_url]">View Resume Detail</a>.<br/><br/>You can manage your resumes in <a href="[resume_manage_url]">Manage Resumes</a><br/><br/>Best regards,<br/>[site_name]', 'job-listings-resume' ),
			'fields'    => array(
				'[resume_title]'      => __( 'inserting resume title', 'job-listings-resume' ),
				'[resume_url]'        => __( 'inserting resume URL', 'job-listings-resume' ),
				'[resume_category]'   => __( 'inserting resume category', 'job-listings-resume' ),
				'[resume_location]'   => __( 'inserting resume location', 'job-listings-resume' ),
				'[candidate_name]'    => __( 'inserting candidate name', 'job-listings-resume' ),
				'[resume_manage_url]' => __( 'inserting application manage Url', 'job-listings-resume' ),
			),
		),
		'candidate_resume_rejected'  => array(
			'title'     => __( 'Candidate resume rejected', 'job-listings-resume' ),
			'desc'      => __( 'Email to candidate when resume rejected by Admin', 'job-listings-resume' ),
			'recipient' => __( 'Candidate', 'job-listings-resume' ),
			'post_type' => 'resume',
			'subject'   => __( '[[site_name]] Your resume [resume_title] can\'t be published', 'job-listings-resume' ),
			'content'   => __( 'Hi [candidate_name],<br/><br/>Your submitted resume [resume_title] can not be published and has been deleted. You will have to submit another resume.<br/><br/>You can manage your resumes in <a href="[resume_manage_url]">Manage Resumes</a><br/><br/>Best regards,<br/>[site_name]', 'job-listings-resume' ),
			'fields'    => array(
				'[resume_title]'      => __( 'inserting resume title', 'job-listings-resume' ),
				'[resume_url]'        => __( 'inserting resume URL', 'job-listings-resume' ),
				'[resume_category]'   => __( 'inserting resume category', 'job-listings-resume' ),
				'[resume_location]'   => __( 'inserting resume location', 'job-listings-resume' ),
				'[candidate_name]'    => __( 'inserting candidate name', 'job-listings-resume' ),
				'[resume_manage_url]' => __( 'inserting application manage Url', 'job-listings-resume' ),
			),
		),

	);

	return array_merge( $fields, $fields_resume );
}

add_filter( 'jlt_email_template_field', 'jlt_resume_email_template_field' );