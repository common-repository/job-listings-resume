<?php

function jlt_resume_pre_get_posts( $query ) {
	if ( is_admin() ) {
		return;
	}

	if ( jlt_is_resume_query( $query ) ) {
		if ( jlt_is_logged_in() ) {
			$user_id = get_current_user_id();

			// Candidates can view their resumes
			if ( isset( $query->query_vars[ 'author' ] ) && $query->query_vars[ 'author' ] == $user_id ) {
				return;
			}

			// Single resume, let the resume detail page decided
			if ( $query->is_singular || ( count( $query->query_vars[ 'post__in' ] ) == 1 && ! empty( $query->query_vars[ 'post__in' ][ 0 ] ) ) ) {
				return;
			}
		}

		if ( $query->is_post_type_archive ) {
			if ( isset( $_GET[ 'resume_category' ] ) && ! empty( $_GET[ 'resume_category' ] ) ) {
				$resume_category                     = $_GET[ 'resume_category' ];
				$query->query_vars[ 'meta_query' ][] = array(
					'key'     => '_job_category',
					'value'   => '"' . $resume_category . '"',
					'compare' => 'LIKE',
				);
			}
		}

		$query = jlt_resume_query_from_request( $query, $_GET );
	}
}

add_action( 'pre_get_posts', 'jlt_resume_pre_get_posts' );

function jlt_is_resume_query( $query ) {
	return isset( $query->query_vars[ 'post_type' ] ) && ( $query->query_vars[ 'post_type' ] === 'resume' );
}

function jlt_resume_query_from_request( &$query, $REQUEST = array() ) {
	if ( empty( $query ) ) {
		return $query;
	}

	$meta_query = array();

	if ( ! empty( $REQUEST ) ) {
		global $wpdb;
		$candidate_ids = array();
		$resume_ids    = array();
		if ( isset( $REQUEST[ 'candidate_name' ] ) && ! empty( $REQUEST[ 'candidate_name' ] ) ) {
			$s_keyword     = is_object( $query ) ? $query->query_vars[ 's' ] : ( is_array( $query ) ? $query[ 's' ] : '' );
			$candidate_ids = (array) $wpdb->get_col( $wpdb->prepare( '
					SELECT DISTINCT ID FROM %1$s AS u WHERE u.display_name LIKE \'%2$s\'', $wpdb->users, '%' . $s_keyword . '%' ) );

			if ( ! empty( $candidate_ids ) ) {
				if ( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
					$query->query_vars[ 'author__in' ] = $candidate_ids;
				} elseif ( is_array( $query ) ) {
					$query[ 'author__in' ] = $candidate_ids;
				}
			}
		}
		$education  = isset( $REQUEST[ 'education' ] ) && ! empty( $REQUEST[ 'education' ] ) ? true : false;
		$experience = isset( $REQUEST[ 'experience' ] ) && ! empty( $REQUEST[ 'experience' ] ) ? true : false;
		$skill      = isset( $REQUEST[ 'skill' ] ) && ! empty( $REQUEST[ 'skill' ] ) ? true : false;
		if ( $education || $experience || $skill ) {
			$s_keyword    = is_object( $query ) ? $query->query_vars[ 's' ] : ( is_array( $query ) ? $query[ 's' ] : '' );
			$where_string = array();
			if ( $education ) {
				$where_string[] = sprintf( "(m.meta_key = '_education_school' AND m.meta_value LIKE '%%%s%%')", $s_keyword );
			}
			if ( $experience ) {
				$where_string[] = sprintf( "(m.meta_key = '_experience_employer' AND m.meta_value LIKE '%%%s%%')", $s_keyword );
			}
			if ( $skill ) {
				$where_string[] = sprintf( "(m.meta_key = '_skill_name' AND m.meta_value LIKE '%%%s%%')", $s_keyword );
			}

			$query_string = "SELECT DISTINCT post_id FROM {$wpdb->postmeta} AS m WHERE " . implode( ' OR ', $where_string );

			$resume_ids = (array) $wpdb->get_col( $query_string );

			$resume_ids = array_merge( $resume_ids, array( 0 ) );
			// if( !empty( $resume_ids ) ) {
			if ( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
				$query->query_vars[ 'post__in' ] = $resume_ids;
			} elseif ( is_array( $query ) ) {
				$query[ 'post__in' ] = $resume_ids;
			}
			// }
		}
		if ( isset( $REQUEST[ 'no_content' ] ) && ! empty( $REQUEST[ 'no_content' ] ) ) {
			if ( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
				$query->query[ 's' ]      = '';
				$query->query_vars[ 's' ] = '';
			} elseif ( is_array( $query ) ) {
				unset( $query[ 's' ] );
			}
		}

		$resume_fields = jlt_get_resume_search_custom_fields();
		foreach ( $resume_fields as $field ) {
			$field_id = jlt_resume_custom_fields_name( $field[ 'name' ], $field );
			if ( isset( $REQUEST[ $field_id ] ) && ! empty( $REQUEST[ $field_id ] ) ) {
				$value = jlt_sanitize_field( $REQUEST[ $field_id ], $field );
				if ( $field_id == '_job_category' || $field_id == '_job_location' ) {
					$value = ! is_array( $value ) ? array( $value ) : $value;
				}
				if ( is_array( $value ) ) {
					$temp_meta_query = array( 'relation' => 'OR' );
					foreach ( $value as $v ) {
						if ( empty( $v ) ) {
							continue;
						}
						$temp_meta_query[] = array(
							'key'     => $field_id,
							'value'   => '"' . $v . '"',
							'compare' => 'LIKE',
						);
					}
					$meta_query[] = $temp_meta_query;
				} else {
					$meta_query[] = array(
						'key'   => $field_id,
						'value' => $value,
					);
				}
			}
		}
	}

	$meta_query = apply_filters( 'jlt_resume_search_meta_query', $meta_query, $REQUEST );

	if ( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
		$query->query_vars[ 'meta_query' ][] = $meta_query;
	} elseif ( is_array( $query ) ) {
		$query[ 'meta_query' ] = $meta_query;
	}

	return $query;
}

function jlt_resume_listings( $query ) {

	$resume_query_args = array(
		'post_type'   => 'resume',
		'p'           => '',
		'post__in'    => '',
		'offset'      => '',
		'post_status' => '',
		'author'      => '',
		'tax_query'   => array(),
		'meta_query'  => array(),
	);

	$args = array();

	if ( is_object( $query ) && isset( $query->query_vars ) ) {
		$args = $query->query_vars;
	}

	$args = array_merge( $resume_query_args, $args );

	if ( get_query_var( 'paged' ) ) {
		$paged = get_query_var( 'paged' );
	} elseif ( get_query_var( 'page' ) ) {
		$paged = get_query_var( 'page' );
	} else {
		$paged = 1;
	}

	if ( isset( $args[ 'paged' ] ) ) {
		$paged = absint( $args[ 'paged' ] );
	}

	$args[ 'paged' ] = $paged;

	$args = apply_filters( 'jlt_resume_listings_args', $args );

	$result = new WP_Query( $args );
	wp_reset_query();

	return apply_filters( 'jlt_resume_listings', $result, $resume_query_args, $query );
}