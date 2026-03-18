<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var array $query_args */
$qa = $query_args;

// comment_count: detect array vs scalar.
$cc       = $qa['comment_count'] ?? '';
$cc_mode  = is_array( $cc ) ? 'compare' : 'simple';
$cc_val   = is_array( $cc ) ? ( $cc['value'] ?? '' ) : $cc;
$cc_cmp   = is_array( $cc ) ? ( $cc['compare'] ?? '=' ) : '=';
$cc_cmps  = array( '=', '!=', '>', '>=', '<', '<=' );
?>
<h3><?php esc_html_e( 'Advanced Parameters', 'wpqbui' ); ?></h3>

<!-- Sticky -->
<div class="wpqbui-section-group">
	<h4><?php esc_html_e( 'Sticky Posts', 'wpqbui' ); ?></h4>
	<label class="wpqbui-cb-label">
		<input type="checkbox" name="query_args[ignore_sticky_posts]" value="1"
			<?php checked( ! empty( $qa['ignore_sticky_posts'] ) ); ?>>
		<?php esc_html_e( 'ignore_sticky_posts — do not elevate sticky posts to the top', 'wpqbui' ); ?>
	</label>
</div>

<!-- MIME type -->
<div class="wpqbui-section-group">
	<h4><?php esc_html_e( 'MIME Type (Attachments)', 'wpqbui' ); ?></h4>
	<label for="wpqbui-mime-type"><?php esc_html_e( 'post_mime_type:', 'wpqbui' ); ?></label>
	<input type="text" id="wpqbui-mime-type" name="query_args[post_mime_type]"
		value="<?php echo esc_attr( $qa['post_mime_type'] ?? '' ); ?>" class="regular-text"
		placeholder="<?php esc_attr_e( 'e.g. image, image/jpeg, application/pdf', 'wpqbui' ); ?>">
	<p class="description"><?php esc_html_e( 'Limit attachments by MIME type. Only useful when post_type includes "attachment".', 'wpqbui' ); ?></p>
</div>

<!-- Comment count -->
<div class="wpqbui-section-group">
	<h4><?php esc_html_e( 'Comment Count', 'wpqbui' ); ?></h4>
	<div class="wpqbui-toggle-group">
		<label class="wpqbui-radio-label">
			<input type="radio" name="wpqbui_cc_mode" value="simple" class="wpqbui-cc-mode-radio" <?php checked( $cc_mode, 'simple' ); ?>>
			<?php esc_html_e( 'Exact count', 'wpqbui' ); ?>
		</label>
		<label class="wpqbui-radio-label">
			<input type="radio" name="wpqbui_cc_mode" value="compare" class="wpqbui-cc-mode-radio" <?php checked( $cc_mode, 'compare' ); ?>>
			<?php esc_html_e( 'Compare operator', 'wpqbui' ); ?>
		</label>
	</div>
	<div class="wpqbui-cc-simple <?php echo 'compare' === $cc_mode ? 'hidden' : ''; ?>">
		<input type="number" name="query_args[comment_count]" value="<?php echo 'simple' === $cc_mode ? esc_attr( $cc_val ) : ''; ?>" min="0" class="small-text">
	</div>
	<div class="wpqbui-cc-compare <?php echo 'simple' === $cc_mode ? 'hidden' : ''; ?>">
		<select name="query_args[comment_count][compare]">
			<?php foreach ( $cc_cmps as $op ) : ?>
				<option value="<?php echo esc_attr( $op ); ?>" <?php selected( $cc_cmp, $op ); ?>><?php echo esc_html( $op ); ?></option>
			<?php endforeach; ?>
		</select>
		<input type="number" name="query_args[comment_count][value]" value="<?php echo 'compare' === $cc_mode ? esc_attr( $cc_val ) : ''; ?>" min="0" class="small-text">
	</div>
</div>

<!-- Perm -->
<div class="wpqbui-section-group">
	<h4><?php esc_html_e( 'Permission (perm)', 'wpqbui' ); ?></h4>
	<label class="wpqbui-radio-label">
		<input type="radio" name="query_args[perm]" value="" <?php checked( empty( $qa['perm'] ) ); ?>>
		<?php esc_html_e( 'None (ignore permission)', 'wpqbui' ); ?>
	</label>
	<label class="wpqbui-radio-label">
		<input type="radio" name="query_args[perm]" value="readable" <?php checked( ( $qa['perm'] ?? '' ), 'readable' ); ?>>
		<?php esc_html_e( 'readable — only return posts the current user can read', 'wpqbui' ); ?>
	</label>
	<label class="wpqbui-radio-label">
		<input type="radio" name="query_args[perm]" value="editable" <?php checked( ( $qa['perm'] ?? '' ), 'editable' ); ?>>
		<?php esc_html_e( 'editable — only return posts the current user can edit', 'wpqbui' ); ?>
	</label>
</div>

<!-- Cache options -->
<div class="wpqbui-section-group">
	<h4><?php esc_html_e( 'Cache Options', 'wpqbui' ); ?></h4>
	<label class="wpqbui-cb-label">
		<input type="checkbox" name="query_args[cache_results]" value="1"
			<?php checked( $qa['cache_results'] ?? true ); ?>>
		<?php esc_html_e( 'cache_results — cache queried posts (default: true)', 'wpqbui' ); ?>
	</label>
	<br>
	<label class="wpqbui-cb-label">
		<input type="checkbox" name="query_args[update_post_meta_cache]" value="1"
			<?php checked( $qa['update_post_meta_cache'] ?? true ); ?>>
		<?php esc_html_e( 'update_post_meta_cache — prime postmeta cache (default: true)', 'wpqbui' ); ?>
	</label>
	<br>
	<label class="wpqbui-cb-label">
		<input type="checkbox" name="query_args[update_post_term_cache]" value="1"
			<?php checked( $qa['update_post_term_cache'] ?? true ); ?>>
		<?php esc_html_e( 'update_post_term_cache — prime term cache (default: true)', 'wpqbui' ); ?>
	</label>
</div>
