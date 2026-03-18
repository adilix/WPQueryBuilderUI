/**
 * WP Query Builder UI — Post/Author picker with search-as-you-type.
 */
( function () {
	'use strict';

	function getPostType() {
		const sel = document.getElementById( 'wpqbui-post-type' );
		if ( ! sel ) return 'any';
		const selected = Array.from( sel.selectedOptions ).map( o => o.value );
		return selected.length === 1 ? selected[ 0 ] : 'any';
	}

	function initPostPicker( container ) {
		const field      = container.dataset.field;
		const searchInput = container.querySelector( '.wpqbui-post-search' );
		const dropdown   = container.querySelector( '.wpqbui-post-results' );
		const tagsArea   = container.querySelector( '.wpqbui-tags' );
		if ( ! searchInput || ! dropdown ) return;

		const selected = {}; // { ID: 'title' }

		// Collect already-hidden inputs (for edit mode).
		container.querySelectorAll( `input[type="hidden"][name="query_args[${field}][]"]` ).forEach( inp => {
			selected[ inp.value ] = inp.value;
		} );
		renderTags();

		searchInput.addEventListener( 'input', WPQBUI.debounce( function () {
			const q = searchInput.value.trim();
			if ( ! q ) {
				dropdown.hidden = true;
				return;
			}
			const postType = getPostType();
			WPQBUI.ajax( 'wpqbui_get_posts', { post_type: postType, search: q } ).then( data => {
				renderDropdown( data.posts );
			} ).catch( () => {
				dropdown.hidden = true;
			} );
		}, 300 ) );

		searchInput.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) {
				dropdown.hidden = true;
			}
		} );

		document.addEventListener( 'click', function ( e ) {
			if ( ! container.contains( e.target ) ) {
				dropdown.hidden = true;
			}
		} );

		function renderDropdown( posts ) {
			dropdown.innerHTML = '';
			if ( ! posts || ! posts.length ) {
				const el = document.createElement( 'div' );
				el.className = 'wpqbui-dropdown-item';
				el.textContent = WPQBUI.data.i18n ? WPQBUI.data.i18n.noResults : 'No results';
				dropdown.appendChild( el );
				dropdown.hidden = false;
				return;
			}
			posts.forEach( post => {
				const el = document.createElement( 'div' );
				el.className = 'wpqbui-dropdown-item';
				el.textContent = '#' + post.ID + ' — ' + post.post_title + ' (' + post.post_type + ')';
				el.dataset.id    = post.ID;
				el.dataset.title = post.post_title;
				el.addEventListener( 'click', function () {
					addSelected( post.ID, post.post_title );
					searchInput.value = '';
					dropdown.hidden = true;
				} );
				dropdown.appendChild( el );
			} );
			dropdown.hidden = false;
		}

		function addSelected( id, title ) {
			id = String( id );
			if ( selected[ id ] ) return;
			selected[ id ] = title;
			renderTags();
		}

		function removeSelected( id ) {
			delete selected[ id ];
			renderTags();
		}

		function renderTags() {
			// Remove old hidden inputs.
			container.querySelectorAll( `input[type="hidden"][name="query_args[${field}][]"]` ).forEach( i => i.remove() );
			if ( ! tagsArea ) return;
			tagsArea.innerHTML = '';
			Object.entries( selected ).forEach( ( [ id, title ] ) => {
				// Tag element.
				const tag = document.createElement( 'span' );
				tag.className = 'wpqbui-tag';
				tag.innerHTML = `<span>${ escHTML( title || id ) } (#${ escHTML( id ) })</span><span class="wpqbui-tag-remove" aria-label="Remove" data-id="${ escHTML( id ) }">&#x2715;</span>`;
				tag.querySelector( '.wpqbui-tag-remove' ).addEventListener( 'click', () => removeSelected( id ) );
				tagsArea.appendChild( tag );

				// Hidden input.
				const inp = document.createElement( 'input' );
				inp.type  = 'hidden';
				inp.name  = `query_args[${ field }][]`;
				inp.value = id;
				container.appendChild( inp );
			} );
		}
	}

	function initAuthorPicker( container ) {
		const field       = container.dataset.field;
		const searchInput = container.querySelector( '.wpqbui-author-search' );
		const dropdown    = container.querySelector( '.wpqbui-author-results' );
		const tagsArea    = container.querySelector( '.wpqbui-tags' );
		if ( ! searchInput || ! dropdown ) return;

		const selected = {};
		container.querySelectorAll( `input[type="hidden"][name="query_args[${field}][]"]` ).forEach( inp => {
			selected[ inp.value ] = inp.value;
		} );
		renderTags();

		searchInput.addEventListener( 'input', WPQBUI.debounce( function () {
			const q = searchInput.value.trim();
			WPQBUI.dynamicLoaders.searchAuthors( q ).then( users => {
				renderDropdown( users );
			} );
		}, 300 ) );

		document.addEventListener( 'click', function ( e ) {
			if ( ! container.contains( e.target ) ) dropdown.hidden = true;
		} );

		function renderDropdown( users ) {
			dropdown.innerHTML = '';
			if ( ! users || ! users.length ) {
				dropdown.hidden = true;
				return;
			}
			users.forEach( user => {
				const el = document.createElement( 'div' );
				el.className = 'wpqbui-dropdown-item';
				el.textContent = user.display_name;
				el.addEventListener( 'click', () => {
					addSelected( user.ID, user.display_name );
					searchInput.value = '';
					dropdown.hidden = true;
				} );
				dropdown.appendChild( el );
			} );
			dropdown.hidden = false;
		}

		function addSelected( id, name ) {
			id = String( id );
			if ( selected[ id ] ) return;
			selected[ id ] = name;
			renderTags();
		}

		function removeSelected( id ) {
			delete selected[ id ];
			renderTags();
		}

		function renderTags() {
			container.querySelectorAll( `input[type="hidden"][name="query_args[${field}][]"]` ).forEach( i => i.remove() );
			if ( ! tagsArea ) return;
			tagsArea.innerHTML = '';
			Object.entries( selected ).forEach( ( [ id, name ] ) => {
				const tag = document.createElement( 'span' );
				tag.className = 'wpqbui-tag';
				tag.innerHTML = `<span>${ escHTML( name || id ) }</span><span class="wpqbui-tag-remove" data-id="${ escHTML( id ) }">&#x2715;</span>`;
				tag.querySelector( '.wpqbui-tag-remove' ).addEventListener( 'click', () => removeSelected( id ) );
				tagsArea.appendChild( tag );

				const inp = document.createElement( 'input' );
				inp.type  = 'hidden';
				inp.name  = `query_args[${ field }][]`;
				inp.value = id;
				container.appendChild( inp );
			} );
		}
	}

	function escHTML( str ) {
		return String( str ).replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' ).replace( /"/g, '&quot;' );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.wpqbui-post-picker' ).forEach( initPostPicker );
		document.querySelectorAll( '.wpqbui-author-picker' ).forEach( initAuthorPicker );
	} );
} )();
