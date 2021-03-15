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
abstract class _CSSLLC_CPT {

	/** @var string Post type name. */
	protected $type;

	/** @var array Name and action for nonce. */
	protected $nonce;

	/** @var string Plural name of post type. */
	protected $plural;

	/** @var string Singular name of post type. */
	protected $singular;

	/**
	 * @var string CSS 'content' code of Dashicon to represent post type.
	 * @link https://developer.wordpress.org/resource/dashicons/
	 */
	protected $dashicon_code;

	/** @var array Arguments for post type. */
	protected $args = array(
		'labels'   => array(),
		'rewrite'  => array(),
		'supports' => array(),
	);

	/**
	 * Initialize.
	 *
	 * @uses static::instance()
	 * @return void
	 */
	static function init() : void {
		static $init = false;

		if ( true === $init )
			return;

		static::instance();
		$init = true;
	}

	/**
	 * Create or get instance.
	 *
	 * @return self
	 */
	static function instance() : self {
		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new static;

		return $instance;
	}

	/**
	 * Construct.
	 */
	protected function __construct() {
		$this->nonce = array(
			'action' => __FILE__ . '::' . __LINE__,
			  'name' => '_wpnonce_' . $this->type,
		);

		add_action( 'init',       array( $this, 'action__init'       ) );
		add_action( 'admin_init', array( $this, 'action__admin_init' ) );

		add_filter( 'dashboard_glance_items', array( $this, 'filter__dashboard_glance_items' ) );
		add_filter( 'post_updated_messages',  array( $this, 'filter__post_updated_messages'  ) );
	}

