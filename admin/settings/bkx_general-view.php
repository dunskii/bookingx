<?php // phpcs:ignore
/**
 * Template load for Business Information Section and Settings
 *
 * @package Bookingx/admin
 * @since      1.0.8
 */

defined( 'ABSPATH' ) || exit;
$bkx_export_file_name = BKX_PLUGIN_DIR_PATH . 'public/uploads/newfile.xml';
$seat_alias           = bkx_crud_option_multisite( 'bkx_alias_seat' );
$base_alias           = bkx_crud_option_multisite( 'bkx_alias_base' );
$addition_alias       = bkx_crud_option_multisite( 'bkx_alias_addition' );
$bookingx_admin       = new Bookingx_Admin();
if ( ! empty( $current_submenu_active ) && 'alias' === $current_submenu_active ) :?>
	<!---Alias settings-->
	<h3> 
	<?php
	// Translators: $s General Submenu Label for General View.
		printf( esc_html__( '%1$s', 'bookingx' ), esc_html( $bkx_general_submenu_label ) ); // phpcs:ignore
	?>
	</h3>
	<form name="form_alias" id="id_form_alias" method="post">
		<table cellspacing="0" class="widefat " style="margin-top:20px;">
			<tbody>
			<tr class="active">
				<th scope="row"><label
							for="Resource">
							<?php
							echo esc_html__( 'Resource', 'bookingx' );
							?>
						</label></th>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="text" name="seat_alias" id="id_seat_alias" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_alias_seat' ) ); ?>">
					</div>
				</td>
			</tr>
			<tr class="active">
				<th scope="row"><label
							for="Service"><?php echo esc_html__( 'Service', 'bookingx' ); ?></label>
				</th>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="text" name="base_alias" id="id_base_alias" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_alias_base' ) ); ?>">
					</div>
				</td>
			</tr>
			<tr class="active">
				<th scope="row"><label for='Extras'><?php echo esc_html__( 'Extras', 'bookingx' ); ?></label>
				</th>

				<td class="plugin-description">
					<div class="plugin-description">
						<input type="text" name="addition_alias" id="id_addition_alias" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_alias_addition' ) ); ?>">
					</div>
				</td>
			</tr>

			<tr class="active">
				<th scope="row"><label for="bkx_label_of_step1 Text"><?php echo esc_html__( 'Label for booking form Step 1', 'bookingx' ); ?> </label>
				</th>
				<td class="plugin-description">
					<div class="plugin-description">
						<textarea name="bkx_label_of_step1" cols="50"><?php echo esc_html( bkx_crud_option_multisite( 'bkx_label_of_step1' ) ); ?></textarea>
					</div>
				</td>
			</tr>

			<tr class="active">
				<th scope="row"><label for="extended Text">Label for <?php echo esc_html( bkx_crud_option_multisite( 'bkx_alias_base' ) ); ?> time be extended text</label>
				</th>
				<td class="plugin-description">
					<div class="plugin-description">
						<textarea name="bkx_notice_time_extended_text_alias" cols="50"><?php echo esc_html( bkx_crud_option_multisite( 'bkx_notice_time_extended_text_alias' ) ); ?></textarea>
					</div>
				</td>
			</tr>
	<?php do_action( 'bkx_general_alias_settings' ); ?>
			</tbody>
		</table>
		<input type="hidden" name="alias_flag" value="1">
		<input type="hidden" name="bkx_setting_form_init" value="1">
		<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_alias" id="id_save_alias" value="Save Changes"/></p>
	</form>
	<!---End Alias settings-->
<?php endif; ?>

