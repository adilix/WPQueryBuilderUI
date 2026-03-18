/**
 * WP Query Builder UI — Client-side validation display.
 */
( function () {
	'use strict';

	WPQBUI.validation = {};

	/**
	 * Render validation results from the server into the #wpqbui-validation-messages area.
	 *
	 * @param {Array} results  Array of { field, message, severity } objects.
	 */
	WPQBUI.validation.render = function ( results ) {
		const area = document.getElementById( 'wpqbui-validation-messages' );
		if ( ! area ) return;

		area.innerHTML = '';
		if ( ! results || ! results.length ) {
			area.hidden = true;
			return;
		}

		results.forEach( item => {
			const div = document.createElement( 'div' );
			div.className = 'wpqbui-validation-message ' + ( item.severity || 'warning' );
			const icon = item.severity === 'error' ? '✖' : '⚠';
			div.textContent = icon + '  ' + item.message;
			area.appendChild( div );
		} );

		area.hidden = false;
	};

	/**
	 * @param {Array} results
	 * @returns {boolean}  True if there are any errors (form should not submit).
	 */
	WPQBUI.validation.hasErrors = function ( results ) {
		return results.some( r => r.severity === 'error' );
	};

	// Block form submission if validation errors are present.
	document.addEventListener( 'DOMContentLoaded', function () {
		const form = document.getElementById( 'wpqbui-builder-form' );
		if ( ! form ) return;

		form.addEventListener( 'submit', function ( e ) {
			// If there are displayed error messages, block.
			const area = document.getElementById( 'wpqbui-validation-messages' );
			if ( area && area.querySelector( '.wpqbui-validation-message.error' ) ) {
				e.preventDefault();
				area.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			}
		} );
	} );
} )();
