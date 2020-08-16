<?php
/**
 * Plugin name: Advanced Custom Fields add-on: JSON export
 * Author: Caleb Stauffer
 * Author URI: https://develop.calebstauffer.com
 * Plugin URI: https://github.com/cssllc/mu-plugins
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: CSSLLC_ACF
 */
class CSSLLC_ACF {

	/**
	 * @var null|string
	 */
	protected $directory;

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
	 * Construct.
	 *
	 * @uses $this->maybe_create_directory()
	 */
	protected function __construct() {
		$this->directory = trailingslashit( __DIR__ ) . 'acf-json';

		$this->maybe_create_directory();

		add_filter( 'acf/settings/save_json', array( $this, 'filter__acf_settings_save_json' ) );
		add_filter( 'acf/settings/load_json', array( $this, 'filter__acf_settings_load_json' ) );

	}

	/**
	 * Maybe create directory for ACF JSON.
	 */
	protected function maybe_create_directory() {
		if (
			!file_exists( $this->directory )
			||   !is_dir( $this->directory )
		)
			mkdir( $this->directory );

		$filepath = $this->directory . '/index.php';

		if ( file_exists( $filepath ) )
			return;

		file_put_contents( $filepath, "<?php\n/**\n * Silence is golden.\n *\n * Directory contains ACF JSON export files.\n */" );
	}

	/**
	 * Filter: acf/settings/save_json
	 *
	 * - specify directory to save ACF JSON to
	 *
	 * @link https://www.advancedcustomfields.com/resources/local-json/ Documentation.
	 * @param string $path
	 * @return string
	 */
	function filter__acf_settings_save_json( $path = '' ) {
		return $this->directory;
	}

	/**
	 * Filter: acf/settings/load_json
	 *
	 * - specify directories to look for ACF JSON
	 *
	 * @param array $paths
	 * @uses filter__acf_settings_save_json()
	 * @return array
	 */
	function filter__acf_settings_load_json( $paths ) {
		return array( $this->directory );
	}

}

add_action( 'acf/init', array( 'CSSLLC_ACF', 'instance' ), 0 );

?>