<?php if ( ! empty( $current_submenu_active ) && 'content_setting' === $current_submenu_active ) : ?>
	<!--Page Setting-->
	<h3> 
	<?php
	// Translators: $s General Submenu Label for General View.
		printf( esc_html__( '%1$s', 'bookingx' ), $bkx_general_submenu_label ); // phpcs:ignore
	?>
	</h3>

	<!--End Page Setting-->
	<form name="form_template" id="id_form_template" method="post">
		<table cellspacing="0" class="widefat bkx-content-settings" style="margin-top:20px;">
			<tbody>
			<input type="hidden" name="template_flag" value="1">
			<input type="hidden" name="bkx_setting_form_init" value="1">

			<tr class="active header">
				<th scope="row"><label for="<?php echo esc_html__( 'Taxonomy Settings', 'bookingx' ); ?>"><?php echo esc_html__( 'Taxonomy Settings', 'bookingx' ); ?></label>
				</th>
			</tr>

			<tr class="active">
				<td class="plugin-description" colspan="3">
					<div class="plugin-description">
						<b>Note :</b> <?php echo esc_html__( '  If Select "Yes" than Category option enable for this post type.', 'bookingx' ); ?>
					</div>
				</td>
			</tr>

			<tr class="active">
				<th scope="row"><label for="Enable 
				<?php
				// Translators: $s General Submenu Label for Taxonomy.
					printf( esc_html__( '%1$s', 'bookingx' ), esc_attr( $seat_alias ) ); // phpcs:ignore
				?>
			Taxonomy"> 
	<?php
						// Translators: $s Seat Alias.
						printf( esc_html__( 'Enable %1$s Taxonomy', 'bookingx' ), esc_html( $seat_alias ) );
	?>
						</label>
				</th>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="bkx_seat_taxonomy_status" id="id_enable_seat_taxonomy_yes" value="1"
							<?php
							if ( bkx_crud_option_multisite( 'bkx_seat_taxonomy_status' ) === 1
								 || bkx_crud_option_multisite( 'bkx_seat_taxonomy_status' ) === '1' ) {
								echo 'checked';
							}
							?>
						><?php echo esc_html__( 'Yes', 'bookingx' ); ?>
						<input type="radio" name="bkx_seat_taxonomy_status" id="id_enable_seat_taxonomy_no" value="0"
							<?php
							if ( bkx_crud_option_multisite( 'bkx_seat_taxonomy_status' ) === 0 ||
								 bkx_crud_option_multisite( 'bkx_seat_taxonomy_status' ) === '0' ) {
								echo 'checked';
							}
							?>
						>
						<?php echo esc_html__( 'No', 'bookingx' ); ?>
					</div>
				</td>
			</tr>

			<tr class="active">
				<th scope="row"><label for="Enable 
				<?php
					// Translators: $s Base Alias.
					printf( esc_html__( '%1$s', 'bookingx' ), esc_html( $base_alias ) ); // phpcs:ignore
				?>
				Taxonomy">
						<?php
						// Translators: $s Base Alias.
						printf( esc_html__( 'Enable %1$s Taxonomy', 'bookingx' ), esc_html( $base_alias ) ); // phpcs:ignore
						?>
						</label></th>

				<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="bkx_base_taxonomy_status" id="id_enable_base_taxonomy_yes" value="1"
							<?php
							if ( bkx_crud_option_multisite( 'bkx_base_taxonomy_status' ) === 1
								 || bkx_crud_option_multisite( 'bkx_base_taxonomy_status' ) === '1' ) {
								echo 'checked';
							}
							?>
							><?php printf( esc_html__( 'Yes', 'bookingx' ) ); ?>
						<input type="radio" name="bkx_base_taxonomy_status" id="id_enable_base_taxonomy_no" value="0"
							<?php
							if ( bkx_crud_option_multisite( 'bkx_base_taxonomy_status' ) === 0 ||
								 bkx_crud_option_multisite( 'bkx_base_taxonomy_status' ) === '0' ) {
								echo 'checked';
							}
							?>
						>
						<?php echo esc_html__( 'No', 'bookingx' ); ?>
					</div>
				</td>
			</tr>

			<tr class="active">
				<th scope="row"><label for="Enable 
				<?php
					// Translators: $s Addition Alias.
					printf( esc_html__( '%1$s', 'bookingx' ), esc_html( $addition_alias ) ); // phpcs:ignore
				?>
				Taxonomy">
				<?php
						// Translators: $s Addition Alias.
						printf( esc_html__( 'Enable %1$s Taxonomy', 'bookingx' ), esc_html( $addition_alias ) );
				?>
					</label>
				</th>

				<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="bkx_addition_taxonomy_status" id="id_bkx_addition_taxonomy_status_yes" value="1"
							<?php
							if ( bkx_crud_option_multisite( 'bkx_addition_taxonomy_status' ) === 1 ||
								 bkx_crud_option_multisite( 'bkx_addition_taxonomy_status' ) === '1' ) {
								echo 'checked';
							}
							?>
						><?php printf( esc_html__( 'Yes', 'bookingx' ) ); ?>
						<input type="radio" name="bkx_addition_taxonomy_status" id="id_bkx_addition_taxonomy_status_no" value="0"
							<?php
							if ( bkx_crud_option_multisite( 'bkx_addition_taxonomy_status' ) === 0 ||
								 bkx_crud_option_multisite( 'bkx_addition_taxonomy_status' ) === '0' ) {
								echo 'checked';
							}
							?>
							>
						<?php echo esc_html__( 'No', 'bookingx' ); ?>
					</div>
				</td>
			</tr>
			<tr class="active header">
				<th scope="row"><label for="<?php echo esc_html__( 'Other Settings', 'bookingx' ); ?>"><?php echo esc_html__( 'Other Settings', 'bookingx' ); ?></label>
				</th>
			</tr>
			<tr class="active">
				<th scope="row"><label for="Set Booking Page"><?php echo esc_html__( 'Set Booking Page', 'bookingx' ); ?></label>
				</th>

				<td class="plugin-description">
					<?php
					$bkx_set_booking_page = bkx_crud_option_multisite( 'bkx_set_booking_page' );
					$args                 = array(
						'depth'                 => 0,
						'child_of'              => 0,
						'selected'              => esc_html( $bkx_set_booking_page ),
						'echo'                  => 1,
						'name'                  => 'bkx_set_booking_page',
						'id'                    => null, // string.
						'class'                 => null, // string.
						'show_option_none'      => null, // string.
						'show_option_no_change' => null, // string.
						'option_none_value'     => null, // string.
					);
					wp_dropdown_pages( $args ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
					?>
					Shortcode : [bkx_booking_form]
				</td>
			</tr>

			<tr class="active">
				<th scope="row"><label for="Enable Legal options"> <?php echo esc_html__( 'Enable Legal options', 'bookingx' ); ?></label>
				</th>

				<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="bkx_legal_options" id="id_bkx_legal_options_yes" value="1"
							<?php
							if ( bkx_crud_option_multisite( 'bkx_legal_options' ) === 1 ||
								 bkx_crud_option_multisite( 'bkx_legal_options' ) === '1' ) {
								echo 'checked';
							}
							?>
						><?php echo esc_html__( 'Yes', 'bookingx' ); ?>
						<input type="radio" name="bkx_legal_options" id="id_bkx_legal_options_no" value="0"
							<?php
							if ( bkx_crud_option_multisite( 'bkx_legal_options' ) === 0 ||
								 bkx_crud_option_multisite( 'bkx_legal_options' ) === '0' ) {
								echo 'checked';
							}
							?>
							> <?php echo esc_html__( 'No', 'bookingx' ); ?>
					</div>
				</td>
			</tr>

			<tr class="active legal-dropdown">
				<th scope="row"><label
							for="Terms and conditions"> <?php echo esc_html__( 'Set Terms & Conditions', 'bookingx' ); ?></label>
				</th>
				<td class="plugin-description">
					<div class="plugin-description">
						<?php
						$bkx_term_cond_page = bkx_crud_option_multisite( 'bkx_term_cond_page' );
						$args               = array(
							'show_option_none' => 'Select Page',
							'selected'         => esc_html( $bkx_term_cond_page ),
							'name'             => 'bkx_term_cond_page',
						);
						wp_dropdown_pages( $args ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
						?>
					</div>
				</td>
			</tr>

			<tr class="active legal-dropdown">
				<th scope="row"><label
							for="Privacy Policy"><?php echo esc_html__( 'Set Privacy Policy', 'bookingx' ); ?> </label>
				</th>
				<td class="plugin-description">
					<div class="plugin-description">
						<?php
						$bkx_privacy_policy_page = bkx_crud_option_multisite( 'bkx_privacy_policy_page' );
						$args                    = array(
							'show_option_none' => 'Select Page',
							'selected'         => esc_html( $bkx_privacy_policy_page ),
							'name'             => 'bkx_privacy_policy_page',
						);
						wp_dropdown_pages( $args ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
						?>
					</div>
				</td>
			</tr>

			<tr class="active legal-dropdown">
				<th scope="row"><label
							for="Cancellation Policy"><?php echo esc_html__( 'Set Cancellation Policy', 'bookingx' ); ?> </label>
				</th>
				<td class="plugin-description">
					<div class="plugin-description">
						<?php
						$bkx_cancellation_policy = bkx_crud_option_multisite( 'bkx_cancellation_policy_page' );
						$args                    = array(
							'show_option_none' => 'Select Page',
							'selected'         => esc_html( $bkx_cancellation_policy ),
							'name'             => 'bkx_cancellation_policy_page',
						);
						wp_dropdown_pages( $args ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
						?>
					</div>
				</td>
			</tr>

			<tr class="active header">
				<th scope="row"><label
							for="Editor Settings"> <?php echo esc_html__( 'Editor Settings', 'bookingx' ); ?></label>
				</th>
			</tr>
			<tr>
				<th scope="row"><label
							for="enable_editor"><?php echo esc_html__( 'Display Booking X shortcode icon on visual editor and text editor?', 'bookingx' ); ?></label>
				</th>

				<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="enable_editor" id="id_enable_editor_yes" value="1"
							<?php
							if ( bkx_crud_option_multisite( 'enable_editor' ) === 1 ||
								 bkx_crud_option_multisite( 'enable_editor' ) === '1' ) {
								echo 'checked';
							}
							?>
						><?php echo esc_html__( 'Yes', 'bookingx' ); ?>
						<input type="radio" name="enable_editor" id="id_enable_editor_no" value="0"
							<?php
							if ( bkx_crud_option_multisite( 'enable_editor' ) === 0 ||
								 bkx_crud_option_multisite( 'enable_editor' ) === '0' ) {

								echo 'checked';
							}
							?>
						>
						<?php echo esc_html__( 'No', 'bookingx' ); ?>
					</div>
				</td>
			</tr>
	<?php do_action( 'bkx_general_content_setting' ); ?>
			</tbody>
		</table>
		<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_template" id="id_save_template" value="Save Changes"/></p>
	</form>
<?php endif; ?>

<?php if ( ! empty( $current_submenu_active ) && 'emails' === $current_submenu_active ) : ?>
	<?php
	$current_main_tab = ! empty( $_GET['bkx_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['bkx_tab'] ) ) : ''; // phpcs:ignore
	$section          = ! empty( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : ''; // phpcs:ignore
	$sub_tab          = isset( $_GET['sub_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['sub_tab'] ) ) : ''; // phpcs:ignore
	$mailer           = new BKX_Emails_Setup();
	$screen           = get_current_screen();
	$generate_tab_url = $screen->parent_file . '&page=bkx-setting&bkx_tab=bkx_general&section=emails';
	if ( 'bkx_general' === $current_main_tab && 'emails' === $section && isset( $sub_tab ) && '' !== $sub_tab ) {
		$email_templates = $mailer->get_emails();
		if ( $sub_tab ) {
			foreach ( $email_templates as $email_key => $email ) {
				if ( strtolower( $email_key ) === $sub_tab ) {
					?>
					<form name="bkx_emails_settings" id="bkx_emails_settings" method="post">
					<h3> 
					<?php
					// Translators: $s Email Title.
                  printf( esc_html__( '%1$s', 'bookingx' ), $email->title ); //phpcs:ignore
					?>
					<a href="<?php echo esc_attr( $generate_tab_url ); ?>"> Back </a></h3>
					<p>
					<?php
					// Translators: $s Email Desc.
                  printf( esc_html__( '%1$s', 'bookingx' ), $email->description ); //phpcs:ignore
					?>
						</p>
					<?php
					$email->admin_options();
					break;
				}
			}
		}
		?>
		<input type="hidden" name="bkx_emails_settings" value="1">
		<input type="hidden" name="bkx_setting_form_init" value="1">
		<p class="submit"><input type="submit" onclick="" class='button-primary' name="bkx_save_<?php echo esc_attr( $sub_tab ); ?>" id="bkx_save_<?php echo esc_attr( $sub_tab ); ?>" value="Save Changes"/></p>
		</form>
		<?php
	} else {
		?>
		<h3> 
		<?php
		// Translators: $s Email Settings.
      printf( esc_html__( '%1$s', 'bookingx' ), esc_html( $bkx_general_submenu_label ) ); //phpcs:ignore
		?>
		</h3>
		<p>Email notifications sent from Booking X are listed below. Click on an email to configure it.</p>
		<?php
		$email_settings = new BookingX_Email_Content();
		$email_settings->email_notification_setting();
	}
	?>
<?php endif; ?>

<?php if ( ! empty( $current_submenu_active ) && 'css' === $current_submenu_active ) : ?>
	<h3> 
	<?php
	// Translators: $s General View.
		printf( esc_html__( '%1$s', 'bookingx' ), esc_html( $bkx_general_submenu_label ) ); //phpcs:ignore
	?>
	</h3>

	<form name="form_siteuser" id="id_form_sitecss" method="post">
		<table cellspacing="0" class="widefat" style="margin-top:20px;">
			<tbody>
			<input type="hidden" name="sitecss_flag" value="1">
			<input type="hidden" name="bkx_setting_form_init" value="1">
	<?php $bkx_booking_style = bkx_crud_option_multisite( 'bkx_booking_style' ); ?>
			<tr class="active">
				<th scope="row"><label for="Set Booking Style"><?php echo esc_html__( 'Calendar Layout', 'bookingx' ); ?></label></th>
				<td class="plugin-description">
					<select name="bkx_booking_style" class="bkx-booking-style">
						<option value="default" <?php echo( ( '' === $bkx_booking_style || 'default' === $bkx_booking_style ) ? 'selected' : '' ); ?>><?php echo esc_html( 'Classic Calendar' ); ?>
						</option>
						<option value="day_style" <?php echo( ( 'day_style' === $bkx_booking_style ) ? 'selected' : '' ); ?>><?php echo esc_html( 'Sliding Calendar' ); ?>
						</option>
					</select>
					<?php if ( '' === $bkx_booking_style || 'default' === $bkx_booking_style ) { ?>
						<a href="<?php echo esc_url( BKX_PLUGIN_PUBLIC_URL ); ?>/images/preview/2.jpg" class="bkx-preview-<?php echo esc_attr( $bkx_booking_style ); ?> thickbox">Preview</a>
					<?php } ?>
					<?php if ( 'day_style' === $bkx_booking_style ) { ?>
						<a href="<?php echo esc_url( BKX_PLUGIN_PUBLIC_URL ); ?>/images/preview/2b.jpg" class="bkx-preview-<?php echo esc_attr( $bkx_booking_style ); ?> thickbox">Preview</a>
					<?php } ?>
				</td>
			</tr>

			<tr class="active">
				<th scope="row"><label for="Text Colour"><?php echo esc_html__( 'Booking Form Text Colour', 'bookingx' ); ?></label></th>

				<td class="plugin-description">
					<input type="text" name="bkx_text_color" id="id_bkx_text_color" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_form_text_color' ) ); ?>">
				</td>
				<th scope="row"><label class="bkx_form_slot_time_color_label" for="Slot Time Colour"><?php echo esc_html__( 'Booking Form Slot Time Colour', 'bookingx' ); ?></label>
				</th>
				<td class="plugin-description">
					<input type="text" name="bkx_form_slot_time_color" id="id_bkx_form_slot_time_color" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_form_slot_time_color' ) ); ?>">
				</td>
			</tr>

			<tr class="active">
				<th scope="row"><label for="Active Step Color"><?php echo esc_html__( 'Booking Form Active Step Colour', 'bookingx' ); ?></label></th>
				<td class="plugin-description">
					<input type="text" name="bkx_progressbar_color" id="id_bkx_progressbar_color" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_active_step_color' ) ); ?>">
				</td>
				<th scope="row"><label for="Calendar day background colour"><?php echo esc_html__( 'Booking Calendar Day Background Colour', 'bookingx' ); ?></label></th>
				<td class="plugin-description">
					<input type="text" name="bkx_cal_day_color" id="id_bkx_cal_day_color" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_cal_day_color' ) ); ?>">
				</td>
			</tr>
			<tr class="active">
				<th scope="row"><label for="Booking Previous Button"><?php echo esc_html__( 'Booking Previous Button', 'bookingx' ); ?></label></th>
				<td class="plugin-description">
					<input type="text" name="bkx_prev_btn" id="id_bkx_prev_btn" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_prev_btn' ) ); ?>">
				</td>
				<th scope="row"><label for="Booking Next Button"><?php echo esc_html__( 'Booking Next Button', 'bookingx' ); ?></label></th>
				<td class="plugin-description">
					<input type="text" name="bkx_next_btn" id="id_bkx_next_btn" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_next_btn' ) ); ?>">
				</td>
			</tr>
			<tr class="active">
				<th scope="row"><label for="Booking Pay Now Button"><?php echo esc_html__( 'Booking Pay Now Button', 'bookingx' ); ?></label>
				</th>
				<td class="plugin-description">
					<input type="text" name="bkx_pay_now_btn" id="id_bkx_pay_now_btn" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_pay_now_btn' ) ); ?>">
				</td>
				<th scope="row"><label for="Calendar day selected colour"><?php echo esc_html__( 'Booking Calendar Day Selected', 'bookingx' ); ?></label></th>
				<td class="plugin-description">
					<input type="text" name="bkx_cal_day_selected_color" id="id_bkx_cal_day_selected_color" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_cal_day_selected_color' ) ); ?>">
				</td>
			</tr>
			<tr class="active">
				<th scope="row"><label for="Add time available colour option"><?php echo esc_html__( 'Booking Form Time available', 'bookingx' ); ?></label>
				</th>
				<td class="plugin-description">
					<input type="text" name="bkx_time_available_color" id="id_bkx_time_available_color" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_time_available_color' ) ); ?>">
				</td>
				<th scope="row"><label for="Add time selected colour"><?php echo esc_html__( 'Booking Form Selected Time', 'bookingx' ); ?></label></th>
				<td class="plugin-description">
					<input type="text" name="bkx_time_selected_color" id="id_bkx_time_selected_color" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_time_selected_color' ) ); ?>">
				</td>
			</tr>
			<tr class="active">
				<th scope="row"><label for="Add time unavailable colour"><?php echo esc_html__( 'Booking Form unavailable colour', 'bookingx' ); ?></label></th>
				<td class="plugin-description">
					<input type="text" name="bkx_time_unavailable_color" id="id_bkx_time_unavailable_color" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_time_unavailable_color' ) ); ?>">
				</td>
			</tr>
	<?php do_action( 'bkx_general_styling_settings' ); ?>
			</tbody>
		</table>
		<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_sitecss" id="id_save_sitecss" value="Save Changes"/></p>
	</form>
<?php endif; ?>

<?php if ( ! empty( $current_submenu_active ) && 'page_settings' === $current_submenu_active ) : ?>
	<!---Other settings-->
	<h3> 
	<?php
	// Translators: $s General View.
		printf( esc_html__( '%1$s', 'bookingx' ), esc_html( $bkx_general_submenu_label ) ); //phpcs:ignore
	?>
	</h3>
	<form name="form_other_setting" id="id_other_setting" method="post">
		<table cellspacing="0" class="widefat" style="margin-top:20px;">
			<tbody>
			<input type="hidden" name="bkx_setting_form_init" value="1">
			<tr class="active">
				<th scope="row"><label for="Cancel Booking"><?php echo esc_html__( 'Enable Cancel Booking', 'bookingx' ); ?></label></th>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="enable_cancel_booking" id="id_enable_cancel_booking_yes" value="1"
							<?php
							if ( bkx_crud_option_multisite( 'enable_cancel_booking' ) === 1 ||
								 bkx_crud_option_multisite( 'enable_cancel_booking' ) === '1' ) {
								echo 'checked';
							}
							?>
						><?php printf( esc_html__( 'Yes', 'bookingx' ) ); ?>
						<input type="radio" name="enable_cancel_booking" id="id_enable_cancel_booking_no" value="0"
							<?php
							if ( bkx_crud_option_multisite( 'enable_cancel_booking' ) === 0 || bkx_crud_option_multisite( 'enable_cancel_booking' ) === '0' ) {
								echo 'checked';
							}
							?>
						><?php printf( esc_html__( 'No', 'bookingx' ) ); ?>
					</div>
				</td>
			</tr>
			<tr class="active" style="display:none;" id="page_drop_down_cancel_booking">
				<th scope="row"><label for="Cancellation"><?php echo esc_html__( 'Select Page for Cancellation Policy ', 'bookingx' ); ?></label></th>
				<td class="plugin-description">
					<div class="plugin-description">
						<?php
						$args = array( 'selected' => bkx_crud_option_multisite( 'cancellation_policy_page_id' ) );
						wp_dropdown_pages( $args ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
						?>
					</div>
				</td>
			</tr>
			<tr class="active">
				<th scope="row"><label for="ANY"> 
				<?php
						// Translators: $s Seat Alias.
						printf( esc_html__( 'Allow "Any" for %1$s option', 'bookingx' ), esc_html( bkx_crud_option_multisite( 'bkx_alias_seat' ) ) );
				?>
						</label></th>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="enable_any_seat" id="id_enable_any_seat_yes" value="1"
							<?php
							if ( bkx_crud_option_multisite( 'enable_any_seat' ) === 1 ||
								 bkx_crud_option_multisite( 'enable_any_seat' ) === '1' ) {
								echo 'checked';
							}
							?>
						><?php printf( esc_html__( 'Yes', 'bookingx' ) ); ?>
						<input type="radio" name="enable_any_seat" id="id_enable_any_seat_no" value="0"
							<?php
							if ( bkx_crud_option_multisite( 'enable_any_seat' ) === 0 ||
								 bkx_crud_option_multisite( 'enable_any_seat' ) === '0' ) {
								echo 'checked';
							}
							?>
						> <?php printf( esc_html__( 'No', 'bookingx' ) ); ?>
					</div>
				</td>
			</tr>

			<tr class="active bkx-customer-dashboard-main">
				<th scope="row"><label for="edit and delete bookings"><?php echo esc_html__( 'Enable Customer Dashboard', 'bookingx' ); ?></label></th>

				<td class="plugin-description">
					<div class="radio-button-section">
						<input type="radio" name="bkx_enable_customer_dashboard" id="id_bkx_enable_customer_dashboard_yes" value="1"
							<?php
							if ( bkx_crud_option_multisite( 'bkx_enable_customer_dashboard' ) === 1 ||
								 bkx_crud_option_multisite( 'bkx_enable_customer_dashboard' ) === '1' ) {
								echo 'checked';
							}
							?>
						><?php printf( esc_html__( 'Yes', 'bookingx' ) ); ?>
						<input type="radio" name="bkx_enable_customer_dashboard" id="id_bkx_enable_customer_dashboard_no" value="0"
							<?php
							if ( bkx_crud_option_multisite( 'bkx_enable_customer_dashboard' ) === 0 ||
								 bkx_crud_option_multisite( 'bkx_enable_customer_dashboard' ) === '0' ) {
								echo 'checked';
							}
							?>
						> <?php printf( esc_html__( 'No', 'bookingx' ) ); ?>
					</div>
					<div class="shortcode-section" style="display: none;"><label>Shortcode : </label><code spellcheck="false">['bkx_my_account']</code></div>
				</td>
			</tr>

			<tr class="active">
				<th scope="row"><label for="dashboard column's bookings"><?php echo esc_html__( 'Dashboard Column\'s', 'bookingx' ); ?></label></th>
				<td class="plugin-description">
				<div class="plugin-description">
						<?php
						$bkx_dashboard_column_selected = bkx_crud_option_multisite( 'bkx_dashboard_column' );
						if ( empty( $bkx_dashboard_column_selected ) ) {
							$bkx_dashboard_column_selected = $bookingx_admin->bkx_booking_columns_data();
						}
						$bkx_dashboard_column_html = '';
						$bkx_booking_columns       = $bookingx_admin->bkx_booking_columns_data();
						if ( ! empty( $bkx_booking_columns ) ) {
							foreach ( $bkx_booking_columns as $key => $booking_columns ) {
								if ( 'cb' !== $key ) {
											   $bkx_booking_column_checked = ( ! empty( $bkx_dashboard_column_selected ) && in_array( $key, $bkx_dashboard_column_selected, true ) ? " checked='checked' " : '' );
											   $bkx_dashboard_column_html .= '<input type="checkbox" class="bkx-dashboard-column" name="bkx_dashboard_column[]" value="' . esc_attr( $key ) . '" ' . esc_attr( $bkx_booking_column_checked ) . '>' . esc_html( $booking_columns ) . '<br />';
								}
							}
						}
						$count_bkx_dashboard_column_selected = count( $bkx_dashboard_column_selected );
						$count_bkx_booking_columns           = count( $bkx_booking_columns ) - 1;
						$column_checked                      = '';
						if ( $count_bkx_dashboard_column_selected === $count_bkx_booking_columns ) {
							$column_checked = 'checked';
						}
						?>
						<input type="checkbox" class="bkx-dashboard-column-all" <?php echo esc_attr( $column_checked ); ?>> Select
						All <br/>
						<?php echo $bkx_dashboard_column_html; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
					</div>
				</td>
			</tr>

			<tr class="active">
				<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
				</td>
			</tr>
			<?php do_action( 'bkx_general_page_settings' ); ?>
			</tbody>
		</table>
		<input type="hidden" name="other_setting_flag" value="1">
		<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_other_setting" id="id_save_other_setting" value="Save Changes"/></p>
	</form>
	<!---End Other settings-->
<?php endif; ?>

<?php if ( ! empty( $current_submenu_active ) && 'exim' === $current_submenu_active ) : ?>
	<!-- This is for export button   -->
	<div>
		<h3> <?php echo esc_html__( 'Export XML', 'bookingx' ); ?> </h3>
	<?php
	$bkx_export_enable = is_writable( $bkx_export_file_name ) ? '' : 'disabled';
	if ( ! is_writable( $bkx_export_file_name ) ) {
		echo '<strong> Please allow file permission to generate file on ' . esc_url( BKX_PLUGIN_DIR_PATH ) . ' uploads Directory.</strong>';
	}
	?>
		<form name="xml_export" method="post" action="">
			<table class="widefat" style="margin-top:20px;">
				<tr class="active">
					<input type="hidden" id="id_addition_list" name="addition_list" value="">
					<input type="hidden" id="id_type" name="type" value="all">
					<input type="hidden" name="bkx_setting_form_init" value="1">
				</tr>
			</table>
			<p class="submit"><input type="submit" value="Export xml" <?php echo esc_attr( $bkx_export_enable ); ?> class='button-primary' name="export_xml"></p>
		</form>
	</div>
	<!--  End export functionality -->
	<!--Start Import Functionality -->
	<div>
		<h3> <?php echo esc_html__( 'Import XML', 'bookingx' ); ?> </h3>
		<form name="xml_export" method="post" action="" enctype="multipart/form-data">
			<table class="widefat" style="margin-top:20px;">
				<tr class="active">
					<td>
						<div><input type="checkbox" name="truncate_records"><small>
						<?php
								// Translators: $s Seat, Base and Addition Alias.
								printf( esc_html__( '( %1$s %2$s\'s , %3$s\'s , %4$s\'s , Booking data and All Setting\'s  )', 'bookingx' ), 'Check to delete all existing', esc_html( $seat_alias ), esc_html( $base_alias ), esc_html( $addition_alias ) );
						?>
								</small>
						</div>
						<input type="file" name="import_file">
						<input type="hidden" name="bkx_setting_form_init" value="1">
					</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" value="Import Xml" class='button-primary' name="import_xml"></p>
		</form>
	</div>
	<!--End Import Functionality-->
	<?php
endif; ?>

<?php if ( ! empty( $current_submenu_active ) && 'other' === $current_submenu_active ) : ?>
	<!---Other settings-->
	<h3>
	<?php
	// Translators: $s General View.
		printf( esc_html__( '%1$s', 'bookingx' ), $bkx_general_submenu_label ); //phpcs:ignore
	?>
	</h3>
	<form name="form_other_settings_section" id="id_other_setting" method="post">
		<table cellspacing="0" class="widefat" style="margin-top:20px;">
			<tbody>
				<input type="hidden" name="bkx_setting_form_init" value="1">
				<tr class="active">
					<th scope="row"><label for="Display & Require Price Booking"><?php printf( esc_html__( 'Display & Require Price for %1$ss', 'bookingx' ), $base_alias ); ?></label></th>
					<td class="plugin-description">
						<div class="plugin-description">
							<input type="radio" name="enable_price_booking" id="id_enable_price_booking_yes" value="1"
								<?php
								if ( bkx_crud_option_multisite( 'enable_price_booking' ) === 1 ||
								     bkx_crud_option_multisite( 'enable_price_booking' ) == '' ||
									 bkx_crud_option_multisite( 'enable_price_booking' ) === '1' ) {
									echo 'checked';
								}
								?>
							><?php printf( esc_html__( 'Yes', 'bookingx' ) ); ?>
							<input type="radio" name="enable_price_booking" id="id_enable_price_booking_no" value="0"
								<?php
								if ( bkx_crud_option_multisite( 'enable_price_booking' ) === 0 || bkx_crud_option_multisite( 'enable_price_booking' ) === '0' ) {
									echo 'checked';
								}
								?>
							><?php printf( esc_html__( 'No', 'bookingx' ) ); ?>
						</div>
					</td>
				</tr>
				<?php do_action( 'bkx_general_other_settings' ); ?>
			</tbody>
		</table>
		<input type="hidden" name="other_settings_flag" value="1">
		<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_other_setting" id="id_save_other_setting" value="Save Changes"/></p>
	</form>
	<!---End Other settings-->
<?php endif; ?>
