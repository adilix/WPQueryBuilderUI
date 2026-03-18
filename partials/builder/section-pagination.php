<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var array $query_args */
$qa = $query_args;
?>
<h3><?php esc_html_e( 'Pagination', 'wpqbui' ); ?></h3>
<table class="form-table wpqbui-section-table" role="presentation">

	<tr>
		<th scope="row"><label for="wpqbui-posts-per-page"><?php esc_html_e( 'posts_per_page', 'wpqbui' ); ?></label></th>
		<td>
			<input type="number" id="wpqbui-posts-per-page" name="query_args[posts_per_page]"
				value="<?php echo esc_attr( $qa['posts_per_page'] ?? '' ); ?>"
				step="1" class="small-text"
				placeholder="<?php echo esc_attr( get_option( 'posts_per_page', 10 ) ); ?>">
			<p class="description"><?php esc_html_e( 'Number of posts per page. Use -1 to return all posts.', 'wpqbui' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="wpqbui-paged"><?php esc_html_e( 'paged', 'wpqbui' ); ?></label></th>
		<td>
			<input type="number" id="wpqbui-paged" name="query_args[paged]"
				value="<?php echo esc_attr( $qa['paged'] ?? '' ); ?>"
				min="1" step="1" class="small-text" placeholder="1">
			<p class="description"><?php esc_html_e( 'Page number. Use get_query_var("paged") in theme templates.', 'wpqbui' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="wpqbui-offset"><?php esc_html_e( 'offset', 'wpqbui' ); ?></label></th>
		<td>
			<input type="number" id="wpqbui-offset" name="query_args[offset]"
				value="<?php echo esc_attr( $qa['offset'] ?? '' ); ?>"
				min="0" step="1" class="small-text">
			<p class="description wpqbui-warning-text">
				&#9888; <?php esc_html_e( 'Setting "offset" disables WordPress built-in pagination. You must manage the offset manually in loops.', 'wpqbui' ); ?>
			</p>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php esc_html_e( 'nopaging', 'wpqbui' ); ?></th>
		<td>
			<label class="wpqbui-cb-label">
				<input type="checkbox" name="query_args[nopaging]" value="1"
					<?php checked( ! empty( $qa['nopaging'] ) ); ?>>
				<?php esc_html_e( 'Return all posts, disable pagination', 'wpqbui' ); ?>
			</label>
		</td>
	</tr>

</table>
