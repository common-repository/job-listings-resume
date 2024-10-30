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

function jlt_email_template_custom_field_resume() {
	return jlt_get_resume_custom_fields();
}

add_filter( 'jlt_email_template_custom_field_resume', 'jlt_email_template_custom_field_resume' );

function jlt_email_template_resume_custom_fields( $resume_id ) {
	$fields = jlt_get_resume_custom_fields( false, true );

	$arr = array();
	if ( ! empty( $fields ) ) {

		foreach ( $fields as $field ) {
			$value = '';
			$id    = jlt_resume_custom_fields_name( $field[ 'name' ], $field );
			if ( isset( $field[ 'is_tax' ] ) ) {
				$value           = jlt_resume_get_tax_value( $resume_id, $id, '' );
				$terms           = empty( $value ) ? array() : get_terms( substr( $id, 1 ), array(
					'include'    => array_merge( $value, array( - 1 ) ),
					'hide_empty' => 0,
					'fields'     => 'names',
				) );
				$value           = implode( ', ', $terms );
				$field[ 'type' ] = 'text';
			} else {
				$value = jlt_get_post_meta( $resume_id, $id, '' );
			}

			if ( ! empty( $value ) ) {
				$arr[ '[' . $field[ 'name' ] . ']' ] = jlt_et_convert_field_value( $field, $value );
			} else {
				$arr[ '[' . $field[ 'name' ] . ']' ] = '';
			}
		}
	}

	return $arr;
}

add_filter( 'jlt_et_custom_field_resume', 'jlt_email_template_resume_custom_fields' );

function jlt_resume_send_notification( $resume_id = null ) {
	if ( empty( $resume_id ) ) {
		return false;
	}
	$resume = get_post( $resume_id );
	if ( empty( $resume ) ) {
		return;
	}

	$emailed = jlt_get_post_meta( $resume_id, '_new_resume_emailed', 0 );

	if ( $emailed ) {
		return false;
	}

	if ( is_multisite() ) {
		$blogname = $GLOBALS[ 'current_site' ]->site_name;
	} else {
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}
	// Candidate

	$candidate_id = $resume->post_author;
	$candidate    = get_user_by( 'id', $candidate_id );

	// Resume information

	$resume_link  = get_permalink( $resume_id );
	$job_location = jlt_get_post_meta( $resume_id, '_job_location' );
	if ( ! empty( $job_location ) ) {
		$job_location       = jlt_json_decode( $job_location );
		$job_location_terms = empty( $job_location ) ? array() : get_terms( 'job_location', array(
			'include'    => array_merge( $job_location, array( - 1 ) ),
			'hide_empty' => 0,
			'fields'     => 'names',
		) );
		$job_location       = implode( ', ', $job_location_terms );
	}
	$job_category = jlt_get_post_meta( $resume_id, '_job_category' );
	if ( ! empty( $job_category ) ) {
		$job_category       = jlt_json_decode( $job_category );
		$job_category_terms = empty( $job_category ) ? array() : get_terms( 'job_category', array(
			'include'    => array_merge( $job_category, array( - 1 ) ),
			'hide_empty' => 0,
			'fields'     => 'names',
		) );
		$job_category       = implode( ', ', $job_category_terms );
	}

	// Admin resume submitted email

	if ( jlt_email_get_setting( 'admin_resume_submitted', 'active', 1 ) ) {

		$to = get_option( 'admin_email' );

		$array_replace = array(
			'[resume_title]'    => get_the_title( $resume_id ),
			'[resume_url]'      => $resume_link,
			'[resume_category]' => $job_category,
			'[resume_location]' => $job_location,
			'[candidate_name]'  => $candidate->display_name,
			'[site_name]'       => $blogname,
			'[site_url]'        => esc_url( home_url( '' ) ),
		);

		$subject = jlt_email_get_setting( 'admin_resume_submitted', 'subject' );
		$subject = str_replace( array_keys( $array_replace ), $array_replace, $subject );

		$message = jlt_email_get_setting( 'admin_resume_submitted', 'content' );
		$message = str_replace( array_keys( $array_replace ), $array_replace, $message );

		$subject = jlt_et_custom_field( 'resume', $resume_id, $subject );
		$message = jlt_et_custom_field( 'resume', $resume_id, $message );

		$email = jlt_mail( $to, $subject, $message, array(), 'jlt_notify_admin_resume_submitted' );
	}

	// Candidate resume submitted email

	if ( jlt_email_get_setting( 'candidate_resume_submitted', 'active', 1 ) ) {

		$to = $candidate->user_email;

		$array_replace = array(
			'[resume_title]'      => get_the_title( $resume_id ),
			'[resume_url]'        => $resume_link,
			'[resume_category]'   => $job_category,
			'[resume_location]'   => $job_location,
			'[candidate_name]'    => $candidate->display_name,
			'[resume_manage_url]' => JLT_Member::get_endpoint_url( 'manage-resume' ),
			'[site_name]'         => $blogname,
			'[site_url]'          => esc_url( home_url( '' ) ),
		);

		$subject = jlt_email_get_setting( 'candidate_resume_submitted', 'subject' );
		$subject = str_replace( array_keys( $array_replace ), $array_replace, $subject );

		$message = jlt_email_get_setting( 'candidate_resume_submitted', 'content' );
		$message = str_replace( array_keys( $array_replace ), $array_replace, $message );

		$subject = jlt_et_custom_field( 'resume', $resume_id, $subject );
		$message = jlt_et_custom_field( 'resume', $resume_id, $message );

		$email = jlt_mail( $to, $subject, $message, array(), 'jlt_notify_candidate_resume_submitted' );
	}

	update_post_meta( $resume_id, '_new_resume_emailed', 1 );
}

