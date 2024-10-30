<?php
/**
 * resume-application.php
 *
 * @package:
 * @since  : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function jlt_resume_metabox_application() {

	$helper = new JLT_Meta_Boxes_Helper( '', array( 'page' => 'application' ) );

	$meta_box = array(
		'id'       => "resume_application_metabox",
		'title'    => __( 'Application Resume', 'job-listings-resume' ),
		'page'     => 'application',
		'context'  => 'side',
		'priority' => 'high',
		'fields'   => array(
			array(
				'id'       => '_resume',
				'label'    => __( 'Resume', 'job-listings-resume' ),
				'type'     => 'app_resume',
				'callback' => 'jlt_resume_metabox_application_content',
			),
		),
	);
	$helper->add_meta_box( $meta_box );
}

add_action( 'add_meta_boxes', 'jlt_resume_metabox_application', 20 );

function jlt_resume_metabox_application_content( $post, $id, $type, $meta, $std, $field ) {
	$resumes = get_posts( array( 'post_type' => 'resume', 'post_status' => 'publish', 'posts_per_page' => - 1 ) );

	$resume_link = ! empty( $meta ) ? add_query_arg( 'application_id', $meta, get_permalink( $meta ) ) : 'javascript:void(0)';
	?>
	<select id="<?php echo $id; ?>" name="jlt_meta_boxes[<?php echo $id; ?>]">
		<option value="" class="no-hide"><?php echo __( 'Select Resume', 'job-listings-resume' ); ?></option>
		<?php foreach ( $resumes as $resume ) : ?>
			<option <?php selected( $meta, $resume->ID ); ?> value="<?php echo $resume->ID; ?>"
			                                                 class="candidate_<?php echo $resume->post_author; ?>"
			                                                 data-permalink="<?php echo get_permalink( $resume->ID ); ?>"><?php echo $resume->post_title; ?></option>
		<?php endforeach; ?>
	</select>
	<a class="application-resume" href="<?php echo $resume_link; ?>"
	   target="_blank"><?php echo __( 'View Resume', 'job-listings-resume' ); ?></a>
	<script>
		jQuery(document).ready(function ($) {
			if ($('#post_author_override')) {
				var author_el = $('#post_author_override');
				var parentField = $('.<?php echo esc_attr( $id ); ?>');
				var resumes_el = parentField.find('select#<?php echo esc_attr( $id ); ?>');

				if (author_el.val() === "") {
					parentField.hide();
				} else {
					resumes_el.find('option').hide();
					resumes_el.find('option.candidate_' + author_el.val()).show();
					parentField.show();
				}

				author_el.change(function () {
					var $this = $(this);
//						resumes_el.find('option:selected').removeAttr("selected");
					$this.siblings('a.application-resume').attr('href', 'javascript:void(0)');

					if ($this.val() === "") {
						parentField.hide();
					} else {
						resumes_el.find('option:not(.no-hide)').hide();
						resumes_el.find('option.candidate_' + $this.val()).show();
						parentField.show();
					}
				});

				resumes_el.change(function () {
					var $this = $(this);
					var selected_opt = $this.find('option:selected');
					$this.siblings('a.application-resume').attr('href', selected_opt.data('permalink'));
				});
			}
		});
	</script>
	<?php
}

function jlt_resume_application_list_col( $post_id ) {
	$resume = jlt_get_post_meta( $post_id, '_resume', '' );
	if ( ! empty( $resume ) ) :
		$resume_link = add_query_arg( 'application_id', $post_id, get_permalink( $resume ) );
		?>
		<a href="<?php echo esc_url( $resume_link ); ?>" target="_blank"><i
				class="dashicons dashicons-id-alt"></i>&nbsp;<?php echo esc_html__( 'View Resume', 'job-listings-resume' ); ?>
		</a>
	<?php endif;
}

add_action( 'jlt_admin_list_application_attachment', 'jlt_resume_application_list_col' );