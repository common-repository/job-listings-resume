<?php
/**
 * Display resume experience
 *
 * This template can be overridden by copying it to yourtheme/job-listings/resume/resume-experience.php.
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

$experience = $resume->experience();

$enable_experience = $resume->enable_experience;

if ( ! $enable_experience or empty( $experience[ 'employer' ][ 0 ] ) ) {
	return;
}

if ( isset( $experience[ 'employer' ] ) && is_array( $experience[ 'employer' ] ) && count( $experience[ 'employer' ] ) ) :

	?>
	<div class="resume-detail-content resume-detail-timeline resume-experience">

		<h3><?php _e( 'Work Experience', 'job-listings-resume' ); ?></h3>

		<ul>

			<?php foreach ( $experience[ 'employer' ] as $index => $employer ) : ?>

				<li>
					<div class="resume-timeline-title">
						<h6><?php echo esc_attr( $experience[ 'employer' ][ $index ] ); ?></h6>
						<span><?php echo esc_attr( $experience[ 'date' ][ $index ] ); ?></span>
						<span class="resume-timeline-subtitle">
							<?php echo esc_attr( $experience[ 'job' ][ $index ] ); ?>
						</span>
					</div>
					<div class="resume-timeline-detail">
						<?php echo html_entity_decode( $experience[ 'note' ][ $index ] ) ?>
					</div>
				</li>

			<?php endforeach; ?>

		</ul>

	</div>
<?php endif; ?>