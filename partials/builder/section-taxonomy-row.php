<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var array $row  Single tax_query clause */
/** @var int   $i    Row index */
$operators     = array( 'IN', 'NOT IN', 'AND', 'EXISTS', 'NOT EXISTS' );
$field_options = array( 'term_id', 'slug', 'name', 'term_taxonomy_id' );
?>
<table class="wpqbui-row-table">
	<tr>
		<td><label><?php esc_html_e( 'Taxonomy', 'wpqbui' ); ?></label></td>
		<td>
			<select name="query_args[tax_query][rows][<?php echo (int) $i; ?>][taxonomy]" class="wpqbui-tax-taxonomy-select"
					data-selected="<?php echo esc_attr( $row['taxonomy'] ?? '' ); ?>">
				<option value=""><?php esc_html_e( '— select taxonomy —', 'wpqbui' ); ?></option>
				<?php if ( ! empty( $row['taxonomy'] ) ) : ?>
					<option value="<?php echo esc_attr( $row['taxonomy'] ); ?>" selected>
						<?php echo esc_html( $row['taxonomy'] ); ?>
					</option>
				<?php endif; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><label><?php esc_html_e( 'Field', 'wpqbui' ); ?></label></td>
		<td>
			<select name="query_args[tax_query][rows][<?php echo (int) $i; ?>][field]" class="wpqbui-tax-field-select">
				<?php foreach ( $field_options as $fo ) : ?>
					<option value="<?php echo esc_attr( $fo ); ?>" <?php selected( ( $row['field'] ?? 'term_id' ), $fo ); ?>>
						<?php echo esc_html( $fo ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><label><?php esc_html_e( 'Terms', 'wpqbui' ); ?></label></td>
		<td>
			<select name="query_args[tax_query][rows][<?php echo (int) $i; ?>][terms][]" class="wpqbui-tax-terms-select" multiple size="5">
				<?php
				$selected_terms = isset( $row['terms'] ) ? (array) $row['terms'] : array();
				foreach ( $selected_terms as $t ) :
					?>
					<option value="<?php echo esc_attr( $t ); ?>" selected><?php echo esc_html( $t ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><label><?php esc_html_e( 'Operator', 'wpqbui' ); ?></label></td>
		<td>
			<select name="query_args[tax_query][rows][<?php echo (int) $i; ?>][operator]" class="wpqbui-tax-operator-select">
				<?php foreach ( $operators as $op ) : ?>
					<option value="<?php echo esc_attr( $op ); ?>" <?php selected( ( $row['operator'] ?? 'IN' ), $op ); ?>>
						<?php echo esc_html( $op ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php esc_html_e( 'Include Children', 'wpqbui' ); ?></td>
		<td>
			<label class="wpqbui-cb-label">
				<input type="checkbox" name="query_args[tax_query][rows][<?php echo (int) $i; ?>][include_children]" value="1"
					<?php checked( $row['include_children'] ?? true ); ?>>
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
