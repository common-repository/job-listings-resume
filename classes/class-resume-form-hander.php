<?php

class JLT_Resume_Form_Hander {
	protected $resume_id;

	public function __construct() {

		add_action( 'init', array( __CLASS__, 'edit_resume_action' ) );
		add_action( 'init', array( __CLASS__, 'post_resume_action' ) );
		add_action( 'init', array( __CLASS__, 'manage_resume_action' ) );
	}

	public static function get_resume_id() {
		$resume_id = 0;
		if ( ! empty( $_COOKIE[ 'jlt-submitting-resume-id' ] ) && ! empty( $_COOKIE[ 'jlt-submitting-resume-key' ] ) ) {
			$resume_id_cc  = absint( $_COOKIE[ 'jlt-submitting-resume-id' ] );
			$resume_status = get_post_status( $resume_id_cc );

			if ( ( $resume_status === 'draft' ) && get_post_meta( $resume_id_cc, '_submitting_key', true ) === $_COOKIE[ 'jlt-submitting-resume-key' ] ) {
				$resume_id = $resume_id_cc;
			}
		}

		return $resume_id;
	}

	public static function edit_resume_action() {
		if ( 'POST' !== strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
			return;
		}

		if ( empty( $_POST[ 'action' ] ) || 'edit_resume' !== $_POST[ 'action' ] || empty( $_POST[ '_wpnonce' ] ) || ! wp_verify_nonce( $_POST[ '_wpnonce' ], 'edit-resume' ) ) {
			return;
		}

		$_POST[ 'post_status' ] = 'draft'; // Resume post is alway draft. Need to be reviewed first.

		// validate title, content
		if ( isset( $_POST[ 'desc' ] ) && empty( $_POST[ 'desc' ] ) ) {
			jlt_message_add( __( 'Resume content not empty.', 'job-listings-resume' ) );
			$location = array( 'action' => 'resume_post' );
			wp_safe_redirect( esc_url_raw( add_query_arg( $location ) ) );
			exit;
		}
		if ( isset( $_POST[ 'title' ] ) && empty( $_POST[ 'title' ] ) ) {
			jlt_message_add( __( 'Resume title not empty.', 'job-listings-resume' ) );
			$location = array( 'action' => 'resume_post' );
			wp_safe_redirect( esc_url_raw( add_query_arg( $location ) ) );
			exit;
		}

		$resume_id = self::_save_resume( $_POST );
		if ( $resume_id ) {

			// Save main resume ID
			update_user_meta( get_current_user_id(), 'candidate_resume', $resume_id );

			$_POST[ 'resume_id' ] = $resume_id;
			$resume_id            = self::_save_detail_resume( $_POST );
		}

		$resume_action = $_POST[ 'resume_action' ];

		if ( 'edit_resume' == $resume_action ) {
			if ( jlt_resume_check_multiple() == false ) {
				wp_publish_post( $resume_id );
			}
			jlt_message_add( __( 'Resume saved', 'job-listings-resume' ) );
		} else {
			if ( is_wp_error( $resume_id ) ) {
				jlt_message_add( __( 'You can not post resume', 'job-listings-resume' ), 'error' );
				wp_safe_redirect( JLT_Member::get_member_page_url() );
				exit;
			} else {
				$location = array( 'action' => 'resume_preview' );
				wp_safe_redirect( esc_url_raw( add_query_arg( $location ) ) );
				exit;
			}
		}
	}

	public static function post_resume_action() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( 'POST' !== strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
			return;
		}

		if ( empty( $_POST[ 'action' ] ) || 'post_resume' !== $_POST[ 'action' ] || empty( $_POST[ '_wpnonce' ] ) || ! wp_verify_nonce( $_POST[ '_wpnonce' ], 'post-resume' ) ) {
			return;
		}

		$resume_id = self::get_resume_id();

		$resume_need_approve = jlt_get_resume_setting( 'resume_approve', '1' );

		if ( ! $resume_need_approve ) {
			wp_update_post( array(
				'ID'          => $resume_id,
				'post_status' => 'publish',
			) );
		} else {
			wp_update_post( array(
				'ID'          => $resume_id,
				'post_status' => 'pending',
			) );
			update_post_meta( $resume_id, '_in_review', 1 );
		}

