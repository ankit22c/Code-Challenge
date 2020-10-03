<?php

if ( isset( $_POST['action'] ) && $_POST['action'] != '' ) {
	$fileName = $_FILES["event_import"]["tmp_name"];
	if ( $_FILES["event_import"]["size"] > 0 ) {
		  
		$file = fopen( $fileName, "r" );
		
		while ( ( $column = fgetcsv( $file, 10000, "," ) ) !== FALSE ) {

            $event_name = "";
            if ( isset( $column[1] ) && $column[1] != 'Event Name' ) {
                $event_name = $column[1];
            }
			
			$event_content = "";
            if ( isset( $column[2] ) && $column[2] != 'Event Content' ) {
                $event_content = $column[2];
            }
			
			$event_types = array();
            if ( isset( $column[3] ) && $column[3] != 'Event Types' ) {
				$event_types = explode( ",", $column[3] );
            }
			
			$event_start_date = "";
            if ( isset( $column[4] ) && $column[4] != 'Start Date' ) {
                $event_start_date = $column[4];
            }
			
			$event_end_date = "";
            if ( isset( $column[5] ) && $column[5] != 'End Date' ) {
                $event_end_date = $column[5];
            }
			
			$event_venue = "";
            if ( isset( $column[6] ) && $column[6] != 'Event Venue' ) {
                $event_venue = $column[6];
            }
			
			$event_location = "";
            if ( isset( $column[7] ) && $column[7] != 'Location' ) {
                $event_location = $column[7];
            }
			
			if( $event_name != '' ) {
				$event_post_arr = array(
					'post_title'   => $event_name,
					'post_content' => $event_content,
					'post_status'  => 'publish',
					'post_type'	   => 'events',
					'post_author'  => get_current_user_id(),
				);
				
				$event_post_id = wp_insert_post( $event_post_arr, $wp_error );
				
				wp_set_object_terms( $event_post_id, $event_types, 'event-types' );
				
				update_post_meta( $event_post_id, '_start_date', $event_start_date );
				update_post_meta( $event_post_id, '_end_date', $event_end_date );
				update_post_meta( $event_post_id, '_event_venue', $event_venue );
				update_post_meta( $event_post_id, '_location', $event_location );
				
				if ( ! empty( $event_post_id ) ) {
					$type = "success";
					$message = "Events Import Successfully";
				} else {
					$type = "error";
					$message = "Events not Import. Please try again";
				}
				
			}
            
        }
	}
	unlink($_FILES['event_import']['tmp_name']);
	echo $message;
}