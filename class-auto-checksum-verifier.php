<?php
/**
 * Implementation of the Auto Checksum Verifier plugin functionality.
 */

class ACV_Auto_Checksum_Verifier {


	/**
	 * Singleton instance of ACV_Auto_Checksum_Verifier.
	 *
	 * @var ACV_Auto_Checksum_Verifier
	 */
	protected static $instance;

	/**
	 * Only make one instance of ACV_Auto_Checksum_Verifier.
	 *
	 * @return ACV_Auto_Checksum_Verifier
	 */
	public static function get_instance() {
		if ( ! self::$instance instanceof Auto_CheACV_Auto_Checksum_Verifiercksum_Verifier ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	protected function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'acv_verify_checksums', array( $this, 'verify_checksums' ) );
		add_action( 'admin_post_acv_verify_now', array( $this, 'handle_verify_now' ) );
	}

	/**
	 * Schedule the checksum verification event on plugin activation.
	 */
	public static function activate() {
		if ( ! wp_next_scheduled( 'acv_verify_checksums' ) ) {
			$date = new DateTime( 'tomorrow 1 AM', new DateTimeZone( get_option( 'timezone_string' ) ) );
			wp_schedule_event( $date->getTimestamp(), 'daily', 'acv_verify_checksums' );
		}
	}

	/**
	 * Add Settings menu.
	 */
	public function add_admin_menu() {
		add_options_page(
			'Checksums',
			'Checksums',
			'manage_options',
			'acv_checksums',
			array( $this, 'options_page' )
		);
	}

	/**
	 * Initialize plugin settings.
	 */
	public function settings_init() {
		register_setting( 'acv_checksums', 'acv_settings', array( $this, 'sanitize_settings' ) );

		add_settings_section(
			'acv_checksums_section',
			__( 'Checksum Settings', 'auto-checksum-verifier' ),
			null,
			'acv_checksums'
		);

		add_settings_field(
			'acv_email',
			__( 'Email Address', 'auto-checksum-verifier' ),
			array( $this, 'email_render' ),
			'acv_checksums',
			'acv_checksums_section'
		);

		add_settings_field(
			'acv_include_root',
			__( 'Include root files', 'auto-checksum-verifier' ),
			array( $this, 'include_root_render' ),
			'acv_checksums',
			'acv_checksums_section'
		);

		add_settings_field(
			'acv_most_recent',
			__( 'Most recent', 'auto-checksum-verifier' ),
			array( $this, 'most_recent_render' ),
			'acv_checksums',
			'acv_checksums_section'
		);
	}

	/**
	 * Sanitize settings on form submission.
	 *
	 * @param array $input	Settings form input.
	 * @return array		Sanitized settings.
	 */
	public function sanitize_settings( $input ) {
		if ( ! isset( $_POST['acv_nonce'] ) || ! wp_verify_nonce( $_POST['acv_nonce'], 'acv_save_settings' ) ) {
			add_settings_error( 'acv_settings', 'acv_nonce_error', __( 'Nonce verification failed.', 'auto-checksum-verifier' ), 'error' );
			return get_option( 'acv_settings' );
		}

		if ( empty( $input['acv_email'] ) ) {
			add_settings_error( 'acv_settings', 'acv_email_error', __( 'Email address cannot be blank.', 'auto-checksum-verifier' ), 'error' );
			return get_option( 'acv_settings' );
		}

		$input['acv_email'] = sanitize_email( $input['acv_email'] );
		$input['acv_include_root'] = isset( $input['acv_include_root'] ) ? 'on' : '';

		return $input;
	}

	/**
	 * Render email input.
	 */
	public function email_render() {
		$options = get_option( 'acv_settings' );
		$email = isset( $options['acv_email'] ) ? $options['acv_email'] : get_option( 'admin_email' );
		?>
		<input type='email' name='acv_settings[acv_email]' value='<?php echo esc_attr( $email ); ?>'>
		<?php
	}

	/**
	 * Render include root checkbox.
	 */
	public function include_root_render() {
		$options = get_option( 'acv_settings' );
		$checked = isset( $options['acv_include_root'] ) ? $options['acv_include_root'] : '';
		?>
		<input type='checkbox' name='acv_settings[acv_include_root]' <?php checked( $checked, 'on' ); ?>>
		<?php
	}

	/**
	 * Options page UI.
	 */
	public function options_page() {
		?>
		<form action='options.php' method='post'>
			<?php
			settings_fields( 'acv_checksums' );
			do_settings_sections( 'acv_checksums' );
			wp_nonce_field( 'acv_save_settings', 'acv_nonce' );
			submit_button();
			?>
		</form>
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<input type="hidden" name="action" value="acv_verify_now">
			<?php wp_nonce_field( 'acv_verify_now', 'acv_verify_now_nonce' ); ?>
			<?php submit_button( 'Verify Now' ); ?>
		</form>
		<?php
		settings_errors( 'acv_settings' );
	}

	/**
	 * Render most recent scan results
	 */
	public function most_recent_render() {
		$last_scan_time = get_option( 'acv_last_scan_time' );
		$last_scan_result = get_option( 'acv_last_scan_result' );

		if ( ! $last_scan_time || ! $last_scan_result ) {
			echo 'None.';
		} else {
			echo '<strong>' . esc_html( date( 'F jS, Y @g:i A', strtotime( $last_scan_time ) ) ) . "</strong><br>";
			echo '<ul>';
			$lines = explode( "\n", $last_scan_result );
			foreach ( $lines as $line ) {
				if ( ! empty( trim( $line ) ) ) {
					echo '<li style="list-style: disc;">' . esc_html( $line ) . '</li>';
				}
			}
			echo '</ul>';
		}
	}

	/**
	 * Handle the "Verify Now" button click.
	 */
	public function handle_verify_now() {
		if ( ! isset( $_POST['acv_verify_now_nonce'] ) || ! wp_verify_nonce( $_POST['acv_verify_now_nonce'], 'acv_verify_now' ) ) {
			wp_die( __( 'Nonce verification failed.', 'auto-checksum-verifier' ) );
		}

		$this->verify_checksums();

		wp_redirect( admin_url( 'options-general.php?page=acv_checksums' ) );
		exit;
	}

	/**
	 * Verify checksums. Send email if verification fails.
	 */
	public function verify_checksums() {
		$options = get_option( 'acv_settings' );
		$include_root = isset( $options['acv_include_root'] ) ? ' --include-root' : '';
		$command = 'cd ' . ABSPATH . ' && wp core verify-checksums' . $include_root;
		$output = shell_exec( $command . ' 2>&1' );

		$timestamp = current_time( 'mysql' );
		update_option( 'acv_last_scan_time', $timestamp );
		$previous_result = get_option( 'acv_last_scan_result' );

		$lines = explode( "\n", $output );
		if ( isset( $lines[0] ) && strpos( $lines[0], 'Success: ' ) === 0 ) {
			$current_result = 'OK';
		} else {
			$current_result = $output;
		}

		if ( $current_result !== $previous_result ) {
			update_option( 'acv_last_scan_result', $current_result );

			if ( $current_result !== 'OK' ) {
				$email = isset( $options['acv_email'] ) ? $options['acv_email'] : get_option( 'admin_email' );
				$subject = 'WordPress Checksum Verification Failed [' . get_site_url() . ']';
				$headers = array('Content-Type: text/plain; charset=UTF-8');
				$preamble = "WordPress Checksum Verification Failed for your website at " . get_site_url() . "\n\n";
				wp_mail( $email, $subject, $preamble . $output, $headers );
			}
		}

		return $current_result === 'OK';
	}
}
