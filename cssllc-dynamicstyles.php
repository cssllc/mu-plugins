<?php
/**
 * Plugin name: Dynamic Styles
 * Plugin URI: https://github.com/cssllc/mu-plugins
 * Author: Caleb Stauffer
 * Author URI: https://develop.calebstauffer.com
 * Version: 2.0
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: DynamicStyles
 *
 * @todo test
 * @todo add support for "nonce" attribute
 */
class DynamicStyles {

	/**
	 * @var string[] $todo Styles to print.
	 * @var string[] $done Styles printed.
	 */
	protected static $todo = array();
	protected static $done = array();

	/**
	 * Get instance.
	 *
	 * @return self
	 */
	static function instance() {
		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new self;

		return $instance;
	}

	/**
	 * Add style.
	 *
	 * @param string $style
	 * @param null|string $key
	 * @return string
	 */
	static function add( string $style, string $key = null ) {
		if ( is_null( $key ) ) {
			$key = microtime( true );

			while ( isset( static::$todo[ ( string ) $key ] ) )
				$key += 0.00001;
		}

		$key = ( string ) $key;

		static::$todo[$key] = $style;
		return $key;
	}

	/**
	 * Print todo styles.
	 */
	static function print() {
		if ( empty( static::$todo ) )
			return;

		do_action( 'dynamic-styles/before_printing', static::$todo );
		do_action( 'qm/start', ( $timer = __METHOD__ . '()' ) );

		echo '<style data-dynamicstyles="' . esc_attr( count( static::$todo ) ) . '">' . "\n" .
			implode( "\n", static::$todo ) . "\n".
		'</style>' . "\n";

		do_action( 'qm/stop', $timer );
		do_action( 'dynamic-styles/after_printing', static::$todo );

		static::$done = array_merge( static::$done, static::$todo );
		static::$todo = array();
	}

	/**
	 * Construct.
	 */
	protected function __construct() {
		add_action( 'wp_print_scripts',        array( __CLASS__, 'print' ) );
		add_action( 'wp_print_footer_scripts', array( __CLASS__, 'print' ) );
	}

}

# Initialize.
DynamicStyles::instance();

?>