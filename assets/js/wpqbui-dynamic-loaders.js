/**
 * WP Query Builder UI — Dynamic field loaders (post types, taxonomies, terms, authors, meta keys).
 */
( function () {
	'use strict';

	WPQBUI.dynamicLoaders = {};

	// Cache to avoid duplicate requests.
	const cache = {};

	function cacheKey( action, params ) {
		return action + ':' + JSON.stringify( params );
	}

	function cachedAjax( action, params ) {
		const key = cacheKey( action, params );
		if ( cache[ key ] ) {
			return Promise.resolve( cache[ key ] );
		}
		return WPQBUI.ajax( action, params ).then( data => {
			cache[ key ] = data;
			return data;
		} );
	}

	// ----------------------------------------------------------------
	// Post Types
	// ----------------------------------------------------------------
	function loadPostTypes() {
		const select = document.getElementById( 'wpqbui-post-type' );
		if ( ! select ) return;

		// data-selected is set server-side and is the authoritative initial selection.
		const dataSelected = ( select.dataset.selected || '' ).split( ',' ).filter( v => v );
		// Also capture anything already selected in the DOM (covers edge cases).
		const domSelected  = Array.from( select.selectedOptions ).map( o => o.value );
		const preselect    = dataSelected.length ? dataSelected : domSelected;

		cachedAjax( 'wpqbui_get_post_types', {} ).then( types => {
			select.innerHTML = '';
			types.forEach( type => {
				const opt = document.createElement( 'option' );
				opt.value = type.slug;
				opt.textContent = type.label;
				if ( preselect.includes( type.slug ) ) {
					opt.selected = true;
				}
				select.appendChild( opt );
			} );

			// Auto-load taxonomies for the initial selection so tax rows are
			// pre-populated (e.g. 'category' visible when post type is 'post').
			const effectiveType = preselect.length === 1 ? preselect[ 0 ] : 'any';
			document.querySelectorAll( '.wpqbui-tax-taxonomy-select' ).forEach( sel => {
				WPQBUI.dynamicLoaders.loadTaxonomiesForSelect( sel, effectiveType );
			} );
		} );
	}

	// ----------------------------------------------------------------
	// Taxonomies (for a given post type)
	// ----------------------------------------------------------------
	WPQBUI.dynamicLoaders.loadTaxonomiesForSelect = function ( taxSelect, postType ) {
		if ( ! taxSelect ) return;
		// data-selected is set server-side; live value is the fallback for user changes.
		const target = taxSelect.dataset.selected || taxSelect.value || '';
		return cachedAjax( 'wpqbui_get_taxonomies', { post_type: postType } ).then( taxes => {
			taxSelect.innerHTML = '<option value="">' + ( ( WPQBUI.data.i18n || {} ).loading || '— select taxonomy —' ) + '</option>';
			taxes.forEach( tax => {
				const opt = document.createElement( 'option' );
				opt.value = tax.slug;
				opt.textContent = tax.label;
				if ( target === tax.slug ) opt.selected = true;
				taxSelect.appendChild( opt );
			} );
			// If a taxonomy is now selected, trigger change so terms load immediately.
			if ( taxSelect.value ) {
				taxSelect.dispatchEvent( new Event( 'change' ) );
			}
		} );
	};

	// ----------------------------------------------------------------
	// Terms (for a given taxonomy, into a given multi-select)
	// ----------------------------------------------------------------
	WPQBUI.dynamicLoaders.loadTermsForSelect = function ( termsSelect, taxonomy, selectedTerms ) {
		if ( ! termsSelect || ! taxonomy ) return;
		selectedTerms = selectedTerms || [];
		return cachedAjax( 'wpqbui_get_terms', { taxonomy } ).then( terms => {
			termsSelect.innerHTML = '';
			terms.forEach( term => {
				const opt = document.createElement( 'option' );
				opt.value = term.term_id;
				opt.textContent = term.name;
				if ( selectedTerms.includes( String( term.term_id ) ) || selectedTerms.includes( term.slug ) ) {
					opt.selected = true;
				}
				termsSelect.appendChild( opt );
			} );
		} );
	};

	// ----------------------------------------------------------------
	// Meta keys (datalist)
	// ----------------------------------------------------------------
	WPQBUI.dynamicLoaders.refreshMetaKeyDatalist = function ( postType, search ) {
		const datalist = document.getElementById( 'wpqbui-meta-key-datalist' );
		if ( ! datalist ) return;
		WPQBUI.ajax( 'wpqbui_get_meta_keys', { post_type: postType || '', search: search || '' } ).then( keys => {
			datalist.innerHTML = '';
			keys.forEach( key => {
				const opt = document.createElement( 'option' );
				opt.value = key;
				datalist.appendChild( opt );
			} );
		} );
	};

	// ----------------------------------------------------------------
	// Authors (for picker dropdowns)
	// ----------------------------------------------------------------
	WPQBUI.dynamicLoaders.searchAuthors = function ( search ) {
		return WPQBUI.ajax( 'wpqbui_get_authors', { search } );
	};

	// ----------------------------------------------------------------
	// Init
	// ----------------------------------------------------------------
	document.addEventListener( 'DOMContentLoaded', function () {
		loadPostTypes();

		// When post type selection changes, refresh meta keys & taxonomy selects in tax rows.
		const postTypeSelect = document.getElementById( 'wpqbui-post-type' );
		if ( postTypeSelect ) {
			postTypeSelect.addEventListener( 'change', function () {
				const selected = Array.from( postTypeSelect.selectedOptions ).map( o => o.value );
				const postType = selected.length === 1 ? selected[ 0 ] : 'any';

				// Refresh meta key datalist.
				WPQBUI.dynamicLoaders.refreshMetaKeyDatalist( postType, '' );

				// Refresh taxonomy selects in all existing tax rows.
				document.querySelectorAll( '.wpqbui-tax-taxonomy-select' ).forEach( sel => {
					WPQBUI.dynamicLoaders.loadTaxonomiesForSelect( sel, postType );
				} );
			} );
		}
	} );
} )();
