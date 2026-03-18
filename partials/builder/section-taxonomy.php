<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var array $query_args */
$qa         = $query_args;
$tax_query  = $qa['tax_query'] ?? array();
$relation   = is_array( $tax_query ) && isset( $tax_query['relation'] ) ? $tax_query['relation'] : 'AND';
$rows       = array_filter( is_array( $tax_query ) ? $tax_query : array(), 'is_array' );
?>
<h3><?php esc_html_e( 'Taxonomy Query', 'wpqbui' ); ?></h3>
<p class="description"><?php esc_html_e( 'Build tax_query clauses. Each row is one clause. Nested relations are not supported in this UI.', 'wpqbui' ); ?></p>

<div class="wpqbui-relation-row">
	<label><strong><?php esc_html_e( 'Relation between clauses:', 'wpqbui' ); ?></strong></label>
	<label class="wpqbui-radio-label">
		<input type="radio" name="query_args[tax_query][relation]" value="AND" <?php checked( $relation, 'AND' ); ?>>
		AND
	</label>
	<label class="wpqbui-radio-label">
		<input type="radio" name="query_args[tax_query][relation]" value="OR" <?php checked( $relation, 'OR' ); ?>>
		OR
	</label>
</div>

<?php
// On a new query (no saved rows), add one default row pre-set to category.
if ( empty( $rows ) ) {
	$rows = array(
		0 => array(
			'taxonomy'         => 'category',
			'field'            => 'term_id',
			'terms'            => array(),
			'operator'         => 'IN',
			'include_children' => true,
		),
	);
}
?>
<div id="wpqbui-tax-rows" class="wpqbui-repeater">
	<?php foreach ( $rows as $i => $row ) : ?>
	<div class="wpqbui-repeater-row wpqbui-tax-row" data-index="<?php echo (int) $i; ?>">
		<?php include WPQBUI_DIR . 'partials/builder/section-taxonomy-row.php'; ?>
	</div>
	<?php endforeach; ?>
</div>

<button type="button" class="button wpqbui-add-row" data-target="wpqbui-tax-rows" data-template="wpqbui-tax-row-template">
	+ <?php esc_html_e( 'Add Tax Clause', 'wpqbui' ); ?>
</button>

<!-- Row template -->
<script type="text/html" id="wpqbui-tax-row-template">
<div class="wpqbui-repeater-row wpqbui-tax-row" data-index="__INDEX__">
	<table class="wpqbui-row-table">
		<tr>
			<td><label><?php esc_html_e( 'Taxonomy', 'wpqbui' ); ?></label></td>
			<td>
				<select name="query_args[tax_query][rows][__INDEX__][taxonomy]" class="wpqbui-tax-taxonomy-select">
					<option value=""><?php esc_html_e( '— select taxonomy —', 'wpqbui' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Field', 'wpqbui' ); ?></label></td>
			<td>
				<select name="query_args[tax_query][rows][__INDEX__][field]" class="wpqbui-tax-field-select">
					<option value="term_id"><?php esc_html_e( 'term_id', 'wpqbui' ); ?></option>
					<option value="slug"><?php esc_html_e( 'slug', 'wpqbui' ); ?></option>
					<option value="name"><?php esc_html_e( 'name', 'wpqbui' ); ?></option>
					<option value="term_taxonomy_id"><?php esc_html_e( 'term_taxonomy_id', 'wpqbui' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Terms', 'wpqbui' ); ?></label></td>
			<td>
				<select name="query_args[tax_query][rows][__INDEX__][terms][]" class="wpqbui-tax-terms-select" multiple size="5">
					<option value=""><?php esc_html_e( '(select taxonomy first)', 'wpqbui' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Operator', 'wpqbui' ); ?></label></td>
			<td>
				<select name="query_args[tax_query][rows][__INDEX__][operator]" class="wpqbui-tax-operator-select">
					<option value="IN">IN</option>
					<option value="NOT IN">NOT IN</option>
					<option value="AND">AND</option>
					<option value="EXISTS">EXISTS</option>
					<option value="NOT EXISTS">NOT EXISTS</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Include Children', 'wpqbui' ); ?></label></td>
			<td>
				<label class="wpqbui-cb-label">
					<input type="checkbox" name="query_args[tax_query][rows][__INDEX__][include_children]" value="1" checked>
					<?php esc_html_e( 'Yes (for hierarchical taxonomies)', 'wpqbui' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<button type="button" class="button wpqbui-remove-row"><?php esc_html_e( 'Remove this clause', 'wpqbui' ); ?></button>
			</td>
		</tr>
	</table>
</div>
</script>
