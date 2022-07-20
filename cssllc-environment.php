<?php
/**
 * Description: Environment identification and adjustments.
 * Author URI: https://develop.calebstauffer.com
 * Author: Caleb Stauffer
 * Plugin URI: https://github.com/cssllc/mu-plugins
 */

# Set development email address.
$dev_email_address = '';

if ( !function_exists( 'cssllc_require_set_environment_type' ) ) {

	/**
	 * Prevent site load if environment type not explicitly set.
	 *
	 * @link https://make.wordpress.org/core/2020/07/24/new-wp_get_environment_type-function-in-wordpress-5-5/ Make WordPress Core article.
	 * @link https://core.trac.wordpress.org/ticket/50896 Trac ticket discussing filtering value.
	 * @link https://core.trac.wordpress.org/ticket/50896#comment:13 Trac ticket comment where mu-plugin first posted.
	 *
	 * @uses wp_installing()
	 * @uses wp_get_environment_type()
	 * @uses wp_die()
	 */
	function cssllc_require_set_environment_type() : void {
		if ( !function_exists( 'wp_get_environment_type' ) )
			return;

		if ( wp_installing() )
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

}

if ( !function_exists( 'is_production' ) ) {

	/**
	 * Check if production environment.
	 *
	 * @return bool
	 */
	function is_production() : bool {
		return 'production' === wp_get_environment_type();
	}

}

cssllc_require_set_environment_type();


/*
   ###    ########        ## ##     ##  ######  ######## ##     ## ######## ##    ## ########  ######
  ## ##   ##     ##       ## ##     ## ##    ##    ##    ###   ### ##       ###   ##    ##    ##    ##
 ##   ##  ##     ##       ## ##     ## ##          ##    #### #### ##       ####  ##    ##    ##
##     ## ##     ##       ## ##     ##  ######     ##    ## ### ## ######   ## ## ##    ##     ######
######### ##     ## ##    ## ##     ##       ##    ##    ##     ## ##       ##  ####    ##          ##
##     ## ##     ## ##    ## ##     ## ##    ##    ##    ##     ## ##       ##   ###    ##    ##    ##
##     ## ########   ######   #######   ######     ##    ##     ## ######## ##    ##    ##     ######
*/

if ( empty( $dev_email_address ) )
	trigger_error( 'Update <code>$dev_email_address</code> in ' . __FILE__, E_USER_WARNING );

/**
 * Check if production environment.
 */
if ( is_production() ) {

	/**
	 * Add inactive status indication to mu-plugin row meta.
	 */
	add_filter( 'plugin_row_meta', function ( array $meta, string $plugin_file ) : array {
		if ( basename( __FILE__ ) !== $plugin_file )
			return $meta;

		array_unshift( $meta, '<strong>Inactive</strong>' );
		return $meta;
	}, 10, 2 );

	return;

}

/**
 * Action: wp_dashboard_setup
 *
 * Add Dashboard widget to list adjustments.
 *
 * @uses wp_add_dashboard_widget()
 * @return void
 */
add_action( 'wp_dashboard_setup', function() : void {
	wp_add_dashboard_widget(
		'cssllc-development-environment',
		'Development Environment Adjustments',
		function() {
			echo '<ul style="list-style-type: disc; margin-left: 20px">'

				# WC_SQUARE_ENABLE_STAGING constant
				. '<li>Set constant to disable Square payments</li>'
				
				# `pre_option_blog_public` filters
				. '<li>Prevent search engine indexing</li>'

				# `pre_option_admin_email` and `pre_site_option_admin_email` filters
				. '<li>Override WordPress admin email address</li>'

				# `wp_mail` filter
				. '<li>Override <code>wp_mail()</code> args to use development email address</li>'

				# `AW_PREVENT_WORKFLOWS` constant, `automatewoo_custom_validate_workflow` filter
				. '<li>Prevent running AutomateWoo workflows</li>'

				# `woocommerce_subscriptions_is_duplicate_site` filter
				. '<li>Identify site as duplicate for WooCommerce Subscriptions</li>'

				# `user_has_cap` filter
				. '<li>Always show Query Monitor output on frontend</li>'
				
				# 'action_scheduler_run_queue' action
				. '<li>Disabled Action Scheduler default runner</li>'

			. '</ul>';
		},
		null,
		null,
		'normal',
		'high'
	);
} );

/**
 * Action: admin_notices
 *
 * Add a notice in admin so we're aware of the preventions.
 *
 * @return void
 */
add_action( 'admin_notices', function() : void {
	echo '<div class="notice notice-warning">' .
		'<p>Functionality adjustments due to development environment; see <abbr title="' . esc_attr( __FILE__ ) . '">' . basename( __FILE__ ) . '</abbr> mu-plugin.</p>' .
	'</div>';
}, -999 );

/**
 * Filter: plugin_row_meta
 *
 * Add active status indication to mu-plugin row meta.
 *
 * @param array $meta
 * @param string $plugin_file
 * @return array
 */
add_filter( 'plugin_row_meta', function ( array $meta, string $plugin_file ) : array {
	if ( basename( __FILE__ ) !== $plugin_file )
		return $meta;

	array_unshift( $meta, '<strong style="color: red;">!!! ACTIVE !!!</strong>' );
	return $meta;
}, 10, 2 );

/**
 * Set staging constant for Square payment gateway.
 */
define( 'WC_SQUARE_ENABLE_STAGING', true );

/**
 * Filters: pre_option_admin_email, pre_site_option_admin_email
 *
 * Override WordPress admin email.
 *
 * @param string $email_address
 * @return string
 */
add_filter(      'pre_option_admin_email', function ( string $email_address ) use ( $dev_email_address ) : string { return $dev_email_address; } );
add_filter( 'pre_site_option_admin_email', function ( string $email_address ) use ( $dev_email_address ) : string { return $dev_email_address; } );

/**
 * Filter: wp_mail
 *
 * Direct all emails to development (prevent emails to customers).
 *
 * @param array $args
 * @return array
 */
add_filter( 'wp_mail', function ( array $args ) use ( $dev_email_address ) : array { $args['to'] = $dev_email_address; return $args; }, 999 );

/**
 * Prevent running of AutomateWoo workflows.
 *
 * @link https://automatewoo.com/ Plugin site.
 * @see AutomateWoo\Workflow::run()
 * @since AutomateWoo 2.6.6
 */
define( 'AW_PREVENT_WORKFLOWS', true );

/**
 * Filter: woocommerce_subscriptions_is_duplicate_site
 *
 * Prevent WooCommerce Subscription renewals.
 *
 * @link https://woocommerce.com/products/woocommerce-subscriptions/ Plugin site.
 * @see WC_Subscriptions::is_duplicate_site()
 *
 * @param bool
 * @uses __return_true()
 * @return true
 */
add_filter( 'woocommerce_subscriptions_is_duplicate_site', '__return_true' );

/**
 * Filter: automatewoo_custom_validate_workflow
 *
 * Prevent processing of AutomateWoo workflows.
 *
 * @link https://automatewoo.com/ Plugin site.
 * @see AutomateWoo\Workflow::validate_workflow()
 *
 * @param bool
 * @uses __return_false()
 * @return false
 */
add_filter( 'automatewoo_custom_validate_workflow', '__return_false' );

/**
 * Filter: user_has_cap
 *
 * Enable Query Monitor output.
 *
 * @param array $allcaps
 * @return array
 */
add_filter( 'user_has_cap', function( array $allcaps ) : array {
	if (
		defined( 'QM_DISABLED' )
		&& QM_DISABLED
	)
		return $allcaps;

	$allcaps['view_query_monitor'] = true;
	return $allcaps;
} );

/**
 * Action: send_headers, wp_head
 *
 * - change 'init' version to timestamp
 *
 * Safari can be really sticky with cache, so always change the
 * version to ensure latest asset.
 *
 * @return void
 */
$change_asset_versions = function() : void {
	foreach ( wp_scripts()->registered as $handle => &$script )
		if ( 'init' === $script->ver )
			$script->ver = WP_START_TIMESTAMP;

	foreach ( wp_styles()->registered as $handle => &$style )
		if ( 'init' === $style->ver )
			$style->ver = WP_START_TIMESTAMP;
};

add_action( 'send_headers', $change_asset_versions, 5 );
add_action( 'wp_head',      $change_asset_versions, 5 );

/**
 * Action: init
 *
 * Disable Action Scheduler default runner.
 *
 * @uses ActionScheduler::runner()
 *
 * @return void
 */
add_action( 'init', static function() : void {
	if ( ! class_exists( 'ActionScheduler' ) ) {
		return;
	}
	
	remove_action( 'action_scheduler_run_queue', array( ActionScheduler::runner(), 'run' ) );
} );

/**
 * Action: plugins_loaded
 *
 * Disable BackWPup scheduled backups.
 *
 * @return void
 */
add_action( 'plugins_loaded', static function() : void {
	if ( ! defined( 'DOING_CRON' ) || ! DOING_CRON ) {
		return;
	}

	remove_action( 'backwpup_cron',          array( 'BackWPup_Cron', 'run' ) );
	remove_action( 'backwpup_check_cleanup', array( 'BackWPup_Cron', 'check_cleanup' ) );
}, 12 );

/**
 * Filter: pre_option_blog_public, pre_cache_alloptions, alloptions
 *
 * Prevent search engine indexing.
 *
 * @param $value
 *
 * @return int
 */
add_filter( 'pre_option_blog_public', static function ( $value ) {
	if ( is_string(  $value ) ) { return '0'; }
	if ( is_numeric( $value ) ) { return  0;  }

	return false;
} );

add_filter( 'pre_cache_alloptions', static function ( array $alloptions ) : array {
	if ( is_string( $alloptions['blog_public'] ) ) {
		$alloptions['blog_public'] = '0';
	} else if ( is_numeric( $alloptions['blog_public'] ) ) {
		$alloptions['blog_public'] = 0;
	} else {
		$alloptions['blog_public'] = false;
	}

	return $alloptions;
} );

add_filter( 'alloptions', static function ( array $alloptions ) : array {
	if ( is_string( $alloptions['blog_public'] ) ) {
		$alloptions['blog_public'] = '0';
	} else if ( is_numeric( $alloptions['blog_public'] ) ) {
		$alloptions['blog_public'] = 0;
	} else {
		$alloptions['blog_public'] = false;
	}

	return $alloptions;
} );
