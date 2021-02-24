<?php
defined( 'ABSPATH' ) || exit;

/**
 * Bookingx Import tool real xml file only supported which export by Bookingx.
 */
class BkxImport {


	/**
	 * @var string
	 */
	public $file_type = 'xml'; // File Extension .xml.

	/**
	 * @var array
	 */
	public $import_type = array( 'all', 'seat', 'base', 'extra', 'users', 'booking', 'settings' ); // Allowed export type.

	/**
	 * @var int
	 */
	public $max_file_size = 5; // In MB.

	/**
	 * @var string
	 */
	public $upload_dir = '';

	/**
	 * @var string
	 */
	public $target_file = '';

	/**
	 * @var array
	 */
	var $errors = array();


	/**
	 * BkxImport constructor.
	 *
	 * @param string $type
	 */
	function __construct( $type = 'all' ) {
		if ( isset( $_POST['import_xml'] ) && sanitize_text_field( wp_unslash( $_POST['import_xml'] ) ) == 'Import Xml' ) {
			$this->upload_dir = BKX_PLUGIN_DIR_PATH . 'public/uploads/importXML/';
			if ( isset( $this->errors['errors'] ) && ! empty( $this->errors['errors'] ) ) {
				$this->errors = $this->errors['errors'];
			}
			if ( ! in_array( $type, $this->import_type, true ) ) {
				$this->errors['type_error'] = "Oops.. , '$type' type does not exists.";
				return $this->errors;
			}
		}
	}

	/**
	 * @param  null $fileobj
	 * @param  null $post_data
	 * @return array
	 */
	function import_now( $fileobj = null, $post_data = null ) {
		try {
			if ( isset( $_POST['import_xml'] ) && sanitize_text_field( wp_unslash( $_POST['import_xml'] ) ) == 'Import Xml' ) {
				$file_data = $this->check_file_is_ok( $fileobj );
				if ( ! empty( $this->errors ) ) {
					return $this->errors;
				}
				if ( isset( $post_data['truncate_records'] ) ) {
					$truncate_records = apply_filters( 'bkx_truncate_records', $post_data['truncate_records'] );
					$this->truncate_old_data( $truncate_records );
				}
				$http_referer = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
				if ( ! empty( $file_data ) ) {
					$loadxml = new SimpleXMLElement( $file_data );
					if ( isset( $loadxml ) && count( $loadxml ) > 0 ) {
						$SeatPosts    = $loadxml->SeatPosts;
						$BasePosts    = $loadxml->BasePosts;
						$ExtraPosts   = $loadxml->ExtraPosts;
						$BookingPosts = $loadxml->BookingPosts;
						$BkxUsers     = $loadxml->BkxUsers;
						$Settings     = $loadxml->Settings;
						$this->generate_post( $SeatPosts, 'bkx_seat' );
						$this->generate_post( $BasePosts, 'bkx_base' );
						$this->generate_post( $ExtraPosts, 'bkx_addition' );
						$this->generate_setting( $Settings );
						$this->generate_post( $BookingPosts, 'bkx_booking' );
						$this->generate_bkx_users( $BkxUsers );
						$redirect = add_query_arg( array( 'bkx_success' => 'FIS' ), $http_referer );
						header( "Location: {$redirect}" );
					}
				}
			}
		} catch ( Exception  $exception ) {
			$redirect = add_query_arg( array( 'bkx_error' => 'FIE' ), $http_referer );
			header( "Location: {$redirect}" );
		}

	}

	/**
	 * @param null $xml_data
	 */
	function generate_setting( $xml_data = null ) {
		if ( ! empty( $xml_data ) ) {
			foreach ( $xml_data as $value ) {
				foreach ( $value as $key => $setting ) {
					$setting = maybe_unserialize( reset( $setting ) );
					bkx_crud_option_multisite( $key, $setting, 'update' );
				}
			}
		}
	}

