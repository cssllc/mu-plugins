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
	 * @var string
	 */
	protected $directory;

	/**
	 * Initialize.
	 *
	 * @return void
	 */
	public static function init() {
		static::instance();
	}

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

		add_action( 'acf/input/admin_head', array( $this, 'action__acf_input_admin_head' ) );

		add_filter( 'acf/settings/enable_post_types', '__return_false' );
		add_filter( 'acf/settings/save_json', array( $this, 'filter__acf_settings_save_json' ) );
		add_filter( 'acf/settings/load_json', array( $this, 'filter__acf_settings_load_json' ) );

	}

	/**
	 * Maybe create directory for ACF JSON.
	 *
	 * @return void
	 */
	protected function maybe_create_directory() {
		if (
			! file_exists( $this->directory )
			||   ! is_dir( $this->directory )
		) {
			mkdir( $this->directory );
		}

		$filepath = $this->directory . '/index.php';

		if ( file_exists( $filepath ) ) {
			return;
		}

		file_put_contents( $filepath, "<?php\n/**\n * Silence is golden.\n *\n * Directory contains ACF JSON export files.\n */" );
	}

	/**
	 * Action: acf/input/admin_head
	 *
	 * Add class to hide label.
	 *
	 * @return void
	 */
	function action__acf_input_admin_head() : void {
		echo '<style>.hide-label .acf-label { display: none; }</style>';
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
	 * @param string[] $paths
	 * @return string[]
	 */
	function filter__acf_settings_load_json( $paths ) {
		$pattern = trailingslashit( get_template_directory() ) . 'template-*/';
		$paths   = glob( $pattern, GLOB_ONLYDIR );
		$paths[] = $this->directory;

		return $paths;
	}

}

add_action( 'init', array( 'CSSLLC_ACF', 'init' ), 0 );

?>