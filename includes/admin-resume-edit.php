<?php

function jlt_admin_resume_edit_title_placeholder( $text, $post ) {
	if ( $post->post_type == 'resume' ) {
		return __( 'Resume Title', 'job-listings-resume' );
	}

	return $text;
}

add_filter( 'enter_title_here', 'jlt_admin_resume_edit_title_placeholder', 10, 2 );

function jlt_extend_resume_status() {
	global $post, $post_type;
	if ( $post_type === 'resume' ) {
		$html = $selected_label = '';
		foreach ( (array) jlt_get_resume_status() as $status => $label ) {
			$seleced = selected( $post->post_status, esc_attr( $status ), false );
			if ( $seleced ) {
				$selected_label = $label;
			}
			$html .= "<option " . $seleced . " value='" . esc_attr( $status ) . "'>" . $label . "</option>";
		}
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				<?php if ( ! empty( $selected_label ) ) : ?>
				jQuery('#post-status-display').html('<?php echo esc_js( $selected_label ); ?>');
				<?php endif; ?>
				var select = jQuery('#post-status-select').find('select');
				jQuery(select).html("<?php echo( $html ); ?>");
			});
		</script>
		<?php
	}
}

foreach ( array( 'post', 'post-new' ) as $hook ) {
	add_action( "admin_footer-{$hook}.php", 'jlt_extend_resume_status' );
}

function jlt_resume_meta_boxes() {
	// Declare helper object
	$helper = new JLT_Meta_Boxes_Helper( '', array( 'page' => 'resume' ) );

	// General Info
	$meta_box = array(
		'id'          => '_general_info',
		'title'       => __( 'General Information', 'job-listings-resume' ),
		'context'     => 'normal',
		'priority'    => 'core',
		'description' => '',
		'fields'      => array(),
	);

	$fields = jlt_get_resume_custom_fields();
	if ( $fields ) {
		foreach ( $fields as $field ) {
			$id = jlt_resume_custom_fields_name( $field[ 'name' ], $field );

			$new_field = jlt_custom_field_to_meta_box( $field, $id );

			if ( $field[ 'name' ] == '_job_location' ) {
				$new_field[ 'type' ] = 'select';
				$job_locations       = array();
				// $job_locations[] = array('value'=>'','label'=>__('- Select a location -','job-listings-resume'));
				$job_locations_terms = (array) get_terms( 'job_location', array( 'hide_empty' => 0 ) );

				if ( ! empty( $job_locations_terms ) ) {
					foreach ( $job_locations_terms as $location ) {
						$job_locations[] = array( 'value' => $location->term_id, 'label' => $location->name );
					}
				}

				$new_field[ 'options' ]  = $job_locations;
				$new_field[ 'multiple' ] = true;
			}

			if ( $field[ 'name' ] == '_job_category' ) {
				$new_field[ 'type' ] = 'select';
				$job_categories      = array();
				// $job_categories[] = array('value'=>'','label'=>__('- Select a category -','job-listings-resume'));
				$job_categories_terms = (array) get_terms( 'job_category', array( 'hide_empty' => 0 ) );

				if ( ! empty( $job_categories_terms ) ) {
					foreach ( $job_categories_terms as $category ) {
						$job_categories[] = array( 'value' => $category->term_id, 'label' => $category->name );
					}
				}

				$new_field[ 'options' ]  = $job_categories;
				$new_field[ 'multiple' ] = true;
			}

			$meta_box[ 'fields' ][] = $new_field;
		}
	}

	$helper->add_meta_box( $meta_box );

	// Education
	if ( jlt_get_resume_setting( 'enable_education', '1' ) ) {
		$meta_box = array(
			'id'          => '_education',
			'title'       => __( 'Education', 'job-listings-resume' ),
			'context'     => 'normal',
			'priority'    => 'core',
			'description' => '',
			'fields'      => array(
				array(
					'id'       => '_education',
					'label'    => '',
					'type'     => 'education',
					'std'      => '',
					'callback' => 'jlt_meta_box_field_resume_detail',
				),
			),
		);

		$helper->add_meta_box( $meta_box );
	}

	// Experience
	if ( jlt_get_resume_setting( 'enable_experience', '1' ) ) {
		$meta_box = array(
			'id'          => '_experience',
			'title'       => __( 'Work Experience', 'job-listings-resume' ),
			'context'     => 'normal',
			'priority'    => 'core',
			'description' => '',
			'fields'      => array(
				array(
					'id'       => '_experience',
					'label'    => '',
					'type'     => 'experience',
					'std'      => '',
					'callback' => 'jlt_meta_box_field_resume_detail',
				),
			),
		);

		$helper->add_meta_box( $meta_box );
	}

	// Skill
	if ( jlt_get_resume_setting( 'enable_skill', '1' ) ) {
		$meta_box = array(
			'id'          => '_skill',
			'title'       => __( 'Summary of Skills', 'job-listings-resume' ),
			'context'     => 'normal',
			'priority'    => 'core',
			'description' => '',
			'fields'      => array(
				array(
					'id'       => '_skill',
					'label'    => '',
					'type'     => 'skill',
					'std'      => '',
					'callback' => 'jlt_meta_box_field_resume_detail',
				),
			),
		);

		$helper->add_meta_box( $meta_box );
	}

	// Candidate
	$meta_box = array(
		'id'          => 'candidate',
		'title'       => __( 'Candidate', 'job-listings-resume' ),
		'context'     => 'side',
		'priority'    => 'default',
		'description' => '',
		'fields'      => array(
			array(
				'id'       => 'author',
				'label'    => __( 'This Resume belongs to Candidate', 'job-listings-resume' ),
				'desc'     => '',
				'type'     => 'candidate_author',
				'std'      => '',
				'callback' => 'jlt_meta_box_field_resume_detail',
			),
		),
	);

	$helper->add_meta_box( $meta_box );

	// Attachment
	if ( jlt_get_resume_setting( 'enable_upload_resume', '1' ) ) :
		$meta_box = array(
			'id'          => 'attachment',
			'title'       => __( 'Resume Attachment', 'job-listings-resume' ),
			'context'     => 'side',
			'priority'    => 'default',
			'description' => '',
			'fields'      => array(
				array(
					'id'      => '_jlt_file_cv',
					'type'    => 'attachment',
					'std'     => '',
					'options' => array(
						'extensions' => jlt_get_file_upload_types(),
					),
				),
			),
		);

		$helper->add_meta_box( $meta_box );
	endif;
}

