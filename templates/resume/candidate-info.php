<?php
/**
 * Display candidate custom info
 *
 * This template can be overridden by copying it to yourtheme/job-listings/resume/candidate-info.php.
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
global $resume;

$candidate_info = $resume->candidate_info();
$file_cv        = $resume->cv_file();

$can_view_contact = jlt_can_view_candidate_contact( $resume->id );

if ( ! $can_view_contact ) {
	return;
}

?>
<div class="candidate-info">

	<ul class="candidate-info-list">

		<?php foreach ( $candidate_info as $data ): ?>

			<li class="jlt-custom-field jlt-custom-field-resume jlt-custom-field-<?php echo esc_attr( $data[ 'field' ][ 'type' ] ); ?> resume-info-field resume-info_<?php echo esc_attr( $data[ 'id' ] ); ?>">

				<?php
				echo jlt_display_field( $data[ 'field' ], $data[ 'id' ], $data[ 'value' ], array(
					'label_tag'   => 'div',
					'label_class' => 'jlt-custom-field-label resume-cf',
					'value_tag'   => 'div',
				), false ) ?>

			</li>

		<?php endforeach; ?>

		<li class="jlt-custom-field jlt-custom-field-resume jlt-custom-field-candidate-email resume-info-field">

			<div class="label-email jlt-custom-field-label resume-cf"><?php _e( 'Email', 'job-listings-resume' ); ?></div>

			<div class="jlt-custom-field-value value-email cf-text-value"><?php echo $resume->email(); ?></div>

		</li>

		<?php if ( ! empty( $file_cv[ 0 ] ) ): ?>
			<li class="jlt-custom-field jlt-custom-field-resume jlt-custom-field-file-vc resume-info-field">

				<div class="label-field-vc jlt-custom-field-label resume-cf">
					<?php _e( 'CV File', 'job-listings-resume' ); ?>
				</div>

				<div class="jlt-custom-field-value cf-text-value">
					<i class="jlt-icon jltfa-download text-primary"></i>
					<a target="_blank" href="<?php echo jlt_get_file_upload( $file_cv[ 0 ] ); ?>"
					   title="<?php _e( 'Download My Attachment', 'job-listings-resume' ); ?>">
						<?php _e( 'Download My Attachment', 'job-listings-resume' ); ?>
					</a>
				</div>
			</li>
		<?php endif; ?>

	</ul>

</div>