	function generate_bkx_users( $xml_data = null ) {
		global $wpdb;
		if ( ! empty( $xml_data ) ) {
			foreach ( $xml_data as $value ) {
				foreach ( $value as $key => $user_obj ) {
					$create_user_data = $create_user_meta = array();
					$UserDataObj      = $user_obj->UserData;
					$UserCapsObj      = $user_obj->UserCaps;
					$UserRole         = reset( $user_obj->UserRoles );
					$UserMetaDataObj  = $user_obj->UserMetaData;
					if ( ! empty( $UserDataObj ) ) {
						foreach ( $UserDataObj as $key => $UserData ) {
							foreach ( $UserData as $key => $UserDataVal ) {
								   $create_user_data[ $key ] = reset( $UserDataVal );
							}
						}
					}
					$posts = array();
					if ( isset( $create_user_data['user_email'] ) && $create_user_data['user_email'] != '' ) {
						$posts = $wpdb->get_results(
							$wpdb->prepare(
								"SELECT * FROM {$wpdb->postmeta} 
			  									 WHERE meta_key = 'seatEmail'
			  									 AND meta_value =  %s
			  									 LIMIT 1",
								$create_user_data['user_email']
							),
							ARRAY_A
						);
					}
					$post_obj = reset( $posts );
					if ( isset( $create_user_data['user_login'] ) && username_exists( $create_user_data['user_login'] ) ) {
						$user_id = username_exists( $create_user_data['user_login'] );
						if ( is_multisite() ) {
							if ( is_user_member_of_blog( $user_id ) == false ) {
								add_user_to_blog( get_current_blog_id(), $user_id, $UserRole );
							}
							$userobj = get_user_by( 'login', $create_user_data['user_login'] );
							if ( ! empty( $userobj ) && ! is_wp_error( $userobj ) ) {
								$user_id = $userobj->ID;
							}
						}
					} else {
						$user_id = wp_insert_user( $create_user_data );
					}
					if ( isset( $user_id ) && $user_id != '' ) {
						if ( ! empty( $UserMetaDataObj ) ) {
							foreach ( $UserMetaDataObj as $key => $UserMetaData ) {
								foreach ( $UserMetaData as $key => $UserMetaDataVal ) {
									$meta_value = reset( $UserMetaDataVal );
									update_user_meta( $user_id, $key, $meta_value );
								}
							}
						}
						if ( ! empty( $post_obj ) && isset( $post_obj->post_id ) && $post_obj->post_id != '' ) {
							update_user_meta( $user_id, 'seat_post_id', $post_obj->post_id );
							update_post_meta( $post_obj->post_id, 'seat_user_id', $user_id );
						}
						$user = get_userdata( $user_id );
						if ( $user && $user->exists() ) {
							$user->add_role( $UserRole );
						}
					}
				}
			}
		}
	}

