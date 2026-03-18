/**
 * WP Query Builder UI — Date Query repeatable rows.
 */
( function () {
	'use strict';

	let rowIndex = 0;

	function wireRow( row ) {
		const afterInput  = row.querySelector( '.wpqbui-date-after' );
		const beforeInput = row.querySelector( '.wpqbui-date-before' );
		const idx         = row.dataset.index;

		// Convert date input value → hidden year/month/day fields for submission.
		function splitDate( dateVal, prefix ) {
			if ( ! dateVal ) return;
			const [ y, m, d ] = dateVal.split( '-' );
			setHiddenDatePart( row, idx, prefix, 'year', y );
			setHiddenDatePart( row, idx, prefix, 'month', m );
			setHiddenDatePart( row, idx, prefix, 'day', d );
		}

		function validateDates() {
			if ( ! afterInput || ! beforeInput ) return;
			const after  = afterInput.value ? new Date( afterInput.value ) : null;
			const before = beforeInput.value ? new Date( beforeInput.value ) : null;
			if ( after && before && after > before ) {
				afterInput.setCustomValidity( 'After date cannot be later than before date.' );
			} else {
				afterInput.setCustomValidity( '' );
			}
		}

		if ( afterInput ) {
			afterInput.addEventListener( 'change', function () {
				splitDate( afterInput.value, 'after' );
				validateDates();
			} );
		}
		if ( beforeInput ) {
			beforeInput.addEventListener( 'change', function () {
				splitDate( beforeInput.value, 'before' );
				validateDates();
			} );
		}

		// Remove row.
		const removeBtn = row.querySelector( '.wpqbui-remove-row' );
		if ( removeBtn ) {
			removeBtn.addEventListener( 'click', () => row.remove() );
		}
	}

	function setHiddenDatePart( row, idx, prefix, part, value ) {
		const name = `query_args[date_query][rows][${idx}][${prefix}][${part}]`;
		let inp = row.querySelector( `[name="${CSS.escape( name )}"]` );
		if ( ! inp ) {
			inp = document.createElement( 'input' );
			inp.type = 'hidden';
			inp.name = name;
			row.appendChild( inp );
		}
		inp.value = parseInt( value, 10 ) || 0;
	}

	function addRow() {
		const container = document.getElementById( 'wpqbui-date-rows' );
		const template  = document.getElementById( 'wpqbui-date-row-template' );
		if ( ! container || ! template ) return;

		rowIndex++;
		const html   = template.innerHTML.replace( /__INDEX__/g, String( rowIndex ) );
		const tmp    = document.createElement( 'div' );
		tmp.innerHTML = html;
		const newRow = tmp.firstElementChild;
		newRow.dataset.index = rowIndex;
		container.appendChild( newRow );
		wireRow( newRow );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '#wpqbui-date-rows .wpqbui-date-row' ).forEach( row => {
			rowIndex = Math.max( rowIndex, parseInt( row.dataset.index || 0, 10 ) );
			wireRow( row );
		} );
		document.querySelector( '.wpqbui-add-date-row' )
			?.addEventListener( 'click', addRow );
	} );
} )();
