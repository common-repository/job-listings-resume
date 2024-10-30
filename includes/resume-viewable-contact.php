<?php

function jlt_is_enabled_employer_package_view_candidate_contact() {
	return 'package' == jlt_get_action_control( 'view_candidate_contact', 'public' );
}

function jlt_can_view_candidate_contact( $resume_id = null ) {

	if ( empty( $resume_id ) ) {
		return false;
	}

	$can_view_candidate_contact_setting = jlt_get_action_control( 'view_candidate_contact', 'public' );
	if ( empty( $can_view_candidate_contact_setting ) or 'public' == $can_view_candidate_contact_setting ) {
		return true;
	}

	// Resume's author can view his/her contact
	$candidate_id = get_post_field( 'post_author', $resume_id );
	if ( $candidate_id == get_current_user_id() ) {
		return true;
	}

	if ( isset( $_GET[ 'application_id' ] ) && ! empty( $_GET[ 'application_id' ] ) ) {
		// Employers can view candidate contact from their applications

		$job_id = get_post_field( 'post_parent', $_GET[ 'application_id' ] );

		$employer_id = get_post_field( 'post_author', $job_id );
		if ( $employer_id == get_current_user_id() ) {
			if ( $resume_id == jlt_get_post_meta( $_GET[ 'application_id' ], '_resume', '' ) ) {
				return true;
			}
		}
	}

	switch ( $can_view_candidate_contact_setting ) {
		case 'private':
			$can_view_candidate_contact = false;
			break;
		case 'employer':
			$can_view_candidate_contact = jlt_is_employer();
			break;
		case 'package':
			$can_view_candidate_contact = false;

			$package = jlt_get_job_posting_info();
			if ( jlt_is_employer() ) {
				$can_view_candidate_contact = isset( $package[ 'can_view_candidate_contact' ] ) && $package[ 'can_view_candidate_contact' ] == '1';
			}
			break;
		default:
			$can_view_candidate_contact = true;
			break;
	}

	return apply_filters( 'jlt_can_view_candidate_contact', $can_view_candidate_contact, $resume_id );
}

function jlt_employer_package_view_candidate_contact_user_data( $data, $product ) {
	if ( jlt_is_enabled_employer_package_view_candidate_contact() && is_object( $product ) ) {
		$data[ 'can_view_candidate_contact' ] = $product->can_view_candidate_contact;
	}

	return $data;
}

add_filter( 'jlt_employer_package_user_data', 'jlt_employer_package_view_candidate_contact_user_data', 10, 2 );
function jlt_employer_package_view_candidate_contact_features( $product ) {
	if ( jlt_is_enabled_employer_package_view_candidate_contact() && $product->can_view_candidate_contact == '1' ) : ?>
		<li class="jlt-li-icon"><i
				class="fa fa-check-circle"></i> <?php _e( 'Allow viewing Candidate Contact', 'job-listings-resume' ); ?>
		</li>
	<?php endif;
}

add_action( 'jlt_employer_package_features_list', 'jlt_employer_package_view_candidate_contact_features' );