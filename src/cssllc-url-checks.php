<?php declare( strict_types=1 );

/**
 * Used by GitHub actions to retrieve URLs to check after deployments.
 */

class CSSLLC_URL_Checks {

	const QUERY_VAR     = 'cssllc-url-checks';
	const TRANSIENT_KEY = 'cssllc-url-checks';

	/**
	 * Create or get instance.
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
	 */
	protected function __construct() {
		add_action( 'init', array( $this, 'action__init' ) );
		add_action( 'template_redirect', array( $this, 'action__template_redirect' ) );
		add_action( 'save_post_page', array( $this, 'action__save_post_page' ) );

		add_filter( 'query_vars', array( $this, 'filter__query_vars' ) );
	}

	/**
	 * Action: init
	 *
	 * Add rewrite rule.
	 *
	 * @uses add_rewrite_rule()
	 * @return void
	 */
	public function action__init() : void {
		if ( 'init' !== current_action() ) {
			return;
		}

		$rules = get_option( 'rewrite_rules', array() );
		$regex = '^cssllc/url-checks/?$';
		$query = sprintf( 'index.php?%s=1', self::QUERY_VAR );

		add_rewrite_rule( $regex, $query, 'top' );

		if ( empty( $rules ) || ! is_array( $rules ) || isset( $rules[ $regex ] ) ) {
			return;
		}

		flush_rewrite_rules();
	}

	/**
	 * Action: template_redirect
	 *
	 * Print URLs when requested.
	 *
	 * @uses $this->get_urls()
	 * @return void
	 */
	public function action__template_redirect() : void {
		if ( 'template_redirect' !== current_action() ) {
			return;
		}

		if ( false === get_query_var( self::QUERY_VAR, false ) ) {
			return;
		}

		header( 'Content-Type: text/plain' );
		header( 'X-Robots-Tag: noindex, nofollow' );

		echo implode( PHP_EOL, $this->get_urls() );
		exit;
	}

	/**
	 * Action: save_post_page
	 *
	 * Delete cache transient on page save.
	 *
	 * @uses delete_transient()
	 * @return void
	 */
	public function action__save_post_page() : void {
		if ( 'save_post_page' !== current_action() ) {
			return;
		}

		delete_transient( self::TRANSIENT_KEY );
	}

	/**
	 * Filter: query_vars
	 *
	 * Add custom query var.
	 *
	 * @param string[] $vars
	 * @return string[]
	 */
	public function filter__query_vars( array $vars ) : array {
		if ( 'query_vars' !== current_filter() ) {
			return $vars;
		}

		$vars[] = self::QUERY_VAR;

		return $vars;
	}

	/**
	 * Get URLs to check.
	 *
	 * @uses get_transient()
	 * @uses $this->get_page_template_urls()
	 * @uses set_transient()
	 * @return string[]
	 */
	protected function get_urls() : array {
		$urls = get_transient( self::TRANSIENT_KEY );

		if ( ! empty( $urls ) && is_array( $urls ) ) {
			return $urls;
		}

		// Add more URLs here.
		$urls = array(
			add_query_arg( 'quicklist', 1, get_site_url() ),
		);

		$urls = array_merge( $urls, $this->get_page_template_urls() );
		$urls = array_unique( $urls );

		set_transient( self::TRANSIENT_KEY, $urls );

		return $urls;
	}

	/**
	 * Get URLs of page templates.
	 *
	 * @uses wp_get_theme()
	 * @uses WP_Theme->get_page_templates()
	 * @uses get_permalink()
	 * @return string[]
	 */
	protected function get_page_template_urls() : array {
		global $wpdb;

		$urls = array();

		$templates = wp_get_theme()->get_page_templates();
		$templates = array_keys( $templates );

		$meta_value = implode( ', ', array_fill( 0, count( $templates ), '%s' ) );
		$format     = "SELECT `post_id`, `meta_value` FROM $wpdb->postmeta WHERE `meta_key` = %s AND `meta_value` IN ( $meta_value ) ORDER BY `post_id` DESC";
		$args       = array_merge( array( '_wp_page_template' ), $templates );
		$query      = $wpdb->prepare( $format, ...$args );
		$results    = $wpdb->get_results( $query );

		foreach ( $results as $row ) {
			$key = $row->meta_value;

			if ( isset( $urls[ $key ] ) ) {
				continue;
			}

			if ( 'publish' !== get_post_status( $row->post_id ) ) {
				continue;
			}

			$urls[ $key ] = get_permalink( $row->post_id );
		}

		$urls = array_values( $urls );

		return $urls;
	}

}

CSSLLC_URL_Checks::instance();