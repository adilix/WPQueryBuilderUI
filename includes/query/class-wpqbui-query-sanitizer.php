<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitises raw form POST data into a clean query_args array.
 */
class WPQBUI_Query_Sanitizer {

	/** Allowed orderby values */
	const ORDERBY_ALLOWED = array(
		'none', 'ID', 'author', 'title', 'name', 'type', 'date', 'modified',
		'parent', 'rand', 'comment_count', 'relevance', 'menu_order',
		'meta_value', 'meta_value_num', 'post__in', 'post_name__in',
		'post_parent__in', 'include_slugs',
	);

	/** Allowed fields values */
	const FIELDS_ALLOWED = array( '', 'ids', 'id=>parent' );

	/** Allowed perm values */
	const PERM_ALLOWED = array( '', 'readable', 'editable' );

	/**
	 * Sanitise raw data from form submission.
	 *
	 * @param array $raw  Raw $_POST data (already passed through wp_unslash).
	 * @return array      Clean query_args array.
	 */
	public function sanitize( array $raw ) {
		$args = array();

		// --- Basic ---
		if ( ! empty( $raw['post_type'] ) ) {
			$args['post_type'] = $this->sanitize_string_or_array( $raw['post_type'], 'sanitize_key' );
		}
		if ( ! empty( $raw['post_status'] ) ) {
			$args['post_status'] = $this->sanitize_string_or_array( $raw['post_status'], 'sanitize_key' );
		}
		if ( isset( $raw['has_password'] ) && '' !== $raw['has_password'] ) {
			$args['has_password'] = (bool) $raw['has_password'];
		}
		if ( ! empty( $raw['post_password'] ) ) {
			$args['post_password'] = sanitize_text_field( $raw['post_password'] );
		}
		if ( isset( $raw['fields'] ) && in_array( $raw['fields'], self::FIELDS_ALLOWED, true ) ) {
			$args['fields'] = $raw['fields'];
		}
		if ( isset( $raw['no_found_rows'] ) ) {
			$args['no_found_rows'] = (bool) $raw['no_found_rows'];
		}
		if ( isset( $raw['suppress_filters'] ) ) {
			$args['suppress_filters'] = (bool) $raw['suppress_filters'];
		}

		// --- Author ---
		if ( ! empty( $raw['author'] ) ) {
			// Accept comma-separated IDs or single ID.
			$args['author'] = implode( ',', array_map( 'intval', array_filter( explode( ',', $raw['author'] ) ) ) );
		}
		if ( ! empty( $raw['author_name'] ) ) {
			$args['author_name'] = sanitize_user( $raw['author_name'] );
		}
		if ( ! empty( $raw['author__in'] ) ) {
			$args['author__in'] = $this->sanitize_id_list( $raw['author__in'] );
		}
		if ( ! empty( $raw['author__not_in'] ) ) {
			$args['author__not_in'] = $this->sanitize_id_list( $raw['author__not_in'] );
		}

		// --- Post IDs ---
		if ( ! empty( $raw['p'] ) ) {
			$args['p'] = absint( $raw['p'] );
		}
		if ( ! empty( $raw['post__in'] ) ) {
			$args['post__in'] = $this->sanitize_id_list( $raw['post__in'] );
		}
		if ( ! empty( $raw['post__not_in'] ) ) {
			$args['post__not_in'] = $this->sanitize_id_list( $raw['post__not_in'] );
		}
		if ( ! empty( $raw['post_name__in'] ) ) {
			$slugs = explode( "\n", $raw['post_name__in'] );
			$args['post_name__in'] = array_filter( array_map( 'sanitize_title', $slugs ) );
		}

		// --- Parent ---
		if ( isset( $raw['post_parent'] ) && '' !== $raw['post_parent'] ) {
			$args['post_parent'] = absint( $raw['post_parent'] );
		}
		if ( ! empty( $raw['post_parent__in'] ) ) {
			$args['post_parent__in'] = $this->sanitize_id_list( $raw['post_parent__in'] );
		}
		if ( ! empty( $raw['post_parent__not_in'] ) ) {
			$args['post_parent__not_in'] = $this->sanitize_id_list( $raw['post_parent__not_in'] );
		}

		// --- Pagination ---
		if ( isset( $raw['posts_per_page'] ) && '' !== $raw['posts_per_page'] ) {
			$args['posts_per_page'] = intval( $raw['posts_per_page'] );
		}
		if ( isset( $raw['paged'] ) && '' !== $raw['paged'] ) {
			$args['paged'] = max( 1, absint( $raw['paged'] ) );
		}
		if ( isset( $raw['offset'] ) && '' !== $raw['offset'] ) {
			$args['offset'] = absint( $raw['offset'] );
		}
		if ( isset( $raw['nopaging'] ) ) {
			$args['nopaging'] = (bool) $raw['nopaging'];
		}

		// --- Order ---
		if ( ! empty( $raw['orderby'] ) ) {
			$args['orderby'] = $this->sanitize_orderby( $raw['orderby'] );
		}
		if ( ! empty( $raw['order'] ) && in_array( strtoupper( $raw['order'] ), array( 'ASC', 'DESC' ), true ) ) {
			$args['order'] = strtoupper( $raw['order'] );
		}
		if ( ! empty( $raw['meta_key'] ) ) {
			$args['meta_key'] = sanitize_key( $raw['meta_key'] );
		}

		// --- Search ---
		if ( isset( $raw['s'] ) && '' !== $raw['s'] ) {
			$args['s'] = sanitize_text_field( $raw['s'] );
		}
		if ( isset( $raw['sentence'] ) ) {
			$args['sentence'] = (bool) $raw['sentence'];
		}
		if ( isset( $raw['exact'] ) ) {
			$args['exact'] = (bool) $raw['exact'];
		}
		if ( ! empty( $raw['search_columns'] ) && is_array( $raw['search_columns'] ) ) {
			$allowed_cols = array( 'post_title', 'post_excerpt', 'post_content' );
			$args['search_columns'] = array_values( array_intersect( $raw['search_columns'], $allowed_cols ) );
		}

		// --- Sticky ---
		if ( isset( $raw['ignore_sticky_posts'] ) ) {
			$args['ignore_sticky_posts'] = (bool) $raw['ignore_sticky_posts'];
		}

		// --- Mime type ---
		if ( ! empty( $raw['post_mime_type'] ) ) {
			$args['post_mime_type'] = sanitize_text_field( $raw['post_mime_type'] );
		}

		// --- Comment count ---
		if ( isset( $raw['comment_count'] ) && '' !== $raw['comment_count'] ) {
			if ( is_array( $raw['comment_count'] ) ) {
				$args['comment_count'] = array(
					'value'   => absint( $raw['comment_count']['value'] ),
					'compare' => $this->sanitize_compare( $raw['comment_count']['compare'] ),
				);
			} else {
				$args['comment_count'] = absint( $raw['comment_count'] );
			}
		}

		// --- Perm ---
		if ( isset( $raw['perm'] ) && in_array( $raw['perm'], self::PERM_ALLOWED, true ) ) {
			$args['perm'] = $raw['perm'];
		}

		// --- Cache ---
		if ( isset( $raw['cache_results'] ) ) {
			$args['cache_results'] = (bool) $raw['cache_results'];
		}
		if ( isset( $raw['update_post_meta_cache'] ) ) {
			$args['update_post_meta_cache'] = (bool) $raw['update_post_meta_cache'];
		}
		if ( isset( $raw['update_post_term_cache'] ) ) {
			$args['update_post_term_cache'] = (bool) $raw['update_post_term_cache'];
		}

		// --- Tax query ---
		if ( ! empty( $raw['tax_query'] ) && is_array( $raw['tax_query'] ) ) {
			$args['tax_query'] = $this->sanitize_tax_query( $raw['tax_query'] );
		}

		// --- Meta query ---
		if ( ! empty( $raw['meta_query'] ) && is_array( $raw['meta_query'] ) ) {
			$args['meta_query'] = $this->sanitize_meta_query( $raw['meta_query'] );
		}

		// --- Date query ---
		if ( ! empty( $raw['date_query'] ) && is_array( $raw['date_query'] ) ) {
			$args['date_query'] = $this->sanitize_date_query( $raw['date_query'] );
		}

		return $args;
	}

