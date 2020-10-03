( function( $ ) {
	"use strict";
	
	jQuery( document ).ready( function() {
		var dateformat = 'mm/dd/yy';
		$( '.event-start-date-admin, .event-end-date-admin' ).datepicker({
	  		minDate: 1,
	  		dateFormat: dateformat,
		});

		$( '.event-start-date' ).datepicker({
	  		dateFormat: dateformat
		});
		
		/* Event Search with filter */
		$( '#events-submit' ).on( 'click', function( event ) {
			
			jQuery.ajax({
				url: eventsconfig.ajaxurl,
				type:'post',
				data: 'action=events_search&' + $( 'form#events-search-form' ).serialize(),				
				beforeSend: function() {
					$( '.event-loading' ).show();
				},
				success: function( data ){
					var $html = $( data );
					$( '.event-loading' ).hide();
					$( '#load-events' ).html( $html.find( '#load-events' ).html() );
				},
				
			});
			
			event .preventDefault();
			return false;
			
		});

		/* Event Import Ajax */
		$( '#events-import-form' ).on( 'submit', function( event ) {
			var totalErrors = 0;
			var extension_list = ["csv"];
			var events_file_string = $( '#events-import-form input[type="file"]' ).val();
			var extension = events_file_string.replace(/^.*\./, '');
			if ( events_file_string != '' && extension != '' && jQuery.inArray(extension, extension_list) == -1 ) {
				$( '#events-import-form input[type="file"]' ).after( "<span class='events-input-message'>Please Upload .csv file extension only</span>" );
				totalErrors++;
			}
			
			if( totalErrors == 0 ) {
				var formData = new FormData( this );

				jQuery.ajax({
					url: eventsconfig.ajaxurl,
					type:'post',
					data: formData,
					contentType: false,
				    processData: false,
				    method: 'POST',		
					beforeSend: function() {
						$( '.events-import-form-msg' ).show();
						$( '.events-import-form-msg p' ).text( 'Loading....' );
					},
					success: function( data ){
						$( '#events-import-form input[type="file"]' ).val( '' );
						$( '.events-import-form-msg' ).show();
						$( '.events-import-form-msg p' ).text( data );
					},
					
				});
			}
			
			event .preventDefault();
			return false;
			
		});
		
	});
	 
})( jQuery );