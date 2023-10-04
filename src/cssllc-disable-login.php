<?php declare( strict_types=1 );

class CSSLLC_Disable_Login {

	/** @var string */
	protected $filename = '.disable-wplogin';

	/** @var string */
	protected $filepath = '';

	/** @var string */
	protected $headline = '';

	/** @var string */
	protected $message = '';

	/** @var string[] */
	protected $overrides = array(
		'charliealphalimaechobravo',
	);

	/** @var int */
	protected $unlock = 0;

	/**
	 * Initialize.
	 *
	 * @return void
	 */
	public static function init() : void {
		$instance = new self;

		if ( defined( 'WP_CLI' ) && constant( 'WP_CLI' ) ) {
			WP_CLI::add_command( 'login lock', array( $instance, 'cli__login_lock' ) );
			WP_CLI::add_command( 'login unlock', array( $instance, 'cli__login_unlock' ) );
			WP_CLI::add_command( 'login status', array( $instance, 'cli__login_status' ) );

			return;
		}

		if ( constant( 'PHP_SESSION_NONE' ) === session_status() ) {
			session_start();
		}

		add_action( 'login_init', array( $instance, 'action__login_init' ) );
		add_action( 'wp_login', array( $instance, 'action__wp_login' ) );
	}

	/**
	 * Construct.
	 *
	 * @return void
	 */
	protected function __construct() {
		$this->filepath = sprintf( '%s/%s', constant( 'WP_CONTENT_DIR' ), $this->filename );
		$this->headline = __( 'Login disabled' );
		$this->message  = __( 'Please check back later.' );

		$this->read_file();
	}

	/**
	 * Action: login_init
	 *
	 * @return void
	 */
	public function action__login_init() : void {
		if ( 'login_init' !== current_action() ) {
			return;
		}

		if ( ! $this->locked() ) {
			return;
		}

		if ( $this->override() ) {
			return;
		}

		$content = sprintf( '<h1>%s</h1>%s', esc_html( $this->headline ), wpautop( $this->message ) );

		wp_die( $content );
	}

	/**
	 * Action: wp_login
	 *
	 * Delete created session.
	 *
	 * @return void
	 */
	public function action__wp_login() : void {
		if ( constant( 'PHP_SESSION_NONE' ) === session_status() ) {
			session_start();
		}

		unset( $_SESSION['cssllc-disable-login'] );
	}

