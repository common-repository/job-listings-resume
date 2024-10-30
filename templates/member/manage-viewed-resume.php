<?php
/**
 * Manage Viewed Resume Page.
 *
 * This template can be overridden by copying it to yourtheme/job-listings/member/manage-viewed_resume.php.
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

$package             = jlt_get_job_posting_info();
$can_view_resume     = isset( $package[ 'can_view_resume' ] ) ? $package[ 'can_view_resume' ] === '1' : false;
$resume_view_limit   = isset( $package[ 'resume_view_limit' ] ) ? intval( $package[ 'resume_view_limit' ] ) : 0;
$resume_remain       = jlt_get_resume_view_remain();
$resume_view_expired = jlt_is_resume_view_expired();

do_action( 'jlt_member_manage_viewed_resume_before' );

?>
	<div class="member-manage">


		<ul class="member-manage-notices">

			<?php if ( $viewed_resumes->have_posts() ): ?>
				<li>
				<?php echo sprintf( _n( "You've received %s resume", "You've received %s resumes", $viewed_resumes->found_posts, 'job-listings-resume' ), $viewed_resumes->found_posts ); ?>
			<?php else : ?>
				<?php _e( "You've received no resume", 'job-listings-resume' ); ?>
				</li>
			<?php endif; ?>

			<?php if ( $can_view_resume ) : ?>
				<?php if ( $resume_remain == 0 || $resume_view_expired ) : ?>

					<?php if ( $resume_view_limit > 0 ) : ?>
						<li>
							<?php echo sprintf( __( 'You can view %d more resumes', 'job-listings-resume' ), $resume_remain ); ?>
						</li>
					<?php endif; ?>

				<?php endif; ?>
			<?php endif; ?>

		</ul>

		<?php if ( $viewed_resumes->have_posts() ): ?>
			<form method="post">
				<div class="member-manage-table">
					<ul class="jlt-list jlt-list-viewed-resume">

						<li>
							<div class="col-rs-title jlt-col-25"><?php _e( 'Title', 'job-listings-resume' ) ?></div>
							<div class="col-rs-category jlt-col-30"><?php _e( 'Category', 'job-listings-resume' ) ?></div>
							<div class="col-rs-location jlt-col-30"><?php _e( 'Location', 'job-listings-resume' ) ?></div>
							<div class="col-rs-date jlt-col-15"><?php _e( 'Date Modified', 'job-listings-resume' ) ?></div>
						</li>

						<?php
						while ( $viewed_resumes->have_posts() ): $viewed_resumes->the_post();
							global $post, $resume;

							?>
							<li>
								<div class="col-rs-title jlt-col-25">

									<a href="<?php the_permalink() ?>"><strong><?php the_title() ?></strong></a>

								</div>

								<div class="col-rs-category jlt-col-30">

									<?php
									$job_categories = $resume->category();
									if ( ! empty( $job_categories ) ):
										?>
										<i class="jlt-icon jltfa-archive"></i>&nbsp;
										<?php echo implode( ', ', $job_categories ); ?>
									<?php endif; ?>

								</div>

								<div class="col-rs-location jlt-col-30">

									<?php
									$job_locations = $resume->location();
									if ( ! empty( $job_locations ) ):
										?>
										<i class="jlt-icon jltfa-map-marker"></i>&nbsp;
										<?php echo implode( ' / ', $job_locations ); ?>
									<?php endif; ?>

								</div>

								<div class="col-rs-date jlt-col-15">

									<i class="jlt-icon jltfa-calendar"></i>&nbsp;<em><?php the_modified_date(); ?></em>

								</div>
							</li>
						<?php endwhile; ?>
					</ul>
				</div>

				<?php jlt_member_pagination( $viewed_resumes ) ?>

			</form>
		<?php endif; ?>
	</div>
<?php
do_action( 'jlt_member_manage_viewed_resume_after' );