	// -------------------------------------------------------------------------
	// Sub-sanitisers
	// -------------------------------------------------------------------------

	private function sanitize_string_or_array( $value, $callback ) {
		if ( is_array( $value ) ) {
			return array_values( array_filter( array_map( $callback, $value ) ) );
		}
		return call_user_func( $callback, $value );
	}

	private function sanitize_id_list( $value ) {
		if ( is_array( $value ) ) {
			return array_values( array_filter( array_map( 'absint', $value ) ) );
		}
		return array_values( array_filter( array_map( 'absint', explode( ',', $value ) ) ) );
	}

	private function sanitize_orderby( $value ) {
		if ( ! is_array( $value ) ) {
			return in_array( $value, self::ORDERBY_ALLOWED, true ) ? $value : 'date';
		}

		$clean = array();
		foreach ( $value as $ob => $ord ) {
			// Form POST format: indexed rows like [ 0 => ['ob' => 'date', 'order' => 'DESC'] ]
			if ( is_array( $ord ) ) {
				$ob_val  = sanitize_key( $ord['ob'] ?? '' );
				$ord_val = strtoupper( $ord['order'] ?? 'DESC' );
				$ord_val = in_array( $ord_val, array( 'ASC', 'DESC' ), true ) ? $ord_val : 'DESC';
				if ( in_array( $ob_val, self::ORDERBY_ALLOWED, true ) ) {
					$clean[ $ob_val ] = $ord_val;
				}
				continue;
			}
			// JS preview format: associative map like [ 'date' => 'DESC' ]
			$ob  = sanitize_key( $ob );
			$ord = in_array( strtoupper( $ord ), array( 'ASC', 'DESC' ), true ) ? strtoupper( $ord ) : 'DESC';
			if ( in_array( $ob, self::ORDERBY_ALLOWED, true ) ) {
				$clean[ $ob ] = $ord;
			}
		}

		// If only one orderby key, return it as a plain string (cleaner output).
		if ( count( $clean ) === 1 ) {
			reset( $clean );
			return key( $clean );
		}

		return $clean ?: 'date';
	}