	/**
	 * Logic to determine if locked.
	 *
	 * @return bool
	 */
	protected function locked() : bool {
		if ( ! file_exists( $this->filepath ) ) {
			return false;
		}

		if ( empty( $this->unlock ) ) {
			return true;
		}

		if ( time() >= $this->unlock ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if provided override code is valid.
	 *
	 * @return bool
	 */
	protected function override() : bool {
		if ( ! empty( $_SESSION ) && $_SESSION['cssllc-disable-login'] === $this->hash() ) {
			return true;
		}

		if ( empty( $_GET['override'] ) ) {
			return false;
		}

		$override = sanitize_text_field( $_GET['override'] );

		if ( ! in_array( $override, $this->overrides ) ) {
			return false;
		}

		$_SESSION['cssllc-disable-login'] = $this->hash();

		return true;
	}

	/**
	 * Read .disable-wplogin file and process data.
	 *
	 * @return void
	 */
	protected function read_file() : void {
		if ( ! file_exists( $this->filepath ) ) {
			return;
		}

		$contents = file_get_contents( $this->filepath );

		if ( empty( $contents ) ) {
			$contents = '{}';
		}

		$contents = ( array ) json_decode( $contents, true );

		$contents = wp_parse_args( $contents, array(
			'overrides' => $this->overrides,
			'headline'  => $this->headline,
			'message'   => $this->message,
			'unlock'    => $this->unlock,
		) );

		$this->overrides = $contents['overrides'];
		$this->headline  = $contents['headline'];
		$this->message   = $contents['message'];
		$this->unlock    = $contents['unlock'];
	}

	/**
	 * Generate non-secured hash to invalidate override.
	 *
	 * @return string
	 */
	protected function hash() : string {
		$data = file_get_contents( $this->filepath );

		if ( false === $data ) {
			$data = filemtime( $this->filepath );
		}

		if ( false === $data ) {
			$data = wp_create_nonce( __FILE__ );
		}

		return wp_hash( ( string ) $data );
	}

	/**
	 * Lock the login screen.
	 *
	 * ## OPTIONS
	 *
	 * [--headline=<headline>]
	 * : Set the headline for the locked login screen.
	 * default: Login disabled
	 *
	 * [--message=<message>]
	 * : Set the message for the locked login screen.
	 * default: Please check back later.
	 *
	 * [--overrides=<overrides>]
	 * : Set the accepted override codes.
	 * default: ["charliealphalimaechobravo"]
	 *
	 * [--unlock=<unlock>]
	 * : Set the timestamp to automatically unlock the login screen.
	 * default: 0 (no auto-unlock)
	 *
	 * @param string[] $args
	 * @param array<string, string> $assoc_args
	 * @return void
	 */
	public function cli__login_lock( array $args, array $assoc_args = array() ) : void {
		if ( $this->locked() ) {
			WP_CLI::warning( 'Login is already locked.' );
			return;
		}

		WP_CLI::debug( 'Checking for existing file at ' . $this->filepath );

		if ( file_exists( $this->filepath ) ) {
			$result = unlink( $this->filepath );
			$debug  = 'Deleted existing lock file';

			if ( ! $result ) {
				$debug = 'Unable to delete existing lock file';
			}

			WP_CLI::debug( $debug );
		}

		$args = array();

		foreach ( array( 'headline', 'message', 'overrides', 'unlock' ) as $arg ) {
			if ( ! array_key_exists( $arg, $assoc_args ) ) {
				continue;
			}

			$value = WP_CLI\Utils\get_flag_value( $assoc_args, $arg, '' );

			if ( ! is_string( $value ) || empty( $value ) ) {
				continue;
			}

			$args[ $arg ] = json_decode( $value );
		}

		$result = file_put_contents( $this->filepath, json_encode( $args ) );

		WP_CLI::debug( 'file_put_contents() returned `' . ( false === $result ? 'false' : $result ) . '`' );

		if ( ! file_exists( $this->filepath ) ) {
			WP_CLI::error( 'Unable to lock login.' );
		}

		WP_CLI::success( 'Login locked.' );
		WP_CLI::line( 'Remember to shuffle the salts to logout all sessions (`wp config shuffle-salts`).' );
	}

	/**
	 * Unlock the login screen.
	 *
	 * ## EXAMPLES
	 *
	 *	# Unlock the login screen
	 *	$ wp login unlock
	 * 	Success: Login unlocked.
	 *
	 * @return void
	 */
	public function cli__login_unlock() : void {
		if ( ! $this->locked() ) {
			WP_CLI::warning( 'Login is not locked.' );
			return;
		}

		WP_CLI::debug( 'Deleting ' . $this->filepath );

		$result = unlink( $this->filepath );

		if ( ! $result ) {
			WP_CLI::error( 'Unable to unlock login.' );
		}

		WP_CLI::success( 'Login unlocked.' );
	}

	/**
	 * Display locked status, or locked parameters.
	 *
	 * ## OPTIONS
	 *
	 * [--all]
	 * : Display all parameters for locked login.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *  - table
	 *  - csv
	 *  - count
	 *  - json
	 *  - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 * 	# Display locked status.
	 * 	$ wp login status
	 * 	locked
	 *
	 * 	# Display locked parameters.
	 * 	$ wp login status --all
	 * 	+-----------+-------------------------------+---------+
	 * 	| key       | value                         | default |
	 * 	+-----------+-------------------------------+---------+
	 * 	| locked    | 1                             |         |
	 * 	| headline  | Login disabled                | 1       |
	 * 	| message   | Please check back later.      | 1       |
	 * 	| overrides | ["charliealphalimaechobravo"] | 1       |
	 * 	| unlock    | 0                             | 1       |
	 * 	+-----------+-------------------------------+---------+
	 *
	 * @param string[] $args
	 * @param array<string, string> $assoc_args
	 * @return void
	 */
	public function cli__login_status( array $args, array $assoc_args = array() ) : void {
		$status = 'unlocked';

		if ( $this->locked() ) {
			$status = 'locked';
		}

		if ( ! WP_CLI\Utils\get_flag_value( $assoc_args, 'all', false ) ) {
			WP_CLI::line( $status );
			return;
		}

		$format = WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' );

		if ( ! is_string( $format ) ) {
			$format = 'table';
		}

		$fields = array( 'key', 'value', 'default' );
		$items  = array(
				array(
					'key'     => 'locked',
					'value'   => $this->locked(),
					'default' => ''
				),
				array(
					'key'     => 'headline',
					'value'   => $this->headline,
					'default' => __( 'Login disabled' ) === $this->headline,
				),
				array(
					'key'     => 'message',
					'value'   => $this->message,
					'default' => __( 'Please check back later.' ) === $this->message,
				),
				array(
					'key'     => 'overrides',
					'value'   => $this->overrides,
					'default' => array( 'charliealphalimaechobravo' ) === $this->overrides,
				),
				array(
					'key'     => 'unlock',
					'value'   => $this->unlock,
					'default' => 0 === $this->unlock,
				),
		);

		WP_CLI\Utils\format_items( $format, $items, $fields );
	}

}

CSSLLC_Disable_Login::init();