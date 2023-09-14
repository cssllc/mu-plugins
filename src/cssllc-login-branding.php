<?php

class CSSLLC_LoginBranding {

	/**
	 * Create or get instance.
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
	 */
	protected function __construct() {

		add_action( 'login_head', array( $this, 'action__login_head' ) );
		add_filter( 'login_headerurl', array( $this, 'filter__login_headerurl' ) );
		add_filter( 'login_headertext', array( $this, 'filter__login_headertext' ) );

	}

	/**
	 * Action: login_head
	 *
	 * - print styles
	 *
	 * @uses $this->print_styles()
	 * @return void
	 */
	function action__login_head() {
		$this->print_styles();
	}

	/**
	 * Filter: login_headerurl
	 *
	 * - change header link
	 *
	 * @param string $url
	 * @return string
	 */
	function filter__login_headerurl( $url ) {
		return site_url();
	}

	/**
	 * Filter: login_headertext
	 *
	 * @param string $text
	 * @return string
	 */
	function filter__login_headertext( $text ) {
		return $text;
	}

	/**
	 * Print login styles.
	 *
	 * @link https://crstauf.github.io/WordPress-Login-Styles-Generator/ Generator.
	 * @return void
	 */
	protected function print_styles() {
		?>

		<style>
		.login h1 a {}
		</style>

		<?php
	}

}

CSSLLC_LoginBranding::instance();

?>