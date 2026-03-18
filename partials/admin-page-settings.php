<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$settings = get_option( 'wpqbui_settings', array() );
?>
<div class="wrap wpqbui-wrap">
	<h1><?php esc_html_e( 'WP Query Builder Settings', 'wpqbui' ); ?></h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'wpqbui_settings' ); ?>
		<table class="form-table" role="presentation">

			<tr>
				<th scope="row"><?php esc_html_e( 'Enable Shortcode', 'wpqbui' ); ?></th>
				<td>
					<label class="wpqbui-cb-label">
						<input type="checkbox" name="wpqbui_settings[enable_shortcode]" value="1"
							<?php checked( ! empty( $settings['enable_shortcode'] ) ); ?>>
						<?php esc_html_e( 'Register the [wpqbui] front-end shortcode', 'wpqbui' ); ?>
					</label>
					<p class="description">
						<?php esc_html_e( 'When enabled, use [wpqbui id="N"] in posts and pages to render query results. A template override can be placed at wpqbui/query-results.php in your theme.', 'wpqbui' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="wpqbui-default-post-type"><?php esc_html_e( 'Default Post Type', 'wpqbui' ); ?></label></th>
				<td>
					<input type="text" id="wpqbui-default-post-type" name="wpqbui_settings[default_post_type]"
						value="<?php echo esc_attr( $settings['default_post_type'] ?? 'post' ); ?>" class="regular-text">
					<p class="description"><?php esc_html_e( 'Pre-selected post type when creating a new query.', 'wpqbui' ); ?></p>
				</td>
			</tr>

		</table>
		<?php submit_button(); ?>
	</form>
</div>
