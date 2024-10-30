<?php
/**
 * Display resume skills
 *
 * This template can be overridden by copying it to yourtheme/job-listings/resume/resume-skill.php.
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

$skill = $resume->skill();

$enable_skill = $resume->enable_skill;

if ( ! $enable_skill or empty( $skill['name'][0] ) ) {
	return;
}

if ( isset( $skill[ 'name' ] ) && is_array( $skill[ 'name' ] ) && count( $skill[ 'name' ] ) ) :

	?>
	<div class="resume-detail-content resume-skill">

		<h3><?php _e( 'Professional skills', 'job-listings-resume' ); ?></h3>
		
		<ul>

			<?php foreach ( $skill[ 'name' ] as $index => $name ) : ?>

				<li>
					<div class="resume-skill-title">
						<h6><?php echo esc_attr( $skill[ 'name' ][ $index ] ); ?></h6>
						<span class="resume-skill-percent">
							<?php echo esc_attr( $skill[ 'percent' ][ $index ] ); ?><?php _e( '%', 'job-listings-resume' ); ?></span>
					</div>
					<div class="resume-skill-bar">
						<div class="resume-skill-bar-percent" data-percent="<?php echo absint( $skill[ 'percent' ][ $index ] ); ?>%"></div>
					</div>
				</li>

			<?php endforeach; ?>

		</ul>

	</div>
<?php endif; ?>