add_action( 'add_meta_boxes', 'jlt_resume_meta_boxes', 30 );

function jlt_meta_box_field_resume_detail( $post, $id, $type, $meta, $std = null, $field = null ) {
	switch ( $type ) {
		case 'candidate_author':

			$user_list = get_users( array( 'role' => JLT_Member::CANDIDATE_ROLE ) );

			echo '<select name="post_author_override" id="post_author_override" class="jlt-admin-chosen' . ( is_rtl() ? ' chosen-rtl' : '' ) . '" data-placeholder="' . __( '- Select a Candidate - ', 'job-listings-resume' ) . '">';
			echo '	<option value=""></option>';
			foreach ( $user_list as $user ) {
				echo '<option value="' . $user->ID . '"';
				selected( $post->post_author, $user->ID, true );
				echo '>' . $user->display_name . '</option>';
			}
			echo '</select>';

			break;
		case 'education':
			$meta = array();
			$meta[ 'school' ] = jlt_json_decode( jlt_get_post_meta( get_the_ID(), $id . '_school' ) );
			$meta[ 'qualification' ] = jlt_json_decode( jlt_get_post_meta( get_the_ID(), $id . '_qualification' ) );
			$meta[ 'date' ] = jlt_json_decode( jlt_get_post_meta( get_the_ID(), $id . '_date' ) );
			$meta[ 'note' ] = jlt_json_decode( jlt_get_post_meta( get_the_ID(), $id . '_note' ) );

			foreach ( $meta as $key => $value ) {
				if ( empty( $value ) ) {
					$meta[ $key ] = array();
				}
			}

			?>
			<div class="jlt-metabox-addable" data-name="<?php echo esc_attr( $id ); ?>">
				<table class="jlt-addable-fields">
					<thead>
					<tr>
						<th><label><?php _e( 'School name', 'job-listings-resume' ); ?></label></th>
						<th><label><?php _e( 'Qualification(s)', 'job-listings-resume' ); ?></label></th>
						<th><label><?php _e( 'Start/end date', 'job-listings-resume' ); ?></label></th>
						<th><label><?php _e( 'Note', 'job-listings-resume' ); ?></label></th>
						<th></th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="4">
							<input type='hidden' value=""
							       name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_school'; ?>]'/>
							<input type='hidden' value=""
							       name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_qualification'; ?>]'/>
							<input type='hidden' value=""
							       name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_date'; ?>]'/>
							<input type='hidden' value=""
							       name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_note'; ?>]'/>
							<input type="button" value="<?php _e( 'Add Education', 'job-listings-resume' ); ?>"
							       class="button button-default jlt-clone-fields"
							       data-template="<tr><td><input type='text' name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_school'; ?>][]' /></td><td><input type='text' name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_qualification'; ?>][]' /></td><td><input type='text' name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_date'; ?>][]' /></td><td><textarea name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_note'; ?>][]' ></textarea> </td><td><a href='javascript:void()' class='jlt-remove-fields'><?php _e( 'x', 'job-listings-resume' ); ?></a></td></tr>"/>
						</td>
					</tr>
					</tfoot>
					<tbody>
					<?php
					foreach ( $meta[ 'school' ] as $index => $school ) :
						?>
						<tr>
							<td><input type="text" name="jlt_meta_boxes[<?php echo esc_attr( $id ) . '_school'; ?>][]"
							           value="<?php echo esc_attr( $meta[ 'school' ][ $index ] ); ?>"/></td>
							<td><input type="text"
							           name="jlt_meta_boxes[<?php echo esc_attr( $id ) . '_qualification'; ?>][]"
							           value="<?php echo esc_attr( $meta[ 'qualification' ][ $index ] ); ?>"/></td>
							<td><input type="text" name="jlt_meta_boxes[<?php echo esc_attr( $id ) . '_date'; ?>][]"
							           value="<?php echo esc_attr( $meta[ 'date' ][ $index ] ); ?>"/></td>
							<td><textarea
									name="jlt_meta_boxes[<?php echo esc_attr( $id ) . '_note'; ?>][]"><?php echo esc_attr( $meta[ 'note' ][ $index ] ); ?></textarea>
							</td>
							<td><a href="javascript:void()"
							       class="jlt-remove-fields"><?php _e( 'x', 'job-listings-resume' ); ?></a></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php
			break;
		case 'experience':
			$meta = array();
			$meta[ 'employer' ] = jlt_json_decode( jlt_get_post_meta( get_the_ID(), $id . '_employer' ) );
			$meta[ 'job' ] = jlt_json_decode( jlt_get_post_meta( get_the_ID(), $id . '_job' ) );
			$meta[ 'date' ] = jlt_json_decode( jlt_get_post_meta( get_the_ID(), $id . '_date' ) );
			$meta[ 'note' ] = jlt_json_decode( jlt_get_post_meta( get_the_ID(), $id . '_note' ) );

			foreach ( $meta as $key => $value ) {
				if ( empty( $value ) ) {
					$meta[ $key ] = array();
				}
			}

			?>
			<div class="jlt-metabox-addable" data-name="<?php echo esc_attr( $id ); ?>">
				<table class="jlt-addable-fields">
					<thead>
					<tr>
						<th><label><?php _e( 'Employer', 'job-listings-resume' ); ?></label></th>
						<th><label><?php _e( 'Job Title', 'job-listings-resume' ); ?></label></th>
						<th><label><?php _e( 'Start/end date', 'job-listings-resume' ); ?></label></th>
						<th><label><?php _e( 'Note', 'job-listings-resume' ); ?></label></th>
						<th></th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="4">
							<input type='hidden' value=""
							       name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_employer'; ?>]'/>
							<input type='hidden' value=""
							       name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_job'; ?>]'/>
							<input type='hidden' value=""
							       name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_date'; ?>]'/>
							<input type='hidden' value=""
							       name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_note'; ?>]'/>
							<input type="button" value="<?php _e( 'Add Experience', 'job-listings-resume' ); ?>"
							       class="button button-default jlt-clone-fields"
							       data-template="<tr><td><input type='text' name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_employer'; ?>][]' /></td><td><input type='text' name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_job'; ?>][]' /></td><td><input type='text' name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_date'; ?>][]' /></td><td><textarea name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_note'; ?>][]' ></textarea> </td><td><a href='javascript:void()' class='jlt-remove-fields'><?php _e( 'x', 'job-listings-resume' ); ?></a></td></tr>"/>
						</td>
					</tr>
					</tfoot>
					<tbody>
					<?php
					foreach ( $meta[ 'employer' ] as $index => $employer ) :
						// if( empty( $employer ) ) continue;
						?>
						<tr>
							<td><input type="text" name="jlt_meta_boxes[<?php echo esc_attr( $id ) . '_employer'; ?>][]"
							           value="<?php echo esc_attr( $meta[ 'employer' ][ $index ] ); ?>"/></td>
							<td><input type="text" name="jlt_meta_boxes[<?php echo esc_attr( $id ) . '_job'; ?>][]"
							           value="<?php echo esc_attr( $meta[ 'job' ][ $index ] ); ?>"/></td>
							<td><input type="text" name="jlt_meta_boxes[<?php echo esc_attr( $id ) . '_date'; ?>][]"
							           value="<?php echo esc_attr( $meta[ 'date' ][ $index ] ); ?>"/></td>
							<td><textarea
									name="jlt_meta_boxes[<?php echo esc_attr( $id ) . '_note'; ?>][]"><?php echo esc_attr( $meta[ 'note' ][ $index ] ); ?></textarea>
							</td>
							<td><a href="javascript:void()"
							       class="jlt-remove-fields"><?php _e( 'x', 'job-listings-resume' ); ?></a></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php
			break;
		case 'skill':
			$meta = array();
			$meta[ 'name' ] = jlt_json_decode( jlt_get_post_meta( get_the_ID(), $id . '_name' ) );
			$meta[ 'percent' ] = jlt_json_decode( jlt_get_post_meta( get_the_ID(), $id . '_percent' ) );

			foreach ( $meta as $key => $value ) {
				if ( empty( $value ) ) {
					$meta[ $key ] = array();
				}
			}

			?>
			<div class="jlt-metabox-addable" data-name="<?php echo esc_attr( $id ); ?>">
				<table class="jlt-addable-fields">
					<thead>
					<tr>
						<th><label><?php _e( 'Skill Name', 'job-listings-resume' ); ?></label></th>
						<th style="width:20%;">
							<label><?php _e( 'Percent % ( 1 to 100 )', 'job-listings-resume' ); ?></label></th>
						<th></th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="2">
							<input type='hidden' value=""
							       name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_name'; ?>]'/>
							<input type='hidden' value=""
							       name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_percent'; ?>]'/>
							<input type="button" value="<?php _e( 'Add Skill', 'job-listings-resume' ); ?>"
							       class="button button-default jlt-clone-fields"
							       data-template="<tr><td><input type='text' name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_name'; ?>][]' /></td><td><input type='text' name='jlt_meta_boxes[<?php echo esc_attr( $id ) . '_percent'; ?>][]' /></td><td><a href='javascript:void()' class='jlt-remove-fields'><?php _e( 'x', 'job-listings-resume' ); ?></a></td></tr>"/>
						</td>
					</tr>
					</tfoot>
					<tbody>
					<?php
					foreach ( $meta[ 'name' ] as $index => $name ) :
						// if( empty( $name ) ) continue;
						?>
						<tr>
							<td><input type="text" name="jlt_meta_boxes[<?php echo esc_attr( $id ) . '_name'; ?>][]"
							           value="<?php echo esc_attr( $meta[ 'name' ][ $index ] ); ?>"/></td>
							<td><input type="text" name="jlt_meta_boxes[<?php echo esc_attr( $id ) . '_percent'; ?>][]"
							           value="<?php echo esc_attr( $meta[ 'percent' ][ $index ] ); ?>"/></td>
							<td><a href="javascript:void()"
							       class="jlt-remove-fields"><?php _e( 'x', 'job-listings-resume' ); ?></a></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php
			break;
	}
}

function jlt_meta_box_function_sanitize_html_list_value( $values ) {
	if ( ! is_array( $values ) ) {
		return $values;
	}

	$count = count( $values );
	for ( $index = 0; $index < $count; $index ++ ) {
		$values[ $index ] = htmlentities( $values[ $index ], ENT_QUOTES );
	}

	return $values;
}

add_filter( 'jlt_sanitize_meta__education_note', 'jlt_meta_box_function_sanitize_html_list_value' );
add_filter( 'jlt_sanitize_meta__experience_note', 'jlt_meta_box_function_sanitize_html_list_value' );
