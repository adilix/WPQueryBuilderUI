<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var array $query_args */
$qa = $query_args;
$search_columns = isset( $qa['search_columns'] ) ? (array) $qa['search_columns'] : array();
?>
<h3><?php esc_html_e( 'Search', 'wpqbui' ); ?></h3>
<table class="form-table wpqbui-section-table" role="presentation">

	<tr>
		<th scope="row"><label for="wpqbui-s"><?php esc_html_e( 's (search term)', 'wpqbui' ); ?></label></th>
		<td>
			<input type="text" id="wpqbui-s" name="query_args[s]"
				value="<?php echo esc_attr( $qa['s'] ?? '' ); ?>" class="regular-text"
				placeholder="<?php esc_attr_e( 'Search keyword', 'wpqbui' ); ?>">
		</td>
	</tr>

	<tr>
		<th scope="row"><?php esc_html_e( 'Search Mode', 'wpqbui' ); ?></th>
		<td>
			<label class="wpqbui-cb-label">
				<input type="checkbox" name="query_args[sentence]" value="1"
					<?php checked( ! empty( $qa['sentence'] ) ); ?>>
				<?php esc_html_e( 'sentence — treat as exact phrase (no split into individual words)', 'wpqbui' ); ?>
			</label>
			<br>
			<label class="wpqbui-cb-label">
				<input type="checkbox" name="query_args[exact]" value="1"
					<?php checked( ! empty( $qa['exact'] ) ); ?>>
				<?php esc_html_e( 'exact — require exact keyword match (no partial matches)', 'wpqbui' ); ?>
			</label>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php esc_html_e( 'search_columns', 'wpqbui' ); ?></th>
		<td>
			<?php
			$col_options = array(
				'post_title'   => __( 'Post Title', 'wpqbui' ),
				'post_excerpt' => __( 'Post Excerpt', 'wpqbui' ),
				'post_content' => __( 'Post Content', 'wpqbui' ),
			);
			foreach ( $col_options as $col => $col_label ) :
				?>
				<label class="wpqbui-cb-label">
					<input type="checkbox" name="query_args[search_columns][]"
						value="<?php echo esc_attr( $col ); ?>"
						<?php checked( in_array( $col, $search_columns, true ) ); ?>>
					<?php echo esc_html( $col_label ); ?>
				</label>
			<?php endforeach; ?>
			<p class="description"><?php esc_html_e( 'Which columns to search. Leave all unchecked for default (title + excerpt + content).', 'wpqbui' ); ?></p>
		</td>
	</tr>

</table>
