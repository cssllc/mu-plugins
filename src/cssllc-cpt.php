<?php
/**
 * Abstract for CPT registrars.
 *
 * Description: Abstract class for CPT registrars from CSSLLC.
 * Author URI: https://develop.calebstauffer.com
 * Author: Caleb Stauffer
 * Plugin URI: https://github.com/cssllc/mu-plugins
 */

defined( 'ABSPATH' ) || die();

/**
 * Abstract class for custom post types.
 */
abstract class CSSLLC_CPT {

	/**
	 * @var string Custom post type slug.
	 */
	const TYPE = '';

	/**
	 *@var string Singular label.
	 */
	const SINGULAR = '';

	/**
	 * @var string Plural label.
	 */
	const PLURAL = '';

	/**
	 * @var string CSS font code for Dashicon (ex: '\\f120').
	 * @link https://developer.wordpress.org/resource/dashicons/#wordpress Collection.
	 */
	const DASHICON_CODE = '';

	/**
	 * @var mixed[] Arguments for custom post type.
	 */
	protected $args = array();

	/**
	 * @var mixed[] Default arguments for custom post type.
	 */
	protected static $default_args = array(
		'public'       => true,
		'has_archive'  => true,
		'hierarchical' => false,

		'rewrite' => array(
			'with_front' => false,
		),

	);


	/*
	 ######  ########    ###    ######## ####  ######
	##    ##    ##      ## ##      ##     ##  ##    ##
	##          ##     ##   ##     ##     ##  ##
	 ######     ##    ##     ##    ##     ##  ##
	      ##    ##    #########    ##     ##  ##
	##    ##    ##    ##     ##    ##     ##  ##    ##
	 ######     ##    ##     ##    ##    ####  ######
	*/

	/**
	 * Initialize.
	 *
	 * @uses static::instance()
	 * @return void
	 */
	static function init() : void {
		static $init = array();

		if ( in_array( static::class, $init ) )
			return;

		static::instance();

		$init[] = static::class;
	}

	/**
	 * Create or get instance.
	 *
	 * @return CSSLLC_CPT
	 */
	static function instance() : CSSLLC_CPT {
		static $instances = array();

		if ( ! array_key_exists( static::class, $instances ) )
			$instances[ static::class ] = new static;

		return $instances[ static::class ];
	}

	/**
	 * Register the post type.
	 *
	 * @uses static::instance()
	 * @uses register_post_type()
	 * @link https://developer.wordpress.org/reference/functions/register_post_type/#parameter-detail-information Parameters documentation.
	 * @return void
	 */
	protected static function register() : void {
		if (
			   empty( static::TYPE     )
			|| empty( static::SINGULAR )
			|| empty( static::PLURAL   )
		) {
			trigger_error( sprintf( 'Custom post type `<code>%s</code>` is not setup.', static::TYPE ), E_USER_WARNING );
			return;
		}

		$args = wp_parse_args( static::instance()->args, static::$default_args );
		register_post_type( static::TYPE, $args );
	}

	/**
	 * Generate labels for post type.
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_post_type_labels/ List of labels.
	 * @return array<string, string>
	 */
	protected static function default_labels() : array {
		return array(
			'name'                  => static::PLURAL,
			'singular_name'         => static::SINGULAR,
			'add_new'               => 'Add ' . static::SINGULAR,
			'add_new_item'          => 'Add New ' . static::SINGULAR,
			'edit_item'             => 'Edit ' . static::SINGULAR,
			'new_item'              => 'New ' . static::SINGULAR,
			'view_item'             => 'View ' . static::SINGULAR,
			'search_items'          => 'Search ' . static::PLURAL,
			'not_found'             => 'No ' . strtolower( static::PLURAL ) . ' found',
			'not_found_in_trash'    => 'No ' . strtolower( static::PLURAL ) . ' found in Trash',
			'parent_item_colon'     => 'Parent ' . static::PLURAL . ':',
			'all_items'             => 'All ' . static::PLURAL,
			'archives'              => static::SINGULAR . ' Archives',
			'attributes'            => static::SINGULAR . ' Attributes',
			'insert_into_item'      => 'Insert into ' . strtolower( static::SINGULAR ),
			'uploaded_to_this_item' => 'Uploaded to this ' . strtolower( static::SINGULAR ),
			// 'featured_image'        => 'Featured Image',
			// 'set_featured_image'    => 'Set Featured Image',
			// 'remove_featured_image' => 'Remove featured image',
			// 'use_featured_image'    => 'Use as featured image',
			'menu_name'             => static::PLURAL,
			// 'filter_items_list'     => static::PLURAL,
			// 'items_list_navigation' => static::PLURAL,
			'items_list'            => static::PLURAL,
			'name_admin_bar'        => static::SINGULAR,
		);
	}

