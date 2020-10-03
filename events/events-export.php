<?php

$path1 = preg_replace( '/wp-content(?!.*wp-content).*/', '', __DIR__ );
include( $path1.'wp-load.php' );

if ( current_user_can( 'activate_plugins' ) ) {
	
	$events_file = "events_export.csv";
	$events_file_open = fopen( $events_file, "a" ) or die( "Error Couldn't open $events_file for writing!" );
	
	$args = array(
				'post_type'		=> 'events',
				'posts_per_page'=> -1,
				'post_status'	=> 'publish',
				'orderby'       => 'date',
				'order'         => 'desc',
			);
	$events_posts = new WP_Query( $args );
	
	if ( $events_posts->have_posts() ) :
	
		$i=1; 
		$events_fields_title = array( 'Event ID', 'Event Name', 'Event Content', 'Event Types', 'Start Date', 'End Date', 'Event Venue', 'Location' );
		fputcsv( $events_file_open, $events_fields_title );
		
		while ( $events_posts->have_posts() ) : $events_posts->the_post();
		
			$event_types_name = array();
			$event_types_arrays = wp_get_object_terms( get_the_ID(), 'event-types' );
				
			if( !empty( $event_types_arrays ) ) {
				if( !is_wp_error( $event_types_arrays ) ) {
					foreach( $event_types_arrays as $event_types_array ) {
						$event_types_name[] = htmlspecialchars_decode( $event_types_array->name );
					}
				}
				else {
					$event_types_name[] = '- error -';
				}
			}
			else {
				$event_types_name[] = '';
			}
					
			$event_ID			= $i++;
			$event_name			= $post->post_title;
			$event_content		= $post->post_content;
			$event_types		= implode( ',', $event_types_name );
			$event_start_date	= get_post_meta( get_the_ID(), '_start_date', true );
			$event_end_date		= get_post_meta( get_the_ID(), '_end_date', true );
			$event_venue		= get_post_meta( get_the_ID(), '_event_venue', true );
			$event_location		= get_post_meta( get_the_ID(), '_location', true );

			$events_fields = array( $event_ID, $event_name, $event_content, $event_types, $event_start_date, $event_end_date, $event_venue, $event_location );		
			fputcsv( $events_file_open, $events_fields );
			
		endwhile;
		
		fclose( $events_file_open );

		ignore_user_abort(true);
		set_time_limit(0); // disable the time limit for this script

		 // change the path to fit your websites document structure
		$path = plugins_url( '/', __FILE__ );
		$dl_file = preg_replace( "([^\w\s\d\-_~,;:\[\]\(\].]|[\.]{2,})", '', $_GET['download_file'] ); // simple file name validation
		$dl_file = filter_var( $dl_file, FILTER_SANITIZE_URL ); // Remove (more) invalid characters
		$fullPath = $path.$dl_file;

		if ( $fd = fopen( $fullPath, "r" ) ) {
			$path_parts = pathinfo( $fullPath );
			$ext = strtolower( $path_parts["extension"] );
			switch ( $ext ) {
				case "csv":
				header("Content-type: application/csv");
				header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] . "\""); // use 'attachment' to force a file download
				break;
				// add more headers for other content types here
				default;
				header( "Content-type: application/octet-stream" );
				header( "Content-Disposition: filename=\"" . $path_parts["basename"] . "\"" );
				break;
			}
			header( "Cache-control: private" ); //use this to open files directly
			while( !feof( $fd ) ) {
				$buffer = fread( $fd, 2048 );
				echo $buffer;
			}
		}
		fclose( $fd );
	endif;
	wp_reset_postdata();
	
	$events_file = "events_export.csv";
	unlink( $events_file );

	exit;

}