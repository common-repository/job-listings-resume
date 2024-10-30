<?php
/**
 * Display resume education
 *
 * This template can be overridden by copying it to yourtheme/job-listings/resume/resume-education.php.
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

$education = $resume->education();

$enable_education = $resume->enable_education;

if ( ! $enable_education or empty( $education[ 'school' ][0] ) ) {
	return;
}

if ( isset( $education[ 'school' ] ) && is_array( $education[ 'school' ] ) && count( $education[ 'school' ] ) ) :

	?>
	<div class="resume-detail-content resume-detail-timeline resume-education">

		<h3><?php _e( 'Education', 'job-listings-resume' ); ?></h3>

		<ul>

			<?php foreach ( $education[ 'school' ] as $index => $school ) : ?>

				<li>
					<div class="resume-timeline-title">
						<h6><?php echo esc_attr( $education[ 'school' ][ $index ] ); ?></h6>
						<span><?php echo esc_attr( $education[ 'date' ][ $index ] ); ?></span>
						<span class="resume-timeline-subtitle">
							<?php echo esc_attr( $education[ 'qualification' ][ $index ] ); ?>
						</span>
					</div>
					<div class="resume-timeline-detail">
						<?php echo html_entity_decode( $education[ 'note' ][ $index ] ) ?>
					</div>
				</li>

			<?php endforeach; ?>

		</ul>

	</div>
<?php endif; ?>