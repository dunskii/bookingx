<?php
/**
 * Generate Breadcrumb for Listing Page, Single Page, Archive Page
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! empty( $args ) ) {
    echo $args['wrap_before'];

    foreach ( $args['breadcrumb'] as $key => $crumb ) {

        echo $args['before'];
        $breadcrumb = $args['breadcrumb'];
        if ( ! empty( $crumb['hyperlink'] ) && sizeof( $breadcrumb ) !== $key + 1 ) {
            echo '<a href="' . esc_url( $crumb['hyperlink'] ) . '">' . esc_html( $crumb['title'] ) . '</a>';
        } else {
            echo esc_html( $crumb['title'] );
        }

        echo $args['after'];

        if ( sizeof( $breadcrumb ) !== $key + 1 ) {
            echo $args['delimiter'];
        }
    }
    echo $args['wrap_after'];
}