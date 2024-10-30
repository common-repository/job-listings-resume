<?php

function jlt_is_woo_resume_posting() {
	$candidate_package_actions = array(
		jlt_get_action_control( 'post_resume' ),
		jlt_get_action_control( 'view_job' ),
		jlt_get_action_control( 'apply_job' ),
	);

	return in_array( 'package', $candidate_package_actions );
}