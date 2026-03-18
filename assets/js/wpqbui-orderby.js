/**
 * WP Query Builder UI — Orderby repeatable sortable rows.
 */
( function ( $ ) {
	'use strict';

	let rowIndex = 0;

	const META_ORDERBY = new Set( [ 'meta_value', 'meta_value_num' ] );

	function wireRow( row ) {
		const select    = row.querySelector( '.wpqbui-orderby-select' );
		const metaInput = row.querySelector( '.wpqbui-orderby-meta-key' );
		const removeBtn = row.querySelector( '.wpqbui-remove-row' );

		function toggleMetaKey() {
			if ( ! select || ! metaInput ) return;
			if ( META_ORDERBY.has( select.value ) ) {
				metaInput.hidden = false;
				metaInput.required = true;
			} else {
				metaInput.hidden = true;
				metaInput.required = false;
			}
		}

		if ( select ) {
			select.addEventListener( 'change', toggleMetaKey );
			toggleMetaKey();
		}

		if ( removeBtn ) {
			removeBtn.addEventListener( 'click', function () {
				const container = document.getElementById( 'wpqbui-orderby-rows' );
				if ( container && container.querySelectorAll( '.wpqbui-orderby-row' ).length <= 1 ) {
					return; // Keep at least one row.
				}
				row.remove();
			} );
		}
	}

	function addRow() {
		const container = document.getElementById( 'wpqbui-orderby-rows' );
		const template  = document.getElementById( 'wpqbui-orderby-row-template' );
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
		document.querySelectorAll( '#wpqbui-orderby-rows .wpqbui-orderby-row' ).forEach( row => {
			rowIndex = Math.max( rowIndex, parseInt( row.dataset.index || 0, 10 ) );
			wireRow( row );
		} );

		document.querySelector( '.wpqbui-add-orderby-row' )
			?.addEventListener( 'click', addRow );

		// Make sortable via jQuery UI.
		const container = document.getElementById( 'wpqbui-orderby-rows' );
		if ( container && $ && $.fn.sortable ) {
			$( container ).sortable( {
				handle:      '.wpqbui-drag-handle',
				placeholder: 'ui-sortable-placeholder',
				items:       '.wpqbui-orderby-row',
			} );
		}
	} );
} )( window.jQuery );