function jlt_resume_status_send_notification( $resume_id, $type = 'approved' ) {

	$resume = get_post( $resume_id );
	if ( empty( $resume ) ) {
		return;
	}

	$candidate_id = $resume->post_author;

	$candidate = get_user_by( 'id', $candidate_id );

	$to = $candidate->user_email;

	// Site data

	if ( is_multisite() ) {
		$blogname = $GLOBALS[ 'current_site' ]->site_name;
	} else {
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	// Resume information

	$resume_link  = get_permalink( $resume_id );
	$job_location = jlt_get_post_meta( $resume_id, '_job_location' );
	if ( ! empty( $job_location ) ) {
		$job_location       = jlt_json_decode( $job_location );
		$job_location_terms = empty( $job_location ) ? array() : get_terms( 'job_location', array(
			'include'    => array_merge( $job_location, array( - 1 ) ),
			'hide_empty' => 0,
			'fields'     => 'names',
		) );
		$job_location       = implode( ', ', $job_location_terms );
	}
	$job_category = jlt_get_post_meta( $resume_id, '_job_category' );
	if ( ! empty( $job_category ) ) {
		$job_category       = jlt_json_decode( $job_category );
		$job_category_terms = empty( $job_category ) ? array() : get_terms( 'job_category', array(
			'include'    => array_merge( $job_category, array( - 1 ) ),
			'hide_empty' => 0,
			'fields'     => 'names',
		) );
		$job_category       = implode( ', ', $job_category_terms );
	}

	// Candidate resume submitted email

	if ( $type == 'approved' ) {
		if ( jlt_email_get_setting( 'candidate_resume_approved', 'active', 1 ) ) {

			$array_replace = array(
				'[resume_title]'      => get_the_title( $resume_id ),
				'[resume_url]'        => $resume_link,
				'[resume_category]'   => $job_category,
				'[resume_location]'   => $job_location,
				'[candidate_name]'    => $candidate->display_name,
				'[resume_manage_url]' => JLT_Member::get_endpoint_url( 'manage-resume' ),
				'[site_name]'         => $blogname,
				'[site_url]'          => esc_url( home_url( '' ) ),
			);

			$subject = jlt_email_get_setting( 'candidate_resume_approved', 'subject' );
			$subject = str_replace( array_keys( $array_replace ), $array_replace, $subject );

			$message = jlt_email_get_setting( 'candidate_resume_approved', 'content' );
			$message = str_replace( array_keys( $array_replace ), $array_replace, $message );

			$subject = jlt_et_custom_field( 'resume', $resume_id, $subject );
			$message = jlt_et_custom_field( 'resume', $resume_id, $message );

			$email = jlt_mail( $to, $subject, $message, array(), 'jlt_notify_candidate_resume_approved' );
		}
	} else {
		if ( jlt_email_get_setting( 'candidate_resume_rejected', 'active', 1 ) ) {

			$array_replace = array(
				'[resume_title]'      => get_the_title( $resume_id ),
				'[resume_url]'        => $resume_link,
				'[resume_category]'   => $job_category,
				'[resume_location]'   => $job_location,
				'[candidate_name]'    => $candidate->display_name,
				'[resume_manage_url]' => JLT_Member::get_endpoint_url( 'manage-resume' ),
				'[site_name]'         => $blogname,
				'[site_url]'          => esc_url( home_url( '' ) ),
			);

			$subject = jlt_email_get_setting( 'candidate_resume_rejected', 'subject' );
			$subject = str_replace( array_keys( $array_replace ), $array_replace, $subject );

			$message = jlt_email_get_setting( 'candidate_resume_rejected', 'content' );
			$message = str_replace( array_keys( $array_replace ), $array_replace, $message );

			$subject = jlt_et_custom_field( 'resume', $resume_id, $subject );
			$message = jlt_et_custom_field( 'resume', $resume_id, $message );

			$email = jlt_mail( $to, $subject, $message, array(), 'jlt_notify_candidate_resume_rejected' );
		}
	}
}