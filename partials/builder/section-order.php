<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var array $query_args */
$qa = $query_args;

// Normalise orderby into an array of { ob, order } rows for display.
$orderby_rows = array();
if ( isset( $qa['orderby'] ) ) {
	if ( is_array( $qa['orderby'] ) ) {
		foreach ( $qa['orderby'] as $ob => $ord ) {
			$orderby_rows[] = array( 'orderby' => $ob, 'order' => $ord );
		}
	} elseif ( is_string( $qa['orderby'] ) ) {
		$orderby_rows[] = array( 'orderby' => $qa['orderby'], 'order' => $qa['order'] ?? 'DESC' );
	}
}
if ( empty( $orderby_rows ) ) {
	$orderby_rows[] = array( 'orderby' => 'date', 'order' => 'DESC' );
}

$orderby_options = array(
	'date', 'modified', 'title', 'name', 'ID', 'author', 'type', 'parent',
	'comment_count', 'menu_order', 'relevance', 'rand', 'none',
	'meta_value', 'meta_value_num', 'post__in', 'post_name__in', 'post_parent__in',
);
?>
<h3><?php esc_html_e( 'Order / Orderby', 'wpqbui' ); ?></h3>
<p class="description"><?php esc_html_e( 'Add one or more orderby clauses. Drag rows to set priority.', 'wpqbui' ); ?></p>

<div id="wpqbui-orderby-rows" class="wpqbui-repeater wpqbui-sortable">
	<?php foreach ( $orderby_rows as $i => $row ) : ?>
	<div class="wpqbui-repeater-row wpqbui-orderby-row" data-index="<?php echo (int) $i; ?>">
		<span class="wpqbui-drag-handle dashicons dashicons-move" title="<?php esc_attr_e( 'Drag to reorder', 'wpqbui' ); ?>"></span>
		<select name="query_args[orderby][<?php echo (int) $i; ?>][ob]" class="wpqbui-orderby-select">
			<?php foreach ( $orderby_options as $ob ) : ?>
				<option value="<?php echo esc_attr( $ob ); ?>" <?php selected( $row['orderby'], $ob ); ?>><?php echo esc_html( $ob ); ?></option>
			<?php endforeach; ?>
		</select>
		<select name="query_args[orderby][<?php echo (int) $i; ?>][order]" class="wpqbui-order-select">
			<option value="DESC" <?php selected( $row['order'], 'DESC' ); ?>>DESC</option>
			<option value="ASC"  <?php selected( $row['order'], 'ASC' ); ?>>ASC</option>
		</select>
		<input type="text" name="query_args[orderby][<?php echo (int) $i; ?>][meta_key]"
			class="wpqbui-orderby-meta-key regular-text"
			placeholder="<?php esc_attr_e( 'meta_key (required for meta_value/meta_value_num)', 'wpqbui' ); ?>"
			value="<?php echo esc_attr( $qa['meta_key'] ?? '' ); ?>"
			<?php echo in_array( $row['orderby'], array( 'meta_value', 'meta_value_num' ), true ) ? '' : 'hidden'; ?>>
		<button type="button" class="button wpqbui-remove-row <?php echo count( $orderby_rows ) <= 1 ? 'disabled' : ''; ?>">
			<?php esc_html_e( 'Remove', 'wpqbui' ); ?>
		</button>
	</div>
	<?php endforeach; ?>
</div>

<button type="button" class="button wpqbui-add-orderby-row">
	+ <?php esc_html_e( 'Add Orderby Clause', 'wpqbui' ); ?>
</button>

<script type="text/html" id="wpqbui-orderby-row-template">
<div class="wpqbui-repeater-row wpqbui-orderby-row" data-index="__INDEX__">
	<span class="wpqbui-drag-handle dashicons dashicons-move"></span>
	<select name="query_args[orderby][__INDEX__][ob]" class="wpqbui-orderby-select">
		<?php foreach ( $orderby_options as $ob ) : ?>
			<option value="<?php echo esc_attr( $ob ); ?>"><?php echo esc_html( $ob ); ?></option>
		<?php endforeach; ?>
	</select>
	<select name="query_args[orderby][__INDEX__][order]" class="wpqbui-order-select">
		<option value="DESC">DESC</option>
		<option value="ASC">ASC</option>
	</select>
	<input type="text" name="query_args[orderby][__INDEX__][meta_key]"
		class="wpqbui-orderby-meta-key regular-text"
		placeholder="<?php esc_attr_e( 'meta_key (required for meta_value/meta_value_num)', 'wpqbui' ); ?>"
		value="" hidden>
	<button type="button" class="button wpqbui-remove-row"><?php esc_html_e( 'Remove', 'wpqbui' ); ?></button>
</div>
</script>
