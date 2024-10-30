<?php

function jlt_admin_resume_list_columns_header( $columns ) {
	unset( $columns[ 'date' ] );

	$before = array_slice( $columns, 0, 2 );
	$after  = array_slice( $columns, 2 );

	$new_columns = array(
		'candidate_id' => __( 'Candidate', 'job-listings-resume' ),
		'job_category' => __( 'Job Category', 'job-listings-resume' ),
		'job_location' => __( 'Job Location', 'job-listings-resume' ),
		'status'       => __( 'Status', 'job-listings-resume' ),
		'date'         => __( 'Date', 'job-listings-resume' ),
		'job_actions'  => __( 'Actions', 'job-listings-resume' ),
	);

	$columns = array_merge( $before, $new_columns, $after );

	return $columns;
}

add_filter( 'manage_edit-resume_columns', 'jlt_admin_resume_list_columns_header' );

function jlt_admin_resume_list_columns_data( $column ) {
	GLOBAL $post;
	$post_id = get_the_ID();

	if ( $column == 'candidate_id' ) {
		$candidate_id = esc_attr( $post->post_author );

		if ( ! empty( $candidate_id ) ) {
			$candidate = get_userdata( $candidate_id );
			$name      = ! empty( $candidate->display_name ) ? $candidate->display_name : $candidate->login_name;

			echo '<a href="' . get_edit_user_link( $candidate_id ) . '" target="_blank">' . $candidate->display_name . '</a>';
		}
	}

	if ( $column == 'job_category' ) {
		$job_category = jlt_get_post_meta( $post_id, '_job_category' );
		$job_category = jlt_json_decode( $job_category );

		if ( ! empty( $job_category ) ) {
			$job_category_terms = get_terms( 'job_category', array(
				'hide_empty' => 0,
				'include'    => array_merge( $job_category, array( - 1 ) ),
			) );
			$category_terms     = array();
			foreach ( $job_category_terms as $job_category_term ) {
				$category_terms[] = edit_term_link( $job_category_term->name, '', '', $job_category_term, false );
			}
			echo implode( ', ', $category_terms );
		}
	}

	if ( $column == 'job_location' ) {
		$job_location = jlt_get_post_meta( $post_id, '_job_location' );
		$job_location = jlt_json_decode( $job_location );

		if ( ! empty( $job_location ) ) {
			$job_location_terms = get_terms( 'job_location', array(
				'hide_empty' => 0,
				'include'    => array_merge( $job_location, array( - 1 ) ),
			) );
			$location_terms     = array();
			foreach ( $job_location_terms as $job_location_term ) {
				$location_terms[] = edit_term_link( $job_location_term->name, '', '', $job_location_term, false );
			}
			echo implode( ', ', $location_terms );
		}
	}

	if ( $column == 'status' ) {
		$status      = $post->post_status;
		$status_text = '';
		$statuses    = jlt_get_resume_status();
		if ( isset( $statuses[ $status ] ) ) {
			$status_text = $statuses[ $status ];
		} else {
			$status_text = __( 'Inactive', 'job-listings-resume' );
		}
		echo esc_html( $status_text );
	}
	if ( $column == 'job_actions' ) {
		echo '<div class="actions">';
		$admin_actions = array();
		if ( $post->post_status == 'pending' && current_user_can( 'publish_post', $post->ID ) ) {
			$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=jlt_approve_resume&resume_id=' . $post->ID ), 'resume-approve' );
			echo '<a href="' . esc_url( $url ) . '" title="' . __( 'Toggle viewable', 'job-listings-resume' ) . '">';
			$admin_actions[ 'approve' ] = array(
				'action' => 'approve',
				'name'   => __( 'Approve', 'job-listings-resume' ),
				'url'    => $url,
				'icon'   => 'yes',
			);
		}
		if ( $post->post_status !== 'trash' ) {
			if ( current_user_can( 'read_post', $post->ID ) ) {
				$admin_actions[ 'view' ] = array(
					'action' => 'view',
					'name'   => __( 'View', 'job-listings-resume' ),
					'url'    => $post->post_status == 'draft' ? esc_url( get_preview_post_link( $post ) ) : get_permalink( $post->ID ),
					'icon'   => 'visibility',
				);
			}
			if ( current_user_can( 'edit_post', $post->ID ) ) {
				$admin_actions[ 'edit' ] = array(
					'action' => 'edit',
					'name'   => __( 'Edit', 'job-listings-resume' ),
					'url'    => get_edit_post_link( $post->ID ),
					'icon'   => 'edit',
				);
			}
			if ( current_user_can( 'delete_post', $post->ID ) ) {
				$admin_actions[ 'delete' ] = array(
					'action' => 'delete',
					'name'   => __( 'Delete', 'job-listings-resume' ),
					'url'    => get_delete_post_link( $post->ID ),
					'icon'   => 'trash',
				);
			}
		}

		$admin_actions = apply_filters( 'resume_manager_admin_actions', $admin_actions, $post );

		foreach ( $admin_actions as $action ) {
			printf( '<a class="button tips action-%1$s" href="%2$s" data-tip="%3$s">%4$s</a>', $action[ 'action' ], esc_url( $action[ 'url' ] ), esc_attr( $action[ 'name' ] ), '<i class="dashicons dashicons-' . $action[ 'icon' ] . '"></i>' );
		}

		echo '</div>';
	}
}

