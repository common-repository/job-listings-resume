<?php
/**
 * Display single resume field.
 *
 * This template can be overridden by copying it to yourtheme/job-listings/resume/resume-info.php.
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
$info = $resume->info();
?>
<div id="resume-info" class="resume-info">

	<h3><?php _e( 'Resume infomation', 'job-listings-resume' ); ?></h3>

	<ul class="resume-info-list">

		<?php foreach ( $info as $data ): ?>

			<li class="jlt-custom-field jlt-custom-field-resume jlt-custom-field-<?php echo esc_attr( $data[ 'field' ][ 'type' ] ); ?> resume-info-field resume-info_<?php echo esc_attr( $data[ 'id' ] ); ?>">

				<?php
				echo jlt_display_field( $data[ 'field' ], $data[ 'id' ], $data[ 'value' ], array(
					'label_tag'   => 'div',
					'label_class' => 'jlt-custom-field-label resume-cf',
					'value_tag'   => 'div',
				), false ) ?>

			</li>

		<?php endforeach; ?>

	</ul>

</div>
