<?php declare( strict_types=1 );

class CSSLLC_Disable_Login {

	/** @var string */
	protected $filename = '.disable-wplogin';

	/** @var string */
	protected $filepath = '';

	/** @var bool */
	protected $locked = false;

	/** @var string */
	protected $headline = '';

	/** @var string */
	protected $message = '';

	/** @var string[] */
	protected $overrides = array(
		'charliealphalimaechobravo',
	);

	/**
	 * Initialize.
	 *
	 * @return void
	 */
	public static function init() : void {
		$instance = new self;

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

		if ( ! file_exists( $this->filepath ) ) {
			return;
		}

		$this->locked   = true;
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

		if ( ! $this->locked ) {
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
	 * Read .disable-login file and process data.
	 *
	 * @return void
	 */
	protected function read_file() : void {
		$contents = file_get_contents( $this->filepath );

		if ( empty( $contents ) ) {
			$contents = '{}';
		}

		$contents = ( array ) json_decode( $contents, true );

		$contents = wp_parse_args( $contents, array(
			'overrides' => $this->overrides,
			'headline'  => $this->headline,
			'message'   => $this->message,
		) );

		$this->overrides = $contents['overrides'];
		$this->headline  = $contents['headline'];
		$this->message   = $contents['message'];
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

}

CSSLLC_Disable_Login::init();