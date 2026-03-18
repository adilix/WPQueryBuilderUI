/**
 * WP Query Builder UI — Shared namespace and utilities.
 * Must be loaded first (all other modules depend on it).
 */
/* global wpqbuiData */
( function () {
	'use strict';

	window.WPQBUI = window.WPQBUI || {};

	WPQBUI.data = typeof wpqbuiData !== 'undefined' ? wpqbuiData : {};
	WPQBUI.version = '2.0.0';

	/**
	 * Debounce: returns a function that delays invoking fn until after wait ms.
	 */
	WPQBUI.debounce = function ( fn, wait ) {
		let timer;
		return function ( ...args ) {
			clearTimeout( timer );
			timer = setTimeout( () => fn.apply( this, args ), wait );
		};
	};

	/**
	 * Deep-get a nested value by dot-path: deepGet(obj, 'a.b.c')
	 */
	WPQBUI.deepGet = function ( obj, path ) {
		return path.split( '.' ).reduce( ( acc, key ) => ( acc && acc[ key ] !== undefined ? acc[ key ] : undefined ), obj );
	};

	/**
	 * Collect all form field values inside a container as a plain object.
	 */
	WPQBUI.serializeContainer = function ( container ) {
		const data = {};
		const inputs = container.querySelectorAll( 'input, select, textarea' );
		inputs.forEach( el => {
			if ( ! el.name ) return;
			if ( ( el.type === 'checkbox' || el.type === 'radio' ) && ! el.checked ) return;
			WPQBUI.setNestedValue( data, el.name, el.value );
		} );
		return data;
	};

	/**
	 * Set a value in a nested object based on a name like "a[b][c]".
	 */
	WPQBUI.setNestedValue = function ( obj, name, value ) {
		const parts = name.replace( /\]/g, '' ).split( '[' );
		let current = obj;
		for ( let i = 0; i < parts.length - 1; i++ ) {
			const part = parts[ i ];
			if ( current[ part ] === undefined ) {
				current[ part ] = /^\d+$/.test( parts[ i + 1 ] ) ? [] : {};
			}
			current = current[ part ];
		}
		const last = parts[ parts.length - 1 ];
		if ( Array.isArray( current ) || ( current[ last ] !== undefined ) ) {
			if ( current[ last ] === undefined ) {
				current[ last ] = value;
			} else {
				if ( ! Array.isArray( current[ last ] ) ) {
					current[ last ] = [ current[ last ] ];
				}
				current[ last ].push( value );
			}
		} else {
			current[ last ] = value;
		}
	};

	/**
	 * Show a temporary copy-confirmation message on a button.
	 */
	WPQBUI.flashCopied = function ( btn ) {
		const orig = btn.textContent;
		btn.textContent = WPQBUI.data.i18n ? WPQBUI.data.i18n.copied : 'Copied!';
		btn.disabled = true;
		setTimeout( () => {
			btn.textContent = orig;
			btn.disabled = false;
		}, 1500 );
	};
} )();
