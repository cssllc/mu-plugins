<?php
/**
 * Plugin name: Gravity Forms Customizations
 * Author: Caleb Stauffer
 * Author URI: https://develop.calebstauffer.com
 * Plugin URI: https://gist.github.com/crstauf/605e44201b3748bbf63b3966825881e6
 */

final class CSSLLC_GravityForms {

	/**
	 * Get instance.
	 *
	 * @return self
	 */
	static function instance() : self {
		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new self;

		return $instance;
	}

	/**
	 * Construct.
	 */
	protected function __construct() {

		add_filter( 'gform_get_form_filter', array( $this, 'filter__gform_get_form_filter' ), 10, 2 );

	}

	/**
	 * Check current user has full access.
	 *
	 * @return bool
	 */
	protected function full() : bool {
		static $result = null;

		if ( is_null( $result ) )
			$result = current_user_can( 'gform_full_access' );

		return $result;
	}

	/**
	 * Check user has edit access.
	 *
	 * @uses $this->full()
	 * @return bool
	 */
	protected function edit() : bool {
		static $result = null;

		if ( is_null( $result ) )
			$result = $this->full();

		if ( empty( $result ) )
			$result = current_user_can( 'gravityforms_edit_forms' );

		return $result;
	}

	/**
	 * Check user has view access.
	 *
	 * @uses $this->full()
	 * @return bool
	 */
	protected function view() : bool {
		static $result = null;

		if ( is_null( $result ) )
			$result = $this->full();

		if ( empty( $result ) )
			$result = current_user_can( 'gravityforms_view_entries' );

		return $result;
	}

	/**
	 * Filter: gform_get_form_filter
	 *
	 * - add quick links to edit form and view submissions
	 *
	 * @uses $this->edit()
	 * @uses $this->view()
	 * @param string $html
	 * @param mixed[] $form
	 * @return string
	 */
	function filter__gform_get_form_filter( string $html, array $form ) : string {
		if (
			   !$this->edit()
			&& !$this->view()
		)
			return $html;

		$actions = array();

		if ( $this->edit() ) {
			$url = add_query_arg( array(
				'page' => 'gf_edit_forms',
				'id'   => absint( $form['id'] )
			), admin_url( 'admin.php' ) );

			$actions[] = sprintf( '<a href="%s">Edit form</a>', esc_attr( esc_url( $url ) ) );
		}

		if ( $this->view() ) {
			$url = add_query_arg( array(
				'page' => 'gf_entries',
				'id'   => absint( $form['id'] ),
			), admin_url( 'admin.php' ) );

			$actions[] = sprintf( '<a href="%s">View entries</a>', $url );
		}

		return !empty( $actions )
			? $html . '<p class="gform-form-actions">' . implode( ' | ', $actions ) . '</p>'
			: $html;
	}

}

CSSLLC_GravityForms::instance();

?>