	/**
	 * Get WordPress post type object.
	 *
	 * @uses get_post_type_object()
	 * @return WP_Post_Type
	 */
	static function object() : WP_Post_Type {
		$object = get_post_type_object( static::TYPE );

		if ( is_null( $object ) )
			return new WP_Post_Type( 'does_not_exist' );

		return $object;
	}


	/*
	#### ##    ##  ######  ########    ###    ##    ##  ######  ########
	 ##  ###   ## ##    ##    ##      ## ##   ###   ## ##    ## ##
	 ##  ####  ## ##          ##     ##   ##  ####  ## ##       ##
	 ##  ## ## ##  ######     ##    ##     ## ## ## ## ##       ######
	 ##  ##  ####       ##    ##    ######### ##  #### ##       ##
	 ##  ##   ### ##    ##    ##    ##     ## ##   ### ##    ## ##
	#### ##    ##  ######     ##    ##     ## ##    ##  ######  ########
	*/

	/**
	 * Construct.
	 *
	 * @uses $this->setup()
	 */
	protected function __construct() {
		$this->setup();

		add_action( 'init',       array( $this, 'action__init'       ) );
		add_action( 'admin_init', array( $this, 'action__admin_init' ) );

		add_filter( 'dashboard_glance_items',     array( $this, 'filter__dashboard_glance_items' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'filter__bulk_post_updated_messages' ), 10, 2 );
		add_filter( 'post_updated_messages',      array( $this, 'filter__post_updated_messages'  ) );

	}

