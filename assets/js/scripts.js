jQuery( document ).ready(
	function () {
		jQuery( document ).on(
			'click',
			'.wsal-installer-notice .notice-dismiss',
			function () {
				jQuery.ajax(
					{
						url: WSALCoreData.ajaxURL,
						data: {
							action: 'dismiss_notice'
						}
					}
				)
			}
		);

		// Add on installer
		jQuery( ".install-wsal:not(.disabled)" ).click(
			function (e) {
				jQuery( this ).html( WSALCoreData.installing );
				var currentButton     = jQuery( this );
				var PluginSlug        = jQuery( this ).attr( 'data-plugin-slug' );
				var nonceValue        = jQuery( this ).attr( 'data-nonce' );
				var PluginDownloadUrl = jQuery( this ).attr( 'data-plugin-download-url' );
				var RedirectToTab     = jQuery( this ).attr( 'data-plugin-event-tab-id' );
				var isNetworkInstall  = jQuery( this ).data( 'plugins-network' );
				jQuery( currentButton ).next( '.spinner' ).show( '200' );
				e.preventDefault();
				jQuery.ajax(
					{
						type: 'POST',
						dataType : "json",
						url: WSALCoreData.ajaxURL,
						data : {
							action: "run_wsal_install",
							plugin_slug: PluginSlug,
							plugin_url: PluginDownloadUrl,
							is_network: isNetworkInstall,
							_wpnonce: nonceValue
						},
						complete: function ( data ) {
							if (data.responseText == '"already_installed"' ) {
								jQuery( currentButton ).html( WSALCoreData.already_installed ).addClass( 'disabled' );
								jQuery( currentButton ).next( '.spinner' ).hide( '200' );
							} else if (data.responseText == '"activated"' ) {
								jQuery( currentButton ).html( WSALCoreData.activated ).addClass( 'disabled' );
								jQuery( currentButton ).next( '.spinner' ).hide( '200' );
							} else if (JSON.stringify( data.responseText ).toLowerCase().indexOf( 'failed' ) >= 0 ) {
								jQuery( currentButton ).html( WSALCoreData.failed ).addClass( 'disabled' );
								jQuery( currentButton ).next( '.spinner' ).hide( '200' );
							} else if (data.responseText == '"success"' || JSON.stringify( data.responseText ).toLowerCase().indexOf( 'success' ) >= 0 ) {
								jQuery( currentButton ).html( WSALCoreData.installed ).addClass( 'disabled' );
								jQuery( currentButton ).next( '.spinner' ).hide( '200' );
								// Reload as tabs are not present on page.
								location.reload();
							}
						},
					}
				);
			}
		);

		jQuery( ".activate-addon" ).click(
			function (e) {
				var PluginSlug = jQuery( this ).attr( 'data-plugin-slug' );
				jQuery('[data-plugin="'+ PluginSlug +'"] .activate a')[0].click();
			}
		);

	}
);
