<?php
/**
 * Plugin name: Gravity Forms Customizations
 * Author: Caleb Stauffer
 * Author URI: https://develop.calebstauffer.com
 * Plugin URI: https://gist.github.com/crstauf/605e44201b3748bbf63b3966825881e6
 */

final class CSSLLC_WooCommerce {

	const TRANSIENT_NAME = 'cssllc_woocommerce_orders_count';

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

		add_action( 'admin_enqueue_scripts',  array( $this, 'action__admin_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts',     array( $this, 'action__wp_enqueue_scripts' ) );
		add_action( 'wp_dashboard_setup',     array( $this, 'action__wp_dashboard_setup' ) );
		add_action( 'admin_bar_menu',         array( $this, 'action__admin_bar_menu' ), 65 );

		add_filter( 'dashboard_glance_items', array( $this, 'filter__dashboard_glance_items' ) );

	}

	/**
	 * Add menu to admin bar.
	 *
	 * @param WP_Admin_Bar $bar
	 * @return void
	 */
	protected function add_menu( WP_Admin_Bar $bar ) : void {
		$menu_id = 'cssllc-woocommerce-orders';
		$count   = $this->orders_count();
		$text    = sprintf( _n( '%s order today', '%s orders today', $count ), number_format_i18n( $count ) );
		$icon    = '<span class="ab-icon"></span>';
		$url     = add_query_arg( 'post_type', 'shop_order', admin_url( 'edit.php' ) );

		$title = sprintf( '<span class="ab-label count-%1$s" aria-hidden="true">%2$s</span><span class="screen-reader-text">%3$s</span>',
			esc_attr( $count ),
			number_format_i18n( $count ),
			$text
		);

		$bar->add_menu( array(
			'id'    => $menu_id,
			'title' => $icon . $title,
			'href'  => $url,
		) );

		$current_id = '';

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();

			if (
				'shop_order' === $screen->id
				&&    'post' === $screen->base
			)
				$current_id = $_GET['post'];
		}

		$title = sprintf( '<input type="text" id="%1$s" placeholder="Order ID" value="%2$s" />',
			'cssllc-woocommerce-admin-bar-orders-search',
			esc_attr( $current_id )
		);

