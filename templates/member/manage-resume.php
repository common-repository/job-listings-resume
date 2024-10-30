<?php
/**
 * Manage Resume Page.
 *
 * This template can be overridden by copying it to yourtheme/job-listings/member/manage-resume.php.
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

?>
<?php

do_action( 'jlt_member_manage_resume_before' );

?>
	<div class="member-manage">

		<?php if ( $list_resumes->have_posts() ): ?>

			<div><?php echo sprintf( _n( "You've saved %s resume", "You've saved %s resumes", $list_resumes->found_posts, 'job-listings-resume' ), '<span>' . $list_resumes->found_posts . '</span>' ); ?></div>

			<form method="post">
				<div class="member-manage-table">
					<ul class="jlt-list jlt-list-resume">

						<li>
							<div class="jlt-col-40 col-resume-title"><strong><?php _e( 'Title', 'job-listings-resume' ); ?></strong></div>
							<div class="jlt-col-30 col-category"><strong><?php _e( 'Category', 'job-listings-resume' ); ?></strong></div>
							<div class="jlt-col-15 col-status"><strong><?php _e( 'Status', 'job-listings-resume' ); ?></strong></div>
							<div class="jlt-col-15 col-actions"><strong><?php _e( 'Action', 'job-listings-resume' ); ?></strong></div>
						</li>

						<?php while ( $list_resumes->have_posts() ): $list_resumes->the_post();

							global $post, $resume;

							$status = jlt_resume_status( $post );

							?>
							<li>
								<div class="jlt-col-40 col-resume-title">
									<?php if ( $status[ 'class' ] == 'publish' ) : ?>

										<a href="<?php the_permalink() ?>"><strong><?php the_title() ?></strong></a>

									<?php else : ?>

										<a href="<?php echo jlt_resume_preview_url(); ?>"><strong><?php the_title() ?></strong></a>

									<?php endif; ?>
									<i class="jlt-icon jltfa-calendar"></i> <em><?php the_modified_date(); ?></em>
								</div>
								<div class="jlt-col-30 col-category">
									<em>
										<?php
										$resume_category = $resume->category();
										echo implode( ', ', $resume_category );
										?>
									</em>
								</div>
								<div class="jlt-col-15 col-status">
									<span class="jlt-resume-status jlt-resume-<?php echo $status[ 'class' ]; ?>">
										<?php echo $status[ 'text' ]; ?>
									</span>
								</div>
								<div class="jlt-col-15 col-actions">

									<a href="<?php echo jlt_resume_edit_url(); ?>" class="jlt-btn-link"
									   title="<?php esc_attr_e( 'Edit Resume', 'job-listings-resume' ) ?>">
										<i class="jlt-icon jltfa-pencil"></i>
									</a>

									<a href="<?php echo jlt_resume_delete_url(); ?>" class="jlt-btn-link"
									   title="<?php esc_attr_e( 'Delete Resume', 'job-listings-resume' ) ?>">
										<i class="jlt-icon jltfa-trash-o"></i>
									</a>

								</div>
							</li>

						<?php endwhile; ?>

					</ul>

				</div>

				<?php jlt_member_pagination( $list_resumes ) ?>

				<?php jlt_form_nonce( 'resume-manage-action' ) ?>
			</form>

		<?php else: ?>

			<p><?php echo __( "You have no resume, why don't you start posting one.", 'job-listings-resume' ) ?></p>

		<?php endif; ?>
	</div>
<?php
do_action( 'jlt_member_manage_resume_after' );