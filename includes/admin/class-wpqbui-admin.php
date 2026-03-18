<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin hub: menus, assets, page dispatch.
 */
class WPQBUI_Admin {

	/** @var string[] Page hooks returned by add_*_page */
	private $page_hooks = array();

	public function register( WPQBUI_Loader $loader ) {
		$loader->add_action( 'admin_menu',             $this, 'register_menus' );
		$loader->add_action( 'admin_enqueue_scripts',  $this, 'enqueue_assets' );
		$loader->add_action( 'admin_notices',          $this, 'show_notices' );
		$loader->add_action( 'admin_init',             $this, 'register_settings' );
		$loader->add_action( 'admin_post_wpqbui_save', $this, 'handle_save' );
	}

	public function register_menus() {
		$this->page_hooks['builder'] = add_menu_page(
			__( 'WP Query Builder', 'wpqbui' ),
			__( 'Query Builder', 'wpqbui' ),
			'manage_options',
			'wpqbui-builder',
			array( $this, 'render_builder' ),
			'dashicons-filter',
			81
		);

		$this->page_hooks['builder_sub'] = add_submenu_page(
			'wpqbui-builder',
			__( 'Query Builder', 'wpqbui' ),
			__( 'Query Builder', 'wpqbui' ),
			'manage_options',
			'wpqbui-builder',
			array( $this, 'render_builder' )
		);

		$this->page_hooks['saved'] = add_submenu_page(
			'wpqbui-builder',
			__( 'Saved Queries', 'wpqbui' ),
			__( 'Saved Queries', 'wpqbui' ),
			'manage_options',
			'wpqbui-saved',
			array( $this, 'render_saved' )
		);

		$this->page_hooks['settings'] = add_submenu_page(
			'wpqbui-builder',
			__( 'Query Builder Settings', 'wpqbui' ),
			__( 'Settings', 'wpqbui' ),
			'manage_options',
			'wpqbui-settings',
			array( $this, 'render_settings' )
		);
	}

	public function enqueue_assets( $hook ) {
		if ( ! in_array( $hook, $this->page_hooks, true ) ) {
			return;
		}

		// Core dependencies.
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		// Plugin CSS.
		wp_enqueue_style(
			'wpqbui-admin',
			WPQBUI_URL . 'assets/css/admin.css',
			array(),
			WPQBUI_VERSION
		);

		// Plugin JS files in dependency order.
		$js_files = array(
			'wpqbui-ns'          => array( 'deps' => array( 'jquery' ),         'file' => 'wpqbui-namespace.js' ),
			'wpqbui-ajax'        => array( 'deps' => array( 'wpqbui-ns' ),       'file' => 'wpqbui-ajax.js' ),
			'wpqbui-loaders'     => array( 'deps' => array( 'wpqbui-ajax' ),     'file' => 'wpqbui-dynamic-loaders.js' ),
			'wpqbui-tax-query'   => array( 'deps' => array( 'wpqbui-loaders' ),  'file' => 'wpqbui-tax-query.js' ),
			'wpqbui-meta-query'  => array( 'deps' => array( 'wpqbui-loaders' ),  'file' => 'wpqbui-meta-query.js' ),
			'wpqbui-date-query'  => array( 'deps' => array( 'wpqbui-ns' ),       'file' => 'wpqbui-date-query.js' ),
			'wpqbui-orderby'     => array( 'deps' => array( 'wpqbui-ns', 'jquery-ui-sortable' ), 'file' => 'wpqbui-orderby.js' ),
			'wpqbui-post-picker' => array( 'deps' => array( 'wpqbui-ajax' ),     'file' => 'wpqbui-post-picker.js' ),
			'wpqbui-validation'  => array( 'deps' => array( 'wpqbui-ns' ),       'file' => 'wpqbui-validation.js' ),
			'wpqbui-codegen'     => array( 'deps' => array( 'wpqbui-loaders', 'wpqbui-tax-query', 'wpqbui-meta-query', 'wpqbui-date-query', 'wpqbui-orderby', 'wpqbui-post-picker', 'wpqbui-validation' ), 'file' => 'wpqbui-codegen.js' ),
			'wpqbui-saved-list'  => array( 'deps' => array( 'wpqbui-ajax' ),     'file' => 'wpqbui-saved-list.js' ),
		);

		foreach ( $js_files as $handle => $data ) {
			wp_enqueue_script(
				$handle,
				WPQBUI_URL . 'assets/js/' . $data['file'],
				$data['deps'],
				WPQBUI_VERSION,
				true
			);
		}

		// Localise the codegen script.
		wp_localize_script( 'wpqbui-codegen', 'wpqbuiData', $this->get_js_data() );
	}

