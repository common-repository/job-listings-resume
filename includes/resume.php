<?php
/**
 * resume.php
 *
 * @package:
 * @since  : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'JLT_Resume' ) ):
	if ( ! class_exists( 'JLT_CPT' ) ) {
		require_once JLT_PLUGIN_DIR . 'includes/admin/class-jlt-cpt.php';
	}

	class JLT_Resume extends JLT_CPT {

		static $instance = false;

		public static function get_setting( $id, $default = null ) {
			return jlt_get_resume_setting( $id, $default );
		}

		public static function enable_resume_detail() {
			$education  = jlt_get_resume_setting( 'enable_education', '1' );
			$experience = jlt_get_resume_setting( 'enable_experience', '1' );
			$skill      = jlt_get_resume_setting( 'enable_skill', '1' );

			return $education || $experience || $skill;
		}

		public static function notify_candidate( $resume_id = null, $user_id = 0 ) {
			if ( ! jlt_is_logged_in() || empty( $resume_id ) ) {
				return false;
			}

			$user = false;
			if ( ! empty( $user_id ) ) {
				$user = get_userdata( $user_id );
			} else {
				$user = wp_get_current_user();
			}

			if ( ! $user ) {
				return false;
			}

			if ( ! jlt_resume_is_owner( $user->ID, $resume_id ) ) {
				return false;
			}

			$emailed = jlt_get_post_meta( $resume_id, '_new_resume_emailed', 0 );
			if ( $emailed ) {
				return false;
			}

			$candidate_email = $user->user_email;
			$resume          = get_post( $resume_id );
			$job_location    = jlt_get_post_meta( $resume_id, '_job_location' );
			if ( ! empty( $job_location ) ) {
				$job_location       = jlt_json_decode( $job_location );
				$job_location_terms = empty( $job_location ) ? array() : get_terms( 'job_location', array(
					'include'    => array_merge( $job_location, array( - 1 ) ),
					'hide_empty' => 0,
					'fields'     => 'names',
				) );
				$job_location       = implode( ', ', $job_location_terms );
			}
			$job_category = jlt_get_post_meta( $resume_id, '_job_category' );
			if ( ! empty( $job_category ) ) {
				$job_category       = jlt_json_decode( $job_category );
				$job_category_terms = empty( $job_category ) ? array() : get_terms( 'job_category', array(
					'include'    => array_merge( $job_category, array( - 1 ) ),
					'hide_empty' => 0,
					'fields'     => 'names',
				) );
				$job_category       = implode( ', ', $job_category_terms );
			}

			$resume_need_approve = jlt_get_resume_setting( 'resume_approve', 'yes' ) == 'yes';

			if ( $resume_need_approve ) {
				$resume_link = esc_url( add_query_arg( 'resume_id', $resume_id, JLT_Member::get_endpoint_url( 'preview-resume' ) ) );
			} else {
				$resume_link = get_permalink( $resume_id );
			}

			// admin email
			if ( jlt_et_get_setting( 'admin_resume_activated' ) ) {
				$subject = jlt_et_get_setting( 'admin_resume_subject' );

				$array_subject = array(
					'[resume_title]' => get_the_title( $resume_id ),
					'[site_name]'    => $blogname,
					'[site_url]'     => esc_url( home_url( '' ) ),
				);
				$subject       = str_replace( array_keys( $array_subject ), $array_subject, $subject );

				$to = get_option( 'admin_email' );

				$array_message = array(
					'[resume_title]'    => get_the_title( $resume_id ),
					'[resume_url]'      => $resume_link,
					'[resume_category]' => $job_category,
					'[resume_location]' => $job_location,
					'[candidate_name]'  => $user->display_name,
					'[site_name]'       => $blogname,
					'[site_url]'        => esc_url( home_url( '' ) ),
				);

				$message = jlt_et_get_setting( 'admin_resume_content' );
				$message = str_replace( array_keys( $array_message ), $array_message, $message );

				$subject = jlt_et_custom_field( 'resume', $resume_id, $subject );
				$message = jlt_et_custom_field( 'resume', $resume_id, $message );

				jlt_mail( $to, $subject, $message, array(), 'jlt_notify_resume_submitted_admin' );
			}

			//candidate email
			if ( jlt_et_get_setting( 'candidate_resume_activated' ) ) {
				$blogname = get_bloginfo( 'name' );
				$to       = $user->user_email;

				$subject = jlt_et_get_setting( 'candidate_resume_subject' );

				$array_subject = array(
					'[resume_title]' => get_the_title( $resume_id ),
					'[site_name]'    => $blogname,
					'[site_url]'     => esc_url( home_url( '' ) ),
				);

				$subject = str_replace( array_keys( $array_subject ), $array_subject, $subject );

				$array_message = array(
					'[resume_title]'      => get_the_title( $resume_id ),
					'[resume_url]'        => $resume_link,
					'[resume_category]'   => $job_category,
					'[resume_location]'   => $job_location,
					'[candidate_name]'    => $user->display_name,
					'[resume_manage_url]' => JLT_Member::get_endpoint_url( 'manage-resume' ),
					'[site_name]'         => $blogname,
					'[site_url]'          => esc_url( home_url( '' ) ),
				);

				$message = jlt_et_get_setting( 'candidate_resume_content' );
				$message = str_replace( array_keys( $array_message ), $array_message, $message );

				$subject = jlt_et_custom_field( 'resume', $resume_id, $subject );
				$message = jlt_et_custom_field( 'resume', $resume_id, $message );

				jlt_mail( $to, $subject, $message, array(), 'jlt_notify_resume_submitted_candidate' );
			}

			update_post_meta( $resume_id, '_new_resume_emailed', 1 );
		}

		public function __construct() {

			$this->post_type  = 'resume';
			$this->slug       = 'resumes';
			$this->prefix     = 'resume';
			$this->option_key = 'jlt_resume';

			$this->setting_title = __( 'Resume Settings', 'job-listings-resume' );

			// add_shortcode('resume', array(&$this,'jlt_resume_shortcode'));

			// add_action('wp_ajax_nopriv_jlt_resume_nextajax', array(&$this,'jlt_resume_shortcode'));
			// add_action('wp_ajax_jlt_resume_nextajax', array(&$this,'jlt_resume_shortcode'));
			add_action( 'save_post', array( $this, 'save_data_attachment_file' ) );
		}

		public function save_data_attachment_file( $post_id ) {

			// Check if our nonce is set.
			if ( ! isset( $_POST[ 'attachment_file_nonce' ] ) ) {
				return;
			}

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST[ 'attachment_file_nonce' ], 'save_attachment_file' ) ) {
				return;
			}

			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// Sanitize user input.
			$my_data = sanitize_text_field( $_POST[ 'file_cv' ] );

			// Update the meta field in the database.
			update_post_meta( $post_id, '_jlt_file_cv', $my_data );
		}

		public static function is_page_post_resume() {
			return jlt_is_resume_posting_page();
		}

		public static function need_login() {
			return ! jlt_is_candidate();
		}

		public static function login_handler() {
			if ( ! self::need_login() ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'action', 'postresume' ) ) );
			}

			return;
		}

		public static function get_default_fields() {
			return jlt_get_resume_default_fields();
		}

		public static function count_viewable_resumes( $candidate_id = 0, $count_all = false ) {
			if ( empty( $candidate_id ) && ! $count_all ) {
				return 0;
			}

			$args = array(
				'post_type'     => 'resume',
				'post_per_page' => - 1,
				'post_status'   => array( 'publish' ),
				'author'        => $candidate_id,
				'meta_query'    => array(
					array(
						'key'   => '_viewable',
						'value' => 'yes',
					),
				),
			);

			if ( ! $count_all ) {
				$args[ 'author' ] = $candidate_id;
			}

			$query = new WP_Query( $args );

			return $query->found_posts;
		}

		public static function can_view_resume( $resume_id = null, $is_loop = false ) {
			return jlt_can_view_resume( $resume_id, $is_loop );
		}

		public static function get_resume_permission_message( $viewable = true ) {
			$title                   = __( 'You don\'t have permission to view resumes.', 'job-listings-resume' );
			$link                    = '';
			$can_view_resume_setting = jlt_get_resume_setting( 'can_view_resume', 'employer' );
			if ( ! $viewable ) {
				$title = __( 'This resume is private.', 'job-listings-resume' );
			} elseif ( $can_view_resume_setting == 'employer' ) {
				$title = __( 'Only employers can view resumes.', 'job-listings-resume' );
				$link  = JLT_Member::get_logout_url();

				if ( ! jlt_is_logged_in() ) {
					$link = JLT_Member::get_login_url();
					$link = '<a href="' . esc_url( $link ) . '"><i class="fa fa-long-arrow-right"></i>&nbsp;' . __( 'Login as Employer', 'job-listings-resume' ) . '</a>';
				} elseif ( ! JLT_Member::is_employer() ) {
					$link = JLT_Member::get_logout_url();
					$link = '<a href="' . esc_url( $link ) . '"><i class="fa fa-long-arrow-right"></i>&nbsp;' . __( 'Logout then Login as Employer', 'job-listings-resume' ) . '</a>';
				}
			} elseif ( $can_view_resume_setting == 'package' ) {
				$title = __( 'Only paid employers can view resumes.', 'job-listings-resume' );
				$link  = JLT_Member::get_endpoint_url( 'manage-plan' );

				if ( ! jlt_is_logged_in() ) {
					$link = JLT_Member::get_login_url();
					$link = '<a href="' . esc_url( $link ) . '"><i class="fa fa-long-arrow-right"></i>&nbsp;' . __( 'Login as Employer', 'job-listings-resume' ) . '</a>';
				} elseif ( ! JLT_Member::is_employer() ) {
					$link = JLT_Member::get_logout_url();
					$link = '<a href="' . esc_url( $link ) . '"><i class="fa fa-long-arrow-right"></i>&nbsp;' . __( 'Logout then Login as Employer', 'job-listings-resume' ) . '</a>';
				} else {
					$title = __( 'Your membership doesn\'t allow you to view resumes.', 'job-listings-resume' );
					$link  = JLT_Member::get_endpoint_url( 'manage-plan' );
					$link  = '<a href="' . esc_url( $link ) . '"><i class="fa fa-long-arrow-right"></i>&nbsp;' . __( 'Upgrade your membership', 'job-listings-resume' ) . '</a>';
				}
			}

			return array( $title, $link );
		}

		public static function display_detail( $query = null, $hide_profile = false ) {
			jlt_resume_detail( $query, $hide_profile );
		}

		public static function jlt_resume_shortcode( $atts, $content = null ) {
			return jlt_jlt_resumes_shortcode( $atts, $content );
		}

		public static function loop_display( $args = '' ) {
			jlt_resume_loop( $args );
		}
	}

	new JLT_Resume();
endif;
