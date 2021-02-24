<?php
/**
 * BookingX Legal Options Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;
$privacy_policy     = bkx_crud_option_multisite( 'bkx_privacy_policy_page' );
$cancellation       = bkx_crud_option_multisite( 'bkx_cancellation_policy_page' );
$term_cond          = bkx_crud_option_multisite( 'bkx_term_cond_page' );
$privacy_policy_url = ( isset( $privacy_policy ) ) && $privacy_policy != '' ? get_permalink( $privacy_policy ) : '';
$term_cond_url      = ( isset( $term_cond ) ) && $term_cond != '' ? get_permalink( $term_cond ) : '';
$cancellation_url   = ( isset( $cancellation ) ) && $cancellation != '' ? get_permalink( $cancellation ) : '';
$bkx_legal          = bkx_crud_option_multisite( 'bkx_legal_options' );
if ( isset( $bkx_legal ) && $bkx_legal == 1 ) : ?>
	<?php if ( isset( $term_cond_url ) && $term_cond_url != '' ) : ?>
		<div class="modal fade bkx-tc" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"> <?php echo esc_html( get_the_title( $term_cond ) ); ?> </h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
		<?php
						echo get_the_content( '', false, get_post( $term_cond ) ); //phpcs:ignore
		?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( isset( $privacy_policy_url ) && $privacy_policy_url != '' ) : ?>
		<?php
		$privacy_policy_data = get_post( $privacy_policy );
		$data                = get_the_content( '', false, $privacy_policy_data );
		?>
		<div class="modal fade bkx-pp" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"> <?php echo esc_html( get_the_title( $privacy_policy ) ); ?> </h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<?php echo $data; //phpcs:ignore ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( isset( $cancellation_url ) && $cancellation_url != '' ) : ?>
		<?php
		$cancellation_data = get_post( $cancellation );
		$data              = get_the_content( '', false, $cancellation_data );
		?>
		<div class="modal fade bkx-cp" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"> <?php echo esc_html( get_the_title( $cancellation ) ); ?> </h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<?php echo $data; //phpcs:ignore ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

<?php endif; ?>
