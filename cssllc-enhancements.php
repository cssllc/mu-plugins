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
} );

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
	if ( has_post_thumbnail() ) {
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
	if ( has_post_thumbnail( $post_id ) )
		$classes[] = 'post-thumbnail-' . get_post_thumbnail_id( $post_id );

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
		a[href*="google.com"],
		a[href*="javascript"] {
			position: relative;
			counter-increment: empty-links;
		}

		a[href=""]::before,
		a[href="#"]::before,
		a[href*="google.com"]::before,
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

	# Store data.
	$id = $post->post_parent;
	$parent = get_post( $id );
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