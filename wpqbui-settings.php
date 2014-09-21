<?php
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

if (!class_exists("WPQBUI_Settings")) :

/* 
	WP Query Builder Plugin options
	-
	-
*/
	class WPQBUI_Settings {

		public static $default_settings = 
		array( 	
			'generate_shortcode' => 'Generate Shortcode',
			'generate_phpcode' => 'Generate PHP',
			);
		var $pagehook, $page_id, $settings_field, $options;


		function __construct() {	
			$this->page_id = 'wpqbui';
			// This is the get_options slug used in the database to store our plugin option values.
			$this->settings_field = 'wpqbui_options';
			$this->options = get_option( $this->settings_field );

			add_action('admin_init', array($this,'admin_init'), 20 );
			add_action( 'admin_menu', array($this, 'admin_menu'), 20);
		}

		function admin_init() {
			register_setting( $this->settings_field, $this->settings_field, array($this, 'sanitize_theme_options') );
			add_option( $this->settings_field, WPQBUI_Settings::$default_settings );


		/* 
			Sets up different sections and the fields within each section.
		*/
			add_settings_section('wpqbui_main', '',  
				array($this, 'main_section_text'), 'wpqbui_settings_page');

			add_settings_field('generatephp_checkbox', 'Output to generate', 
				array($this, 'render_generate_checkbox'), 'wpqbui_settings_page', 'wpqbui_main', 
				array('id' => 'output_checkbox_php', 'value' => 'php', 'text' => 'PHP') );
			add_settings_field('generateshortcode_checkbox', '', 
				array($this, 'render_generate_checkbox'), 'wpqbui_settings_page', 'wpqbui_main', 
				array('id' => 'output_checkbox_shortcode', 'value' => 'shortcode', 'text' => 'WP Shortcode') );
		}

		function admin_menu() {
			if ( ! current_user_can('update_plugins') )
				return;
			add_menu_page( 	__('WP Query Builder UI', 'wpqbui'), 
											__('WPQBUI', 'wpqbui'), 
											'administrator', 
											'wpqbui-plugin', 
											array($this,'render_generator'), 
											'', 
											81 
										);
		// Add a new submenu to the standard Tools panel
			$this->pagehook = $page =  add_submenu_page(	'wpqbui-plugin',
				__('Settings WP Query Builder UI', 'wpqbui'), __('Settings', 'wpqbui'), 
				'administrator', $this->page_id, array($this,'render_settings') );

		// Executed on-load. Add all metaboxes.
			add_action( 'load-' . $this->pagehook, array( $this, 'metaboxes' ) );

		// Include js, css, or header *only* for our settings page
			add_action("admin_print_scripts-$page", array($this, 'js_includes'));
//		add_action("admin_print_styles-$page", array($this, 'css_includes'));
			add_action("admin_head-$page", array($this, 'admin_head') );
		}

		function admin_head() { ?>
		<style>
		.settings_page_wpqbui label { display:inline-block; width: 150px; }
		</style>

		<?php }


		function js_includes() {
		// Needed to allow metabox layout and close functionality.
			wp_enqueue_script( 'postbox' );
		}


	/*
		Sanitize our plugin settings array as needed.
	*/	
		function sanitize_theme_options($options) {
			$options['example_text'] = stripcslashes($options['example_text']);
			return $options;
		}


	/*
		Settings access functions.
		
	*/
		protected function get_field_name( $name ) {

			return sprintf( '%s[%s]', $this->settings_field, $name );

		}

		protected function get_field_id( $id ) {

			return sprintf( '%s[%s]', $this->settings_field, $id );

		}

		protected function get_field_value( $key ) {

			return $this->options[$key];

		}
		

	/*
		Render settings page.
		
	*/

		function render_settings() {
			global $wp_meta_boxes;

			$title = __('WP Query Builder UI - Settings', 'wpqbui');
			?>
			<div class="wrap">   
				<h2><?php echo esc_html( $title ); ?></h2>

				<form method="post" action="options.php">
					<div class="metabox-holder">
						<div class="postbox-container" style="width: 99%;">
							<?php 
						// Render metaboxes
							settings_fields($this->settings_field); 
							do_meta_boxes( $this->pagehook, 'main', null );
							if ( isset( $wp_meta_boxes[$this->pagehook]['column2'] ) )
								do_meta_boxes( $this->pagehook, 'column2', null );
							?>
						</div>
					</div>
					<p>
						<input type="submit" class="button button-primary" name="save_options" value="<?php esc_attr_e('Save Options'); ?>" />
					</p>
				</form>
			</div>
			<!-- Needed to allow metabox layout and close functionality. -->
			<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function ($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			});
			//]]>
			</script>
			<?php 
		}
		
		function metaboxes() {
		// Example metabox showing plugin version and release date. 
		// Also includes and example input text box, rendered in HTML in the info_box function
				add_meta_box( 'wpqbui-version', __( 'Information', 'wpqbui' ), array( $this, 'info_box' ), $this->pagehook, 'main', 'high' );

		// Example metabox containing an example text box & two example checkbox controls.
		// Example settings rendered by WordPress using the do_settings_sections function.
				add_meta_box( 	'wpqbui-all', 
					__( 'Output preferences', 'wpqbui' ), 
					array( $this, 'do_settings_box' ), $this->pagehook, 'main' );
		}

		function info_box() {

			?>
			<p><strong><?php _e( 'Version:', 'wpqbui' ); ?></strong> <?php echo WPQBUI_VERSION; ?> <?php echo '&middot;'; ?> <strong><?php _e( 'Released:', 'wpqbui' ); ?></strong> <?php echo WPQBUI_RELEASE_DATE; ?></p>
			<?php

		}

		function do_settings_box() {
			do_settings_sections('wpqbui_settings_page'); 
		}

		function main_section_text() {
			echo '<p>Please select your plugin\'s result preferences</p>';
		}

		function render_generate_checkbox($args) {
			$id = 'wpqbui_options['.$args['id'].']';
			?>
			<input name="<?php echo $id;?>" type="checkbox" value="<?php echo $args['value'];?>" <?php echo isset($this->options[$args['id']]) ? 'checked' : '';?> /> <?php echo " {$args['text']}"; ?> <br/>
			<?php 
		}

		function render_generator() {

			$title = __('WP Query Builder UI - Generator', 'wpqbui');
			?>
			<div class="wrap">   
				<h2><?php echo esc_html( $title ); ?></h2>
			</div>
			<!-- Needed to allow metabox layout and close functionality. -->
			<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function ($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			});
			//]]>
			</script>
			<?php 
		}

} // end class
endif;
?>