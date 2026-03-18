<?php
/**
 * Default front-end template for [wpqbui] shortcode output.
 * Override in your theme at: wpqbui/query-results.php
 *
 * Available variables:
 *   $query             WP_Query object
 *   $wpqbui_definition WPQBUI_Query_Definition object
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpqbui-results">
	<ul class="wpqbui-posts-list">
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
		<li class="wpqbui-post-item">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</li>
		<?php endwhile; ?>
	</ul>
</div>
