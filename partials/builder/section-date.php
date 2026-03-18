<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var array $query_args */
$qa         = $query_args;
$date_query = $qa['date_query'] ?? array();
$relation   = is_array( $date_query ) && isset( $date_query['relation'] ) ? $date_query['relation'] : 'AND';
$rows       = array_values( array_filter( is_array( $date_query ) ? $date_query : array(), 'is_array' ) );

$compare_ops      = array( '=', '!=', '>', '>=', '<', '<=', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' );
$column_options   = array(
	'post_date'          => 'post_date',
	'post_date_gmt'      => 'post_date_gmt',
	'post_modified'      => 'post_modified',
	'post_modified_gmt'  => 'post_modified_gmt',
	'comment_date'       => 'comment_date',
	'comment_date_gmt'   => 'comment_date_gmt',
);
?>
<h3><?php esc_html_e( 'Date Query', 'wpqbui' ); ?></h3>
<p class="description"><?php esc_html_e( 'Filter posts by date. Each row is one date clause.', 'wpqbui' ); ?></p>

<div class="wpqbui-relation-row">
	<label><strong><?php esc_html_e( 'Relation between clauses:', 'wpqbui' ); ?></strong></label>
	<label class="wpqbui-radio-label">
		<input type="radio" name="query_args[date_query][relation]" value="AND" <?php checked( $relation, 'AND' ); ?>> AND
	</label>
	<label class="wpqbui-radio-label">
		<input type="radio" name="query_args[date_query][relation]" value="OR" <?php checked( $relation, 'OR' ); ?>> OR
	</label>
</div>

<div id="wpqbui-date-rows" class="wpqbui-repeater">
	<?php foreach ( $rows as $i => $row ) : ?>
	<div class="wpqbui-repeater-row wpqbui-date-row" data-index="<?php echo (int) $i; ?>">
		<table class="wpqbui-row-table">
			<tr>
				<td><label><?php esc_html_e( 'Year', 'wpqbui' ); ?></label></td>
				<td><input type="number" name="query_args[date_query][rows][<?php echo (int) $i; ?>][year]" value="<?php echo esc_attr( $row['year'] ?? '' ); ?>" min="1970" max="2100" class="small-text" placeholder="<?php esc_attr_e( 'e.g. 2024', 'wpqbui' ); ?>"></td>
				<td><label><?php esc_html_e( 'Month (1-12)', 'wpqbui' ); ?></label></td>
				<td><input type="number" name="query_args[date_query][rows][<?php echo (int) $i; ?>][month]" value="<?php echo esc_attr( $row['month'] ?? '' ); ?>" min="1" max="12" class="small-text"></td>
				<td><label><?php esc_html_e( 'Day (1-31)', 'wpqbui' ); ?></label></td>
				<td><input type="number" name="query_args[date_query][rows][<?php echo (int) $i; ?>][day]" value="<?php echo esc_attr( $row['day'] ?? '' ); ?>" min="1" max="31" class="small-text"></td>
			</tr>
			<tr>
				<td><label><?php esc_html_e( 'Hour (0-23)', 'wpqbui' ); ?></label></td>
				<td><input type="number" name="query_args[date_query][rows][<?php echo (int) $i; ?>][hour]" value="<?php echo esc_attr( $row['hour'] ?? '' ); ?>" min="0" max="23" class="small-text"></td>
				<td><label><?php esc_html_e( 'Minute (0-60)', 'wpqbui' ); ?></label></td>
				<td><input type="number" name="query_args[date_query][rows][<?php echo (int) $i; ?>][minute]" value="<?php echo esc_attr( $row['minute'] ?? '' ); ?>" min="0" max="60" class="small-text"></td>
				<td><label><?php esc_html_e( 'Second (0-60)', 'wpqbui' ); ?></label></td>
				<td><input type="number" name="query_args[date_query][rows][<?php echo (int) $i; ?>][second]" value="<?php echo esc_attr( $row['second'] ?? '' ); ?>" min="0" max="60" class="small-text"></td>
			</tr>
			<tr>
				<td colspan="2">
					<label><?php esc_html_e( 'After (date, inclusive)', 'wpqbui' ); ?></label>
					<input type="date" name="query_args[date_query][rows][<?php echo (int) $i; ?>][_after_date]"
						value="<?php echo esc_attr( isset( $row['after'] ) ? sprintf( '%04d-%02d-%02d', $row['after']['year'] ?? 0, $row['after']['month'] ?? 1, $row['after']['day'] ?? 1 ) : '' ); ?>"
						class="wpqbui-date-after">
				</td>
				<td colspan="2">
					<label><?php esc_html_e( 'Before (date, inclusive)', 'wpqbui' ); ?></label>
					<input type="date" name="query_args[date_query][rows][<?php echo (int) $i; ?>][_before_date]"
						value="<?php echo esc_attr( isset( $row['before'] ) ? sprintf( '%04d-%02d-%02d', $row['before']['year'] ?? 0, $row['before']['month'] ?? 1, $row['before']['day'] ?? 1 ) : '' ); ?>"
						class="wpqbui-date-before">
				</td>
				<td colspan="2">
					<label class="wpqbui-cb-label">
						<input type="checkbox" name="query_args[date_query][rows][<?php echo (int) $i; ?>][inclusive]" value="1" <?php checked( $row['inclusive'] ?? false ); ?>>
						<?php esc_html_e( 'inclusive', 'wpqbui' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td><label><?php esc_html_e( 'Compare', 'wpqbui' ); ?></label></td>
				<td>
					<select name="query_args[date_query][rows][<?php echo (int) $i; ?>][compare]">
						<?php foreach ( $compare_ops as $op ) : ?>
							<option value="<?php echo esc_attr( $op ); ?>" <?php selected( ( $row['compare'] ?? '=' ), $op ); ?>><?php echo esc_html( $op ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td><label><?php esc_html_e( 'Column', 'wpqbui' ); ?></label></td>
				<td colspan="3">
					<select name="query_args[date_query][rows][<?php echo (int) $i; ?>][column]">
						<option value=""><?php esc_html_e( '— default (post_date) —', 'wpqbui' ); ?></option>
						<?php foreach ( $column_options as $val => $label ) : ?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( ( $row['column'] ?? '' ), $val ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<button type="button" class="button wpqbui-remove-row"><?php esc_html_e( 'Remove', 'wpqbui' ); ?></button>
				</td>
			</tr>
		</table>
	</div>
	<?php endforeach; ?>
</div>

<button type="button" class="button wpqbui-add-date-row">
	+ <?php esc_html_e( 'Add Date Clause', 'wpqbui' ); ?>
</button>

<script type="text/html" id="wpqbui-date-row-template">
<div class="wpqbui-repeater-row wpqbui-date-row" data-index="__INDEX__">
	<table class="wpqbui-row-table">
		<tr>
			<td><label><?php esc_html_e( 'Year', 'wpqbui' ); ?></label></td>
			<td><input type="number" name="query_args[date_query][rows][__INDEX__][year]" value="" min="1970" max="2100" class="small-text" placeholder="<?php esc_attr_e( 'e.g. 2024', 'wpqbui' ); ?>"></td>
			<td><label><?php esc_html_e( 'Month (1-12)', 'wpqbui' ); ?></label></td>
			<td><input type="number" name="query_args[date_query][rows][__INDEX__][month]" value="" min="1" max="12" class="small-text"></td>
			<td><label><?php esc_html_e( 'Day (1-31)', 'wpqbui' ); ?></label></td>
			<td><input type="number" name="query_args[date_query][rows][__INDEX__][day]" value="" min="1" max="31" class="small-text"></td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Hour (0-23)', 'wpqbui' ); ?></label></td>
			<td><input type="number" name="query_args[date_query][rows][__INDEX__][hour]" value="" min="0" max="23" class="small-text"></td>
			<td><label><?php esc_html_e( 'Minute (0-60)', 'wpqbui' ); ?></label></td>
			<td><input type="number" name="query_args[date_query][rows][__INDEX__][minute]" value="" min="0" max="60" class="small-text"></td>
			<td><label><?php esc_html_e( 'Second (0-60)', 'wpqbui' ); ?></label></td>
			<td><input type="number" name="query_args[date_query][rows][__INDEX__][second]" value="" min="0" max="60" class="small-text"></td>
		</tr>
		<tr>
			<td colspan="2">
				<label><?php esc_html_e( 'After', 'wpqbui' ); ?></label>
				<input type="date" name="query_args[date_query][rows][__INDEX__][_after_date]" value="" class="wpqbui-date-after">
			</td>
			<td colspan="2">
				<label><?php esc_html_e( 'Before', 'wpqbui' ); ?></label>
				<input type="date" name="query_args[date_query][rows][__INDEX__][_before_date]" value="" class="wpqbui-date-before">
			</td>
			<td colspan="2">
				<label class="wpqbui-cb-label">
					<input type="checkbox" name="query_args[date_query][rows][__INDEX__][inclusive]" value="1">
					<?php esc_html_e( 'inclusive', 'wpqbui' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Compare', 'wpqbui' ); ?></label></td>
			<td>
				<select name="query_args[date_query][rows][__INDEX__][compare]">
					<?php foreach ( $compare_ops as $op ) : ?>
						<option value="<?php echo esc_attr( $op ); ?>"><?php echo esc_html( $op ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
			<td><label><?php esc_html_e( 'Column', 'wpqbui' ); ?></label></td>
			<td colspan="3">
				<select name="query_args[date_query][rows][__INDEX__][column]">
					<option value=""><?php esc_html_e( '— default (post_date) —', 'wpqbui' ); ?></option>
					<?php foreach ( $column_options as $val => $label ) : ?>
						<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="6">
				<button type="button" class="button wpqbui-remove-row"><?php esc_html_e( 'Remove', 'wpqbui' ); ?></button>
			</td>
		</tr>
	</table>
</div>
</script>
