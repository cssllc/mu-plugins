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
	public static function instance() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
		}

		return $instance;
	}

	/**
	 * Construct.
	 *
	 * @uses $this->maybe_create_directory()
	 */
	protected function __construct() {
		$this->directory = trailingslashit( __DIR__ ) . 'acf-json';

		if ( defined( 'WPMU_PLUGIN_DIR' ) ) {
			$this->directory = trailingslashit( constant( 'WPMU_PLUGIN_DIR' ) ) . 'acf-json';
		}

		$this->maybe_create_directory();

		add_action( 'acf/input/admin_head', array( $this, 'action__acf_input_admin_head' ) );

		add_filter( 'acf/load_field/type=image', array( $this,'filter__acf_load_field_type_image' ) );
		add_filter( 'acf/settings/enable_post_types', '__return_false' );
		add_filter( 'acf/settings/enable_options_pages_ui', '__return_false' );
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
			|| ! is_dir( $this->directory )
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
	public function action__acf_input_admin_head() : void {
		echo '<style>.hide-label .acf-label { display: none; }</style>';
	}

	/**
	 * Filter: acf/load_field/type=image
	 *
	 * Remove requirement from image fields on development environments.
	 *
	 * @param array<string, mixed> $field
	 * @return array<string, mixed>
	 */
	public function filter__acf_load_field_type_image( array $field ) : array {
		$screen = get_current_screen();

		if ( 'acf-field-group' === $screen->id ) {
			return $field;
		}

		if ( ! in_array( wp_get_environment_type(), array( 'local', 'development' ) ) ) {
			return $field;
		}

		if ( empty( $field['required'] ) ) {
			return $field;
		}

		$field['required'] = false;
		$field['label']   .= ' <span class="acf-required" title="Required, but disabled in dev environment">*</span>';

		return $field;
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
	public function filter__acf_settings_save_json( $path = '' ) {
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
	public function filter__acf_settings_load_json( $paths ) {
		if ( ! is_array( $paths ) ) {
			$paths = array();
		}

		$paths[] = $this->directory;

		return $paths;
	}

}

add_action( 'init', array( 'CSSLLC_ACF', 'init' ), 0 );