	private function get_js_data() {
		return array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'wpqbui_ajax' ),
			'i18n'    => array(
				'loading'          => __( 'Loading…', 'wpqbui' ),
				'noResults'        => __( 'No results found.', 'wpqbui' ),
				'addRow'           => __( 'Add row', 'wpqbui' ),
				'removeRow'        => __( 'Remove', 'wpqbui' ),
				'confirmDelete'    => __( 'Delete this query?', 'wpqbui' ),
				'confirmDuplicate' => __( 'Duplicate this query?', 'wpqbui' ),
				'generating'       => __( 'Generating…', 'wpqbui' ),
				'copied'           => __( 'Copied!', 'wpqbui' ),
				'errorGeneric'     => __( 'An error occurred. Please try again.', 'wpqbui' ),
				'searchPosts'      => __( 'Search posts…', 'wpqbui' ),
			),
			'postStatuses' => array(
				array( 'slug' => 'publish',    'label' => __( 'Published', 'wpqbui' ) ),
				array( 'slug' => 'pending',    'label' => __( 'Pending Review', 'wpqbui' ) ),
				array( 'slug' => 'draft',      'label' => __( 'Draft', 'wpqbui' ) ),
				array( 'slug' => 'auto-draft', 'label' => __( 'Auto Draft', 'wpqbui' ) ),
				array( 'slug' => 'future',     'label' => __( 'Scheduled', 'wpqbui' ) ),
				array( 'slug' => 'private',    'label' => __( 'Private', 'wpqbui' ) ),
				array( 'slug' => 'inherit',    'label' => __( 'Inherit', 'wpqbui' ) ),
				array( 'slug' => 'trash',      'label' => __( 'Trash', 'wpqbui' ) ),
				array( 'slug' => 'any',        'label' => __( 'Any status', 'wpqbui' ) ),
			),
			'orderbyOptions' => array(
				array( 'slug' => 'date',            'label' => __( 'Date', 'wpqbui' ) ),
				array( 'slug' => 'modified',        'label' => __( 'Modified Date', 'wpqbui' ) ),
				array( 'slug' => 'title',           'label' => __( 'Title', 'wpqbui' ) ),
				array( 'slug' => 'name',            'label' => __( 'Name (slug)', 'wpqbui' ) ),
				array( 'slug' => 'ID',              'label' => __( 'ID', 'wpqbui' ) ),
				array( 'slug' => 'author',          'label' => __( 'Author', 'wpqbui' ) ),
				array( 'slug' => 'type',            'label' => __( 'Post Type', 'wpqbui' ) ),
				array( 'slug' => 'parent',          'label' => __( 'Parent ID', 'wpqbui' ) ),
				array( 'slug' => 'comment_count',   'label' => __( 'Comment Count', 'wpqbui' ) ),
				array( 'slug' => 'menu_order',      'label' => __( 'Menu Order', 'wpqbui' ) ),
				array( 'slug' => 'relevance',       'label' => __( 'Relevance (search)', 'wpqbui' ) ),
				array( 'slug' => 'rand',            'label' => __( 'Random', 'wpqbui' ) ),
				array( 'slug' => 'none',            'label' => __( 'None', 'wpqbui' ) ),
				array( 'slug' => 'meta_value',      'label' => __( 'Meta Value (string)', 'wpqbui' ) ),
				array( 'slug' => 'meta_value_num',  'label' => __( 'Meta Value (numeric)', 'wpqbui' ) ),
				array( 'slug' => 'post__in',        'label' => __( 'Preserve post__in order', 'wpqbui' ) ),
				array( 'slug' => 'post_name__in',   'label' => __( 'Preserve post_name__in order', 'wpqbui' ) ),
				array( 'slug' => 'post_parent__in', 'label' => __( 'Preserve post_parent__in order', 'wpqbui' ) ),
			),
			'compareOperators' => array( '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS', 'NOT EXISTS', 'REGEXP', 'NOT REGEXP' ),
			'metaTypes'        => array( 'CHAR', 'NUMERIC', 'BINARY', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED' ),
		);
	}

	public function register_settings() {
		register_setting( 'wpqbui_settings', 'wpqbui_settings', array( $this, 'sanitize_settings' ) );
	}

	public function sanitize_settings( $input ) {
		$clean = array();
		$clean['enable_shortcode'] = ! empty( $input['enable_shortcode'] ) ? 1 : 0;
		$clean['default_post_type'] = isset( $input['default_post_type'] ) ? sanitize_key( $input['default_post_type'] ) : 'post';
		return $clean;
	}

	public function handle_save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'wpqbui' ) );
		}
		check_admin_referer( 'wpqbui_save_query', 'wpqbui_nonce' );

		$raw = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification

		$sanitizer   = new WPQBUI_Query_Sanitizer();
		$clean_args  = $sanitizer->sanitize( isset( $raw['query_args'] ) ? (array) $raw['query_args'] : array() );

		$validator   = new WPQBUI_Query_Validator();
		$errors      = array_filter( $validator->validate( $clean_args ), function( $v ) {
			return 'error' === $v['severity'];
		} );

		if ( ! empty( $errors ) ) {
			$msg = implode( ' | ', array_column( $errors, 'message' ) );
			set_transient( 'wpqbui_notice_error_' . get_current_user_id(), $msg, 60 );
			wp_safe_redirect( wp_get_referer() ?: admin_url( 'admin.php?page=wpqbui-builder' ) );
			exit;
		}

		$def              = WPQBUI_Query_Definition::from_array( array(
			'id'          => $raw['wpqbui_id'] ?? 0,
			'name'        => $raw['wpqbui_name'] ?? '',
			'description' => $raw['wpqbui_description'] ?? '',
			'query_args'  => $clean_args,
		) );

		$repo   = new WPQBUI_Query_Repository();
		$new_id = $repo->save_or_update( $def );

		set_transient( 'wpqbui_notice_success_' . get_current_user_id(), __( 'Query saved successfully.', 'wpqbui' ), 60 );
		wp_safe_redirect( admin_url( 'admin.php?page=wpqbui-builder&wpqbui_id=' . $new_id ) );
		exit;
	}

	public function show_notices() {
		$uid = get_current_user_id();

		$error = get_transient( 'wpqbui_notice_error_' . $uid );
		if ( $error ) {
			delete_transient( 'wpqbui_notice_error_' . $uid );
			echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $error ) . '</p></div>';
		}

		$success = get_transient( 'wpqbui_notice_success_' . $uid );
		if ( $success ) {
			delete_transient( 'wpqbui_notice_success_' . $uid );
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $success ) . '</p></div>';
		}
	}

	// -------------------------------------------------------------------------
	// Page renderers
	// -------------------------------------------------------------------------

	public function render_builder() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$query_id = isset( $_GET['wpqbui_id'] ) ? absint( $_GET['wpqbui_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		$def      = null;
		if ( $query_id ) {
			$repo = new WPQBUI_Query_Repository();
			$def  = $repo->find_by_id( $query_id );
		}
		include WPQBUI_DIR . 'partials/admin-page-builder.php';
	}

	public function render_saved() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		include WPQBUI_DIR . 'partials/admin-page-saved.php';
	}

	public function render_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		include WPQBUI_DIR . 'partials/admin-page-settings.php';
	}
}
