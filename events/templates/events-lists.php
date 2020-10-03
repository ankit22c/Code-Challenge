<?php
$args = array(
			'post_type'		=> 'events',
			'posts_per_page'=> -1,
			'post_status'	=> 'publish',
			'orderby'       => 'date',
			'order'         => 'desc',
		);

if ( isset( $_POST['action'] ) && $_POST['action'] != '' ) {

	/* Search from Event Types Taxonomy */
	if ( isset( $_POST['event_types'] ) && $_POST['event_types'] != '' ) {
		$args['tax_query']['relation'] = 'AND';
		$args['tax_query'][]= array(
								'taxonomy' => 'event-types',
								'field'    => 'id',
								'terms'    => $_POST['event_types'],
							);
	}

	/* Search from Event Start Date */
	if ( isset($_POST['event_start_date']) && $_POST['event_start_date'] != '' ) {
		$args['meta_query']['relation'] = 'AND';
		$args['meta_query'][]= array(
								'key' 		=> '_start_date',
								'value'    	=> $_POST['event_start_date'],
								'compare'   => '=',
							);
	}

}

$events_posts = new WP_Query( $args );
?>

<div id="events-lists" class="events-lists">
	<!-- I don't use table format but here I have used table format to reduce css. -->
	<table>
		<thead>
			<tr>
				<td><?php esc_html_e( 'No', 'events' ); ?></td>
				<td><?php esc_html_e( 'Event Name', 'events' ); ?></td>
				<td><?php esc_html_e( 'Event Types', 'events' ); ?></td>
				<td><?php esc_html_e( 'Start Date', 'events' ); ?></td>
				<td><?php esc_html_e( 'End Date', 'events' ); ?></td>
				<td><?php esc_html_e( 'Event Venue', 'events' ); ?></td>
				<td><?php esc_html_e( 'Location', 'events' ); ?></td>
			</tr>
		</thead>
		<tbody id="load-events">
			<?php if ( $events_posts->have_posts() ) : ?>

				<?php $i=1; while ( $events_posts->have_posts() ) : $events_posts->the_post();

						$_start_date	= get_post_meta( get_the_ID(), '_start_date', true );
						$_end_date		= get_post_meta( get_the_ID(), '_end_date', true );
						$_event_venue	= get_post_meta( get_the_ID(), '_event_venue', true );
						$_location		= get_post_meta( get_the_ID(), '_location', true );

						$event_types = get_the_term_list( get_the_ID(), 'event-types', '', ',');

				?>
					<tr>
						<td><?php echo $i++;?></td>
						<td>
							<?php the_title( '<h6 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h6>' );  ?>
						</td>
						<td>
							<?php echo $event_types;?>
						</td>
						<td>
							<?php echo $_start_date; ?>
						</td>
						<td>
							<?php echo $_end_date; ?>
						</td>
						<td>
							<?php echo $_event_venue; ?>
						</td>
						<td>
							<?php echo $_location; ?>
						</td>
					</tr>
				<?php endwhile;?>
			<?php else:?>
				<tr>
					<td colspan="7" align="center"><?php esc_html_e( 'No Event found...', 'events' ); ?></td>
				</tr>
			<?php endif;
			wp_reset_postdata();
			?>
		</tbody>
	</table>
</div>