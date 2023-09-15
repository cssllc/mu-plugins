<?php
/**
 * Plugin name: Advanced Custom Fields add-on: Page Slug rule
 * Author: Caleb Stauffer
 * Author URI: https://develop.calebstauffer.com
 * Plugin URI: https://github.com/cssllc/mu-plugins
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: CSSLLC_ACF__Rule_PageSlug
 */
class CSSLLC_ACF__Rule_PageSlug {

	/**
	 * @return void
	 */
	public static function init() {
		static::instance();
	}

	/**
	 * @return self
	 */
	public static function instance() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
		}

		return $instance;
	}

	protected function __construct() {
		add_filter( 'acf/location/rule_types', array( $this, 'filter__acf_location_rule_types' ) );
		add_filter( 'acf/location/rule_operators/slug', array( $this, 'filter__acf_location_rule_operators_slug' ) );
		add_filter( 'acf/location/rule_values/slug', array( $this, 'filter__acf_location_rule_values_slug' ) );
		add_filter( 'acf/location/rule_match/slug', array( $this, 'filter__acf_location_rule_match_slug' ), 10, 3 );
	}

	/**
	 * @param array<string, array<string, string>> $choices
	 * @return array<string, array<string, string>>
	 */
	public function filter__acf_location_rule_types( array $choices ) {
		$choices['Page']['page'] = 'Page ID';
		$choices['Page']['slug'] = 'Page Slug';

		return $choices;
	}

	/**
	 * @param array<string, string> $choices
	 * @return array<string, string>
	 */
	public function filter__acf_location_rule_operators_slug( array $choices ) {
		$choices['contains']         = 'contains';
		$choices['does not contain'] = 'does not contain';

		return $choices;
	}

	/**
	 * @param array<string, string> $choices
	 * @return array<string, string>
	 */
	public function filter__acf_location_rule_values_slug( array $choices ) {
		$query = new WP_Query( array(
			'post_type'      => 'page',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'asc',
		) );

		$choices = array();

		foreach ( $query->posts as $post ) {
			if ( ! is_object( $post ) || ! is_a( $post, WP_Post::class ) ) {
				continue;
			}

			$choices[ $post->post_name ] = get_the_title( $post );
		}

		return $choices;
	}

	/**
	 * @param bool $match
	 * @param array<string, mixed> $rule
	 * @param array<mixed> $options
	 * @return bool
	 */
	public function filter__acf_location_rule_match_slug( $match, $rule, $options ) {
		if (
			! isset( $options['post_type'] )
			|| 'page' !== $options['post_type']
		) {
			return $match;
		}

		$page = get_post( $options['post_id'] );

		if ( is_null( $page ) || ! is_string( $rule['value'] ) ) {
			return false;
		}

		switch ( $rule['operator'] ) {

			case '==':
				return $page->post_name === $rule['value'];

			case '!=':
				return $page->post_name !== $rule['value'];

			case 'contains':
				return false !== stripos( $page->post_name, $rule['value'] );

			case 'does not contain':
				return false === stripos( $page->post_name, $rule['value'] );

		}

		return $match;
	}

}

add_action( 'init', array( 'CSSLLC_ACF__Rule_PageSlug', 'init' ), 5 );
