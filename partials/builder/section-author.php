<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var array $query_args */
$qa = $query_args;
?>
<h3><?php esc_html_e( 'Author Parameters', 'wpqbui' ); ?></h3>
<table class="form-table wpqbui-section-table" role="presentation">

	<tr>
		<th scope="row"><label for="wpqbui-author"><?php esc_html_e( 'author', 'wpqbui' ); ?></label></th>
		<td>
			<input type="text" id="wpqbui-author" name="query_args[author]"
				value="<?php echo esc_attr( $qa['author'] ?? '' ); ?>" class="regular-text"
				placeholder="<?php esc_attr_e( 'User ID, or comma-separated IDs (prefix – to exclude)', 'wpqbui' ); ?>">
			<p class="description"><?php esc_html_e( 'Single author ID, or comma-separated IDs. Prefix an ID with a minus sign to exclude that author.', 'wpqbui' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="wpqbui-author-name"><?php esc_html_e( 'author_name', 'wpqbui' ); ?></label></th>
		<td>
			<input type="text" id="wpqbui-author-name" name="query_args[author_name]"
				value="<?php echo esc_attr( $qa['author_name'] ?? '' ); ?>" class="regular-text"
				placeholder="<?php esc_attr_e( 'user_nicename (not login name)', 'wpqbui' ); ?>">
		</td>
	</tr>

	<tr>
		<th scope="row"><label><?php esc_html_e( 'author__in', 'wpqbui' ); ?></label></th>
		<td>
			<div class="wpqbui-author-picker" data-field="author__in">
				<input type="text" class="wpqbui-author-search regular-text"
					placeholder="<?php esc_attr_e( 'Search authors…', 'wpqbui' ); ?>">
				<div class="wpqbui-dropdown wpqbui-author-results" hidden></div>
				<div class="wpqbui-tags" id="wpqbui-author-in-tags"></div>
				<?php
				$author_in = isset( $qa['author__in'] ) ? (array) $qa['author__in'] : array();
				foreach ( $author_in as $uid ) :
					?>
					<input type="hidden" name="query_args[author__in][]" value="<?php echo absint( $uid ); ?>">
				<?php endforeach; ?>
			</div>
			<p class="description"><?php esc_html_e( 'Include posts by these author IDs.', 'wpqbui' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label><?php esc_html_e( 'author__not_in', 'wpqbui' ); ?></label></th>
		<td>
			<div class="wpqbui-author-picker" data-field="author__not_in">
				<input type="text" class="wpqbui-author-search regular-text"
					placeholder="<?php esc_attr_e( 'Search authors…', 'wpqbui' ); ?>">
				<div class="wpqbui-dropdown wpqbui-author-results" hidden></div>
				<div class="wpqbui-tags" id="wpqbui-author-not-in-tags"></div>
				<?php
				$author_not_in = isset( $qa['author__not_in'] ) ? (array) $qa['author__not_in'] : array();
				foreach ( $author_not_in as $uid ) :
					?>
					<input type="hidden" name="query_args[author__not_in][]" value="<?php echo absint( $uid ); ?>">
				<?php endforeach; ?>
			</div>
			<p class="description"><?php esc_html_e( 'Exclude posts by these author IDs.', 'wpqbui' ); ?></p>
		</td>
	</tr>

</table>
