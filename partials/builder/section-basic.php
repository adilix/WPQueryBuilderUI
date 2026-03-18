<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var array $query_args */
$qa = $query_args;
?>
<h3><?php esc_html_e( 'Basic Parameters', 'wpqbui' ); ?></h3>
<table class="form-table wpqbui-section-table" role="presentation">

	<tr>
		<th scope="row"><label><?php esc_html_e( 'Post Type', 'wpqbui' ); ?></label></th>
		<td>
			<?php
			// Default to 'post' for new queries; preserve saved selection on edit.
			$selected_post_types = isset( $qa['post_type'] )
				? (array) $qa['post_type']
				: array( 'post' );
			$known_defaults = array(
				'any'        => __( 'Any (all post types)', 'wpqbui' ),
				'post'       => __( 'Post (post)', 'wpqbui' ),
				'page'       => __( 'Page (page)', 'wpqbui' ),
				'attachment' => __( 'Attachment (attachment)', 'wpqbui' ),
			);
			?>
			<select name="query_args[post_type][]" id="wpqbui-post-type" multiple size="6" class="wpqbui-multiselect"
				data-selected="<?php echo esc_attr( implode( ',', $selected_post_types ) ); ?>">
				<?php foreach ( $selected_post_types as $pt ) : ?>
					<option value="<?php echo esc_attr( $pt ); ?>" selected>
						<?php echo esc_html( $known_defaults[ $pt ] ?? $pt ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<p class="description"><?php esc_html_e( 'Select one or more post types, or "any" to include all.', 'wpqbui' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label><?php esc_html_e( 'Post Status', 'wpqbui' ); ?></label></th>
		<td>
			<div class="wpqbui-checkbox-group" id="wpqbui-post-status">
				<?php
				$statuses = array(
					'publish'    => __( 'Published', 'wpqbui' ),
					'pending'    => __( 'Pending Review', 'wpqbui' ),
					'draft'      => __( 'Draft', 'wpqbui' ),
					'auto-draft' => __( 'Auto Draft', 'wpqbui' ),
					'future'     => __( 'Scheduled', 'wpqbui' ),
					'private'    => __( 'Private', 'wpqbui' ),
					'inherit'    => __( 'Inherit', 'wpqbui' ),
					'trash'      => __( 'Trash', 'wpqbui' ),
					'any'        => __( 'Any', 'wpqbui' ),
				);
				$selected_statuses = isset( $qa['post_status'] ) ? (array) $qa['post_status'] : array( 'publish' );
				foreach ( $statuses as $slug => $label ) :
					?>
					<label class="wpqbui-cb-label">
						<input type="checkbox" name="query_args[post_status][]"
							value="<?php echo esc_attr( $slug ); ?>"
							<?php checked( in_array( $slug, $selected_statuses, true ) ); ?>>
						<?php echo esc_html( $label ); ?>
					</label>
				<?php endforeach; ?>
			</div>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php esc_html_e( 'Password', 'wpqbui' ); ?></th>
		<td>
			<div class="wpqbui-field-group">
				<label class="wpqbui-cb-label">
					<input type="checkbox" id="wpqbui-has-password" name="query_args[has_password]" value="1"
						<?php checked( isset( $qa['has_password'] ) && true === $qa['has_password'] ); ?>>
					<?php esc_html_e( 'has_password — only return password-protected posts', 'wpqbui' ); ?>
				</label>
				<br>
				<label for="wpqbui-post-password"><?php esc_html_e( 'post_password — exact password:', 'wpqbui' ); ?></label>
				<input type="text" id="wpqbui-post-password" name="query_args[post_password]"
					value="<?php echo esc_attr( $qa['post_password'] ?? '' ); ?>" class="regular-text"
					placeholder="<?php esc_attr_e( 'Leave blank to ignore', 'wpqbui' ); ?>">
			</div>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="wpqbui-fields"><?php esc_html_e( 'Return Fields', 'wpqbui' ); ?></label></th>
		<td>
			<select name="query_args[fields]" id="wpqbui-fields">
				<option value="" <?php selected( empty( $qa['fields'] ) ); ?>><?php esc_html_e( 'Full post objects (default)', 'wpqbui' ); ?></option>
				<option value="ids" <?php selected( $qa['fields'] ?? '', 'ids' ); ?>><?php esc_html_e( 'IDs only', 'wpqbui' ); ?></option>
				<option value="id=>parent" <?php selected( $qa['fields'] ?? '', 'id=>parent' ); ?>><?php esc_html_e( 'ID => Parent (id=>parent)', 'wpqbui' ); ?></option>
			</select>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php esc_html_e( 'Performance', 'wpqbui' ); ?></th>
		<td>
			<label class="wpqbui-cb-label">
				<input type="checkbox" name="query_args[no_found_rows]" value="1"
					<?php checked( ! empty( $qa['no_found_rows'] ) ); ?>>
				<?php esc_html_e( 'no_found_rows — skip counting total rows (faster, disables pagination)', 'wpqbui' ); ?>
			</label>
			<br>
			<label class="wpqbui-cb-label">
				<input type="checkbox" name="query_args[suppress_filters]" value="1"
					<?php checked( ! empty( $qa['suppress_filters'] ) ); ?>>
				<strong><?php esc_html_e( 'suppress_filters', 'wpqbui' ); ?></strong>
				— <?php esc_html_e( 'suppress SQL filters (use with caution)', 'wpqbui' ); ?>
			</label>
		</td>
	</tr>

</table>
