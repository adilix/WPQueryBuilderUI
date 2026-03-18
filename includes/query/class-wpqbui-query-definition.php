<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Value object representing one saved query configuration.
 */
class WPQBUI_Query_Definition {

	/** @var int|null */
	public $id;

	/** @var string */
	public $name = '';

	/** @var string */
	public $slug = '';

	/** @var string */
	public $description = '';

	/** @var array  Raw WP_Query args */
	public $query_args = array();

	/** @var string */
	public $created_at = '';

	/** @var string */
	public $updated_at = '';

	/** @var int */
	public $created_by = 0;

	/**
	 * Build from sanitised form POST data.
	 *
	 * @param array $data
	 * @return self
	 */
	public static function from_array( array $data ) {
		$def              = new self();
		$def->id          = isset( $data['id'] ) && $data['id'] ? (int) $data['id'] : null;
		$def->name        = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
		$def->slug        = isset( $data['slug'] ) ? sanitize_title( $data['slug'] ) : '';
		$def->description = isset( $data['description'] ) ? sanitize_textarea_field( $data['description'] ) : '';
		$def->query_args  = isset( $data['query_args'] ) && is_array( $data['query_args'] ) ? $data['query_args'] : array();
		$def->created_by  = get_current_user_id();
		return $def;
	}

	/**
	 * Build from a DB row object.
	 *
	 * @param object $row
	 * @return self
	 */
	public static function from_db_row( $row ) {
		$def              = new self();
		$def->id          = (int) $row->id;
		$def->name        = $row->name;
		$def->slug        = $row->slug;
		$def->description = $row->description;
		$def->query_args  = json_decode( $row->query_args, true ) ?: array();
		$def->created_at  = $row->created_at;
		$def->updated_at  = $row->updated_at;
		$def->created_by  = (int) $row->created_by;
		return $def;
	}

	/**
	 * Return a clean WP_Query args array, stripping empty / null / false / '' values.
	 *
	 * @return array
	 */
	public function to_args_array() {
		return self::strip_empty( $this->query_args );
	}

	/**
	 * JSON-encode for DB storage.
	 *
	 * @return string
	 */
	public function to_json() {
		return wp_json_encode( $this->query_args );
	}

	// -------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------

	private static function strip_empty( array $args ) {
		$clean = array();
		foreach ( $args as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = self::strip_empty( $value );
				if ( ! empty( $value ) ) {
					$clean[ $key ] = $value;
				}
			} elseif ( '' !== $value && null !== $value && false !== $value ) {
				$clean[ $key ] = $value;
			}
		}
		return $clean;
	}
}
