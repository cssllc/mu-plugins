<?php

if ( ! defined( 'WPINC' ) ) {
	return;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! class_exists( 'WP_CLI' ) ) {
	return;
}

class CSSLLC_Flush_All_Rewrites {

	public function register(): void {
		WP_CLI::add_command( $this->command(), [ $this, 'run_command' ], [
			'shortdesc' => $this->description(),
			'synopsis'  => $this->arguments(),
		] );
	}

	/**
	 * Hard‑flushes rewrite rules for all sites in the network.
	 *
	 * ## EXAMPLES
	 *
	 *     wp rewrite flush-all
	 *     wp rewrite flush-all --debug
	 *     wp rewrite flush-all --debug=flush-all
	 *
	 * @when after_wp_load
	 */
	public function run_command( array $args = [], array $assoc_args = [] ): void {
		if ( true !== is_multisite() ) {
			\WP_CLI::error( 'This command only works in a Multisite environment.' );
		}

		\WP_CLI::debug( 'Starting flush-all command.', 'flush-all' );

		$site_ids = $this->get_all_site_ids();
		$total    = count( $site_ids );

		if ( 0 === $total ) {
			\WP_CLI::warning( 'No sites found in the network.' );

			return;
		}

		// translators: %d is the number of sites.
		\WP_CLI::debug(
			sprintf(
				_n(
					'Found %d site.',
					'Found %d sites.',
					$total
				),
				$total
			),
			'flush-all'
		);

		$progress = \WP_CLI\Utils\make_progress_bar( 'Flushing rewrite rules', $total );

		$success_count = 0;
		$fail_count    = 0;
		$current       = 0;

		foreach ( $site_ids as $site_id ) {
			++$current;

			$blog_id = absint( $site_id['blog_id'] );

			try {
				switch_to_blog( $blog_id );

				\WP_CLI::debug( sprintf( 'Switched to blog ID: %d', $blog_id ), 'flush-all' );

				flush_rewrite_rules();

				\WP_CLI::debug( sprintf( 'Flushed rewrites for blog ID %d.', $blog_id ), 'flush-all' );

				++$success_count;
			} catch ( \Throwable $e ) {
				\WP_CLI::debug(
					sprintf( 'Error flushing blog ID %d: %s', $blog_id, $e->getMessage() ),
					'flush-all'
				);
				++$fail_count;
			} finally {
				restore_current_blog();
				\WP_CLI::debug( 'Restored to main blog context.', 'flush-all' );
			}

			$progress->tick( 1, sprintf( 'Flushing %d / %d sites', $current, $total ) );

			usleep( 0.1 * 1000000 );
		}

		$progress->finish();

		// Empty line.
		\WP_CLI::log( '' );

		// translators: %d is the number of successfully flushed sites.
		\WP_CLI::success(
			sprintf(
				_n(
					'Flushed rewrites for %d site.',
					'Flushed rewrites for %d sites.',
					$success_count
				),
				$success_count
			)
		);

		if ( 0 < $fail_count ) {
			// translators: %d is the number of failed sites.
			\WP_CLI::warning(
				sprintf(
					_n(
						'Failures: %d site.',
						'Failures: %d sites.',
						$fail_count
					),
					$fail_count
				)
			);
		}

		\WP_CLI::debug( 'Flush-all command completed.', 'flush-all' );
	}

	protected function command(): string {
		return 'rewrite flush-all';
	}

	protected function description(): string {
		return __( 'Flush rewrite rules for all sites on a network.', 'tribe' );
	}

	protected function arguments(): array {
		return [];
	}

	/**
	 * Retrieve all site IDs from the current network.
	 *
	 * @return array[]
	 */
	private function get_all_site_ids(): array {
		global $wpdb;

		$site_id = defined( 'SITE_ID_CURRENT_SITE' ) ? SITE_ID_CURRENT_SITE : 1;

		$sql = '
			SELECT blog_id
			FROM ' . $wpdb->blogs . '
			WHERE site_id = %d
			  AND spam = 0
			  AND deleted = 0
			  AND archived = \'0\'
		';

		\WP_CLI::debug( sprintf( 'Querying sites with site_id = %d', $site_id ), 'flush-all' );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return $wpdb->get_results( $wpdb->prepare( $sql, $site_id ), ARRAY_A );
	}

}

add_action( 'init', static function(): void {
	( new CSSLLC_Flush_All_Rewrites() )->register();
} );