add_filter( 'manage_resume_posts_custom_column', 'jlt_admin_resume_list_columns_data' );

function jlt_admin_resume_list_filter() {
	$type = 'post';
	if ( isset( $_GET[ 'post_type' ] ) ) {
		$type = sanitize_text_field($_GET[ 'post_type' ]);
	}

	//only add filter to post type you want
	if ( 'resume' == $type ) {
		global $post;

		// Candidate
		$candidates = get_users( array( 'role' => JLT_Member::CANDIDATE_ROLE, 'orderby' => 'display_name' ) );
		?>
		<select name="candidate">
			<option value=""><?php _e( 'All Candidates', 'job-listings-resume' ); ?></option>
			<?php
			$current_v = isset( $_GET[ 'candidate' ] ) ? intval($_GET[ 'candidate' ]) : '';
			foreach ( $candidates as $candidate ) {
				printf( '<option value="%s"%s>%s</option>', $candidate->ID, $candidate->ID == $current_v ? ' selected="selected"' : '', empty( $candidate->display_name ) ? $candidate->login_name : $candidate->display_name );
			}
			?>
		</select>
		<?php
		// Job Category
		$job_categories = get_terms( 'job_category', array( 'hide_empty' => false ) );
		?>
		<select name="category">
			<option value=""><?php _e( 'All Categories', 'job-listings-resume' ); ?></option>
			<?php
			$current_v = isset( $_GET[ 'category' ] ) ? $_GET[ 'category' ] : '';
			foreach ( $job_categories as $job_category ) {
				printf( '<option value="%s"%s>%s</option>', $job_category->term_id, $job_category->term_id == $current_v ? ' selected="selected"' : '', $job_category->name );
			}
			?>
		</select>
		<?php
		// Job Locations
		$job_locations = get_terms( 'job_location', array( 'hide_empty' => false ) );
		?>
		<select name="location">
			<option value=""><?php _e( 'All Locations', 'job-listings-resume' ); ?></option>
			<?php
			$current_v = isset( $_GET[ 'location' ] ) ? $_GET[ 'location' ] : '';
			foreach ( $job_locations as $job_location ) {
				printf( '<option value="%s"%s>%s</option>', $job_location->term_id, $job_location->term_id == $current_v ? ' selected="selected"' : '', $job_location->name );
			}
			?>
		</select>
		<?php
	}
}

add_action( 'restrict_manage_posts', 'jlt_admin_resume_list_filter' );

