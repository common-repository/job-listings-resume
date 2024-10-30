<?php

function jlt_get_resume_custom_fields( $include_disabled_fields = false ) {
	$custom_fields = jlt_get_custom_fields( 'jlt_resume_custom_field', 'jlt_resume_field_' );

	$default_fields = jlt_get_resume_default_fields();

	$custom_fields = jlt_merge_custom_fields( $default_fields, $custom_fields, $include_disabled_fields );

	return apply_filters( 'jlt_resume_custom_fields', $custom_fields );
}

function jlt_get_resume_search_custom_fields() {
	$custom_fields = jlt_get_resume_custom_fields();

	$not_searchable = jlt_not_searchable_custom_fields_type();
	foreach ( $custom_fields as $key => $field ) {
		if ( ! empty( $field[ 'type' ] ) ) {
			if ( in_array( $field[ 'type' ], $not_searchable ) ) {
				unset( $custom_fields[ $key ] );
			}
		}
	}

	return apply_filters( 'jlt_resume_search_custom_fields', $custom_fields );
}

function jlt_get_resume_custom_fields_option( $key = '', $default = null ) {
	$custom_fields = jlt_get_setting( 'jlt_resume_custom_field', array() );

	if ( ! $custom_fields || ! is_array( $custom_fields ) ) {
		return $default;
	}

	if ( isset( $custom_fields[ '__options__' ] ) && isset( $custom_fields[ '__options__' ][ $key ] ) ) {

		return $custom_fields[ '__options__' ][ $key ];
	}

	return $default;
}

function jlt_rcf_settings_tabs( $tabs = array() ) {
	$temp1 = array_slice( $tabs, 0, 1 );
	$temp2 = array_slice( $tabs, 1 );

	$resume_cf_tab = array( 'resume' => __( 'Resume', 'job-listings-resume' ) );

	return array_merge( $temp1, $resume_cf_tab, $temp2 );
}

// add to page Custom field (cf) tab.
add_filter( 'jlt_custom_field_setting_tabs', 'jlt_rcf_settings_tabs' );

function jlt_resume_custom_fields_setting() {
	wp_enqueue_style( 'jlt-custom-fields' );
	wp_enqueue_script( 'jlt-custom-fields' );

	jlt_custom_fields_setting( 'jlt_resume_custom_field', 'jlt_resume_field_', jlt_get_resume_custom_fields( true ) );

	do_action( 'jlt_resume_custom_fields_setting_options' );
}

add_action( 'jlt_custom_field_setting_resume', 'jlt_resume_custom_fields_setting' );

function jlt_resume_render_form_field( $field = array(), $resume_id = 0 ) {
	$field_id = jlt_resume_custom_fields_name( $field[ 'name' ], $field );

	if ( ! empty( $field[ 'remove_prefix' ] ) ) {
		$field_id = esc_attr( $field[ 'name' ] );
	}
	if ( in_array( $field[ 'name' ], array( 'title' ) ) ) {
		$value = $field[ 'value' ];
	} else {
		$value = ! empty( $resume_id ) ? jlt_get_post_meta( $resume_id, $field_id, '' ) : '';
		$value = ! is_array( $value ) ? trim( $value ) : $value;
	}

	$params = apply_filters( 'jlt_resume_render_form_field_params', compact( 'field', 'field_id', 'value' ), $resume_id );
	extract( $params );
	$object = array( 'ID' => $resume_id, 'type' => 'post' );

	?>
	<fieldset class="fieldset-<?php jlt_custom_field_class( $field, $object ); ?>">

		<label for="<?php echo esc_attr( $field_id ) ?>">
			<?php echo( isset( $field[ 'label_translated' ] ) ? $field[ 'label_translated' ] : $field[ 'label' ] ) ?>
			<?php echo isset( $field[ 'required' ] ) && $field[ 'required' ] ? '<span class="label-required">' . __( '*', 'job-listings-resume' ) . '</span>' : ''; ?>
		</label>

		<div class="field">
			<?php jlt_render_field( $field, $field_id, $value, '', $object ); ?>
		</div>

	</fieldset>
	<?php
}

