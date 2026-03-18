<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validates a clean query_args array and returns warnings/errors.
 */
class WPQBUI_Query_Validator {

	/**
	 * @param array $args  Clean query_args from WPQBUI_Query_Sanitizer.
	 * @return array  Array of [ 'field' => string, 'message' => string, 'severity' => 'error'|'warning' ]
	 */
	public function validate( array $args ) {
		$results = array();

		// Check offset + paged combination.
		if ( isset( $args['offset'] ) && $args['offset'] > 0 && ! empty( $args['paged'] ) && $args['paged'] > 1 ) {
			$results[] = array(
				'field'    => 'offset',
				'message'  => __( 'Using "offset" disables the built-in WordPress pagination — "paged" will be ignored. Manage pagination manually.', 'wpqbui' ),
				'severity' => 'warning',
			);
		}

		// nopaging + posts_per_page.
		if ( ! empty( $args['nopaging'] ) && isset( $args['posts_per_page'] ) && (int) $args['posts_per_page'] !== -1 ) {
			$results[] = array(
				'field'    => 'nopaging',
				'message'  => __( '"nopaging" overrides "posts_per_page". The posts_per_page value will be ignored.', 'wpqbui' ),
				'severity' => 'warning',
			);
		}

		// orderby meta_value without meta_key.
		if ( ! empty( $args['orderby'] ) ) {
			$meta_orderby = array( 'meta_value', 'meta_value_num' );
			$orderby      = $args['orderby'];
			$needs_key    = false;
			if ( is_string( $orderby ) && in_array( $orderby, $meta_orderby, true ) ) {
				$needs_key = true;
			} elseif ( is_array( $orderby ) ) {
				foreach ( array_keys( $orderby ) as $ob ) {
					if ( in_array( $ob, $meta_orderby, true ) ) {
						$needs_key = true;
						break;
					}
				}
			}
			if ( $needs_key && empty( $args['meta_key'] ) ) {
				$results[] = array(
					'field'    => 'orderby',
					'message'  => __( 'Ordering by "meta_value" or "meta_value_num" requires a "meta_key" to be set.', 'wpqbui' ),
					'severity' => 'error',
				);
			}
		}

		// meta_query: IN/NOT IN/BETWEEN with scalar value.
		if ( ! empty( $args['meta_query'] ) ) {
			foreach ( $args['meta_query'] as $clause ) {
				if ( ! is_array( $clause ) ) {
					continue;
				}
				$compare = $clause['compare'] ?? '=';
				if ( in_array( $compare, array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ), true ) && isset( $clause['value'] ) && ! is_array( $clause['value'] ) ) {
					$results[] = array(
						'field'    => 'meta_query',
						'message'  => sprintf(
							/* translators: %s: compare operator */
							__( 'Meta query with compare "%s" expects an array of values.', 'wpqbui' ),
							esc_html( $compare )
						),
						'severity' => 'error',
					);
				}
			}
		}

		// tax_query: taxonomy must exist.
		if ( ! empty( $args['tax_query'] ) ) {
			foreach ( $args['tax_query'] as $clause ) {
				if ( ! is_array( $clause ) || ! isset( $clause['taxonomy'] ) ) {
					continue;
				}
				if ( ! taxonomy_exists( $clause['taxonomy'] ) ) {
					$results[] = array(
						'field'    => 'tax_query',
						'message'  => sprintf(
							/* translators: %s: taxonomy slug */
							__( 'Taxonomy "%s" does not exist on this site.', 'wpqbui' ),
							esc_html( $clause['taxonomy'] )
						),
						'severity' => 'error',
					);
				}
			}
		}

		// Overlapping post__in and post__not_in.
		if ( ! empty( $args['post__in'] ) && ! empty( $args['post__not_in'] ) ) {
			$overlap = array_intersect( $args['post__in'], $args['post__not_in'] );
			if ( ! empty( $overlap ) ) {
				$results[] = array(
					'field'    => 'post__in',
					'message'  => sprintf(
						/* translators: %s: list of IDs */
						__( 'IDs appear in both "post__in" and "post__not_in": %s. These posts will be excluded.', 'wpqbui' ),
						implode( ', ', $overlap )
					),
					'severity' => 'warning',
				);
			}
		}

		// post_mime_type used with non-attachment post type.
		if ( ! empty( $args['post_mime_type'] ) && ! empty( $args['post_type'] ) ) {
			$types = (array) $args['post_type'];
			if ( ! in_array( 'attachment', $types, true ) && ! in_array( 'any', $types, true ) ) {
				$results[] = array(
					'field'    => 'post_mime_type',
					'message'  => __( '"post_mime_type" is only meaningful when querying attachments.', 'wpqbui' ),
					'severity' => 'warning',
				);
			}
		}

		// date_query after > before.
		if ( ! empty( $args['date_query'] ) ) {
			foreach ( $args['date_query'] as $clause ) {
				if ( ! is_array( $clause ) || empty( $clause['after'] ) || empty( $clause['before'] ) ) {
					continue;
				}
				$after  = mktime( 0, 0, 0, $clause['after']['month'] ?? 1, $clause['after']['day'] ?? 1, $clause['after']['year'] ?? 1970 );
				$before = mktime( 0, 0, 0, $clause['before']['month'] ?? 1, $clause['before']['day'] ?? 1, $clause['before']['year'] ?? 1970 );
				if ( $after !== false && $before !== false && $after > $before ) {
					$results[] = array(
						'field'    => 'date_query',
						'message'  => __( 'Date query "after" date is later than "before" date — this will return no results.', 'wpqbui' ),
						'severity' => 'error',
					);
				}
			}
		}

		// Empty query warning.
		$meaningful_keys = array_filter( array_keys( $args ), function( $k ) {
			return ! in_array( $k, array( 'cache_results', 'update_post_meta_cache', 'update_post_term_cache', 'suppress_filters', 'fields', 'no_found_rows' ), true );
		} );
		if ( empty( $meaningful_keys ) ) {
			$results[] = array(
				'field'    => '',
				'message'  => __( 'No query parameters are set. This will return the default WordPress query.', 'wpqbui' ),
				'severity' => 'warning',
			);
		}

		return $results;
	}
}
