/**
 * WP Query Builder UI — Saved queries list page interactions.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		// Select-all checkbox.
		const selectAll = document.getElementById( 'wpqbui-select-all' );
		if ( selectAll ) {
			selectAll.addEventListener( 'change', function () {
				document.querySelectorAll( '.wpqbui-saved-table input[type="checkbox"][name="wpqbui_ids[]"]' )
					.forEach( cb => { cb.checked = selectAll.checked; } );
			} );
		}

		// Delete buttons.
		document.querySelectorAll( '.wpqbui-delete-btn' ).forEach( btn => {
			btn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				const id  = btn.dataset.id;
				const row = btn.closest( 'tr' );

				// Inline confirm.
				const i18n = WPQBUI.data.i18n || {};
				const msg  = i18n.confirmDelete || 'Delete this query?';

				// Replace the delete link with confirm/cancel links.
				const span = btn.parentElement;
				span.innerHTML = `<span class="wpqbui-confirm-delete" style="color:#d63638">
					${ escHTML( msg ) }
					<a href="#" class="wpqbui-confirm-yes" data-id="${ escHTML( id ) }"> Yes</a> /
					<a href="#" class="wpqbui-confirm-no"> No</a>
				</span>`;

				span.querySelector( '.wpqbui-confirm-yes' ).addEventListener( 'click', function ( ev ) {
					ev.preventDefault();
					WPQBUI.ajax( 'wpqbui_delete_query', { query_id: id } ).then( () => {
						if ( row ) row.remove();
					} ).catch( err => {
						alert( err.message || 'Delete failed.' );
					} );
				} );

				span.querySelector( '.wpqbui-confirm-no' ).addEventListener( 'click', function ( ev ) {
					ev.preventDefault();
					location.reload();
				} );
			} );
		} );

		// Duplicate buttons.
		document.querySelectorAll( '.wpqbui-duplicate-btn' ).forEach( btn => {
			btn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				const id = btn.dataset.id;
				WPQBUI.ajax( 'wpqbui_duplicate_query', { query_id: id } ).then( data => {
					// Reload to show the new entry.
					window.location.href =
						( WPQBUI.data.ajaxUrl || '' ).replace( 'admin-ajax.php', '' ) +
						'admin.php?page=wpqbui-saved';
				} ).catch( err => {
					alert( err.message || 'Duplicate failed.' );
				} );
			} );
		} );
	} );

	function escHTML( str ) {
		return String( str ).replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' ).replace( /"/g, '&quot;' );
	}
} )();
