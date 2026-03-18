/**
 * WP Query Builder UI — Meta Query repeatable rows.
 */
( function () {
	'use strict';

	let rowIndex = 0;

	const MULTI_COMPARES = new Set( [ 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ] );
	const NO_VALUE_COMPARES = new Set( [ 'EXISTS', 'NOT EXISTS' ] );

	function wireRow( row ) {
		const compareSelect = row.querySelector( '.wpqbui-meta-compare-select' );
		const valueRow      = row.querySelector( '.wpqbui-meta-value-row' );
		const valueInput    = row.querySelector( '.wpqbui-meta-value-input' );
		const keyInput      = row.querySelector( '.wpqbui-meta-key-input' );

		function updateValueVisibility() {
			if ( ! compareSelect ) return;
			const compare = compareSelect.value;
			if ( NO_VALUE_COMPARES.has( compare ) ) {
				if ( valueRow ) valueRow.style.display = 'none';
			} else {
				if ( valueRow ) valueRow.style.display = '';
				if ( valueInput && MULTI_COMPARES.has( compare ) ) {
					valueInput.placeholder = 'val1, val2, val3 (comma-separated)';
				} else if ( valueInput ) {
					valueInput.placeholder = '';
				}
			}
		}

		if ( compareSelect ) {
			compareSelect.addEventListener( 'change', updateValueVisibility );
			updateValueVisibility();
		}

		// Meta key: refresh datalist on focus.
		if ( keyInput ) {
			keyInput.addEventListener( 'focus', WPQBUI.debounce( function () {
				const postType = ( document.getElementById( 'wpqbui-post-type' ) || {} ).value || '';
				WPQBUI.dynamicLoaders.refreshMetaKeyDatalist( postType, keyInput.value );
			}, 300 ) );
		}

		// Remove row.
		const removeBtn = row.querySelector( '.wpqbui-remove-row' );
		if ( removeBtn ) {
			removeBtn.addEventListener( 'click', function () {
				row.remove();
			} );
		}
	}

	function addRow() {
		const container = document.getElementById( 'wpqbui-meta-rows' );
		const template  = document.getElementById( 'wpqbui-meta-row-template' );
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
		document.querySelectorAll( '#wpqbui-meta-rows .wpqbui-meta-row' ).forEach( row => {
			rowIndex = Math.max( rowIndex, parseInt( row.dataset.index || 0, 10 ) );
			wireRow( row );
		} );

		document.querySelector( '.wpqbui-add-meta-row' )
			?.addEventListener( 'click', addRow );
	} );

	WPQBUI.metaQuery = { addRow };
} )();
