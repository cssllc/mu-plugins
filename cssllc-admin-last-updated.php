<?php declare( strict_types=1 );

class CSSLLC_Admin_Last_Updated {

	const OPTION_NAME = '_cssllc_admin_last_updated';

	public static function init() : void {
		static $once = false;

		if ( ! empty( $once ) ) {
			return;
		}

		$once = true;

		new self;
	}

	/**
	 * Generate option name for timestamp.
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	protected static function timestamp_option_name( string $id ) : string {
		return sprintf( '_cssllc_admin_last_updated_%s', $id );
	}

	/**
	 * Construct.
	 */
	protected function __construct() {

		add_action( 'admin_print_scripts', array( $this, 'action__admin_print_scripts' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'action__admin_print_footer_scripts' ) );

		add_action( 'update_option_blog_public', array( $this, 'save_option_timestamp' ), 10, 3 );
		add_action( 'delete_option_blog_public', array( $this, 'save_option_timestamp' ) );

	}

	/**
	 * Update option.
	 *
	 * @param string $id
	 *
	 * @return void
	 */
	protected function update_option( string $id ) : void {
		$option = get_option( self::OPTION_NAME, array() );

		if ( empty( $option ) ) {
			$option = array();
		}

		$option[ $id ] = current_time( 'timestamp', true );

		update_option( self::OPTION_NAME, $option, false );
	}

	/**
	 * Print JSON object of timestamps.
	 *
	 * @return void
	 */
	protected function print_json_object() : void {
		$option = get_option( self::OPTION_NAME, array() );

		if ( empty( $option ) ) {
			$option = array();
		}

		$option = array_map( static function( int $value ) : string {
			return human_time_diff( $value ) . ' ago';
		}, $option );

		$option = json_encode( $option );
		?>

		<script>
			// CSSLLC Admin Last Updated
			window.cssllc_admin_last_updated = <?php echo $option ?>;
		</script>

		<?php
	}

	/**
	 * Action: admin_print_scripts
	 *
	 * Print JSON object of timestamps.
	 *
	 * @uses $this->print_json_object()
	 *
	 * @return void
	 */
	public function action__admin_print_scripts() : void {
		if ( 'admin_print_scripts' !== current_action() ) {
			return;
		}

		$this->print_json_object();
	}

	/**
	 * Action: admin_print_footer_scripts
	 *
	 * Print JavaScript to add "Last Updated" text.
	 *
	 * @return void
	 */
	public function action__admin_print_footer_scripts() : void {
		if ( 'admin_print_footer_scripts' !== current_action() ) {
			return;
		}

		if ( 'options-reading' !== get_current_screen()->base ) {
			return;
		}
		?>

		<script>
			// CSSLLC Admin Last Updated
			( function() {
				var data = window.cssllc_admin_last_updated;

				if ( document.querySelector( 'tr.option-site-visibility th' ) && data.blog_public ) {
					document.querySelector( 'tr.option-site-visibility th' ).innerHTML += '<br /><small style="font-weight: 400;">Last updated: ' + data.blog_public + '</small>';
				}

			} () );
		</script>

		<?php
	}

	/**
	 * Action: update_option_{$option_name}
	 *
	 * Save timestamp on option update.
	 *
	 * @param mixed $old_value
	 * @param mixed $new_value
	 * @param mixed $option_name
	 *
	 * @uses $this->update_option()
	 *
	 * @action update_option_{$option_name}
	 * @action delete_option_{$option_name}
	 *
	 * @return void
	 */
	public function save_option_timestamp( ...$args ) : void {

		// action: delete_option_{$option_name}
		$option_name = $args[0];

		// action: update_option_{$option_name}
		if ( 3 === count( $args ) ) {
			$old_value   = $args[0];
			$new_value   = $args[1];
			$option_name = $args[2];

			if ( $old_value === $new_value ) {
				return;
			}
		}

		$this->update_option( $option_name );
	}

}

if ( ! is_admin() ) {
	return;
}

CSSLLC_Admin_Last_Updated::init();