		$bar->add_menu( array(
			'parent' => $menu_id,
			'id'     => 'cssllc-woocommerce-orders-search',
			'title'  => $title,
		) );
	}

	/**
	 * Get orders count.
	 *
	 * @uses $this->count_orders()
	 * @return int
	 */
	protected function orders_count() : int {
		$count = absint( get_transient( self::TRANSIENT_NAME ) );

		if ( !empty( $count ) )
			return $count;

		$count = $this->count_orders();

		set_transient( self::TRANSIENT_NAME, $count, MINUTE_IN_SECONDS * 5 );

		return $count;
	}

	/**
	 * Count orders for today.
	 *
	 * @uses $wpdb->prepare()
	 * @uses $wpdb->get_var()
	 * @return int
	 */
	protected function count_orders() : int {
		global $wpdb;

		$values       = wc_get_order_types( 'order-count' );
		// $values[] = 'post';
		$placeholders = implode( ', ', array_fill( 0, count( $values ), '%s' ) );
		$start        = strtotime( 'midnight', time() + ( HOUR_IN_SECONDS * get_option( 'gmt_offset' ) ) );
		$values[]     = date( 'Y-m-d H:i:s', $start );

		$query  = $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts WHERE `post_type` IN ( $placeholders ) AND `post_date` >= %s", $values );
		$result = $wpdb->get_var( $query );

		return absint( $result );
	}

	/**
	 * Add inline styles and scripts.
	 *
	 * @uses $this->inline_script__admin_bar()
	 * @uses $this->inline_style__admin_bar()
	 * @return void
	 */
	protected function enqueue_assets() : void {
		wp_add_inline_script( 'admin-bar', $this->inline_script__admin_bar() );
		wp_add_inline_style(  'admin-bar', $this->inline_style__admin_bar()  );
	}

	/**
	 * Inline style: dashboard
	 *
	 * @return string
	 */
	function inline_style__dashboard() : string {
		ob_start();
		?>

		.icon-wc-product::before {
			font-family: 'dashicons' !important;
			content: '\f480' !important;
		}
		.icon-wc-shop_order::before {
			font-family: 'WooCommerce' !important;
			content: '\e03d' !important;
		}
		.icon-wc-shop_coupon::before {
			font-family: 'WooCommerce' !important;
			content: '\e600' !important;
		}

		<?php
		return ob_get_clean();
	}

	/**
	 * Inline script: admin-bar
	 *
	 * @return string
	 */
	protected function inline_script__admin_bar() : string {
		ob_start();
		?>

		jQuery( document ).ready( function() {

			jQuery( '#wp-admin-bar-cssllc-woocommerce-orders' ).hoverIntent( {
				over: function() {
					jQuery( this ).addClass( 'hover' );
					jQuery( '#cssllc-woocommerce-admin-bar-orders-search' ).focus();
				},
				out: function() {
					jQuery( this ).removeClass( 'hover' );
					jQuery( '#cssllc-woocommerce-admin-bar-orders-search' ).val( document.getElementById( 'cssllc-woocommerce-admin-bar-orders-search' ).getAttribute( 'value' ) );
				},
				timeout: 180,
				sensitivity: 7,
				interval: 100
			} );

			jQuery( '#cssllc-woocommerce-admin-bar-orders-search' ).on( 'keydown', function( ev ) {
				if ( 13 !== ev.keyCode )
					return true;

				var search_val = jQuery( this ).val();

				if ( parseFloat( search_val ) == search_val ) { /* then object ID */
					window.location = "<?php echo esc_js( esc_url( add_query_arg( 'action', 'edit', admin_url( 'post.php' ) ) ) ) ?>&post=" + parseFloat( search_val );
				} else {
					window.location = "<?php echo esc_js( esc_url( add_query_arg( 'post_type', 'shop_order', admin_url( 'edit.php' ) ) ) ) ?>&_billing_email=" + search_val;
				}
			} );

		} );

		<?php
		return ob_get_clean();
	}

	/**
	 * Inline style: admin-bar
	 *
	 * @return string
	 */
	protected function inline_style__admin_bar() : string {
		ob_start();
		?>

		#wp-admin-bar-cssllc-woocommerce-orders .ab-item {
			height: auto;
		}

		#wp-admin-bar-cssllc-woocommerce-orders .ab-label.count-0,
		#wp-admin-bar-cssllc-woocommerce-orders .ab-label.count-na { opacity: 0.5; }

		#wp-admin-bar-cssllc-woocommerce-orders .ab-icon::before {
			top: 2px;
			font-family: 'WooCommerce' !important;
			content: '\e03d' !important;
		}

		#wp-admin-bar-calyx-orders-default {
			padding: 0 !important;
		}

		#wp-admin-bar-cssllc-woocommerce-orders-search .ab-item {
			height: auto !important;
		}

		#cssllc-woocommerce-admin-bar-orders-search {
			padding: 0 5px;
			background-color: inherit;
			box-sizing: border-box;
			text-align: center;
			border: none;
			color: inherit;
		}

		#cssllc-woocommerce-admin-bar-orders-search::-webkit-input-placeholder {
			color: inherit;
			opacity: 0.5;
		}

		<?php
		return ob_get_clean();
	}


	/*
	   ###     ######  ######## ####  #######  ##    ##  ######
	  ## ##   ##    ##    ##     ##  ##     ## ###   ## ##    ##
	 ##   ##  ##          ##     ##  ##     ## ####  ## ##
	##     ## ##          ##     ##  ##     ## ## ## ##  ######
	######### ##          ##     ##  ##     ## ##  ####       ##
	##     ## ##    ##    ##     ##  ##     ## ##   ### ##    ##
	##     ##  ######     ##    ####  #######  ##    ##  ######
	*/

	/**
	 * Action: admin_enqueue_scripts
	 *
	 * @uses $this->enqueue_assets()
	 * @return void
	 */
	function action__admin_enqueue_scripts() : void {
		if ( 'admin_enqueue_scripts' !== current_action() )
			return;

		$this->enqueue_assets();
	}

	/**
	 * Action: wp_enqueue_scripts
	 *
	 * @uses $this->enqueue_assets()
	 * @return void
	 */
	function action__wp_enqueue_scripts() : void {
		if ( 'wp_enqueue_scripts' !== current_action() )
			return;

		$this->enqueue_assets();
	}

	/**
	 * Action: wp_dashboard_setup
	 *
	 * @uses $this->enqueue_assets()
	 * @return void
	 */
	function action__wp_dashboard_setup() : void {
		if ( 'wp_dashboard_setup' !== current_action() )
			return;

		wp_add_inline_style( 'dashboard', $this->inline_style__dashboard() );
	}

	/**
	 * Action: admin_bar_menu
	 *
	 * @param WP_Admin_Bar $bar
	 * @uses $this->add_menu()
	 * @return void
	 */
	function action__admin_bar_menu( WP_Admin_Bar $bar ) : void {
		if ( 'admin_bar_menu' !== current_action() )
			return;

		$this->add_menu( $bar );
	}

	/*
	######## #### ##       ######## ######## ########   ######
	##        ##  ##          ##    ##       ##     ## ##    ##
	##        ##  ##          ##    ##       ##     ## ##
	######    ##  ##          ##    ######   ########   ######
	##        ##  ##          ##    ##       ##   ##         ##
	##        ##  ##          ##    ##       ##    ##  ##    ##
	##       #### ########    ##    ######## ##     ##  ######
	*/

	/**
	 * Filter: dashboard_glance_items
	 *
	 * - add count of CPTs to 'At a Glance' dashboard widget.
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	function filter__dashboard_glance_items( $items ) {
		foreach ( array( 'product', 'shop_order', 'shop_coupon' ) as $post_type ) {
			$object = get_post_type_object( $post_type );
			$count  = wp_count_posts( $post_type );
			$url    = add_query_arg( 'post_type', $post_type, admin_url( 'edit.php' ) );

			$items['count_' . $post_type] = sprintf( '<a class="icon-wc-%1$s" href="%2$s">%3$s %4$s</a>',
				esc_attr( sanitize_html_class( $post_type ) ),
				esc_attr( esc_url( $url ) ),
				esc_html( number_format_i18n( $count->publish ) ),
				esc_html( _n( $object->labels->singular_name, $object->labels->name, $count->publish ) )
			);
		}

		return $items;
	}

}

add_action( 'woocommerce_loaded', array( 'CSSLLC_WooCommerce', 'instance' ) );