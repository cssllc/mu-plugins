<?php
/**
 * Description: WordPress helpers from CSSLLC.
 * Author URI: https://develop.calebstauffer.com
 * Author: Caleb Stauffer
 * Plugin URI: https://github.com/cssllc/mu-plugins
 */

if ( !function_exists( 'create_post_type_labels' ) ) {

	/**
	* Helper to create post type labels.
	*
	* @param string $singular
	* @param string $plural
	* @param array $overrides
	* @return array
	*/
	function create_post_type_labels( string $singular, string $plural, array $overrides = array() ) {
		return array_filter( wp_parse_args( $overrides, array(
			'name'                     => $plural,
			'singular_name'            => $singular,
			'add_new'                  => sprintf( 'Add %s', $singular ),
			'add_new_item'             => sprintf( 'Add New %s', $singular ),
			'edit_item'                => sprintf( 'Edit %s', $singular ),
			'new_item'                 => sprintf( 'New %s', $singular ),
			'view_item'                => sprintf( 'View %s', $singular ),
			'view_items'               => sprintf( 'View %s', $plural ),
			'search_items'             => sprintf( 'Search %s', $plural ),
			'not_found'                => sprintf( 'No %s found', strtolower( $plural ) ),
			'not_found_in_trash'       => sprintf( 'No %s found in Trash', strtolower( $plural ) ),
			'parent_item_colon'        => sprintf( 'Parent %s:', $plural ),
			'all_items'                => sprintf( 'All %s', $plural ),
			'archives'                 => sprintf( '%s Archives', $singular ),
			'attributes'               => sprintf( '%s Attributes', $singular ),
			'insert_into_item'         => sprintf( 'Insert into %s', strtolower( $singular ) ),
			'uploaded_to_this_item'    => sprintf( 'Uploaded to this %s', strtolower( $singular ) ),
			'featured_image'           => null,
			'set_featured_image'       => null,
			'remove_featured_image'    => null,
			'use_featured_image'       => null,
			'menu_name'                => $plural,
			'filter_items_list'        => sprintf( 'Filter %s list', $plural ),
			'items_list_navigation'    => sprintf( '%s list navigation', $plural ),
			'items_list'               => sprintf( '%s list', $plural ),
			'item_published'           => sprintf( '%s published.', $singular ),
			'item_published_privately' => sprintf( '%s published privately.', $singular ),
			'item_reverted_to_draft'   => sprintf( '%s reverted to draft.', $singular ),
			'item_scheduled'           => sprintf( '%s scheduled.', $singular ),
			'item_updated'             => sprintf( '%s updated.', $singular ),
		) ) );
	}

}

if ( !function_exists( 'create_taxonomy_labels' ) ) {

	/**
	 * Helper to create taxonomy labels.
	 *
	 * @param string $singular
	 * @param string $plural
	 * @param array $overrides
	 * @return array
	 */
	function create_taxonomy_labels( string $singular, string $plural, array $overrides = array() ) {
		return array_filter( wp_parse_args( $overrides, array(
			'name'                       => $plural,
			'singular_name'              => $singular,
			'search_items'               => sprintf( 'Search %s', $plural ),
			'popular_items'              => sprintf( 'Popular %s', $plural ),
			'all_items'                  => sprintf( 'All %s', $plural ),
			'parent_item'                => sprintf( 'Parent %s', $singular ),
			'parent_item_colon'          => sprintf( 'Parent %s:', $singular ),
			'edit_item'                  => sprintf( 'Edit %s', $singular ),
			'view_item'                  => sprintf( 'View %s', $singular ),
			'update_item'                => sprintf( 'Update %s', $singular ),
			'add_new_item'               => sprintf( 'Add New %s', $singular ),
			'new_item_name'              => sprintf( 'New %s Name', $singular ),
			'separate_items_with_commas' => sprintf( 'Separate %s with commas', strtolower( $singular ) ),
			'add_or_remove_items'        => sprintf( 'Add or remove %s', strtolower( $singular ) ),
			'choose_from_most_used'      => sprintf( 'Choose from the most used %s', strtolower( $singular ) ),
			'not_found'                  => sprintf( 'No %s found', strtolower( $plural ) ),
			'no_terms'                   => sprintf( 'No %s', $plural ),
			'items_list_navigation'      => sprintf( '%s list navigation', $plural ),
			'items_list'                 => sprintf( '%s list', $plural ),
			'most_used'                  => 'Most Used',
			'back_to_items'              => sprintf( '&larr; Back to %s', $plural ),
			'menu_name'                  => $plural,
		) ) );
	}

}

if ( !function_exists( 'prerender' ) ) {

	/**
	 * Add pre-render link tags for specified URLs.
	 *
	 * @see wp_resource_hints()
	 * @param string|array $prerender_urls
	 * @uses wp_http_validate_url()
	 */
	function prerender( $prerender_urls ) {

		# Check if it's too late.
		if (
			     did_action( 'wp_head' )
			|| doing_action( 'wp_head' )
		) {
			trigger_error( sprintf( '<code>%s</code> must be called before <code>%s</code> action.', __FUNCTION__ . '()', 'wp_head:2' ) );
			return;
		}

		# Cast parameter as an array.
		$prerender_urls = ( array ) $prerender_urls;

		# Evaluate passed values.
		$prerender_urls = array_filter( $prerender_urls, function( $url ) {
			if ( empty( $url ) )
				return false;

			if ( !wp_http_validate_url( $url ) ) {
				trigger_error( sprintf( '<code>%s</code> was evaluated by <code>%s</code> and failed as a valid URL.', $url, 'wp_http_validate_url()' ) );
				return false;
			}

			return true;
		} );

		# If no valid URLs, bail.
		if ( empty( $prerender_urls ) )
			return;

		# Add URLs for use by wp_resource_hints().
		foreach ( $prerender_urls as $url )
			add_filter( 'wp_resource_hints', function( array $urls, string $type ) use( $url ) {
				if ( 'prerender' === $type )
					$urls[] = $url;

				return $urls;
			}, 10, 2 );
	}

}

if ( !function_exists( 'target' ) ) {

	/**
	 * Create "target" attribute.
	 *
	 * @param string $target
	 * @param null|string|false $rel `false` prevents output of "rel" attribute.
	 * @param bool $echo
	 * @return string
	 */
	function target( $target, string $rel = null, bool $echo = true ) {
		if (
			    empty( $target )
			|| !is_string( $target )
		)
			return '';

		# Start creating the output.
		$return = ' target="' . esc_attr( trim( $target ) ) . '"';

		# If not prevented, include "rel" attribute.
		if ( false !== $rel ) {

			# If new window, add "noreferrer" and "noopener".
			if ( '_blank' === $target )
				$rel .= ' noreferrer noopener';

			# Add "rel" attribute.
			if ( !empty( $rel ) )
				$return .= ' rel="' . esc_attr( trim( $rel ) ) . '"';

		}

		# Return attribute(s).
		if ( !$echo )
			return $return;

		# Print attribute(s).
		echo $return;
	}

}

if ( !function_exists( 'make_email_address_clickable' ) ) {

	/**
	 * Add link to email address.
	 *
	 * @param string $email_address
	 * @param bool $antispambot
	 * @return string
	 */
	function make_email_address_clickable( string $email_address, bool $antispambot = true ) {
		if ( !is_email( $email_address ) )
			return $email_address;

		if ( $antispambot )
			$email_address = antispambot( $email_address );

		return '<a href="' . esc_attr( 'mailto:' . $email_address ) . '">' . $email_address . '</a>';
	}

}