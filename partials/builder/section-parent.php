<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var array $query_args */
$qa = $query_args;
?>
<h3><?php esc_html_e( 'Parent Parameters', 'wpqbui' ); ?></h3>
<table class="form-table wpqbui-section-table" role="presentation">

	<tr>
		<th scope="row"><label for="wpqbui-post-parent"><?php esc_html_e( 'post_parent', 'wpqbui' ); ?></label></th>
		<td>
			<input type="number" id="wpqbui-post-parent" name="query_args[post_parent]"
				value="<?php echo esc_attr( $qa['post_parent'] ?? '' ); ?>" min="0" step="1" class="small-text">
			<p class="description"><?php esc_html_e( 'Return only children of this post ID. Use 0 for top-level posts.', 'wpqbui' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="wpqbui-post-parent-in"><?php esc_html_e( 'post_parent__in', 'wpqbui' ); ?></label></th>
		<td>
			<input type="text" id="wpqbui-post-parent-in" name="query_args[post_parent__in]"
				value="<?php echo esc_attr( isset( $qa['post_parent__in'] ) ? implode( ',', (array) $qa['post_parent__in'] ) : '' ); ?>"
				class="regular-text"
				placeholder="<?php esc_attr_e( '1,2,3 — comma-separated IDs', 'wpqbui' ); ?>">
			<p class="description"><?php esc_html_e( 'Return children of these post IDs.', 'wpqbui' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="wpqbui-post-parent-not-in"><?php esc_html_e( 'post_parent__not_in', 'wpqbui' ); ?></label></th>
		<td>
			<input type="text" id="wpqbui-post-parent-not-in" name="query_args[post_parent__not_in]"
				value="<?php echo esc_attr( isset( $qa['post_parent__not_in'] ) ? implode( ',', (array) $qa['post_parent__not_in'] ) : '' ); ?>"
				class="regular-text"
				placeholder="<?php esc_attr_e( '1,2,3 — comma-separated IDs', 'wpqbui' ); ?>">
			<p class="description"><?php esc_html_e( 'Exclude children of these post IDs.', 'wpqbui' ); ?></p>
		</td>
	</tr>

</table>
