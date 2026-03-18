<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var WPQBUI_Query_Definition|null $def */
$query_args = $def ? $def->query_args : array();
$query_id   = $def ? (int) $def->id : 0;
?>
<div class="wrap wpqbui-wrap">
	<h1 class="wp-heading-inline">
		<?php echo $query_id ? esc_html__( 'Edit Query', 'wpqbui' ) : esc_html__( 'New Query', 'wpqbui' ); ?>
	</h1>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpqbui-saved' ) ); ?>" class="page-title-action">
		<?php esc_html_e( 'All Saved Queries', 'wpqbui' ); ?>
	</a>
	<hr class="wp-header-end">

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="wpqbui-builder-form">
		<?php wp_nonce_field( 'wpqbui_save_query', 'wpqbui_nonce' ); ?>
		<input type="hidden" name="action" value="wpqbui_save">
		<input type="hidden" name="wpqbui_id" value="<?php echo esc_attr( $query_id ); ?>">

		<!-- Query meta -->
		<div class="wpqbui-query-meta postbox">
			<div class="inside">
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="wpqbui_name"><?php esc_html_e( 'Query Name', 'wpqbui' ); ?> <span class="required">*</span></label></th>
						<td>
							<input type="text" id="wpqbui_name" name="wpqbui_name" class="regular-text"
								value="<?php echo esc_attr( $def ? $def->name : '' ); ?>" required>
							<p class="description"><?php esc_html_e( 'A memorable name for this query configuration.', 'wpqbui' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="wpqbui_description"><?php esc_html_e( 'Description', 'wpqbui' ); ?></label></th>
						<td>
							<textarea id="wpqbui_description" name="wpqbui_description" class="large-text" rows="2"><?php echo esc_textarea( $def ? $def->description : '' ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<!-- Tab navigation -->
		<div class="wpqbui-tabs">
			<ul class="wpqbui-tab-nav" role="tablist">
				<?php
				$tabs = array(
					'basic'         => __( 'Basic', 'wpqbui' ),
					'author'        => __( 'Author', 'wpqbui' ),
					'posts'         => __( 'Post IDs', 'wpqbui' ),
					'parent'        => __( 'Parent', 'wpqbui' ),
					'taxonomy'      => __( 'Taxonomy', 'wpqbui' ),
					'meta'          => __( 'Meta Query', 'wpqbui' ),
					'date'          => __( 'Date Query', 'wpqbui' ),
					'pagination'    => __( 'Pagination', 'wpqbui' ),
					'order'         => __( 'Order', 'wpqbui' ),
					'search'        => __( 'Search', 'wpqbui' ),
					'advanced'      => __( 'Advanced', 'wpqbui' ),
				);
				foreach ( $tabs as $slug => $label ) :
					?>
					<li role="presentation">
						<a href="#wpqbui-tab-<?php echo esc_attr( $slug ); ?>"
						   role="tab"
						   class="wpqbui-tab-link"
						   data-tab="<?php echo esc_attr( $slug ); ?>">
							<?php echo esc_html( $label ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php foreach ( array_keys( $tabs ) as $slug ) : ?>
			<div id="wpqbui-tab-<?php echo esc_attr( $slug ); ?>" class="wpqbui-tab-panel" role="tabpanel">
				<?php include WPQBUI_DIR . 'partials/builder/section-' . $slug . '.php'; ?>
			</div>
			<?php endforeach; ?>
		</div><!-- /.wpqbui-tabs -->

		<!-- Validation messages area -->
		<div id="wpqbui-validation-messages" class="wpqbui-validation-area" hidden></div>

		<!-- Action buttons -->
		<div class="wpqbui-actions">
			<button type="button" id="wpqbui-btn-preview" class="button button-secondary">
				<?php esc_html_e( 'Preview & Generate Code', 'wpqbui' ); ?>
			</button>
			<input type="submit" id="wpqbui-btn-save" class="button button-primary"
				value="<?php esc_attr_e( 'Save Query', 'wpqbui' ); ?>">
			<?php if ( $query_id ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpqbui-builder' ) ); ?>" class="button">
				<?php esc_html_e( 'New Query', 'wpqbui' ); ?>
			</a>
			<?php endif; ?>
		</div>
	</form>

	<!-- Output panels -->
	<div class="wpqbui-output-panels" id="wpqbui-output" hidden>
		<h2><?php esc_html_e( 'Generated Output', 'wpqbui' ); ?></h2>

		<!-- Resolved args preview -->
		<div class="wpqbui-panel postbox">
			<div class="postbox-header">
				<h2 class="hndle"><?php esc_html_e( 'Resolved Query Args (Preview)', 'wpqbui' ); ?></h2>
			</div>
			<div class="inside">
				<pre id="wpqbui-resolved-args" class="wpqbui-code-block wpqbui-json"></pre>
			</div>
		</div>

		<!-- PHP code -->
		<div class="wpqbui-panel postbox">
			<div class="postbox-header">
				<h2 class="hndle">
					<?php esc_html_e( 'PHP Code', 'wpqbui' ); ?>
					<label class="wpqbui-include-loop">
						<input type="checkbox" id="wpqbui-include-loop">
						<?php esc_html_e( 'Include loop template', 'wpqbui' ); ?>
					</label>
				</h2>
				<button type="button" class="wpqbui-copy-btn button button-small" data-target="wpqbui-php-code">
					<?php esc_html_e( 'Copy', 'wpqbui' ); ?>
				</button>
			</div>
			<div class="inside">
				<pre id="wpqbui-php-code" class="wpqbui-code-block wpqbui-php"><code></code></pre>
			</div>
		</div>

		<!-- Shortcode -->
		<div class="wpqbui-panel postbox" id="wpqbui-shortcode-panel" <?php echo $query_id ? '' : 'hidden'; ?>>
			<div class="postbox-header">
				<h2 class="hndle"><?php esc_html_e( 'Shortcode', 'wpqbui' ); ?></h2>
				<button type="button" class="wpqbui-copy-btn button button-small" data-target="wpqbui-shortcode-output">
					<?php esc_html_e( 'Copy', 'wpqbui' ); ?>
				</button>
			</div>
			<div class="inside">
				<code id="wpqbui-shortcode-output" class="wpqbui-shortcode-display"></code>
				<p class="description">
					<?php esc_html_e( 'Paste this shortcode into any post, page, or widget. The shortcode must be enabled in Settings.', 'wpqbui' ); ?>
				</p>
			</div>
		</div>
	</div><!-- /#wpqbui-output -->
</div><!-- /.wpqbui-wrap -->

<!-- Hidden: current query_id for JS -->
<script type="text/javascript">
window.wpqbuiCurrentId = <?php echo (int) $query_id; ?>;
</script>
