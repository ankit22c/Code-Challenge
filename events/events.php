<?php
/*
Plugin Name: Events
Plugin URI: https://abc.com/
Description: Events organization with import export events.
Author: xyz
Author URI: https://xyz.com/
Text Domain: events
Domain Path: /languages/
Version: 1.0.0
*/

if ( !defined('ABSPATH') ) {
	exit; // Exit if accessed directly
}

define( 'EVENTS_VERSION', '1.0.0' );
define( 'EVENTS_URL', plugins_url( '/', __FILE__ ) );  // Define Plugin URL
define( 'EVENTS_PATH', plugin_dir_path( __FILE__ ) );  // Define Plugin Directory Path

if ( !class_exists( 'Events' ) ) {

	class Events {

		public function __construct() {

			add_action( 'init',  array( $this, 'events_register_post_types' ) );
			add_action( 'add_meta_boxes', array( $this, 'events_post_type_meta_boxes' ) );
			add_action( 'save_post_events', array( $this, 'events_save_meta_fields' ), 10, 3 );
			add_action( 'admin_enqueue_scripts', array( $this, 'events_admin_script' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'events_wp_script' ) );

			add_shortcode( 'events_search', array( $this, 'events_search_func' ) );

			add_action( 'wp_ajax_events_search', array( $this, 'wp_ajax_events_search_func' ) );
			add_action( 'wp_ajax_nopriv_events_search', array( $this, 'wp_ajax_events_search_func' ) );
			
			add_action( 'wp_ajax_events_import', array( $this, 'wp_ajax_events_import_func' ) );
			add_action( 'wp_ajax_nopriv_events_import', array( $this, 'wp_ajax_events_import_func' ) );

			add_action( 'admin_menu', array( $this, 'events_menu_pages' ) );
			
			register_activation_hook( __FILE__, array( $this, 'events_custom_page' ) );

		}

		/*
		 * enqueue Fronted side js and css file
		 */
		public function events_wp_script() {
			
			wp_register_style( 'bootstrap',  plugins_url( '/css/bootstrap.min.css', __FILE__ ) );
			wp_enqueue_style( 'bootstrap' );
			wp_register_style( 'jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );
			wp_enqueue_style( 'jquery-ui' );

			wp_register_style( 'events', plugins_url( '/css/events.css', __FILE__ ) );
			wp_enqueue_style( 'events' );


			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'events', plugins_url( '/js/events.js', __FILE__ ), array( 'jquery' ) );

			$locale_settings = [
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
				];

			$locale_settings = apply_filters( 'events_localize_settings', $locale_settings );

			wp_localize_script(
				'events',
				'eventsconfig',
				$locale_settings
			);
		}

		/*
		 * enqueue admin side js and css file
		 */
		public function events_admin_script() {

			wp_register_style( 'jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );
			wp_enqueue_style( 'jquery-ui' );
			
			wp_register_style( 'admin-events', plugins_url( '/css/admin-events.css', __FILE__ ) );
			wp_enqueue_style( 'admin-events' );

			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'events', plugins_url( '/js/events.js', __FILE__ ), array( 'jquery' ) );
			
			$locale_settings = [
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
				];

			$locale_settings = apply_filters( 'events_localize_settings', $locale_settings );

			wp_localize_script(
				'events',
				'eventsconfig',
				$locale_settings
			);
		}
		
		/*
		 * Add Events page
		 */
		function events_custom_page() {
			
			$events_page_title = wp_strip_all_tags( 'Events' );
			$events_page_check = get_page_by_title( $events_page_title );

			$events_post = array(
			  'post_title'    => $events_page_title,
			  'post_content'  => '[events_search]',
			  'post_status'   => 'publish',
			  'post_author'   => 1,
			  'post_type'     => 'page',
			);
			
			if( !isset( $events_page_check->ID ) ){
				wp_insert_post( $events_post );
			}
			
		}

		/*
		 * register custom post type
		 */
		public function events_register_post_types() {
			
			include EVENTS_PATH. 'events-custom-post-type.php';

		}

		/*
		 * Add Submenu Page in Events Post Type
		 */
		public function events_menu_pages() {
			
			add_submenu_page( 'edit.php?post_type=events','Events Shortcode', 'Events Shortcode','manage_options', 'events_page', array( $this, 'events_page' ) );
		}

		/*
		 * Add meta box for Events custom post type
		 */
		public function events_post_type_meta_boxes() {

			add_meta_box( 'events-meta-box', esc_html__( 'Events Options', 'events' ), array( $this, 'events_options' ), 'events', 'normal', 'high' );

		}

		/*
		 * Events Post Type custom fields box
		 */
		public function events_options( $post ){
			$post_id = $post->ID;
			
			$_start_date	= get_post_meta( $post_id, '_start_date', true );
			$_end_date		= get_post_meta( $post_id, '_end_date', true );
			$_event_venue	= get_post_meta( $post_id, '_event_venue', true );
			$_location		= get_post_meta( $post_id, '_location', true );
			
			?>
			<table class="form-table" id="form_table">
				<tr class="events-meta-field">
					<th>
						<label><?php esc_html_e( 'Start Date', 'events' )?></label>
					</th>
					<td>
						<input type="text" name="_start_date" value="<?php echo esc_attr( $_start_date );?>" class="regular-text event-start-date-admin events-datepicker" />
					</td>
				</tr>
				<tr class="events-meta-field">
					<th>
						<label><?php esc_html_e( 'End date', 'events' )?></label>
					</th>
					<td>
						<input type="text" name="_end_date" value="<?php echo esc_attr( $_end_date );?>" class="regular-text event-end-date-admin events-datepicker" />
					</td>
				</tr>
				<tr class="events-meta-field">
					<th>
						<label><?php esc_html_e( 'Event Venue', 'events' )?></label>
					</th>
					<td>
						<input type="text" name="_event_venue" value="<?php echo esc_attr( $_event_venue );?>" class="regular-text event-venue-admin" />
					</td>
				</tr>
				<tr class="ad-meta-field">
					<th>
						<label><?php esc_html_e( 'Location', 'events' )?></label>
					</th>
					<td>
						<input type="text" name="_location" value="<?php echo esc_attr( $_location );?>" class="regular-text event-location-admin" />
					</td>
				</tr>
			</table>

			<?php
		}

		/*
		 * Events Shortcode page
		 */
		public function events_page() {
			?>
			<div class="wrap event-information">
				<h1><?php esc_html_e( 'Events Shortcode', 'events' ); ?></h1>
				<div class="event-shortcode-information">
					<code>[events_search]</code>
					<p class="description"><?php esc_html_e( 'Use this shortcode to display Events.', 'events' );?></p>
				</div>
				<div class="event-import-export-wrap">
					<h2><?php esc_html_e( 'Events Import and Export', 'events' ); ?></h2>
					<div class="event-export-block">
						<a class="page-title-action" href="<?php echo plugins_url('events-export.php?download_file=events_export.csv', __FILE__ ); ?>" ><?php esc_html_e( 'Export Events', 'events' ); ?></a>
					</div>
					<div class="event-import-block">
						<form id="events-import-form" action="" method="post" enctype="multipart/form-data" >
							<div class="events-import-form-wrap">
								<label><?php esc_html_e( 'Event import', 'events' ); ?></label>
								<input type="file" name="event_import" value="" class="event-import-input" accept=".csv" required />
							</div>
							<div class="events-import-form-wrap">
								<input type="submit" id="events-import-submit" class="page-title-action" name="submit" value="Import Events" />
								<input type="hidden" id="events-import-action" class="" name="action" value="events_import" />
								<span class="events-sample-csv">
									<?php 
									echo sprintf( wp_kses( __( 'Click <a href="%s">HERE</a> to download sample CSV file.', 'events' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( plugins_url( '/csv/events_import.csv', __FILE__ ) ) );
									?>
								</span>
							</div>
							<div class="events-import-form-msg">
								<p></p>
							</div>
						</form>
					</div>
				</div>
			</div>
			<?php
		}

		/*
		 * Save Events information
		 */
		public function events_save_meta_fields( $post_id, $post, $update ) {
			
			$_start_date	= ( isset( $_POST['_start_date'] ) ) ? $_POST['_start_date'] : '';
			$_end_date		= ( isset( $_POST['_end_date'] ) ) ? $_POST['_end_date'] : '';
			$_event_venue	= ( isset( $_POST['_event_venue'] ) ) ? $_POST['_event_venue'] : '';
			$_location 		= ( isset( $_POST['_location'] ) ) ? $_POST['_location'] : '';
			
			update_post_meta( $post_id, '_start_date', $_start_date );
			update_post_meta( $post_id, '_end_date', $_end_date );
			update_post_meta( $post_id, '_event_venue', $_event_venue );
			update_post_meta( $post_id, '_location', $_location );

		}

		/*
		 * call shortcode page template file.
		 */
		public function events_search_func( $atts ) {

			ob_start();

			events_get_template( 'events-shortcode.php', $atts );

			return ob_get_clean();
		}

		/*
		 * call Bool lists template file on ajax search.
		 */
		public function wp_ajax_events_search_func() {

			events_get_template( 'events-lists.php' );

			wp_die();
		}
		
		/*
		 * call Bool lists template file on ajax search.
		 */
		public function wp_ajax_events_import_func() {

			events_get_template( 'events-import.php' );

			wp_die();
		}
	}
}

new Events();

/**
 * Get other templates passing attributes and including the file.
 *
 */
function events_get_template( $template_name, $args = array(), $template_path = '', $default_path = ''  ) {

	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$located = events_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		/* translators: %s template */
		wc_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'events' ), '<code>' . $located . '</code>' ), '2.1' );
		return;
	}

	include apply_filters( 'events_get_template', $located, $template_name, $args, $template_path, $default_path );

}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 */
function events_locate_template( $template_name, $template_path = '', $default_path = '' ) {

	if ( ! $template_path ) {
		$template_path = apply_filters( 'events_template_path', 'events/' );
	}

	if ( ! $default_path ) {
		$default_path = EVENTS_PATH . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template/.
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}
	// Return what we found.
	return apply_filters( 'events_locate_template', $template, $template_name, $template_path );
}

/*
 * Return custom taxonomy array.
 *
 * @since 1.0.0
 */
function events_custom_taxonomy( $taxonomy = 'category' ) {

    $terms = get_terms( array(
							'taxonomy' => $taxonomy,
							'hide_empty' => true,
						)
					);

    $options = array();
    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}
    }

    return $options;
}