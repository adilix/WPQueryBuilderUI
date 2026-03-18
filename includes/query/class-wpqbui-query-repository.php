<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CRUD operations for saved query configurations.
 */
class WPQBUI_Query_Repository {

	/** @return string */
	private function table() {
		global $wpdb;
		return $wpdb->prefix . 'wpqbui_queries';
	}

	/**
	 * @param array $args  { 'per_page' => int, 'page' => int, 'search' => string, 'orderby' => string, 'order' => string }
	 * @return array { 'items' => WPQBUI_Query_Definition[], 'total' => int }
	 */
	public function find_all( array $args = array() ) {
		global $wpdb;
		$table   = $this->table();
		$per_page = max( 1, intval( $args['per_page'] ?? 20 ) );
		$page     = max( 1, intval( $args['page'] ?? 1 ) );
		$search   = sanitize_text_field( $args['search'] ?? '' );
		$orderby_input = $args['orderby'] ?? 'created_at';
		$orderby       = in_array( $orderby_input, array( 'id', 'name', 'created_at', 'updated_at' ), true ) ? $orderby_input : 'created_at';
		$order    = 'ASC' === strtoupper( $args['order'] ?? 'DESC' ) ? 'ASC' : 'DESC';
		$offset   = ( $page - 1 ) * $per_page;

		$where = '';
		$params = array();
		if ( $search ) {
			$where    = 'WHERE name LIKE %s OR description LIKE %s';
			$like     = '%' . $wpdb->esc_like( $search ) . '%';
			$params[] = $like;
			$params[] = $like;
		}

		// phpcs:disable WordPress.DB.PreparedSQL
		$total_sql = "SELECT COUNT(*) FROM {$table} {$where}";
		$total     = $params ? (int) $wpdb->get_var( $wpdb->prepare( $total_sql, $params ) ) : (int) $wpdb->get_var( $total_sql );

		$data_sql = "SELECT * FROM {$table} {$where} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
		$params[] = $per_page;
		$params[] = $offset;
		$rows     = $wpdb->get_results( $wpdb->prepare( $data_sql, $params ) );
		// phpcs:enable

		$items = array_map( array( 'WPQBUI_Query_Definition', 'from_db_row' ), $rows ?: array() );
		return array( 'items' => $items, 'total' => $total );
	}

	/**
	 * @param int $id
	 * @return WPQBUI_Query_Definition|null
	 */
	public function find_by_id( $id ) {
		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table()} WHERE id = %d", (int) $id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return $row ? WPQBUI_Query_Definition::from_db_row( $row ) : null;
	}

	/**
	 * @param string $slug
	 * @return WPQBUI_Query_Definition|null
	 */
	public function find_by_slug( $slug ) {
		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table()} WHERE slug = %s", sanitize_title( $slug ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return $row ? WPQBUI_Query_Definition::from_db_row( $row ) : null;
	}

	/**
	 * Insert or update depending on whether $def->id is set.
	 *
	 * @param WPQBUI_Query_Definition $def
	 * @return int  New or existing ID.
	 */
	public function save_or_update( WPQBUI_Query_Definition $def ) {
		if ( $def->id ) {
			$this->update( $def );
			return $def->id;
		}
		return $this->save( $def );
	}

	/**
	 * @param WPQBUI_Query_Definition $def
	 * @return int  New row ID.
	 */
	public function save( WPQBUI_Query_Definition $def ) {
		global $wpdb;
		$def->slug = $this->generate_unique_slug( $def->name );
		$wpdb->insert(
			$this->table(),
			array(
				'name'        => $def->name,
				'slug'        => $def->slug,
				'description' => $def->description,
				'query_args'  => $def->to_json(),
				'created_by'  => $def->created_by ?: get_current_user_id(),
			),
			array( '%s', '%s', '%s', '%s', '%d' )
		);
		return (int) $wpdb->insert_id;
	}

	/**
	 * @param WPQBUI_Query_Definition $def
	 * @return bool
	 */
	public function update( WPQBUI_Query_Definition $def ) {
		global $wpdb;
		// Regenerate slug only if name changed (check existing).
		$existing = $this->find_by_id( $def->id );
		if ( $existing && $existing->name !== $def->name ) {
			$def->slug = $this->generate_unique_slug( $def->name, $def->id );
		} elseif ( $existing ) {
			$def->slug = $existing->slug;
		}
		return (bool) $wpdb->update(
			$this->table(),
			array(
				'name'        => $def->name,
				'slug'        => $def->slug,
				'description' => $def->description,
				'query_args'  => $def->to_json(),
			),
			array( 'id' => $def->id ),
			array( '%s', '%s', '%s', '%s' ),
			array( '%d' )
		);
	}

	/**
	 * @param int $id
	 * @return int  New row ID of the duplicate.
	 */
	public function duplicate( $id ) {
		$source = $this->find_by_id( (int) $id );
		if ( ! $source ) {
			return 0;
		}
		$copy              = new WPQBUI_Query_Definition();
		$copy->name        = $source->name . ' ' . __( '(Copy)', 'wpqbui' );
		$copy->description = $source->description;
		$copy->query_args  = $source->query_args;
		$copy->created_by  = get_current_user_id();
		return $this->save( $copy );
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public function delete( $id ) {
		global $wpdb;
		return (bool) $wpdb->delete( $this->table(), array( 'id' => (int) $id ), array( '%d' ) );
	}

	/**
	 * @param string   $name
	 * @param int|null $exclude_id  ID to exclude from the uniqueness check (for updates).
	 * @return string
	 */
	public function generate_unique_slug( $name, $exclude_id = null ) {
		global $wpdb;
		$base   = sanitize_title( $name );
		$slug   = $base;
		$i      = 1;
		$table  = $this->table();
		$exclude_id = $exclude_id ? (int) $exclude_id : 0;
		while ( true ) {
			if ( $exclude_id ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE slug = %s AND id != %d", $slug, $exclude_id ) );
			} else {
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE slug = %s", $slug ) );
			}
			if ( ! $exists ) {
				break;
			}
			$slug = $base . '-' . ( ++$i );
		}
		return $slug;
	}
}
