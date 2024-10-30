<?php
/**
 * Resume Detail Form: Education
 *
 * This template can be overridden by copying it to yourtheme/job-listings/form/resume-submit-education.php.
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

$education        = array();
$enable_education = jlt_get_resume_setting( 'enable_education', '1' );
if ( $enable_education ) {
	$education[ 'school' ]        = jlt_json_decode( jlt_get_post_meta( $resume_id, '_education_school', '' ) );
	$education[ 'qualification' ] = jlt_json_decode( jlt_get_post_meta( $resume_id, '_education_qualification', '' ) );
	$education[ 'date' ]          = jlt_json_decode( jlt_get_post_meta( $resume_id, '_education_date', '' ) );
	$education[ 'note' ]          = jlt_json_decode( jlt_get_post_meta( $resume_id, '_education_note', '' ) );
}

?>
<div class="resume-submit-detail resume-submit-education">

	<fieldset class="fieldset">

		<label><?php _e( 'Education', 'job-listings-resume' ); ?></label>

		<div class="field">
			<div class="field-clone-template jlt-hide" data-template="
			<div class='field-repeat'>
				<input type='text' class='jlt-form-control' placeholder='<?php _e( 'School name', 'job-listings-resume' ); ?>'
				       name='_education_school[]'/>
				<input type='text' class='jlt-form-control' placeholder='<?php _e( 'Qualification(s)', 'job-listings-resume' ); ?>'
				       name='_education_qualification[]'/>
				<input type='text' class='jlt-form-control' placeholder='<?php _e( 'Start/end date', 'job-listings-resume' ); ?>'
				       name='_education_date[]'/>
								<textarea class='jlt-form-control jlt-form-control-editor ignore-valid' id='_education_note'
								          name='_education_note[]' rows='5' placeholder='<?php _e( 'Note', 'job-listings-resume' ); ?>'></textarea>
				<div class='jlt-btn resume-detail-delete'><?php _e( 'Delete', 'job-listings-resume' ); ?></div>
			</div>
			">
			</div>

			<?php if ( ! empty( $education[ 'school' ][ 0 ] ) ) : ?>
				<?php foreach ( $education[ 'school' ] as $index => $school ) : ?>

					<div class="field-repeat">
						<input type="text" class="jlt-form-control" placeholder="<?php _e( 'School name', 'job-listings-resume' ); ?>"
						       name='_education_school[]'
						       value="<?php echo esc_attr( $education[ 'school' ][ $index ] ); ?>"/>
						<input type="text" class="jlt-form-control"
						       placeholder="<?php _e( 'Qualification(s)', 'job-listings-resume' ); ?>"
						       name='_education_qualification[]'
						       value="<?php echo esc_attr( $education[ 'qualification' ][ $index ] ); ?>"/>
						<input type="text" class="jlt-form-control" placeholder="<?php _e( 'Start/end date', 'job-listings-resume' ); ?>"
						       name='_education_date[]'
						       value="<?php echo esc_attr( $education[ 'date' ][ $index ] ); ?>"/>
						<textarea class="jlt-form-control jlt-form-control-editor ignore-valid" id="_education_note"
						          name="_education_note[]" rows="5"
						          placeholder="<?php _e( 'Note', 'job-listings-resume' ); ?>"><?php echo html_entity_decode( $education[ 'note' ][ $index ] ) ?></textarea>
						<div class="jlt-btn resume-detail-delete"><?php _e( 'Delete', 'job-listings-resume' ); ?></div>
					</div>

				<?php endforeach; ?>
			<?php endif; ?>

		</div>

		<div class="jlt-btn resume-detail-add"><i class="jlt-icon jltfa-plus"></i> <?php _e( 'Add new field', 'job-listings-resume' ); ?>
		</div>

	</fieldset>
</div>
