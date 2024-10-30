<?php
/**
 * Display resume item.
 *
 * This template can be overridden by copying it to yourtheme/job-listings/templates/content-resume.php.
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

global $post, $resume;

?>
<li <?php post_class( 'jlt-resume-item' ); ?>>
	<div class="jlt-resume-inner">
		<?php
		$candidate_avatar = jlt_get_avatar( $resume->candidate_id, 80 );
		?>
		<div class="jlt-image">
			<a href="<?php the_permalink(); ?>" title="<?php echo esc_html( $resume->candidate_name ); ?>">
				<?php echo $candidate_avatar; ?>
			</a>
		</div>
		<div class="jlt-text">
			<a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
				<h3><?php the_title(); ?></h3>
			</a>
			<a href="<?php the_permalink(); ?>" title="<?php echo esc_html( $resume->candidate_name ); ?>">
				<?php echo esc_html( $resume->candidate_name ); ?>
			</a>

			<?php
			$job_locations = $resume->location();
			if ( ! empty( $job_locations ) ):
				?>

				<div class="jlt-resume-item-location">
					<i class="jlt-icon jltfa-map-marker"></i>&nbsp;
					<?php echo implode( ' / ', $job_locations ); ?>
				</div>

			<?php endif; ?>

		</div>
	</div>
</li>