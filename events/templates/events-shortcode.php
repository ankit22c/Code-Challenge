<?php
/*
 * Search Events shortcode template file
 */
?>
<div class="events-shortcode alignwide">
	<div class="container">
		<div class="events-search-box">
			<h2><?php esc_html_e( 'Event Search', 'events' );?></h2>
			<form id="events-search-form" action="" >
				<div class="row">
					<div class="col-4">
						<label><?php esc_html_e( 'Event Start Date', 'events' ); ?></label>
						<input type="text" name="event_start_date" value="" class="event-start-date events-datepicker" />
					</div>
					<div class="col-4">
						<label><?php esc_html_e( 'Event Types', 'events' ); ?></label>
						<?php $event_types = events_custom_taxonomy( 'event-types' ); ?>
						
						<select name="event_types">
							<option value=""><?php esc_html_e( 'Select Event Type', 'events' ); ?></option>
							<?php foreach( $event_types as $key=>$value ): ?>
								<option value="<?php echo $key;?>"><?php echo $value; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-4">
						<input type="submit" id="events-submit" name="submit" value="Search" />
						<span class="event-loading"><?php esc_html_e( 'Loading....', 'events' ); ?></span>
					</div>
				</div>
			</form>
		</div>

		<?php events_get_template( 'events-lists.php' ); ?>
	</div>
</div>