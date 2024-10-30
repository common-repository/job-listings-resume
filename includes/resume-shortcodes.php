<?php

function jlt_resume_submit_shortcode() {
	if ( ! isset( $_POST[ 'action' ] ) || empty( $_POST[ 'action' ] ) ) {
		if ( empty( $_GET[ 'action' ] ) ) {
			$GLOBALS[ 'action' ] = '';
		} else {
			$GLOBALS[ 'action' ] = $_GET[ 'action' ];
		}
	} else {
		$GLOBALS[ 'action' ] = $_POST[ 'action' ];
	}

	global $action;

	$package_id = isset( $_REQUEST[ 'package_id' ] ) ? absint( $_REQUEST[ 'package_id' ] ) : '';

	$steps     = jlt_get_page_post_resume_steps();
	$step_keys = array_keys( $steps );
	if ( ! in_array( $action, $step_keys ) ) {
		$action = $step_keys[ 0 ];
	}

	$next_step = current( array_slice( $step_keys, array_search( $action, $step_keys ) + 1, 1 ) );

	jlt_page_post_resume_login_check( $action );

	ob_start();
	JLT_Resume_Form_Hander::display( $action, $next_step );

	return ob_get_clean();
}

add_shortcode( 'resume_submit_form', 'jlt_resume_submit_shortcode' );