function jlt_admin_resume_list_remove_date_filter() {
	if ( 'resume' == get_post_type() ) {
		return true;
	}
}

add_filter( 'disable_months_dropdown', 'jlt_admin_resume_list_remove_date_filter' );

function jlt_admin_resume_list_filter_action( $query ) {
	global $pagenow;
	$type = 'post';
	if ( isset( $_GET[ 'post_type' ] ) ) {
		$type = sanitize_text_field($_GET[ 'post_type' ]);
	}
	if ( 'resume' == $type && is_admin() && $pagenow == 'edit.php' ) {
		if ( ! isset( $query->query_vars[ 'post_type' ] ) || $query->query_vars[ 'post_type' ] == 'resume' ) {
			if ( isset( $_GET[ 'candidate' ] ) && $_GET[ 'candidate' ] != '' ) {
				$candidate_id = intval($_GET[ 'candidate' ]);
				if ( ! is_numeric( $candidate_id ) ) {
					// try get by email
					$candidate    = get_user_by( 'email', trim( $candidate_id ) );
					$candidate_id = ! empty( $candidate ) ? $candidate->ID : '';
				}

				$query->query_vars[ 'author' ] = $candidate_id;
			}
			if ( isset( $_GET[ 'category' ] ) && $_GET[ 'category' ] != '' ) {
				$query->query_vars[ 'meta_query' ][] = array(
					'key'     => '_job_category',
					'value'   => '"' . $_GET[ 'category' ] . '"',
					'compare' => 'LIKE',
				);
			}
			if ( isset( $_GET[ 'location' ] ) && $_GET[ 'location' ] != '' ) {
				$query->query_vars[ 'meta_query' ][] = array(
					'key'     => '_job_location',
					'value'   => '"' . $_GET[ 'location' ] . '"',
					'compare' => 'LIKE',
				);
			}
		}
	}
}

add_filter( 'parse_query', 'jlt_admin_resume_list_filter_action' );

function jlt_admin_resume_list_views_status( $views ) {
	if ( isset( $views[ 'publish' ] ) ) {
		$views[ 'publish' ] = str_replace( 'Published ', _x( 'Active', 'Job status', 'job-listings-resume' ) . ' ', $views[ 'publish' ] );
	}

	return $views;
}

add_filter( 'views_edit-job', 'jlt_admin_resume_list_views_status' );

function jlt_admin_resume_list_approve_ajax() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'job-listings-resume' ), '', array( 'response' => 403 ) );
	}

	if ( ! check_admin_referer( 'resume-approve' ) ) {
		wp_die( __( 'You have taken too long. Please go back and retry.', 'job-listings-resume' ), '', array( 'response' => 403 ) );
	}

	$resume_id = ! empty( $_GET[ 'resume_id' ] ) ? (int) $_GET[ 'resume_id' ] : '';

	if ( ! $resume_id || get_post_type( $resume_id ) !== 'resume' ) {
		die();
	}

	$resume_data = array(
		'ID'          => $resume_id,
		'post_status' => 'publish',
	);
	wp_update_post( $resume_data );

	wp_safe_redirect( esc_url_raw( remove_query_arg( array(
		'trashed',
		'untrashed',
		'deleted',
		'ids',
	), wp_get_referer() ) ) );
	die();
}

add_filter( 'wp_ajax_jlt_approve_resume', 'jlt_admin_resume_list_approve_ajax' );

function jlt_admin_resume_transition_post_status( $new_status, $old_status, $post ) {

	if ( $post->post_type !== 'resume' ) {
		return;
	}

	if ( ! is_admin() ) {
		return;
	}

	if ( $new_status == 'publish' && $old_status != 'publish' ) {
		jlt_resume_status_send_notification( $post->ID, 'approved' );
	}

	if ( $new_status == 'trash' ) {
		jlt_resume_status_send_notification( $post->ID, 'rejected' );
	}
}

add_action( 'transition_post_status', 'jlt_admin_resume_transition_post_status', 10, 3 );