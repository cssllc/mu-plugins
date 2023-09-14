<?php

define( 'WPINC', dirname( __DIR__ ) . 'wp-includes' );

if ( ! function_exists( 'is_production' ) ) {

	/**
	 * Check if production environment.
	 *
	 * @return bool
	 */
	function is_production() : bool {
		return 'production' === wp_get_environment_type();
	}

}