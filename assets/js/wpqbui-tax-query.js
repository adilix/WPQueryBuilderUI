/**
 * WP Query Builder UI — Tax Query repeatable rows.
 */
( function () {
	'use strict';

	let rowIndex = 0;

	function getPostType() {
		const sel = document.getElementById( 'wpqbui-post-type' );
		if ( ! sel ) return 'post';
		// Prefer live selection; fall back to data-selected set server-side.
		const live = Array.from( sel.selectedOptions ).map( o => o.value );
		if ( live.length === 1 ) return live[ 0 ];
		if ( live.length === 0 ) {
			const saved = ( sel.dataset.selected || '' ).split( ',' ).filter( v => v );
			return saved.length === 1 ? saved[ 0 ] : 'any';
		}
		return 'any';
	}

	function wireRow( row ) {
		const taxSelect   = row.querySelector( '.wpqbui-tax-taxonomy-select' );
		const termsSelect = row.querySelector( '.wpqbui-tax-terms-select' );

		if ( ! taxSelect ) return;

		// Load taxonomies on mount.
		WPQBUI.dynamicLoaders.loadTaxonomiesForSelect( taxSelect, getPostType() );

		// When taxonomy changes, reload terms.
		taxSelect.addEventListener( 'change', function () {
			const currentTerms = Array.from( termsSelect.selectedOptions ).map( o => o.value );
			WPQBUI.dynamicLoaders.loadTermsForSelect( termsSelect, taxSelect.value, currentTerms );
		} );

		// Remove row button.
		const removeBtn = row.querySelector( '.wpqbui-remove-row' );
		if ( removeBtn ) {
			removeBtn.addEventListener( 'click', function () {
				row.remove();
			} );
		}
	}

	function addRow() {
		const container = document.getElementById( 'wpqbui-tax-rows' );
		const template  = document.getElementById( 'wpqbui-tax-row-template' );
		if ( ! container || ! template ) return;

		rowIndex++;
		const html   = template.innerHTML.replace( /__INDEX__/g, String( rowIndex ) );
		const tmp    = document.createElement( 'div' );
		tmp.innerHTML = html;
		const newRow = tmp.firstElementChild;
		container.appendChild( newRow );
		wireRow( newRow );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		// Wire existing rows (loaded from saved data).
		document.querySelectorAll( '#wpqbui-tax-rows .wpqbui-tax-row' ).forEach( row => {
			rowIndex = Math.max( rowIndex, parseInt( row.dataset.index || 0, 10 ) );
			wireRow( row );
		} );

		// Add row button.
		document.querySelectorAll( '.wpqbui-add-row[data-target="wpqbui-tax-rows"]' ).forEach( btn => {
			btn.addEventListener( 'click', addRow );
		} );
	} );

	// Export for use by codegen.
	WPQBUI.taxQuery = { addRow };
} )();
