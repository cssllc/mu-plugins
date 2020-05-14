<?php
/**
 * Plugin name: CSSLLC Subresource Integrity
 * Plugin URI: https://gist.github.com/crstauf/9a2f412e48c6630e6de945bd1d0e9e53
 * Description: WordPress drop-in for adding attribute to scripts and stylesheets for subresource integrity implementation.
 * Author URI: https://develop.calebstauffer.com
 * Author: Caleb Stauffer
 * Version: 1.0
 *
 * @link https://www.smashingmagazine.com/2019/04/understanding-subresource-integrity/ Explanation.
 * @link https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity/ MDN documentation.
 */

/**
 * Class: CSSLLC_SubresourceIntegrity
 */
class CSSLLC_SubresourceIntegrity {

	/**
	 * @var string Key for dependency extra data.
	 */
	const KEY = 'integrity_hashes';

	/**
	 * @var string Attribute name for dependency tag.
	 */
	const ATTRIBUTE = 'integrity';

	/**
	 * Create and get instance.
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
	 * Get hash from dependency data.
	 *
	 * @param string $handle
	 * @param bool $is_script Is dependency a script.
	 * @uses WP_Scripts->get_data()
	 * @uses  WP_Styles->get_data()
	 * @return string|false
	 */
	static function get_hash( string $handle, bool $is_script = true ) {
		return $is_script
			? wp_scripts()->get_data( $handle, static::KEY )
			:  wp_styles()->get_data( $handle, static::KEY );
	}

	/**
	 * Set hash to dependency data.
	 *
	 * @param string $handle
	 * @param string $hash
	 * @param bool $is_script Is dependency a script.
	 * @uses static::instance()
	 * @uses static::get_hash()
	 * @uses wp_script_add_data()
	 * @uses  wp_style_add_data()
	 */
	static function set_hash( string $handle, string $hash, bool $is_script = true ) {

		# Create the instance (if it doesn't exist).
		static::instance();

		# Check if dependency already has a hash, and alert.
		if ( !empty( static::get_hash( $handle, $is_script ) ) )
			trigger_error( sprintf( 'Dependency <code>%s</code> already has an SRI hash.', $handle ) );

		# Add hash to dependency data.
		$is_script
			? wp_script_add_data( $handle, static::KEY, $hash )
			:  wp_style_add_data( $handle, static::KEY, $hash );
	}

	/**
	 * Construct.
	 */
	protected function __construct() {

		add_filter( 'script_loader_tag', array( $this, 'filter__script_loader_tag' ), 10, 2 );
		add_filter(  'style_loader_tag', array( $this,  'filter__style_loader_tag' ), 10, 2 );

	}

	/**
	 * Filter: script_loader_tag
	 *
	 * - maybe add attribute to script tags
	 *
	 * @param string $tag
	 * @param string $handle
	 * @uses $this->maybe_add_attribute()
	 * @return string
	 */
	function filter__script_loader_tag( string $tag, string $handle ) {
		return $this->maybe_add_attribute( $tag, $handle );
	}

	/**
	 * Filter: style_loader_tag
	 *
	 * - maybe add attribute to style tags
	 *
	 * @param string $tag
	 * @param string $handle
	 * @uses $this->maybe_add_attribute()
	 * @return string
	 */
	function filter__style_loader_tag( string $tag, string $handle ) {
		return $this->maybe_add_attribute( $tag, $handle, false );
	}

	/**
	 * Maybe add attribute to tag.
	 *
	 * @param string $tag
	 * @param string $handle
	 * @param bool $is_script Is dependency a script.
	 * @uses static::get_hash()
	 * @return string
	 */
	protected function maybe_add_attribute( string $tag, string $handle, bool $is_script = true ) {

		# Provide switch for easy third-party control.
		$add = defined( 'WP_DEVELOP' ) ? !WP_DEVELOP : true;
		if ( !apply_filters( 'add_subresource_integrity', $add, $handle, $is_script ) )
			return $tag;

		# Check if tag already has the attribute.
		if ( false !== strpos( $tag, ' ' . static::ATTRIBUTE . '=' ) ) {
			trigger_error( sprintf( 'Dependency <code>%s</code> already has an <code>%s</code> attribute.', $handle, static::ATTRIBUTE ) );
			return $tag;
		}

		# Get the hash for the dependency.
		$hash = static::get_hash( $handle, $is_script );

		# If no hash set, abort.
		if ( empty( $hash ) )
			return $tag;

		# Create the attribute HTML.
		# WordPress uses single quotes, so use single quotes instead of doubles.
		$attribute = ' ' . static::ATTRIBUTE . '=\'' . esc_attr( $hash ) . '\' crossorigin=\'anonymous\'';

		# Create search and replace strings for stylesheet.
		$search = ' />';
		$replace = $attribute . ' />';

		# Create search and replace strings for script.
		if ( $is_script ) {
			$search = '></script>';
			$replace = $attribute . '></script>';
		}

		return str_replace( $search, $replace, $tag );
	}

}

if ( !function_exists( 'wp_set_script_sri' ) ) {

	/**
	 * Global helper for setting SRI for script.
	 *
	 * @param string $handle
	 * @param string $hash
	 * @uses CSSLLC_SubresourceIntegrity::set_hash()
	 */
	function wp_set_script_sri( string $handle, string $hash ) {
		CSSLLC_SubresourceIntegrity::set_hash( $handle, $hash );
	}

}

if ( !function_exists( 'wp_set_style_sri' ) ) {

	/**
	 * Global helper for setting SRI for stylesheet.
	 *
	 * @param string $handle
	 * @param string $hash
	 * @uses CSSLLC_SubresourceIntegrity::set_hash()
	 */
	function wp_set_style_sri( string $handle, string $hash ) {
		CSSLLC_SubresourceIntegrity::set_hash( $handle, $hash, false );
	}

}


?>