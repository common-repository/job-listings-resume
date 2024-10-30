<?php
/**
 * template-functions.php
 *
 * @package:
 * @since  : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'jlt_setup_resume_data' ) ) {
	function jlt_setup_resume_data( $post ) {
		unset( $GLOBALS[ 'resume' ] );
		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		if ( empty( $post->post_type ) || $post->post_type != 'resume' ) {
			return;
		}

		$GLOBALS[ 'resume' ] = jlt_get_resume( $post );

		return $GLOBALS[ 'resume' ];
	}

	add_action( 'the_post', 'jlt_setup_resume_data' );
}
// Resume Template Loader
if ( ! function_exists( 'jlt_resume_archive_template' ) ) :

	function jlt_resume_archive_template( $query ) {

		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( is_post_type_archive( 'resume' ) ) {

			// If we have a archive-resume.php template, display them.
			if ( '' != locate_template( 'archive-resume.php' ) ) {
				return;
			}

			global $resume_query;

			if ( 'loop_start' === current_filter() ) {
				ob_start();
			} else {
				ob_end_clean();
			}

			// Check can view.

			if ( jlt_can_view_resume( null, true ) ):
				$resume_query = jlt_resume_listings( $query );

				jlt_get_template( 'resume/loop/loop-before.php', compact( 'resume_query' ), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );

				if ( $resume_query->have_posts() ) {

					while ( $resume_query->have_posts() ) {

						// Setup listing data
						$resume_query->the_post();

						global $post;
						jlt_setup_resume_data( $post );
						jlt_get_template( 'content-resume.php', array(), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
					}
				} else {
					jlt_get_template( 'resume/loop/not-founds.php', array(), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
				}

				jlt_get_template( 'resume/loop/loop-after.php', compact( 'resume_query' ), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );

				wp_reset_query();

			else:
				jlt_get_template( 'resume/cannot-view.php', '', '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
			endif;
		}
	}

	add_action( 'loop_start', 'jlt_resume_archive_template' );
	add_action( 'loop_end', 'jlt_resume_archive_template' );

endif;

if ( ! function_exists( 'jlt_resume_single_template' ) ) :

	function jlt_resume_single_template( $query ) {

		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( is_singular( 'resume' ) ) {

			// If we have a single-resume.php template, display them.
			if ( '' != locate_template( 'single-resume.php' ) ) {
				return;
			}

			if ( 'loop_start' === current_filter() ) {
				ob_start();
			} else {
				ob_end_clean();
			}

			global $post;
			jlt_setup_resume_data( $post );

			// Check can view single resume

			if ( jlt_can_view_resume( $post->ID ) ):

				jlt_get_template( 'content-single-resume.php', array(), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );

			else:
				jlt_get_template( 'resume/cannot-view.php', '', '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
			endif;
		}
	}

	add_action( 'loop_start', 'jlt_resume_single_template' );
	add_action( 'loop_end', 'jlt_resume_single_template' );

endif;

// Resume Body class
if ( ! function_exists( 'jlt_resume_body_class' ) ) :

	function jlt_resume_body_class( $classes ) {
		$classes = (array) $classes;

		if ( is_singular( 'resume' ) ) {
			$classes[] = 'jlt-single-resume';
		}

		return array_unique( $classes );
	}

endif;

//Resume Submit Details

if ( ! function_exists( 'jlt_resume_submit_detail' ) ) :

	function jlt_resume_submit_detail( $resume_id ) {
		$args = array(
			'resume_id' => $resume_id,
		);
		jlt_get_template( 'form/resume-submit-detail.php', $args, '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

endif;

if ( ! function_exists( 'jlt_resume_submit_education' ) ) :

	function jlt_resume_submit_education( $resume_id ) {
		$args = array(
			'resume_id' => $resume_id,
		);
		jlt_get_template( 'form/resume-submit-education.php', $args, '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

endif;

if ( ! function_exists( 'jlt_resume_submit_experience' ) ) :

	function jlt_resume_submit_experience( $resume_id ) {
		$args = array(
			'resume_id' => $resume_id,
		);
		jlt_get_template( 'form/resume-submit-experience.php', $args, '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

endif;

if ( ! function_exists( 'jlt_resume_submit_skill' ) ) :

	function jlt_resume_submit_skill( $resume_id ) {
		$args = array(
			'resume_id' => $resume_id,
		);
		jlt_get_template( 'form/resume-submit-skill.php', $args, '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

endif;

/**
 * Single resume
 */

if ( ! function_exists( 'jlt_single_resume_avatar' ) ) :

	function jlt_single_resume_avatar() {
		jlt_get_template( 'resume/candidate-avatar.php', array(), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

endif;

if ( ! function_exists( 'jlt_single_resume_meta' ) ) :

	function jlt_single_resume_meta() {
		jlt_get_template( 'resume/resume-meta.php', array(), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

endif;

if ( ! function_exists( 'jlt_single_resume_candidate_social' ) ) :

	function jlt_single_resume_candidate_social() {
		jlt_get_template( 'resume/candidate-social.php', array(), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

endif;

if ( ! function_exists( 'jlt_single_resume_candidate_info' ) ) :

	function jlt_single_resume_candidate_info() {
		jlt_get_template( 'resume/candidate-info.php', array(), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

endif;

if ( ! function_exists( 'jlt_single_resume_info' ) ) :

	function jlt_single_resume_info() {
		jlt_get_template( 'resume/resume-info.php', array(), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

endif;

if ( ! function_exists( 'jlt_single_resume_detail' ) ) {
	function jlt_single_resume_detail() {
		jlt_get_template( 'resume/resume-detail.php', array(), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}
}

if ( ! function_exists( 'jlt_single_resume_education' ) ) :

	function jlt_single_resume_education() {
		jlt_get_template( 'resume/resume-education.php', array(), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

endif;

if ( ! function_exists( 'jlt_single_resume_experience' ) ) :

	function jlt_single_resume_experience() {
		jlt_get_template( 'resume/resume-experience.php', array(), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

endif;
if ( ! function_exists( 'jlt_single_resume_skill' ) ) :

	function jlt_single_resume_skill() {
		jlt_get_template( 'resume/resume-skill.php', array(), '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

endif;

if ( ! function_exists( 'jlt_resume_attachment' ) ) :

	function jlt_resume_attachment( $post ) {
		$resume_url = jlt_application_resume_url( $post );

		if ( ! empty( $resume_url ) ) :
			?>
			<a href="<?php echo esc_url( $resume_url ); ?>"
			   title="<?php echo esc_attr__( 'Resume', 'job-listings-resume' ); ?>">
				<i class="jlt-icon jltfa-file-text-o"></i>
			</a>
		<?php endif;
	}

endif;