	/**
	 * @param null $xml_data
	 * @param null $type
	 */
	function generate_post( $xml_data = null, $type = null ) {
		$http_referer = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
		try {
			if ( ! empty( $xml_data ) && ! empty( $type ) ) {
				foreach ( $xml_data as $data ) {
					if ( ! empty( $data ) ) {
						foreach ( $data as $posts ) {
							$post_name   = ! empty( $posts->Name ) ? wp_kses_post( $posts->Name ) : '';
							$description = ! empty( $posts->Description ) ? wp_kses_post( $posts->Description ) : '';
							// Create Post.
							$import_post = array(
								'post_title'   => wp_strip_all_tags( $post_name ),
								'post_content' => wp_strip_all_tags( $description ),
								'post_status'  => 'publish',
								'post_type'    => esc_html( $type ),
							);
							if ( $type == 'bkx_booking' ) {
								$import_post['post_status']    = (string) $posts->post_status[0];
								$import_post['comment_status'] = (string) $posts->comment_status[0];
								$import_post['ping_status']    = (string) $posts->ping_status[0];
								$import_post['post_date']      = (string) $posts->post_date[0];
								$import_post['post_date_gmt']  = (string) $posts->post_date_gmt[0];
							}

							// Insert the post into the database.
							$post_id   = wp_insert_post( $import_post );
							$post_data = get_post( $post_id );
							if ( ! empty( $post_data->post_name ) ) {
								$post_data_slug = $post_data->post_name;
								update_post_meta( $post_id, "{$type}_slug", $post_data_slug );
							}
							// Generate Post Meta.
							$postmetaObj = $posts->Postmeta;

							// Generate Comment.
							if ( $type == 'bkx_booking' && ! empty( $posts->CommentData ) ) {
								$commentObj    = json_decode( $posts->CommentData );
								$BkxBookingObj = new BkxBooking( '', $post_id );
								if ( ! empty( $commentObj ) && ! empty( $post_id ) ) {
									foreach ( $commentObj as $key => $comment_arr ) {
										$BkxBookingObj->add_order_note( $comment_arr->comment_content );
									}
								}
							}
							if ( ! empty( $postmetaObj ) && ! empty( $post_id ) ) {
								foreach ( $postmetaObj as $postmeta_arr ) {
									if ( ! empty( $postmeta_arr ) ) {
										foreach ( $postmeta_arr as $key => $postmeta ) {
											$postmeta_data = maybe_unserialize( reset( $postmeta ) );
											update_post_meta( $post_id, $key, $postmeta_data );
											if ( $type == 'bkx_base' && ( $key == 'base_seat_all' || $key == 'base_selected_seats' ) ) {
												if ( $postmeta_data == 'All' ) {
													   $args     = array(
														   'fields' => 'ids',
														   'post_type' => 'bkx_seat',
														   'numberposts' => -1,
													   );
													   $seat_ids = get_posts( $args );
													   update_post_meta( $post_id, 'base_selected_seats', $seat_ids );
												} else {
													if ( $key == 'seat_slugs' && ! empty( $postmeta_data ) ) {
														$args     = array(
															'fields' => 'ids',
															'post_type' => 'bkx_seat',
															'post_name__in' => $postmeta_data,
														);
														$seat_ids = get_posts( $args );
														update_post_meta( $post_id, 'base_selected_seats', $seat_ids );
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		} catch ( Exception  $exception ) {
			$redirect = add_query_arg(
				array(
					'bkx_error'     => 'FIE',
					'error_message' => $exception->getMessage(),
				),
				$http_referer
			);
			header( "Location: {$redirect}" );
		}

	}

	/**
	 * @param  $fileobj
	 * @return array|bool|string
	 */
	function check_file_is_ok( $fileobj ) {
		if ( isset( $fileobj['import_file'] ) && $fileobj['import_file']['size'] > 0 ) :
			if ( $fileobj['import_file']['size'] > 10048576 ) :
				$this->errors['file_size_not_in_range'] = 'Upload file size should be upto 5 MB.';
		 else :
			 // $file_name         = sanitize_file_name( $fileobj['import_file']['name'] );
			 $this->target_file = $this->upload_dir . time() . '.xml';
			 $filetype          = pathinfo( $this->target_file, PATHINFO_EXTENSION );
			 if ( isset( $filetype ) && $filetype == 'xml' ) :
				 if ( move_uploaded_file( $fileobj['import_file']['tmp_name'], "$this->target_file" ) ) :
					 chmod( $this->target_file, 0777 );
					 $file_data = file_get_contents( $this->target_file );
					 return $file_data;
				 endif;
		  else :
			  $this->errors['file_typo'] = 'File type not supported, upload only xml file.';
		  endif;
		 endif;
	 else :
		 $this->errors['file_empty'] = 'File does not exists.';
	 endif;
	 return $this->errors;
	}

	/**
	 * @param  null $flag
	 * @return int
	 */
	function truncate_old_data( $flag = null ) {
		$truncate_flag = 0;
		if ( isset( $flag ) && $flag == 'on' ) {
			global $wpdb;
			// Delete all Posts in posts table.
			$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->posts . ' WHERE ' . $wpdb->posts . '.post_type = %s', 'bkx_seat' ) );
			$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->posts . ' WHERE ' . $wpdb->posts . '.post_type = %s', 'bkx_base' ) );
			$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->posts . ' WHERE ' . $wpdb->posts . '.post_type = %s', 'bkx_addition' ) );
			$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->posts . ' WHERE ' . $wpdb->posts . '.post_type = %s', 'bkx_booking' ) );
			// Delete all postmeta which not exists in posts table.
			$wpdb->query(
				$wpdb->prepare(
					'DELETE FROM ' . $wpdb->postmeta . '  
		    	WHERE ' . $wpdb->postmeta . '.post_id NOT IN ( SELECT ' . $wpdb->posts . '.ID FROM ' . $wpdb->posts . ' )'
				),
				''
			);
			// Delete all setting.
			$BkxExport     = new BkxExport();
			$setting_array = $BkxExport->settings_fields();
			// delete previous setting options value from database.
			if ( ! empty( $setting_array ) ) {
				foreach ( $setting_array as $opt_key ) {
					delete_option( $opt_key );
				}
			}
			$truncate_flag = 1; // Success.
		}
		return $truncate_flag;
	}

	function print_error() {
		global $wpdb;
		if ( $wpdb->last_error !== '' ) :
			$str   = htmlspecialchars( $wpdb->last_result, ENT_QUOTES );
			$query = htmlspecialchars( $wpdb->last_query, ENT_QUOTES );
			print "<div id='error'> 
	        <p class='wpdberror'><strong>WordPress database error:</strong> [$str]<br />
	        <code>$query</code></p>
	        </div>";// phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
		endif;
	}
}

new BkxImport();
