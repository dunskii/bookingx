<?php
/**
 * BookingX Generate Breadcrumb for Listing Page, Single Page, Archive Page Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $args ) ) {
	echo $args['wrap_before']; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

	foreach ( $args['breadcrumb'] as $key => $crumb ) {

		echo $args['before']; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
		$breadcrumb = $args['breadcrumb'];
		if ( ! empty( $crumb['hyperlink'] ) && count( $breadcrumb ) !== $key + 1 ) {
			echo '<a href="' . esc_url( $crumb['hyperlink'] ) . '">' . esc_html( $crumb['title'] ) . '</a>';
		} else {
			echo esc_html( $crumb['title'] );
		}

		echo $args['after']; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

		if ( count( $breadcrumb ) !== $key + 1 ) {
			echo $args['delimiter']; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
		}
	}
	echo $args['wrap_after']; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
}