	/**
	 * Setup properties.
	 *
	 * @uses static::default_labels()
	 * @return void
	 */
	protected function setup() : void {
		if ( !array_key_exists( 'labels',  $this->args ) ) $this->args['labels']  = array();
		if ( !array_key_exists( 'rewrite', $this->args ) ) $this->args['rewrite'] = array();

		if ( is_array( $this->args['labels'] ) )
			$this->args['labels'] = wp_parse_args( $this->args['labels'], static::default_labels() );

		if ( is_array( $this->args['rewrite'] ) )
			$this->args['rewrite'] = wp_parse_args( $this->args['rewrite'], static::$default_args['rewrite'] );
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
	 * Action: init
	 *
	 * Register the post type.
	 *
	 * @uses static::register()
	 * @return void
	 */
	function action__init() : void {
		if ( 'init' !== current_action() )
			return;

		static::register();
	}

	/**
	 * Action: admin_init
	 *
	 * Add CSS class for dashicon.
	 *
	 * @uses wp_add_inline_style()
	 * @return void
	 */
	function action__admin_init() : void {
		if ( 'admin_init' !== current_action() )
			return;

		wp_add_inline_style( 'dashicons', '.icon-cpt-' . esc_attr( static::TYPE ) . ':before { content: "' . static::DASHICON_CODE . '" !important; }' );
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
	 * Add count of CPT to 'At a Glance' dashboard widget.
	 *
	 * @uses static::object()
	 * @uses wp_count_posts()
	 * @param string[] $items
	 * @return string[]
	 */
	function filter__dashboard_glance_items( array $items ) : array {
		if ( 'dashboard_glance_items' !== current_filter() )
			return $items;

		if ( !current_user_can( static::object()->cap->edit_posts ) )
			return $items;

		$count = wp_count_posts( static::TYPE );

		if ( empty( $count->publish ) )
			$count->publish = 0;

		$url = add_query_arg( 'post_type', static::TYPE, 'edit.php' );

		$item  = '<a class="icon-cpt-' . esc_attr( static::TYPE ) . '" href="' . esc_attr( esc_url( $url ) ) . '">';
		$item .= $count->publish . ' ' . _n( static::SINGULAR, static::PLURAL, $count->publish );
		$item .= '</a>';

		$items[] = $item;

		return $items;
	}

	/**
	 * Filter: bulk_post_updated_messages
	 *
	 * Add messages for bulk updating CPT posts.
	 *
	 * @param mixed[] $bulk_messages
	 * @param mixed[] $bulk_counts
	 * @return mixed[]
	 */
	function filter__bulk_post_updated_messages( array $bulk_messages, array $bulk_counts ) : array {
		if ( 'bulk_post_updated_messages' !== current_filter() )
			return $bulk_messages;

		$singular = strtolower( static::SINGULAR );
		$plural   = strtolower( static::PLURAL   );

		$bulk_messages[static::TYPE] = array(
			'updated'   => _n( '%s ' . $singular . ' updated.',                 '%s ' . $plural . ' updated.',                 $bulk_counts['updated']   ),
			'deleted'   => _n( '%s ' . $singular . ' permanently deleted.',     '%s ' . $plural . ' permanently deleted.',     $bulk_counts['deleted']   ),
			'trashed'   => _n( '%s ' . $singular . ' moved to the Trash.',      '%s ' . $plural . ' moved to the Trash.',      $bulk_counts['trashed']   ),
			'untrashed' => _n( '%s ' . $singular . ' restored from the Trash.', '%s ' . $plural . ' restored from the Trash.', $bulk_counts['untrashed'] ),
		);

		$bulk_messages[static::TYPE]['locked'] = _n(
			'%s ' . $singular . ' not updated, somebody is editing it.',
			'%s ' . $plural   . ' not updated, somebody is editing them.',
			$bulk_counts['locked']
		);

		return $bulk_messages;
	}

	/**
	 * Filter: post_updated_messages
	 *
	 * Add messages for updating CPT post.
	 *
	 * @param mixed[] $notices
	 * @return mixed[]
	 */
	function filter__post_updated_messages( array $notices ) : array {
		if ( 'post_updated_messages' !== current_filter() )
			return $notices;

		global $post;

		if ( get_post_type( $post ) !== static::TYPE )
			return $notices;

		$preview_link_html   = '';
		$scheduled_link_html = '';
		$view_link_html      = '';

		if ( is_post_type_viewable( static::object() ) ) {
			$permalink   = get_permalink( $post );
			$preview_url = get_preview_post_link( $post );
		}

		if ( ! empty( $preview_url ) ) {
			$preview_link_html = sprintf(
				' <a href="%1$s">%2$s</a>',
				esc_url( $preview_url ),
				__( 'Preview ' . strtolower( static::SINGULAR ) )
			);
		}

		if ( ! empty( $permalink ) ) {
			$scheduled_link_html = sprintf(
				' <a href="%1$s">%2$s</a>',
				esc_url( $permalink ),
				__( 'Preview ' . strtolower( static::SINGULAR ) )
			);

			$view_link_html = sprintf(
				' <a href="%1$s">%2$s</a>',
				esc_url( $permalink ),
				__( 'View ' . strtolower( static::SINGULAR ) )
			);
		}

		$scheduled_date = sprintf(
			__( '%1$s at %2$s' ),
			date_i18n( _x( 'M j, Y', 'publish box date format' ), strtotime( $post->post_date ) ),
			date_i18n( _x( 'H:i',    'publish box time format' ), strtotime( $post->post_date ) )
		);

		$notices[static::TYPE] = array(
			 0 => '', // Unused. Messages start at index 1.
			 1 => __( static::SINGULAR . ' updated.' ) . $view_link_html,
			 2 => __( 'Custom field updated.' ),
			 3 => __( 'Custom field deleted.' ),
			 4 => __( static::SINGULAR . ' updated.' ),
			 5 => isset( $_GET['revision'] ) ? sprintf( __( static::SINGULAR . ' restored to revision from %s.' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			 6 => __( static::SINGULAR . ' published.' ) . $view_link_html,
			 7 => __( static::SINGULAR . ' saved.' ),
			 8 => __( static::SINGULAR . ' submitted.' ) . $preview_link_html,
			 9 => sprintf( __( static::SINGULAR . ' scheduled for: %s.' ), '<strong>' . $scheduled_date . '</strong>' ) . $scheduled_link_html,
			10 => __( static::SINGULAR . ' draft updated.' ) . $preview_link_html,
		);

		return $notices;
	}

}