	private function sanitize_compare( $compare ) {
		$allowed = array( '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS', 'NOT EXISTS', 'REGEXP', 'NOT REGEXP' );
		return in_array( strtoupper( $compare ), $allowed, true ) ? strtoupper( $compare ) : '=';
	}

	private function sanitize_meta_type( $type ) {
		$allowed = array( 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED' );
		return in_array( strtoupper( $type ), $allowed, true ) ? strtoupper( $type ) : 'CHAR';
	}

	private function sanitize_tax_query( array $raw ) {
		$result = array();
		if ( isset( $raw['relation'] ) && in_array( strtoupper( $raw['relation'] ), array( 'AND', 'OR' ), true ) ) {
			$result['relation'] = strtoupper( $raw['relation'] );
		}
		$rows = isset( $raw['rows'] ) ? $raw['rows'] : $raw;
		if ( ! is_array( $rows ) ) {
			return $result;
		}
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) || empty( $row['taxonomy'] ) ) {
				continue;
			}
			$clause = array(
				'taxonomy'         => sanitize_key( $row['taxonomy'] ),
				'field'            => in_array( $row['field'] ?? 'term_id', array( 'term_id', 'slug', 'name', 'term_taxonomy_id' ), true ) ? $row['field'] : 'term_id',
				'operator'         => in_array( strtoupper( $row['operator'] ?? 'IN' ), array( 'IN', 'NOT IN', 'AND', 'EXISTS', 'NOT EXISTS' ), true ) ? strtoupper( $row['operator'] ) : 'IN',
				'include_children' => isset( $row['include_children'] ) ? (bool) $row['include_children'] : true,
			);
			if ( ! empty( $row['terms'] ) ) {
				if ( 'term_id' === $clause['field'] || 'term_taxonomy_id' === $clause['field'] ) {
					$clause['terms'] = $this->sanitize_id_list( $row['terms'] );
				} else {
					$clause['terms'] = array_values( array_filter( array_map( 'sanitize_text_field', (array) $row['terms'] ) ) );
				}
			}
			$result[] = $clause;
		}
		return $result;
	}

	private function sanitize_meta_query( array $raw ) {
		$result = array();
		if ( isset( $raw['relation'] ) && in_array( strtoupper( $raw['relation'] ), array( 'AND', 'OR' ), true ) ) {
			$result['relation'] = strtoupper( $raw['relation'] );
		}
		$rows = isset( $raw['rows'] ) ? $raw['rows'] : $raw;
		if ( ! is_array( $rows ) ) {
			return $result;
		}
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) || empty( $row['key'] ) ) {
				continue;
			}
			$compare = $this->sanitize_compare( $row['compare'] ?? '=' );
			$clause  = array(
				'key'     => sanitize_text_field( $row['key'] ),
				'compare' => $compare,
				'type'    => $this->sanitize_meta_type( $row['type'] ?? 'CHAR' ),
			);
			if ( ! in_array( $compare, array( 'EXISTS', 'NOT EXISTS' ), true ) ) {
				$value = $row['value'] ?? '';
				if ( in_array( $compare, array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ), true ) && is_string( $value ) ) {
					$value = array_filter( array_map( 'trim', explode( ',', $value ) ) );
				}
				$clause['value'] = $value;
			}
			$result[] = $clause;
		}
		return $result;
	}

	private function sanitize_date_query( array $raw ) {
		$result = array();
		if ( isset( $raw['relation'] ) && in_array( strtoupper( $raw['relation'] ), array( 'AND', 'OR' ), true ) ) {
			$result['relation'] = strtoupper( $raw['relation'] );
		}
		$rows = isset( $raw['rows'] ) ? $raw['rows'] : $raw;
		if ( ! is_array( $rows ) ) {
			return $result;
		}
		$date_int_fields = array( 'year', 'month', 'w', 'day', 'dayofyear', 'hour', 'minute', 'second' );
		$allowed_columns = array( 'post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt', 'comment_date', 'comment_date_gmt' );
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$clause = array();
			foreach ( $date_int_fields as $f ) {
				if ( isset( $row[ $f ] ) && '' !== $row[ $f ] ) {
					$clause[ $f ] = absint( $row[ $f ] );
				}
			}
			if ( ! empty( $row['after'] ) && is_array( $row['after'] ) ) {
				$clause['after'] = array_map( 'absint', array_intersect_key( $row['after'], array( 'year' => 0, 'month' => 0, 'day' => 0 ) ) );
			}
			if ( ! empty( $row['before'] ) && is_array( $row['before'] ) ) {
				$clause['before'] = array_map( 'absint', array_intersect_key( $row['before'], array( 'year' => 0, 'month' => 0, 'day' => 0 ) ) );
			}
			if ( isset( $row['inclusive'] ) ) {
				$clause['inclusive'] = (bool) $row['inclusive'];
			}
			if ( ! empty( $row['compare'] ) ) {
				$clause['compare'] = $this->sanitize_compare( $row['compare'] );
			}
			if ( ! empty( $row['column'] ) && in_array( $row['column'], $allowed_columns, true ) ) {
				$clause['column'] = $row['column'];
			}
			if ( ! empty( $clause ) ) {
				$result[] = $clause;
			}
		}
		return $result;
	}
}
