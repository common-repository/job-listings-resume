<?php
/**
 * Resume Detail Form: Skill
 *
 * This template can be overridden by copying it to yourtheme/job-listings/form/resume-submit-skill.php.
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

$skill        = array();
$enable_skill = jlt_get_resume_setting( 'enable_skill', '1' );
if ( $enable_skill ) {
	$skill[ 'name' ]    = jlt_json_decode( jlt_get_post_meta( $resume_id, '_skill_name', '' ) );
	$skill[ 'percent' ] = jlt_json_decode( jlt_get_post_meta( $resume_id, '_skill_percent', '' ) );
}

?>
<div class="resume-submit-detail resume-submit-skill">
	<fieldset class="fieldset">

		<label><?php _e( 'Professional Skills', 'job-listings-resume' ); ?></label>

		<div class="field">

			<div class="field-clone-template jlt-hide" data-template="
			<div class='field-repeat'>
				<input type='text' class='jlt-form-control' placeholder='<?php _e( 'Skill Name', 'job-listings-resume' ); ?>'
				       name='_skill_name[]'/>

				<input type='text' class='jlt-form-control' placeholder='<?php _e( 'Skill percent', 'job-listings-resume' ); ?>'
				       name='_skill_percent[]'/>

				<div class='jlt-btn resume-detail-delete'><?php _e( 'Delete', 'job-listings-resume' ); ?></div>
			</div>
			">
			</div>
			
			<?php if ( ! empty( $skill[ 'name' ][ 0 ] ) ) : ?>
				<?php foreach ( $skill[ 'name' ] as $index => $name ) : ?>

					<div class="field-repeat">

						<input type="text" class="jlt-form-control" placeholder="<?php _e( 'Skill Name', 'job-listings-resume' ); ?>"
						       name='_skill_name[]'
						       value="<?php echo esc_attr( $skill[ 'name' ][ $index ] ); ?>"/>

						<input type="text" class="jlt-form-control"
						       placeholder="<?php _e( 'Skill percent', 'job-listings-resume' ); ?>"
						       name='_skill_percent[]'
						       value="<?php echo esc_attr( $skill[ 'percent' ][ $index ] ); ?>"/>

						<div class="jlt-btn resume-detail-delete"><?php _e( 'Delete', 'job-listings-resume' ); ?></div>
					</div>

				<?php endforeach; ?>
			<?php endif; ?>

		</div>

		<div class="jlt-btn resume-detail-add"><i class="jlt-icon jltfa-plus"></i> <?php _e( 'Add new field', 'job-listings-resume' ); ?>
		</div>

	</fieldset>
</div>
