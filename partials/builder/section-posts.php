<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var array $query_args */
$qa = $query_args;
?>
<h3><?php esc_html_e( 'Post ID Parameters', 'wpqbui' ); ?></h3>
<table class="form-table wpqbui-section-table" role="presentation">

	<tr>
		<th scope="row"><label for="wpqbui-p"><?php esc_html_e( 'p (single post ID)', 'wpqbui' ); ?></label></th>
		<td>
			<input type="number" id="wpqbui-p" name="query_args[p]"
				value="<?php echo esc_attr( $qa['p'] ?? '' ); ?>" min="1" step="1" class="small-text">
			<p class="description"><?php esc_html_e( 'Return only this specific post by ID.', 'wpqbui' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label><?php esc_html_e( 'post__in', 'wpqbui' ); ?></label></th>
		<td>
			<div class="wpqbui-post-picker" data-field="post__in">
				<input type="text" class="wpqbui-post-search regular-text"
					placeholder="<?php esc_attr_e( 'Search posts to include…', 'wpqbui' ); ?>">
				<div class="wpqbui-dropdown wpqbui-post-results" hidden></div>
				<div class="wpqbui-tags"></div>
				<?php
				$post_in = isset( $qa['post__in'] ) ? (array) $qa['post__in'] : array();
				foreach ( $post_in as $pid ) :
					?>
					<input type="hidden" name="query_args[post__in][]" value="<?php echo absint( $pid ); ?>">
				<?php endforeach; ?>
			</div>
			<p class="description"><?php esc_html_e( 'Include only these post IDs.', 'wpqbui' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label><?php esc_html_e( 'post__not_in', 'wpqbui' ); ?></label></th>
		<td>
			<div class="wpqbui-post-picker" data-field="post__not_in">
				<input type="text" class="wpqbui-post-search regular-text"
					placeholder="<?php esc_attr_e( 'Search posts to exclude…', 'wpqbui' ); ?>">
				<div class="wpqbui-dropdown wpqbui-post-results" hidden></div>
				<div class="wpqbui-tags"></div>
				<?php
				$post_not_in = isset( $qa['post__not_in'] ) ? (array) $qa['post__not_in'] : array();
				foreach ( $post_not_in as $pid ) :
					?>
					<input type="hidden" name="query_args[post__not_in][]" value="<?php echo absint( $pid ); ?>">
				<?php endforeach; ?>
			</div>
			<p class="description"><?php esc_html_e( 'Exclude these post IDs.', 'wpqbui' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="wpqbui-post-name-in"><?php esc_html_e( 'post_name__in', 'wpqbui' ); ?></label></th>
		<td>
			<textarea id="wpqbui-post-name-in" name="query_args[post_name__in]"
				class="large-text" rows="3"
				placeholder="<?php esc_attr_e( 'One slug per line', 'wpqbui' ); ?>"><?php
				$slugs = isset( $qa['post_name__in'] ) ? (array) $qa['post_name__in'] : array();
				echo esc_textarea( implode( "\n", $slugs ) );
			?></textarea>
			<p class="description"><?php esc_html_e( 'Include posts with these slugs (post_name). One per line.', 'wpqbui' ); ?></p>
		</td>
	</tr>

</table>
