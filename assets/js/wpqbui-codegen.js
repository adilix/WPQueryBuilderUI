/**
 * WP Query Builder UI — Code generation and preview.
 */
( function () {
	'use strict';

	/**
	 * Collect all query_args from the form as a structured object.
	 * Handles standard inputs, checkboxes, selects (including multi-select),
	 * and the special orderby multi-row format.
	 */
	function collectFormData() {
		const form = document.getElementById( 'wpqbui-builder-form' );
		if ( ! form ) return {};

		const data = {};
		const elements = form.querySelectorAll( '[name^="query_args"]' );

		elements.forEach( el => {
			if ( el.disabled ) return;
			if ( el.type === 'checkbox' && ! el.checked ) return;
			if ( el.type === 'radio' && ! el.checked ) return;

			// Multi-select: collect all selected options.
			if ( el.tagName === 'SELECT' && el.multiple ) {
				const vals = Array.from( el.selectedOptions ).map( o => o.value ).filter( v => v !== '' );
				if ( vals.length ) {
					setByName( data, el.name, vals );
				}
				return;
			}

			const val = el.value;
			if ( val === '' && el.type !== 'hidden' ) return;
			setByName( data, el.name, val );
		} );

		// Post-process: convert orderby rows into the expected format.
		if ( data.orderby ) {
			data.orderby = normaliseOrderby( data.orderby );
		}

		// Post-process: convert date_query after/before date inputs.
		if ( data.date_query && data.date_query.rows ) {
			data.date_query.rows = normaliseDateQueryRows( data.date_query.rows );
		}

		return data;
	}

	/**
	 * Convert orderby rows like { 0: { ob: 'date', order: 'DESC' }, … }
	 * into { date: 'DESC', … } for a single-key map, or just the key for one row.
	 */
	function normaliseOrderby( raw ) {
		if ( ! raw || typeof raw !== 'object' ) return raw;

		const rows = Object.values( raw );
		if ( ! rows.length ) return 'date';

		if ( rows.length === 1 ) {
			return rows[ 0 ].ob || 'date';
		}

		const map = {};
		rows.forEach( row => {
			if ( row.ob ) {
				map[ row.ob ] = row.order || 'DESC';
			}
		} );
		return Object.keys( map ).length ? map : 'date';
	}

	function normaliseDateQueryRows( rows ) {
		return Object.values( rows ).map( row => {
			// Convert _after_date (native date input) → { year, month, day }.
			if ( row._after_date ) {
				const [ y, m, d ] = row._after_date.split( '-' );
				row.after = { year: parseInt( y, 10 ), month: parseInt( m, 10 ), day: parseInt( d, 10 ) };
				delete row._after_date;
			}
			if ( row._before_date ) {
				const [ y, m, d ] = row._before_date.split( '-' );
				row.before = { year: parseInt( y, 10 ), month: parseInt( m, 10 ), day: parseInt( d, 10 ) };
				delete row._before_date;
			}
			return row;
		} );
	}

	/**
	 * Set nested value on an object from a name like "query_args[tax_query][rows][0][taxonomy]".
	 * Strips the leading "query_args" segment.
	 */
	function setByName( obj, fullName, value ) {
		const name = fullName.replace( /^query_args/, '' );
		const parts = name.replace( /\]$/, '' ).replace( /^\[/, '' ).split( /\]\[/ );

		let cur = obj;
		for ( let i = 0; i < parts.length - 1; i++ ) {
			const key = parts[ i ];
			if ( cur[ key ] === undefined || cur[ key ] === null ) {
				cur[ key ] = {};
			}
			cur = cur[ key ];
		}

		const last = parts[ parts.length - 1 ];
		if ( Array.isArray( value ) ) {
			cur[ last ] = value;
		} else if ( cur[ last ] !== undefined ) {
			if ( ! Array.isArray( cur[ last ] ) ) {
				cur[ last ] = [ cur[ last ] ];
			}
			cur[ last ].push( value );
		} else {
			cur[ last ] = value;
		}
	}

	// -----------------------------------------------------------------------

	function showSpinner( btn ) {
		btn.disabled = true;
		btn.dataset.origText = btn.textContent;
		btn.textContent = WPQBUI.data.i18n ? WPQBUI.data.i18n.generating : 'Generating…';
	}

	function hideSpinner( btn ) {
		btn.disabled = false;
		btn.textContent = btn.dataset.origText || 'Preview & Generate Code';
	}

	function renderOutput( data ) {
		// Show output section.
		const output = document.getElementById( 'wpqbui-output' );
		if ( output ) output.hidden = false;

		// Resolved args (JSON).
		const resolvedEl = document.getElementById( 'wpqbui-resolved-args' );
		if ( resolvedEl ) {
			resolvedEl.textContent = JSON.stringify( data.resolved_args, null, 2 );
		}

		// PHP code.
		const phpEl = document.getElementById( 'wpqbui-php-code' );
		if ( phpEl ) {
			const code = phpEl.querySelector( 'code' ) || phpEl;
			code.textContent = data.php_code || '';
		}

		// Shortcode.
		const scPanel = document.getElementById( 'wpqbui-shortcode-panel' );
		const scOutput = document.getElementById( 'wpqbui-shortcode-output' );
		if ( data.shortcode && scOutput ) {
			scOutput.textContent = data.shortcode;
			if ( scPanel ) scPanel.hidden = false;
		}

		// Validation messages.
		if ( WPQBUI.validation ) {
			WPQBUI.validation.render( data.validation_results || [] );
		}

		// Scroll to output.
		if ( output ) {
			output.scrollIntoView( { behavior: 'smooth', block: 'start' } );
		}
	}

	// -----------------------------------------------------------------------

	document.addEventListener( 'DOMContentLoaded', function () {
		const previewBtn = document.getElementById( 'wpqbui-btn-preview' );
		const includeLoopCb = document.getElementById( 'wpqbui-include-loop' );

		if ( previewBtn ) {
			previewBtn.addEventListener( 'click', function () {
				const queryArgs  = collectFormData();
				const includeLoop = includeLoopCb && includeLoopCb.checked ? 1 : 0;
				const queryId    = window.wpqbuiCurrentId || 0;

				showSpinner( previewBtn );

				WPQBUI.ajax( 'wpqbui_preview_query', {
					query_args:   queryArgs,
					include_loop: includeLoop,
					query_id:     queryId,
				} ).then( data => {
					renderOutput( data );
				} ).catch( err => {
					const area = document.getElementById( 'wpqbui-validation-messages' );
					if ( area ) {
						area.innerHTML = `<div class="wpqbui-validation-message error">✖  ${ err.message || 'Unknown error' }</div>`;
						area.hidden = false;
					}
				} ).finally( () => {
					hideSpinner( previewBtn );
				} );
			} );
		}

		// Copy buttons.
		document.querySelectorAll( '.wpqbui-copy-btn' ).forEach( btn => {
			btn.addEventListener( 'click', function () {
				const targetId = btn.dataset.target;
				const el       = document.getElementById( targetId );
				if ( ! el ) return;
				const text = el.textContent || '';
				navigator.clipboard.writeText( text ).then( () => {
					WPQBUI.flashCopied( btn );
				} );
			} );
		} );

		// Tab navigation.
		const tabLinks = document.querySelectorAll( '.wpqbui-tab-link' );
		const tabPanels = document.querySelectorAll( '.wpqbui-tab-panel' );

		function activateTab( link ) {
			tabLinks.forEach( l => l.classList.remove( 'active' ) );
			tabPanels.forEach( p => p.classList.remove( 'active' ) );
			link.classList.add( 'active' );
			const target = document.getElementById( 'wpqbui-tab-' + link.dataset.tab );
			if ( target ) target.classList.add( 'active' );
		}

		if ( tabLinks.length ) {
			tabLinks[ 0 ].classList.add( 'active' );
			tabPanels[ 0 ] && tabPanels[ 0 ].classList.add( 'active' );

			tabLinks.forEach( link => {
				link.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					activateTab( link );
				} );
			} );
		}
	} );
} )();
