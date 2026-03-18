<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$repo = new WPQBUI_Query_Repository();
$paged = max( 1, absint( $_GET['paged'] ?? 1 ) ); // phpcs:ignore WordPress.Security.NonceVerification
$search = sanitize_text_field( $_GET['s'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification
$data  = $repo->find_all( array( 'per_page' => 20, 'page' => $paged, 'search' => $search ) );

/** @var WPQBUI_Query_Definition[] $queries */
$queries    = $data['items'];
$total      = $data['total'];
$total_pages = max( 1, (int) ceil( $total / 20 ) );
?>
<div class="wrap wpqbui-wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Saved Queries', 'wpqbui' ); ?></h1>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpqbui-builder' ) ); ?>" class="page-title-action">
		<?php esc_html_e( 'New Query', 'wpqbui' ); ?>
	</a>
	<hr class="wp-header-end">

	<!-- Search -->
	<form method="get" action="">
		<input type="hidden" name="page" value="wpqbui-saved">
		<p class="search-box">
			<label class="screen-reader-text" for="wpqbui-search"><?php esc_html_e( 'Search Queries', 'wpqbui' ); ?></label>
			<input type="search" id="wpqbui-search" name="s" value="<?php echo esc_attr( $search ); ?>">
			<input type="submit" class="button" value="<?php esc_attr_e( 'Search', 'wpqbui' ); ?>">
		</p>
	</form>

	<?php if ( empty( $queries ) ) : ?>
		<p><?php esc_html_e( 'No saved queries found.', 'wpqbui' ); ?> <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpqbui-builder' ) ); ?>"><?php esc_html_e( 'Create one now.', 'wpqbui' ); ?></a></p>
	<?php else : ?>
	<form method="post" id="wpqbui-bulk-form">
		<?php wp_nonce_field( 'wpqbui_bulk_action', 'wpqbui_bulk_nonce' ); ?>
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<select name="wpqbui_bulk_action" id="wpqbui-bulk-action">
					<option value=""><?php esc_html_e( 'Bulk Actions', 'wpqbui' ); ?></option>
					<option value="delete"><?php esc_html_e( 'Delete', 'wpqbui' ); ?></option>
				</select>
				<input type="submit" class="button action" value="<?php esc_attr_e( 'Apply', 'wpqbui' ); ?>">
			</div>
			<div class="tablenav-pages">
				<span class="displaying-num">
					<?php printf( esc_html( _n( '%s query', '%s queries', $total, 'wpqbui' ) ), number_format_i18n( $total ) ); ?>
				</span>
			</div>
		</div>

		<table class="wp-list-table widefat fixed striped wpqbui-saved-table">
			<thead>
				<tr>
					<td class="manage-column column-cb check-column">
						<input type="checkbox" id="wpqbui-select-all">
					</td>
					<th scope="col" class="manage-column column-name column-primary"><?php esc_html_e( 'Name', 'wpqbui' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Slug', 'wpqbui' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Shortcode', 'wpqbui' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Created', 'wpqbui' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Updated', 'wpqbui' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $queries as $q ) : ?>
				<tr data-id="<?php echo (int) $q->id; ?>">
					<th scope="row" class="check-column">
						<input type="checkbox" name="wpqbui_ids[]" value="<?php echo (int) $q->id; ?>">
					</th>
					<td class="column-name column-primary">
						<strong>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpqbui-builder&wpqbui_id=' . $q->id ) ); ?>">
								<?php echo esc_html( $q->name ); ?>
							</a>
						</strong>
						<?php if ( $q->description ) : ?>
							<p class="row-description"><?php echo esc_html( $q->description ); ?></p>
						<?php endif; ?>
						<div class="row-actions">
							<span class="edit">
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpqbui-builder&wpqbui_id=' . $q->id ) ); ?>">
									<?php esc_html_e( 'Edit', 'wpqbui' ); ?>
								</a> |
							</span>
							<span class="duplicate">
								<a href="#" class="wpqbui-duplicate-btn" data-id="<?php echo (int) $q->id; ?>">
									<?php esc_html_e( 'Duplicate', 'wpqbui' ); ?>
								</a> |
							</span>
							<span class="delete">
								<a href="#" class="wpqbui-delete-btn" data-id="<?php echo (int) $q->id; ?>">
									<?php esc_html_e( 'Delete', 'wpqbui' ); ?>
								</a>
							</span>
						</div>
					</td>
					<td><code><?php echo esc_html( $q->slug ); ?></code></td>
					<td><code>[wpqbui id="<?php echo (int) $q->id; ?>"]</code></td>
					<td><?php echo esc_html( get_date_from_gmt( $q->created_at, get_option( 'date_format' ) ) ); ?></td>
					<td><?php echo esc_html( get_date_from_gmt( $q->updated_at, get_option( 'date_format' ) ) ); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td class="manage-column column-cb check-column"><input type="checkbox"></td>
					<th scope="col"><?php esc_html_e( 'Name', 'wpqbui' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Slug', 'wpqbui' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Shortcode', 'wpqbui' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Created', 'wpqbui' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Updated', 'wpqbui' ); ?></th>
				</tr>
			</tfoot>
		</table>
	</form>

	<!-- Pagination -->
	<?php if ( $total_pages > 1 ) : ?>
	<div class="tablenav bottom">
		<div class="tablenav-pages">
			<?php
			echo paginate_links( array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'base'    => add_query_arg( 'paged', '%#%' ),
				'format'  => '',
				'current' => $paged,
				'total'   => $total_pages,
			) );
			?>
		</div>
	</div>
	<?php endif; ?>
	<?php endif; ?>
</div>
