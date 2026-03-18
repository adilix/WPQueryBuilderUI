<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var array $query_args */
$qa         = $query_args;
$meta_query = $qa['meta_query'] ?? array();
$relation   = is_array( $meta_query ) && isset( $meta_query['relation'] ) ? $meta_query['relation'] : 'AND';
$rows       = array_values( array_filter( is_array( $meta_query ) ? $meta_query : array(), 'is_array' ) );

$compare_ops = array( '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS', 'NOT EXISTS', 'REGEXP', 'NOT REGEXP' );
$meta_types  = array( 'CHAR', 'NUMERIC', 'BINARY', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED' );
?>
<h3><?php esc_html_e( 'Meta Query', 'wpqbui' ); ?></h3>
<p class="description"><?php esc_html_e( 'Build meta_query clauses. Each row is one custom field condition.', 'wpqbui' ); ?></p>

<div class="wpqbui-relation-row">
	<label><strong><?php esc_html_e( 'Relation between clauses:', 'wpqbui' ); ?></strong></label>
	<label class="wpqbui-radio-label">
		<input type="radio" name="query_args[meta_query][relation]" value="AND" <?php checked( $relation, 'AND' ); ?>> AND
	</label>
	<label class="wpqbui-radio-label">
		<input type="radio" name="query_args[meta_query][relation]" value="OR" <?php checked( $relation, 'OR' ); ?>> OR
	</label>
</div>

<div id="wpqbui-meta-rows" class="wpqbui-repeater">
	<?php foreach ( $rows as $i => $row ) : ?>
	<div class="wpqbui-repeater-row wpqbui-meta-row" data-index="<?php echo (int) $i; ?>">
		<table class="wpqbui-row-table">
			<tr>
				<td><label><?php esc_html_e( 'Key (meta_key)', 'wpqbui' ); ?></label></td>
				<td>
					<input type="text" name="query_args[meta_query][rows][<?php echo (int) $i; ?>][key]"
						value="<?php echo esc_attr( $row['key'] ?? '' ); ?>"
						class="regular-text wpqbui-meta-key-input"
						list="wpqbui-meta-key-datalist"
						placeholder="<?php esc_attr_e( 'custom_field_name', 'wpqbui' ); ?>">
				</td>
			</tr>
			<tr>
				<td><label><?php esc_html_e( 'Compare', 'wpqbui' ); ?></label></td>
				<td>
					<select name="query_args[meta_query][rows][<?php echo (int) $i; ?>][compare]" class="wpqbui-meta-compare-select">
						<?php foreach ( $compare_ops as $op ) : ?>
							<option value="<?php echo esc_attr( $op ); ?>" <?php selected( ( $row['compare'] ?? '=' ), $op ); ?>>
								<?php echo esc_html( $op ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr class="wpqbui-meta-value-row">
				<td><label><?php esc_html_e( 'Value', 'wpqbui' ); ?></label></td>
				<td>
					<?php
					$val = $row['value'] ?? '';
					if ( is_array( $val ) ) {
						$val = implode( ', ', $val );
					}
					?>
					<input type="text" name="query_args[meta_query][rows][<?php echo (int) $i; ?>][value]"
						value="<?php echo esc_attr( $val ); ?>"
						class="regular-text wpqbui-meta-value-input"
						placeholder="<?php esc_attr_e( 'For IN/NOT IN/BETWEEN: comma-separated values', 'wpqbui' ); ?>">
				</td>
			</tr>
			<tr>
				<td><label><?php esc_html_e( 'Type', 'wpqbui' ); ?></label></td>
				<td>
					<select name="query_args[meta_query][rows][<?php echo (int) $i; ?>][type]" class="wpqbui-meta-type-select">
						<?php foreach ( $meta_types as $mt ) : ?>
							<option value="<?php echo esc_attr( $mt ); ?>" <?php selected( ( $row['type'] ?? 'CHAR' ), $mt ); ?>>
								<?php echo esc_html( $mt ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<button type="button" class="button wpqbui-remove-row"><?php esc_html_e( 'Remove', 'wpqbui' ); ?></button>
				</td>
			</tr>
		</table>
	</div>
	<?php endforeach; ?>
</div>

<!-- Meta key datalist (populated via AJAX) -->
<datalist id="wpqbui-meta-key-datalist"></datalist>

<button type="button" class="button wpqbui-add-meta-row">
	+ <?php esc_html_e( 'Add Meta Clause', 'wpqbui' ); ?>
</button>

<!-- Meta row template -->
<script type="text/html" id="wpqbui-meta-row-template">
<div class="wpqbui-repeater-row wpqbui-meta-row" data-index="__INDEX__">
	<table class="wpqbui-row-table">
		<tr>
			<td><label><?php esc_html_e( 'Key (meta_key)', 'wpqbui' ); ?></label></td>
			<td>
				<input type="text" name="query_args[meta_query][rows][__INDEX__][key]"
					value="" class="regular-text wpqbui-meta-key-input"
					list="wpqbui-meta-key-datalist"
					placeholder="<?php esc_attr_e( 'custom_field_name', 'wpqbui' ); ?>">
			</td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Compare', 'wpqbui' ); ?></label></td>
			<td>
				<select name="query_args[meta_query][rows][__INDEX__][compare]" class="wpqbui-meta-compare-select">
					<?php foreach ( $compare_ops as $op ) : ?>
						<option value="<?php echo esc_attr( $op ); ?>"><?php echo esc_html( $op ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr class="wpqbui-meta-value-row">
			<td><label><?php esc_html_e( 'Value', 'wpqbui' ); ?></label></td>
			<td>
				<input type="text" name="query_args[meta_query][rows][__INDEX__][value]"
					value="" class="regular-text wpqbui-meta-value-input"
					placeholder="<?php esc_attr_e( 'For IN/NOT IN/BETWEEN: comma-separated values', 'wpqbui' ); ?>">
			</td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Type', 'wpqbui' ); ?></label></td>
			<td>
				<select name="query_args[meta_query][rows][__INDEX__][type]" class="wpqbui-meta-type-select">
					<?php foreach ( $meta_types as $mt ) : ?>
						<option value="<?php echo esc_attr( $mt ); ?>"><?php echo esc_html( $mt ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<button type="button" class="button wpqbui-remove-row"><?php esc_html_e( 'Remove', 'wpqbui' ); ?></button>
			</td>
		</tr>
	</table>
</div>
</script>
