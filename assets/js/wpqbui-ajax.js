/**
 * WP Query Builder UI — AJAX helper.
 */
( function () {
	'use strict';

	/**
	 * Make an admin-ajax.php POST request.
	 *
	 * @param {string} action  wp_ajax_ action (without prefix).
	 * @param {Object} data    Additional POST fields.
	 * @returns {Promise<any>} Resolves with response.data on success.
	 */
	WPQBUI.ajax = function ( action, data ) {
		const body = new FormData();
		body.append( 'action', action );
		body.append( 'nonce', WPQBUI.data.nonce || '' );

		if ( data && typeof data === 'object' ) {
			Object.entries( data ).forEach( ( [ key, value ] ) => {
				if ( value !== null && value !== undefined ) {
					if ( typeof value === 'object' ) {
						body.append( key, JSON.stringify( value ) );
					} else {
						body.append( key, value );
					}
				}
			} );
		}

		return fetch( WPQBUI.data.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body,
		} )
			.then( res => {
				if ( ! res.ok ) {
					throw new Error( 'HTTP ' + res.status );
				}
				return res.json();
			} )
			.then( json => {
				if ( ! json.success ) {
					const msg = json.data && json.data.message ? json.data.message : 'Unknown error';
					throw new Error( msg );
				}
				return json.data;
			} );
	};
} )();
