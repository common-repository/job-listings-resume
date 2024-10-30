<?php
/**
 * Display resume submit form
 *
 * This template can be overridden by copying it to yourtheme/job-listings/templates/form/resume-submit.php.
 *
 * HOWEVER, on occasion NooTheme will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author      NooTheme
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<form method="post" id="resume-post" class="jlt-form">
	<h3 class="jlt-form-title"><?php echo esc_html( $form_title ); ?></h3>
	<?php do_action( 'jlt_before_resume_post_form', $resume_id ); ?>
	<?php

	$fields = array(
		'title_field'   => array(
			'name'          => 'title',
			'remove_prefix' => true,
			'label'         => __( 'Resume Name', 'job-listings-resume' ),
			'type'          => 'text',
			'value'         => $resume_name,
			'required'      => true,
		),
		'content_field' => array(
			'name'     => 'desc',
			'label'    => __( 'Resume content', 'job-listings-resume' ),
			'type'     => 'textarea',
			'value'    => $resume_content,
			'tinymce'  => true,
			'required' => true,
		),
	);

	$fields = apply_filters( 'jlt_resume_submit_fields', $fields );

	foreach ( $fields as $field ) {
		jlt_resume_render_form_field( $field, $resume_id );
	}

	?>
	<?php

	$fields = jlt_get_resume_custom_fields();
	if ( ! empty( $fields ) ) {
		foreach ( $fields as $field ) {
			jlt_resume_render_form_field( $field, $resume_id );
		}
	}
	
	?>
	<?php if ( jlt_get_resume_setting( 'enable_upload_resume', '1' ) ) : ?>
		<fieldset class="fieldset-file_cv">
			<label for="file_cv"><?php _e( 'Upload your Attachment', 'job-listings-resume' ) ?></label>
			<div class="field">
				<?php jlt_file_upload_form_field( 'file_cv', jlt_get_allowed_attach_file_types(), jlt_get_post_meta( $resume_id, '_jlt_file_cv' ) ) ?>
			</div>
		</fieldset>
	<?php endif; ?>

	<?php do_action( 'jlt_after_resume_post_form', $resume_id ); ?>

	<input type="hidden" name="action" value="edit_resume"/>
	<input type="hidden" name="resume_id" value="<?php echo esc_attr( $resume_id ); ?>"/>
	<input type="hidden" name="resume_action" value="<?php echo esc_attr( $resume_action ); ?>"/>
	<input type="hidden" name="candidate_id" value="<?php echo esc_attr( $candidate_id ); ?>"/>
	<?php jlt_form_nonce( 'edit-resume' ) ?>
	<button type="submit" class="jlt-btn"><?php echo esc_html( $button_text ); ?></button>
</form>