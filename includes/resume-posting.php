<?php
function jlt_is_resume_posting_page( $page_id = '' ) {
	$page_id = empty( $page_id ) ? get_the_ID() : $page_id;
	if ( empty( $page_id ) ) {
		return false;
	}

	$page_setting = jlt_get_resume_setting( 'resume_post_page' );
	if ( empty( $page_setting ) ) {
		return false;
	}

	return $page_id == $page_setting;
}

function jlt_get_resume_posting_remain( $user_id = '' ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	$package      = jlt_get_resume_posting_info( $user_id );
	$resume_limit = empty( $package ) || ! is_array( $package ) || ! isset( $package[ 'resume_limit' ] ) ? 0 : $package[ 'resume_limit' ];
	$resume_added = jlt_get_resume_posting_added( $user_id );

	return absint( $resume_limit ) - absint( $resume_added );
}

function jlt_get_resume_posting_added( $user_id = '' ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	$resume_added = get_user_meta( $user_id, '_resume_added', true );

	return empty( $resume_added ) ? 0 : absint( $resume_added );
}

function jlt_get_resume_posting_info( $user_id = '' ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	if ( jlt_is_woo_resume_posting() ) {
		// delete_user_meta($user_id, '_candidate_package'); // This code is for debuging
		$posting_info = get_user_meta( $user_id, '_candidate_package', true );
	} else {
		$posting_info = array(
			'resume_limit' => absint( jlt_get_resume_setting( 'resume_posting_limit', 5 ) ),
		);
	}

	return apply_filters( 'jlt_resume_posting_info', $posting_info, $user_id );
}

function jlt_increase_resume_posting_count( $user_id = '' ) {
	$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;
	if ( empty( $user_id ) ) {
		return false;
	}

	$_count = jlt_get_resume_posting_added( $user_id );
	update_user_meta( $user_id, '_resume_added', $_count + 1 );
}

function jlt_decrease_resume_posting_count( $user_id = '' ) {
	$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;
	if ( empty( $user_id ) ) {
		return false;
	}

	$_count = jlt_get_resume_posting_added( $user_id );
	update_user_meta( $user_id, '_resume_added', max( 0, $_count - 1 ) );
}

function jlt_can_post_resume( $user_id = '' ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}
	if ( ! jlt_is_candidate( $user_id ) ) {
		return false;
	}

	if ( jlt_is_woo_resume_posting() ) {
		// Resume posting with a package selected
		if ( jlt_is_resume_posting_page() && isset( $_REQUEST[ 'package_id' ] ) ) {
			return true;
		}

		// Check the number of resume added.
		return jlt_get_resume_posting_remain( $user_id ) > 0;
	}

	return true;
}

function jlt_can_edit_resume( $resume_id = 0, $user_id = 0 ) {
	if ( empty( $resume_id ) ) {
		return false;
	}

	$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;
	if ( empty( $user_id ) ) {
		return false;
	}

	return ( $user_id == get_post_field( 'post_author', $resume_id ) );
}