	/**
	 * Getter.
	 *
	 * @param string $property
	 * @return mixed
	 */
	function __get( string $property ) {
		return $this->$property;
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
	 * @uses register_post_type()
	 * @link https://developer.wordpress.org/reference/functions/register_post_type/#parameter-detail-information Parameters documentation.
	 * @return void
	 */
	function action__init() : void {

		$label_defaults = array(
			'name'                  => $this->plural,
			'singular_name'         => $this->singular,
			'add_new'               => 'Add ' . $this->singular,
			'add_new_item'          => 'Add New ' . $this->singular,
			'edit_item'             => 'Edit ' . $this->singular,
			'new_item'              => 'New ' . $this->singular,
			'view_item'             => 'View ' . $this->singular,
			'search_items'          => 'Search ' . $this->plural,
			'not_found'             => 'No ' . strtolower( $this->plural ) . ' found',
			'not_found_in_trash'    => 'No ' . strtolower( $this->plural ) . ' found in Trash',
			'parent_item_colon'     => 'Parent ' . $this->plural . ':',
			'all_items'             => 'All ' . $this->plural,
			'archives'              => $this->singular . ' Archives',
			'attributes'            => $this->singular . ' Attributes',
			'insert_into_item'      => 'Insert into ' . strtolower( $this->singular ),
			'uploaded_to_this_item' => 'Uploaded to this ' . strtolower( $this->singular ),
			// 'featured_image'        => 'Featured Image',
			// 'set_featured_image'    => 'Set Featured Image',
			// 'remove_featured_image' => 'Remove featured image',
			// 'use_featured_image'    => 'Use as featured image',
			'menu_name'             => $this->plural,
			// 'filter_items_list'     => $this->plural,
			// 'items_list_navigation' => $this->plural,
			'items_list'            => $this->plural,
			'name_admin_bar'        => $this->singular,
		);

		if ( function_exists( 'create_post_type_labels' ) )
			$label_defaults = create_post_type_labels( $this->singular, $this->plural );

		$labels = wp_parse_args(
			(
				!empty( $this->args['labels'] )
				? $this->args['labels']
				: array()
			),
			$label_defaults
		);

		foreach ( array( 'labels', 'rewrite', 'supports' ) as $arg )
			if (
				array_key_exists( $arg, $this->args )
				&& array() === $this->args[ $arg ]
			)
				 unset( $this->args[ $arg ] );

		$args = wp_parse_args( $this->args, array(
			'labels'             => $labels,
			'public'             => true,
			'has_archive'        => true,
			'hierarchical'       => false,
			'show_in_rest'       => true,
		) );

		if (
			     !empty( $this->args['rewrite'] )
			&& is_array( $this->args['rewrite'] )
		)
			$args['rewrite'] = wp_parse_args(
				(
					!empty( $this->args['rewrite'] )
					? $this->args['rewrite']
					: array()
				),
				array(
					'slug'       => $this->type,
					'with_front' => false,
				)
			);

		$args['supports'] = array(
			'title',
			'author',
			'editor',
			'excerpt',
			'thumbnail',
		);

		if (
			       !empty( $this->args['supports'] )
			&& array() !== $this->args['supports']
		)
			$args['supports'] = $this->args['supports'];

		register_post_type( $this->type, $args );

	}

	/**
	 * Action: admin_init
	 *
	 * @return void
	 */
	function action__admin_init() : void {
		if ( empty( $this->dashicon_code ) )
			return;

		wp_add_inline_style( 'dashicons', '.icon-cpt-' . $this->type . ':before { content: "' . $this->dashicon_code . '" !important; }' );
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
	 * Add count of CPT to 'At a Glance' dashboard widget.
	 *
	 * @param array $items
	 * @return array
	 */
	function filter__dashboard_glance_items( array $items ) : array {
		$object = get_post_type_object( $this->type );

		if ( !current_user_can( $object->cap->edit_posts ) )
			return $items;

		$count = wp_count_posts( $this->type );

		if ( empty( $count->publish ) )
			$count->publish = 0;

		$items['count_' . $this->type] =
			'<a class="icon-cpt-' . $this->type . '" href="' . admin_url( add_query_arg( 'post_type', $this->type, 'edit.php' ) ) . '">' .
				$count->publish . _n( ' ' . $this->singular, ' ' . $this->plural, $count->publish ) .
			'</a>';

		return $items;
	}

	/**
	 * Add messages for updating the custom post type.
	 *
	 * @param array $notices
	 * @return array
	 */
	function filter__post_updated_messages( array $notices ) : array {
		global $post_ID, $post;

		if ( get_post_type( $post ) !== $this->type )
			return $notices;

		$object = get_post_type_object( get_post_type( $post ) );

		$notices[$this->type] = array(
			 0 => '', // Unused. Messages start at index 1.
			 1 => sprintf( __( $this->singular . ' updated.' . ( true === $object->public ? ' <a href="%s">View ' . $this->singular . '</a>' : '' ) ), esc_url( get_permalink( $post_ID ) ) ),
			 2 => __( 'Custom field updated.' ),
			 3 => __( 'Custom field deleted.' ),
			 4 => __( $this->singular . ' updated.' ),
			 5 => isset( $_GET['revision'] ) ? sprintf( __( $this->singular . ' restored to revision from %s' ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
			 6 => sprintf( __( $this->singular . ' published.' . ( true === $object->public ? ' <a href="%s">View ' . $this->singular . '</a>' : '' ) ), esc_url( get_permalink( $post_ID ) ) ),
			 7 => __( $this->singular . ' saved.'),
			 8 => sprintf( __( $this->singular . ' submitted. <a target="_blank" href="%s">Preview ' . $this->singular . '</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			 9 => sprintf( __( $this->singular . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . $this->singular . '</a>' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( $this->singular . ' draft updated.' . ( true === $object->public ? ' <a target="_blank" href="%s">Preview ' . $this->singular . '</a>' : '' ) ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $notices;
	}


	/*
	######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
	##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
	##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
	######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
	##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
	##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
	##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######
	*/

	/**
	 * Get nonce for CPT.
	 *
	 * @uses $this->get_nonce__action()
	 * @return string
	 */
	function get_nonce() : string {
		return wp_create_nonce( $this->get_nonce_action() );
	}

	/**
	 * Get nonce action for CPT.
	 *
	 * @return string
	 */
	function get_nonce_action() : string {
		return $this->nonce['action'];
	}

	/**
	 * Get nonce name for CPT.
	 *
	 * @return string
	 */
	function get_nonce_name() : string {
		return $this->nonce['name'];
	}

}

?>