		jlt_message_add( __( 'Resume successfully added', 'job-listings-resume' ) );

		jlt_resume_send_notification( $resume_id );

		wp_safe_redirect( JLT_Member::get_endpoint_url( 'manage-resume' ) );
		exit;
	}

	private static function _save_resume( $args = '' ) {
		try {
			$defaults = array(
				'candidate_id' => '',
				'resume_id'    => '',
				'title'        => '',
				'desc'         => '',
				'status'       => 'draft',
			);
			$args     = wp_parse_args( $args, $defaults );

			if ( empty( $args[ 'candidate_id' ] ) ) {
				jlt_message_add( __( 'There\'s an unknown error. Please retry or contact Administrator.', 'job-listings-resume' ), 'error' );

				return false;
			}

			if ( ! empty( $args[ 'resume_id' ] ) ) {
				if ( ! jlt_resume_is_owner( $args[ 'candidate_id' ], $args[ 'resume_id' ] ) ) {
					jlt_message_add( __( 'Sorry, you can\'t edit this resume.', 'job-listings-resume' ), 'error' );

					return false;
				}
			} elseif ( ! jlt_can_post_resume( $args[ 'candidate_id' ] ) ) {
				jlt_message_add( __( 'Sorry, you can\'t post resume.', 'job-listings-resume' ), 'error' );

				return false;
			}

			$no_html = array();

			$resume = array(
				'post_title'   => jlt_kses( $args[ 'title' ] ),
				'post_content' => jlt_kses( $args[ 'desc' ], true ),
				'post_type'    => 'resume',
				'post_status'  => wp_kses( $args[ 'status' ], jlt_html_allowed() ),
				'post_author'  => absint( $args[ 'candidate_id' ] ),
			);

			if ( empty( $resume[ 'post_title' ] ) ) {
				jlt_message_add( __( 'This resume needs a title.', 'job-listings-resume' ), 'error' );

				return false;
			}

			$new_resume = false;
			if ( ! empty( $args[ 'resume_id' ] ) ) {
				$resume[ 'ID' ] = intval( $args[ 'resume_id' ] );
				unset( $resume[ 'post_status' ] );
				$post_id = wp_update_post( $resume );
			} else {
				$post_id    = wp_insert_post( $resume );
				$new_resume = true;

				$submitting_key = uniqid();
				setcookie( 'jlt-submitting-resume-id', $post_id, false, COOKIEPATH, COOKIE_DOMAIN, false );
				setcookie( 'jlt-submitting-resume-key', $submitting_key, false, COOKIEPATH, COOKIE_DOMAIN, false );
				update_post_meta( $post_id, '_submitting_key', $submitting_key );
			}
			if ( ! is_wp_error( $post_id ) && $post_id ) {

				$fields = jlt_get_resume_custom_fields();

				if ( $fields ) {
					foreach ( $fields as $field ) {

						$id = jlt_resume_custom_fields_name( $field[ 'name' ], $field );

						$value = isset( $args[ $id ] ) ? wp_kses( $args[ $id ], $no_html ) : '';

						if ( $id == '_job_category' || $id == '_job_location' ) {
							$value = ! is_array( $value ) ? array( $value ) : $value;
							$value = json_encode( $value );
						}
						update_post_meta( $post_id, $id, $value );
					}
				}

				$file_cv = isset( $args[ 'file_cv' ] ) ? wp_kses( $args[ 'file_cv' ], $no_html ) : '';
				update_post_meta( $post_id, '_jlt_file_cv', $file_cv );

				// Set viewable
				if ( empty( $args[ 'resume_id' ] ) ) {
					$max_viewable_resumes = intval( jlt_get_resume_setting( 'max_viewable_resumes', 1 ) );
					if ( $max_viewable_resumes > 0 ) {
						// @TODO: change this code when we have approve/reject resume function
						$viewable_resumes = absint( JLT_Resume::count_viewable_resumes( get_current_user_id() ) );
						if ( $viewable_resumes < $max_viewable_resumes ) {
							update_post_meta( $post_id, '_viewable', 'yes' );
						}
					} elseif ( $max_viewable_resumes == - 1 ) {
						update_post_meta( $post_id, '_viewable', 'yes' );
					}
				}
			} else {
				jlt_message_add( __( 'There\'s an unknown error. Please retry or contact Administrator.', 'job-listings-resume' ), 'error' );

				return false;
			}
			do_action( 'jlt_after_save_resume', $post_id );
			if ( $new_resume ) {
				do_action( 'jlt_after_new_resume', $post_id );
			}

			return $post_id;
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}
	}

	private static function _save_detail_resume( $args = '' ) {
		try {
			$defaults = array(
				'resume_id'                => '',
				'_education_school'        => '',
				'_education_qualification' => '',
				'_education_date'          => '',
				'_education_school'        => '',
				'_education_note'          => '',
				'_experience_employer'     => '',
				'_experience_job'          => '',
				'_experience_date'         => '',
				'_experience_note'         => '',
				'_skill_name'              => '',
				'_skill_percent'           => '',
			);
			$args     = wp_parse_args( $args, $defaults );

			if ( empty( $args[ 'candidate_id' ] ) || ! is_numeric( $args[ 'candidate_id' ] ) || empty( $args[ 'resume_id' ] ) || ! is_numeric( $args[ 'resume_id' ] ) ) {
				jlt_message_add( __( 'There\'s an unknown error. Please retry or contact Administrator.', 'job-listings-resume' ), 'error' );

				return false;
			}

			if ( ! empty( $args[ 'resume_id' ] ) ) {
				if ( ! jlt_resume_is_owner( $args[ 'candidate_id' ], $args[ 'resume_id' ] ) ) {
					jlt_message_add( __( 'Sorry, you can\'t edit this resume.', 'job-listings-resume' ), 'error' );

					return false;
				}
			} elseif ( ! JLT_Member::can_post_resume( $args[ 'candidate_id' ] ) ) {
				jlt_message_add( __( 'Sorry, you can\'t post resume.', 'job-listings-resume' ), 'error' );

				return false;
			}

			if ( jlt_get_resume_setting( 'enable_education', '1' ) ) {
				$education_school        = $args[ '_education_school' ];
				$education_qualification = $args[ '_education_qualification' ];
				$education_date          = $args[ '_education_date' ];
				$education_note          = $args[ '_education_note' ];

				update_post_meta( $args[ 'resume_id' ], '_education_school', json_encode( $education_school, JSON_UNESCAPED_UNICODE ) );
				update_post_meta( $args[ 'resume_id' ], '_education_qualification', json_encode( $education_qualification, JSON_UNESCAPED_UNICODE ) );
				update_post_meta( $args[ 'resume_id' ], '_education_date', json_encode( $education_date, JSON_UNESCAPED_UNICODE ) );
				update_post_meta( $args[ 'resume_id' ], '_education_note', json_encode( $education_note, JSON_UNESCAPED_UNICODE ) );
			}

			if ( jlt_get_resume_setting( 'enable_experience', '1' ) ) {
				$experience_employer = $args[ '_experience_employer' ];
				$experience_job      = $args[ '_experience_job' ];
				$experience_date     = $args[ '_experience_date' ];
				$experience_note     = $args[ '_experience_note' ];

				update_post_meta( $args[ 'resume_id' ], '_experience_employer', json_encode( $experience_employer, JSON_UNESCAPED_UNICODE ) );
				update_post_meta( $args[ 'resume_id' ], '_experience_job', json_encode( $experience_job, JSON_UNESCAPED_UNICODE ) );
				update_post_meta( $args[ 'resume_id' ], '_experience_date', json_encode( $experience_date, JSON_UNESCAPED_UNICODE ) );
				update_post_meta( $args[ 'resume_id' ], '_experience_note', json_encode( $experience_note, JSON_UNESCAPED_UNICODE ) );
			}

			if ( jlt_get_resume_setting( 'enable_skill', '1' ) ) {
				$skill_name    = $args[ '_skill_name' ];
				$skill_percent = $args[ '_skill_percent' ];

				update_post_meta( $args[ 'resume_id' ], '_skill_name', json_encode( $skill_name, JSON_UNESCAPED_UNICODE ) );
				update_post_meta( $args[ 'resume_id' ], '_skill_percent', json_encode( $skill_percent, JSON_UNESCAPED_UNICODE ) );
			}

			do_action( 'jlt_save_detail_resume', $args[ 'resume_id' ] );

			return $args[ 'resume_id' ];
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}
	}

	public static function display( $action, $next_step ) {
		if ( ! jlt_is_logged_in() ) {
			jlt_get_template( 'form/login.php' );

			return;
		}

		$resume_id  = self::get_resume_id();
		$package_id = isset( $_REQUEST[ 'package_id' ] ) ? absint( $_REQUEST[ 'package_id' ] ) : 0;

		switch ( $action ) {
			case 'login':
				if ( jlt_is_logged_in() ) {
					jlt_force_redirect( esc_url_raw( add_query_arg( 'action', $next_step ) ) );
				}
				break;
			case 'candidate_package':
				if ( jlt_is_woo_resume_posting() ) {
					if ( ! empty( $package_id ) || jlt_get_resume_posting_remain() > 0 ) {
						jlt_force_redirect( esc_url_raw( add_query_arg( 'action', $next_step ) ) );
					}
				} else {
					jlt_force_redirect( esc_url_raw( add_query_arg( 'action', $next_step ) ) );
				}
				self::step_candidate_package( $resume_id );
				break;
			case 'resume_post':
				if ( ! jlt_can_post_resume() ) {
					jlt_message_add( __( 'Sorry, you can\'t post resume.', 'job-listings-resume' ), 'error' );
					if ( jlt_is_woo_resume_posting() && jlt_is_candidate() ) {
						jlt_force_redirect( JLT_Member::get_endpoint_url( 'manage-plan' ) );
					} else {
						jlt_force_redirect( JLT_Member::get_member_page_url() );
					}
				}
				self::step_submit( $resume_id );
				break;
			case 'resume_preview':
				self::step_preview( $resume_id );
				break;
			default:
				do_action( 'jlt_page_post_resume_action', $action );
				break;
		}
	}

	public static function step_submit( $resume_id ) {
		jlt_can_edit_resume();
		$user_id = get_current_user_id();

		$resume         = $resume_id > 0 ? get_post( $resume_id ) : array();
		$resume_name    = ( $resume ? $resume->post_title : '' );
		$resume_content = $resume ? $resume->post_content : '';

		$atts = array(
			'resume_id'      => $resume_id,
			'resume_name'    => $resume_name,
			'resume_content' => $resume_content,
			'candidate_id'   => $user_id,
			'button_text'    => __( 'Preview Resume', 'job-listings-resume' ),
			'form_title'     => __( 'Submit Resume', 'job-listings-resume' ),
			'resume_action'  => 'submit_resume',
		);
		jlt_get_template( 'form/resume-submit.php', $atts, '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

	public static function step_edit() {

		$user_id = get_current_user_id();
		if ( jlt_resume_check_multiple() == false ) {
			$resume_id = jlt_get_candidate_resume( $user_id );
		} else {
			$resume_id = isset( $_GET[ 'resume_id' ] ) ? absint( $_GET[ 'resume_id' ] ) : 0;
		}

		$resume         = $resume_id > 0 ? get_post( $resume_id ) : array();
		$resume_name    = ( $resume ? $resume->post_title : '' );
		$resume_content = $resume ? $resume->post_content : '';

		$atts = array(
			'resume_id'      => $resume_id,
			'resume_name'    => $resume_name,
			'resume_content' => $resume_content,
			'candidate_id'   => $user_id,
			'button_text'    => __( 'Save Resume', 'job-listings-resume' ),
			'form_title'     => __( 'Edit Resume', 'job-listings-resume' ),
			'resume_action'  => 'edit_resume',
		);

		jlt_get_template( 'form/resume-submit.php', $atts, '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

	public static function step_candidate_package( $resume_id ) {
		$atts = array(
			'resume_id' => $resume_id,
		);
		jlt_get_template( 'form/resume-package.php', $atts, '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
	}

	public static function step_preview( $resume_id ) {
		global $post;
		if ( $resume_id && $resume_id > 0 ) {
			$user_id         = get_current_user_id();
			$resume_edit_url = add_query_arg( 'action', 'resume_post' );
			$atts            = array(
				'button_submit_text' => __( 'Submit Resume', 'job-listings-resume' ),
				'button_edit_text'   => __( 'Edit Resume', 'job-listings-resume' ),
				'resume_edit_url'    => $resume_edit_url,
				'resume_id'          => $resume_id,
				'candidate_id'       => $user_id,
			);

			$post = get_post( $resume_id );
			setup_postdata( $post );
			jlt_get_template( 'form/resume-preview.php', $atts, '', JLT_RESUME_PLUGIN_TEMPLATE_DIR );
			wp_reset_postdata();
		}
	}

	public static function manage_resume_action() {
		if ( ! is_user_logged_in() ) {
			return;
		}
		$action = self::current_action();
		if ( ! empty( $action ) && ! empty( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'resume-manage-action' ) ) {
			$resume_id = '';
			if ( isset( $_REQUEST[ 'resume_id' ] ) ) {
				$resume_id = absint( $_REQUEST[ 'resume_id' ] );
			} elseif ( ! empty( $_REQUEST[ 'id' ] ) ) {
				$resume_id = absint( $_REQUEST[ 'id' ] );
			}

			if ( empty( $resume_id ) ) {
				wp_die( __( 'There\'s an unknown error. Please retry or contact Administrator.', 'job-listings-resume' ) );
			}
			try {
				switch ( $action ) {
					case 'edit':
						break;
					case 'toggle_viewable':
						$resume = get_post( $resume_id );
						if ( empty( $resume ) || $resume->post_type !== 'resume' ) {
							jlt_message_add( __( 'Can not find this resume.', 'job-listings-resume' ), 'error' );
							break;
						}
						if ( ! jlt_resume_is_owner( get_current_user_id(), $resume_id ) ) {
							jlt_message_add( __( 'You can not edit this resume.', 'job-listings-resume' ), 'error' );
							break;
						}
						$current_viewable = jlt_get_post_meta( $resume_id, '_viewable', '' );
						if ( $current_viewable == 'yes' ) {
							update_post_meta( $resume_id, '_viewable', 'no' );
						} else {
							$max_viewable_resumes = absint( jlt_get_resume_setting( 'max_viewable_resumes', 1 ) );

							if ( $max_viewable_resumes > 0 ) {
								$viewable_resumes = absint( JLT_Resume::count_viewable_resumes( get_current_user_id() ) );

								if ( $viewable_resumes >= $max_viewable_resumes ) {
									jlt_message_add( sprintf( _n( 'You have already had %d viewable resume.', 'You have already had %d viewable resumes', $max_viewable_resumes, 'job-listings-resume' ), $max_viewable_resumes ), 'error' );
								}

								update_post_meta( $resume_id, '_viewable', 'yes' );
							}
						}

						jlt_message_add( sprintf( __( 'Resume visibility was changed successfully.', 'job-listings-resume' ), $deleted ) );
						do_action( 'manage_resume_action_viewable', $resume_id );
						wp_safe_redirect( JLT_Member::get_endpoint_url( 'manage-resume' ) );
						break;
					case 'delete':
						$resume = get_post( $resume_id );
						if ( empty( $resume ) || $resume->post_type !== 'resume' ) {
							jlt_message_add( __( 'Can not find this resume.', 'job-listings-resume' ), 'error' );
							break;
						}
						if ( ! jlt_resume_is_owner( get_current_user_id(), $resume_id ) ) {
							jlt_message_add( __( 'You can not delete this resume.', 'job-listings-resume' ), 'error' );
							break;
						}
						if ( ! wp_trash_post( $resume_id ) ) {
							jlt_message_add( __( 'Error in deleting.', 'job-listings-resume' ), 'error' );
						}

						jlt_message_add( __( 'Resume was deleted successfully.', 'job-listings-resume' ) );
						do_action( 'manage_resume_action_delete', $resume_id );
						break;
				}

				wp_safe_redirect( JLT_Member::get_endpoint_url( 'manage-resume' ) );
				die;
			} catch ( Exception $e ) {
				throw new Exception( $e->getMessage() );
			}
		}
	}

	public static function current_action() {
		if ( isset( $_REQUEST[ 'action' ] ) && - 1 != $_REQUEST[ 'action' ] ) {
			return $_REQUEST[ 'action' ];
		}

		if ( isset( $_REQUEST[ 'action2' ] ) && - 1 != $_REQUEST[ 'action2' ] ) {
			return $_REQUEST[ 'action2' ];
		}
	}

}

new JLT_Resume_Form_Hander();