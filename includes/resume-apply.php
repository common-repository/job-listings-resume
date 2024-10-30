<?php

function jlt_resume_list( $user_id = '' ) {

	$user_id = ! empty( $user_id ) ? $user_id : get_current_user_id();

	$args    = apply_filters( 'jlt_application_resume_query_args', array(
		'post_type'      => 'resume',
		'posts_per_page' => - 1,
		'post_status'    => array( 'publish' ),
		'author'         => $user_id,
	) );
	$resumes = get_posts( $args );

	return $resumes;
}

function jlt_apply_with_resume() {
	return ( jlt_get_resume_setting( 'apply_with_resume', 1 ) == '1' && jlt_is_candidate() );
}

function jlt_resume_apply_field() {

	$resume_required = jlt_get_resume_setting( 'apply_required_resume', 1 );

	$resumes = jlt_resume_list();

	if ( count( $resumes ) && jlt_apply_with_resume() ):
		?>
		<fieldset class="fieldset <?php echo( $resume_required == 'yes' ? 'required-field' : '' ); ?>">

			<label for="resume"><?php _e( 'Select Resume', 'job-listings-resume' ) ?></label>
			<?php echo( $resume_required == 1 ? '<span class="label-required">*</span>' : '' ); ?>
			<div class="field">
				<select id="resume" class="jlt-form-control"
				        name="resume" <?php echo( $resume_required == 1 ? 'data-validation="required"' : '' ); ?>>
					<option value=""><?php _e( '-Select-', 'job-listings-resume' ) ?></option>
					<?php foreach ( $resumes as $resume ) : ?>
						<option value="<?php echo $resume->ID; ?>"><?php echo $resume->post_title; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</fieldset>
		<?php
	endif;
}

/**
 * Resume Apply Form Hander
 */

function jlt_resume_before_hander_application( $job_id ) {

	if ( ! jlt_is_candidate() ) {
		return;
	}
	$apply_with_resume = jlt_get_application_setting( 'apply_with_resume', 'enabled' );
	$resume_required   = jlt_get_application_setting( 'apply_required_resume', 'yes' );

	$_resume = '';
	if ( $apply_with_resume && isset( $_POST[ 'resume' ] ) ) {
		$resume_id = absint( $_POST[ 'resume' ] );
		if ( ! empty( $resume_id ) && 'resume' === get_post_type( $resume_id ) ) {
			$_resume = $resume_id;
		}
	}

	$_resume = apply_filters( 'jlt_application_resume', $_resume );

	if ( $resume_required && empty( $_resume ) ) {

		jlt_message_add( __( 'Please select a resume', 'job-listings-resume' ), 'error' );

		wp_safe_redirect( get_permalink( $job_id ) );
		exit();
	}
}

add_action( 'new_job_application_before', 'jlt_resume_before_hander_application' );

function jlt_resume_apply_form_hander( $application_id ) {

	$resume_id = isset( $_POST[ 'resume' ] ) ? absint( $_POST[ 'resume' ] ) : '';

	update_post_meta( $application_id, '_resume', $resume_id );
}

add_action( 'jlt_after_hander_application', 'jlt_resume_apply_form_hander' );