<?php

class JLT_Resume_Factory {

	/**
	 * The job (post) ID.
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * $post Stores post data.
	 *
	 * @var $post WP_Post
	 */
	public $post = null;

	public function __construct( $resume ) {
		if ( is_numeric( $resume ) ) {
			$this->id   = absint( $resume );
			$this->post = get_post( $this->id );
		} elseif ( $resume instanceof JLT_Resume_Factory ) {
			$this->id   = absint( $resume->id );
			$this->post = $resume->post;
		} elseif ( isset( $resume->ID ) ) {
			$this->id   = absint( $resume->ID );
			$this->post = $resume;
		}
		$this->candidate_id   = $this->candidate_id();
		$this->candidate_name = $this->candidate_name();

		$this->enable_detail     = $this->enable_detail();
		$this->enable_education  = $this->enable_education();
		$this->enable_experience = $this->enable_experience();
		$this->enable_skill      = $this->enable_skill();
		$this->viewable          = $this->viewable();
	}

	public function get_id() {
		return $this->id;
	}

	public function candidate_id() {
		$data         = $this->post;
		$candidate_id = $data->post_author;

		return apply_filters( 'jlt_get_candidate_id', $candidate_id );
	}

	public function candidate_name() {
		$data           = $this->post;
		$candidate      = get_user_by( 'id', $data->post_author );
		$candidate_name = $candidate->display_name;

		return apply_filters( 'jlt_get_candidate_name', $candidate_name );
	}

	public function enable_detail() {
		$education  = jlt_get_resume_setting( 'enable_education', '1' );
		$experience = jlt_get_resume_setting( 'enable_experience', '1' );
		$skill      = jlt_get_resume_setting( 'enable_skill', '1' );

		return $education || $experience || $skill;
	}

	public function enable_education() {
		return jlt_get_resume_setting( 'enable_education', '1' );
	}

	public function enable_experience() {
		return jlt_get_resume_setting( 'enable_experience', '1' );
	}

	public function enable_skill() {
		return jlt_get_resume_setting( 'enable_skill', '1' );
	}

	public function education() {
		$resume_id = $this->get_id();
		$education = array();

		$enable_education = jlt_get_resume_setting( 'enable_education', '1' );
		if ( $enable_education ) {
			$education[ 'school' ]        = jlt_json_decode( jlt_get_post_meta( $resume_id, '_education_school', '' ) );
			$education[ 'qualification' ] = jlt_json_decode( jlt_get_post_meta( $resume_id, '_education_qualification', '' ) );
			$education[ 'date' ]          = jlt_json_decode( jlt_get_post_meta( $resume_id, '_education_date', '' ) );
			$education[ 'note' ]          = jlt_json_decode( jlt_get_post_meta( $resume_id, '_education_note', '' ) );
		}

		return $education;
	}

	public function experience() {
		$resume_id  = $this->get_id();
		$experience = array();
		if ( $this->enable_experience() ) {
			$experience[ 'employer' ] = jlt_json_decode( jlt_get_post_meta( $resume_id, '_experience_employer', '' ) );
			$experience[ 'job' ]      = jlt_json_decode( jlt_get_post_meta( $resume_id, '_experience_job', '' ) );
			$experience[ 'date' ]     = jlt_json_decode( jlt_get_post_meta( $resume_id, '_experience_date', '' ) );
			$experience[ 'note' ]     = jlt_json_decode( jlt_get_post_meta( $resume_id, '_experience_note', '' ) );
		}

		return $experience;
	}

	public function skill() {
		$resume_id = $this->get_id();
		$skill     = array();
		if ( $this->enable_skill() ) {
			$skill[ 'name' ]    = jlt_json_decode( jlt_get_post_meta( $resume_id, '_skill_name', '' ) );
			$skill[ 'percent' ] = jlt_json_decode( jlt_get_post_meta( $resume_id, '_skill_percent', '' ) );
		}

		return $skill;
	}

	public function viewable() {
		$resume_id = $this->get_id();
		$viewable  = jlt_get_post_meta( $resume_id, '_viewable' );

		return $viewable;
	}

	public function category() {

		$resume_id      = $this->get_id();
		$job_category   = jlt_get_post_meta( $resume_id, '_job_category', '' );
		$job_categories = array();

		if ( ! empty( $job_category ) ) {
			$job_category   = jlt_json_decode( $job_category );
			$job_categories = empty( $job_category ) ? array() : get_terms( 'job_category', array(
				'include'    => array_merge( $job_category, array( - 1 ) ),
				'hide_empty' => 0,
				'fields'     => 'names',
			) );
		}

		return $job_categories;
	}

	public function location() {

		$resume_id     = $this->get_id();
		$job_location  = jlt_get_post_meta( $resume_id, '_job_location', '' );
		$job_locations = array();

		if ( ! empty( $job_location ) ) {
			$job_location  = jlt_json_decode( $job_location );
			$job_locations = empty( $job_location ) ? array() : get_terms( 'job_location', array(
				'include'    => array_merge( $job_location, array( - 1 ) ),
				'hide_empty' => 0,
				'fields'     => 'names',
			) );
		}

		return $job_locations;
	}

	public function social() {
		$all_socials = jlt_get_social_fields();
		$socials     = jlt_get_candidate_socials();

		$html = array();
		foreach ( $socials as $social ) {
			if ( ! isset( $all_socials[ $social ] ) ) {
				continue;
			}
			$data  = $all_socials[ $social ];
			$value = get_user_meta( $this->candidate_id(), $social, true );
			if ( ! empty( $value ) ) {
				$url = $social == 'email' ? 'mailto:' . $value : esc_url( $value );

				$html[] = array(
					'label' => $data[ 'label' ],
					'icon'  => $data[ 'icon' ],
					'link'  => $url,
				);
			}
		}

		return $html;
	}

	public function info() {
		$fields = jlt_get_resume_custom_fields();
		$html   = array();

		foreach ( $fields as $field ) {

			$value    = jlt_get_resume_field_value( $this->get_id(), $field );
			$field_id = jlt_resume_custom_fields_name( $field[ 'name' ], $field );

			if ( ! empty( $value ) ) {
				$html[] = array(
					'field' => $field,
					'id'    => $field_id,
					'value' => $value,
				);
			}
		}

		return $html;
	}

	public function candidate_info() {
		$fields = jlt_get_candidate_custom_fields();
		$html   = array();

		foreach ( $fields as $field ) {
			if ( isset( $field[ 'is_default' ] ) ) {
				if ( in_array( $field[ 'name' ], array( 'first_name', 'last_name', 'full_name', 'email' ) ) ) {
					continue;
				}
			}
			$field_id = jlt_candidate_custom_fields_name( $field[ 'name' ], $field );
			$value    = get_user_meta( $this->candidate_id(), $field_id, true );
			$value    = jlt_convert_custom_field_value( $field, $value );

			if ( ! empty( $value ) ) {
				$html[] = array(
					'field' => $field,
					'id'    => $field_id,
					'value' => $value,
				);
			}
		}

		return $html;
	}

	public function email() {
		$candidate = get_userdata( $this->candidate_id );
		$email     = $candidate->user_email ? $candidate->user_email : '';

		return $email;
	}

	public function cv_file() {
		$file_cv = jlt_json_decode( jlt_get_post_meta( $this->get_id(), '_jlt_file_cv' ) );

		return $file_cv;
	}
}

if ( ! function_exists( 'jlt_get_resume' ) ) :
	function jlt_get_resume( $resume ) {
		return new JLT_Resume_Factory( $resume );
	}
endif;