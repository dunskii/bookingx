<?php
/**
 * Template Loader
 *
 * @package Bookingx/Core/Classes
 */

defined( 'ABSPATH' ) || exit;

class Bkx_Template_Loader {



	private static $post_type = array();

	public static function init() {
		add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
		$bkx_post_type   = array( 'bkx_seat', 'bkx_base', 'bkx_addition' );
		self::$post_type = apply_filters( 'bkx_bookingx_template_custom_post_type', $bkx_post_type );
	}

	/**
	 * @param  $default_file
	 * @return array|mixed|void
	 */
	private static function get_template_loader_files( $default_file ) {
		$check_post_type = get_post_type( get_the_ID() );
		if ( ! in_array( $check_post_type, self::$post_type ) ) {
			return $default_file;
		}

		$templates   = apply_filters( 'bkx_template_loader_files', array(), $default_file );
		$templates[] = 'bookingx.php';

		if ( is_page_template() ) {
			$templates[] = get_page_template_slug();
		}

		if ( is_singular( $check_post_type ) ) {
			$object       = get_queried_object();
			$name_decoded = urldecode( $object->post_name );
			if ( $name_decoded !== $object->post_name ) {
				$templates[] = "single-{$check_post_type}-{$name_decoded}.php";
			}
			$templates[] = "single-{$check_post_type}-{$object->post_name}.php";
		}

		if ( is_tax( get_object_taxonomies( $check_post_type ) ) ) {
			$object      = get_queried_object();
			$templates[] = 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
			$templates[] = BKX()->template_path() . 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
			$templates[] = 'taxonomy-' . $object->taxonomy . '.php';
			$templates[] = BKX()->template_path() . 'taxonomy-' . $object->taxonomy . '.php';
		}

		$templates[] = $default_file;
		$templates[] = BKX()->template_path() . $default_file;

		return array_unique( $templates );
	}

	/**
	 * @param  $template
	 * @return string
	 */
	public static function template_loader( $template ) {
		if ( is_embed() ) {
			return $template;
		}

		$default_file  = self::get_template_loader_default_file();
		$template_path = BKX()->template_path();
		if ( $default_file ) {
			$search_files = self::get_template_loader_files( $default_file );
			$template     = locate_template( $search_files );

			if ( ! $template ) {
				$template = $template_path . '/templates/' . $default_file;
			}
		}
		return $template;
	}

	/**
	 * @return string
	 */
	private static function get_template_loader_default_file() {
		$check_post_type = get_post_type( get_the_ID() );

		if ( ! in_array( $check_post_type, self::$post_type ) ) {
			return '';
		}

		if ( is_singular( $check_post_type ) ) {
			$default_file = "single-{$check_post_type}.php";
		} elseif ( is_tax( get_object_taxonomies( 'bkx_seat' ) ) ) {
			$object = get_queried_object();
			if ( is_tax( "{$check_post_type}_cat" ) ) {
				$default_file = "taxonomy-{$object->taxonomy}.php";
			} else {
				$default_file = "archive-{$check_post_type}.php";
			}
		} elseif ( is_post_type_archive( $check_post_type ) ) {
			$default_file = "archive-{$check_post_type}.php";
		} else {
			$default_file = '';
		}
		return $default_file;
	}
}

add_action( 'init', array( 'Bkx_Template_Loader', 'init' ) );
