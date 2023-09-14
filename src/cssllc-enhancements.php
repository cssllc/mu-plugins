<?php
/**
 * Description: WordPress enhancements from CSSLLC.
 * Author URI: https://develop.calebstauffer.com
 * Author: Caleb Stauffer
 * Plugin URI: https://github.com/cssllc/mu-plugins
 */

/**
 * Action: wp_enqueue_scripts
 *
 * - prevent emoji scripts and styles
 */
add_action( 'wp_enqueue_scripts', function() {
	remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles',     'print_emoji_styles' );
	remove_action( 'admin_print_styles',  'print_emoji_styles' );
	remove_action( 'wp_enqueue_scripts',  'wp_enqueue_global_styles' );
	remove_action( 'wp_body_open',        'wp_global_styles_render_svg_filters' );
}, 0 );

/**
 * Filter: body_class
 *
 * - add post thumbnail classes
 *
 * @param array $classes
 * @uses has_post_thumbnail()
 * @uses get_post_thumbnail_id()
 * @return array
 */
add_filter( 'body_class', function( $classes ) {
	if ( is_singular() && has_post_thumbnail() ) {
		$classes[] = 'has-post-thumbnail';
		$classes[] = 'post-thumbnail-' . get_post_thumbnail_id();
	}

	return $classes;
} );

/**
 * Filter: post_class
 *
 * - add post thumbnail id class
 *
 * @param array $classes
 * @param array $class
 * @param int $post_id
 * @uses has_post_thumbnail()
 * @uses get_post_thumbnail_id()
 * @return array
 */
add_filter( 'post_class', function( $classes, $class, $post_id ) {
	if ( has_post_thumbnail( $post_id ) ) {
		$classes[] = 'has-post-thumbnail';
		$classes[] = 'post-thumbnail-' . get_post_thumbnail_id( $post_id );
	}

	return $classes;
}, 10, 3 );

/**
 * Action: wp_print_footer_scripts
 *
 * - add "Empty Link" badge to empty links
 */
add_action( 'wp_print_footer_scripts', function() {
	if ( !current_user_can( 'edit_post', get_queried_object_id() ) )
		return;
	?>

	<style>
		html { counter-reset: empty-links; }

		a[href=""],
		a[href="#"],
		a[href="http://google.com"],
		a[href="https://google.com"],
		a[href*="javascript"] {
			position: relative;
			counter-increment: empty-links;
		}

		a[href=""]::before,
		a[href="#"]::before,
		a[href="http://google.com"]::before,
		a[href="https://google.com"]::before,
		a[href*="javascript"]::before {
			content: 'Empty link';
			position: absolute;
			left: 0;
			top: 0;
			z-index: 2;
			padding: 8px 15px;
			background-color: #f00;
			transform: rotate( -20deg ) translate( -10%, -10% );
			text-transform: uppercase;
			font-family: sans-serif;
			pointer-events: none;
			white-space: nowrap;
			letter-spacing: 2px;
			font-weight: 600;
			font-size: 9px !important;
			color: #FFF;
			opacity: 1;
			-webkit-text-fill-color: #FFF;
			-webkit-text-stroke-width: 0;

			-webkit-transition: opacity 0.2s;
			        transition: opacity 0.2s;

			-webkit-box-shadow: 2px 2px 5px 0px rgba( 0, 0, 0, 0.5 );
			   -moz-box-shadow: 2px 2px 5px 0px rgba( 0, 0, 0, 0.5 );
			        box-shadow: 2px 2px 5px 0px rgba( 0, 0, 0, 0.5 );
		}

			a[href=""]:hover::before,
			a[href="#"]:hover::before,
			a[href*="google.com"]:hover::before,
			a[href*="javascript"]:hover::before {
				opacity: 0;
			}
	</style>

	<?php
} );

/**
 * Filter: page_template_hierarchy
 *
 * Add child page templates.
 *
 * @see get_page_template()
 * @param string[] $templates
 * @return string[]
 */
add_filter( 'page_template_hierarchy', function( $templates ) {
	$post = get_queried_object();

	# If no post parent, return set templates.
	if ( empty( $post->post_parent ) )
		return $templates;

	$id = $post->post_parent;
	$parent = get_post( $id );

	if ( ! is_object( $parent ) || ! is_a( $parent, WP_Post::class ) ) {
		return $templates;
	}

	$template = get_page_template_slug( $parent );
	$pagename = $parent->post_name;
	$prefix = 'page-parent';

	# Remove and hold onto page.php.
	$last = array_pop( $templates );

	# Start copy from get_page_template().

		if (
			$template
			&& 0 === validate_file( $template )
		)
			$templates[] = $template;

		if ( $pagename ) {
			$pagename_decoded = urldecode( $pagename );

			if ( $pagename_decoded !== $pagename )
				$templates[] = "{$prefix}-{$pagename_decoded}.php";

			$templates[] = "{$prefix}-{$pagename}.php";
		}

		if ( $id )
			$templates[] = "{$prefix}-{$id}.php";

	# End copy from get_page_template().

	# Add base template.
	$templates[] = $prefix . '.php';

	# Add back page.php.
	$templates[] = $last;

	return $templates;
} );

/**
 * Filter: body_class
 *
 * Adjust "page-template-default" class.
 *
 * @param string[] $classes
 * @return string[]
 */
add_filter( 'body_class', function( array $classes ) : array {
	if ( 'page' !== get_post_type( get_queried_object_id() ) )
		return $classes;

	if ( !in_array( 'page-template-default', $classes ) )
		return $classes;

	$template = get_page_template();

	# If default template, bail.
	if ( in_array( $template, array(
		get_stylesheet_directory() . '/page.php',
		  get_template_directory() . '/page.php',
	) ) )
		return $classes;

	# If frontpage, get frontpage template.
	if (
		empty( $template )
		&& is_front_page()
	)
		$template = get_front_page_template();

	# If no template, bail.
	if ( empty( $template ) )
		return $classes;

	# Replace default class with specific class.
	$key = array_search( 'page-template-default', $classes );
	$classes[ $key ] = 'page-template-' . basename( $template, '.php' );

	return $classes;
} );

/**
 * Filter: wp_nav_menu_args
 *
 * Add theme_location to container class.
 *
 * @param array $args
 *
 * @return array
 */
add_filter( 'wp_nav_menu_args', static function( array $args ) {
	if ( empty( $args['theme_location'] ) ) {
		return $args;
	}

	if ( empty( $args['container_class'] ) ) {
		$args['container_class'] = '';
	}

	$menu = wp_get_nav_menu_object( $args['menu'] );

	$locations = get_nav_menu_locations();
	if ( ! $menu && $locations && isset( $locations[ $args['theme_location'] ] ) ) {
		$menu = wp_get_nav_menu_object( $locations[ $args['theme_location'] ] );
	}

	$args['container_class'] .= sprintf( ' menu-location-%s-container', $args['theme_location'] );

	if ( ! empty( $menu ) ) {
		$args['container_class'] .= sprintf( ' menu-%s-container', $menu->slug );
	}

	$args['container_class'] = trim( $args['container_class'] );

	return $args;
} );

/**
 * Filter: split_the_query
 *
 * Improves queries for sites with external object caching
 * by only retrieving post IDs from database.
 *
 * @see https://www.spacedmonkey.com/2023/01/17/improve-wp_query-performance-if-using-external-object-cache/
 * @see https://core.trac.wordpress.org/ticket/57296
 * @return bool
 */
add_filter( 'split_the_query', 'wp_using_ext_object_cache' );