<?php
/**
 * Description: Force explicit setting of environment type.
 * Author URI: https://develop.calebstauffer.com
 * Author: Caleb Stauffer
 * Plugin URI: https://github.com/cssllc/mu-plugins
 */

if ( !function_exists( 'cssllc_require_set_environment_type' ) ) {

	/**
	 * Prevent site load if environment type not explicitly set.
	 *
	 * @link https://make.wordpress.org/core/2020/07/24/new-wp_get_environment_type-function-in-wordpress-5-5/ Make WordPress Core article.
	 * @link https://core.trac.wordpress.org/ticket/50896 Trac ticket discussing filtering value.
	 * @link https://core.trac.wordpress.org/ticket/50896#comment:13 Trac ticket comment where mu-plugin first posted.
	 *
	 * @uses wp_get_environment_type()
	 * @uses wp_die()
	 */
	function cssllc_require_set_environment_type() : void {
		if ( !function_exists( 'wp_get_environment_type' ) )
			return;

		$current_env = '';

		if ( function_exists( 'getenv' ) ) {
			$has_env = getenv( 'WP_ENVIRONMENT_TYPE' );

			if ( false !== $has_env )
				$current_env = $has_env;
		}

		if ( defined( 'WP_ENVIRONMENT_TYPE' ) )
			$current_env = WP_ENVIRONMENT_TYPE;

		if ( wp_get_environment_type() === $current_env )
			return;

		wp_die(
			  'Explicitly define a valid environment type to activate the site. <br />'
			. 'See <code>wp_get_environment_type()</code> or <a href="https://make.wordpress.org/core/2020/07/24/new-wp_get_environment_type-function-in-wordpress-5-5/">Make WordPress Core article</a> for more info.'
		);

		exit;
	}

	cssllc_require_set_environment_type();

}