function jlt_resume_render_search_field( $field = array() ) {
	$field_id = jlt_resume_custom_fields_name( $field[ 'name' ], $field );

	$params = apply_filters( 'jlt_resume_render_search_field_params', compact( 'field', 'field_id', 'value' ) );
	extract( $params );

	$field[ 'required' ] = ''; // no need for required fields in search form

	$value = isset( $_GET[ $field_id ] ) ? $_GET[ $field_id ] : '';
	$value = ! is_array( $value ) ? trim( $value ) : $value;
	?>
	<div class="form-group">
		<label for="<?php echo 'search-' . esc_attr( $field_id ) ?>"
		       class="control-label"><?php echo( isset( $field[ 'label_translated' ] ) ? $field[ 'label_translated' ] : $field[ 'label' ] ) ?></label>
		<div class="advance-search-form-control">
			<?php jlt_render_field( $field, $field_id, $value, 'search' ); ?>
		</div>
	</div>
	<?php
}

function jlt_resume_advanced_search_field( $field_val = '' ) {
	if ( empty( $field_val ) || $field_val == 'no' ) {
		return '';
	}

	$field_arr = explode( '|', $field_val );
	$field_id  = isset( $field_arr[ 0 ] ) ? $field_arr[ 0 ] : '';

	if ( empty( $field_id ) ) {
		return '';
	}

	$fields = jlt_get_resume_search_custom_fields();

	$field_prefix = jlt_resume_custom_fields_prefix();
	$field_id     = str_replace( $field_prefix, '', $field_id );

	foreach ( $fields as $field ) {
		if ( sanitize_title( $field[ 'name' ] ) == $field_id ) {
			jlt_resume_render_search_field( $field );
			break;
		}
	}

	return '';
}

function jlt_resume_custom_fields_prefix() {
	return apply_filters( 'jlt_resume_custom_fields_prefix', '_jlt_resume_field_' );
}

function jlt_resume_custom_fields_name( $field_name = '', $field = array() ) {
	if ( empty( $field_name ) ) {
		return '';
	}

	$cf_name = jlt_resume_custom_fields_prefix() . sanitize_title( $field_name );

	if ( ! empty( $field ) && isset( $field[ 'is_default' ] ) ) {
		$cf_name = $field[ 'name' ];
	}

	return apply_filters( 'jlt_resume_custom_fields_name', $cf_name, $field_name, $field );
}

function jlt_get_resume_field( $field_name = '' ) {

	$custom_fields = jlt_get_resume_custom_fields();
	if ( isset( $custom_fields[ $field_name ] ) ) {
		return $custom_fields[ $field_name ];
	}

	foreach ( $custom_fields as $field ) {
		if ( $field_name == $field[ 'name' ] ) {
			return $field;
		}
	}

	return array();
}

function jlt_get_resume_field_value( $resume_id, $field = array() ) {
	$field[ 'type' ] = isset( $field[ 'type' ] ) ? $field[ 'type' ] : 'text';
	$id              = jlt_resume_custom_fields_name( $field[ 'name' ], $field );

	$value = $resume_id ? jlt_get_post_meta( $resume_id, $id, '' ) : '';
	if ( $id == '_job_category' ) {
		if ( ! empty( $value ) ) {
			$value          = jlt_resume_get_tax_value( $resume_id, $id );
			$category_terms = empty( $value ) ? array() : get_terms( 'job_category', array( 'include'    => array_merge( $value, array( - 1 ) ),
			                                                                                'hide_empty' => 0,
			                                                                                'fields'     => 'names',
			) );
			$value          = implode( ', ', $category_terms );
		}
	} elseif ( $id == '_job_location' ) {
		if ( ! empty( $value ) ) {
			$value          = jlt_resume_get_tax_value( $resume_id, $id );
			$location_terms = empty( $value ) ? array() : get_terms( 'job_location', array( 'include'    => array_merge( $value, array( - 1 ) ),
			                                                                                'hide_empty' => 0,
			                                                                                'fields'     => 'names',
			) );
			$value          = implode( ', ', $location_terms );
		}
	} else {
		$value = ! is_array( $value ) ? trim( $value ) : $value;
	}

	return $value;
}