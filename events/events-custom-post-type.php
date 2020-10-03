<?php
/* Register Events custom Post type */
$labels = array(
	'name'                 => esc_html__( 'Events', 'events' ),
	'singular_name'        => esc_html__( 'event', 'events' ),
	'menu_name'            => esc_html__( 'Events', 'events' ),
	'name_admin_bar'       => esc_html__( 'Events', 'events' ),
	'add_new'              => esc_html__( 'Add New', 'events' ),
	'add_new_item'         => esc_html__( 'Add New Event', 'events' ),
	'new_item'             => esc_html__( 'New Event', 'events' ),
	'edit_item'            => esc_html__( 'Edit Event', 'events' ),
	'view_item'            => esc_html__( 'View Event', 'events' ),
	'all_items'            => esc_html__( 'All Events', 'events' ),
	'search_items'         => esc_html__( 'Search Events', 'events' ),
	'parent_item_colon'    => esc_html__( 'Parent Events:', 'events' ),
	'not_found'            => esc_html__( 'No Events found.', 'events' ),
	'not_found_in_trash'   => esc_html__( 'No Events found in Trash.', 'events' ),
	'featured_image'       => esc_html__( 'Event Image', 'events' ),
	'set_featured_image'   => esc_html__( 'Set Event Image', 'events' ),
	'remove_featured_image'=> esc_html__( 'Remove Event Image', 'events' ),
	'use_featured_image'   => esc_html__( 'Use Event Image', 'events' ),
);

$cpt_events_args = array(
	'labels'             => $labels,	
	'public'             => true,
	'publicly_queryable' => true,
	'show_ui'            => true,
	'show_in_menu'       => true,
	'query_var'          => true,
	'rewrite'            => array( 'slug' => 'events' ),
	'capability_type'    => 'post',
	'has_archive'        => false,
	'hierarchical'       => false,
	'exclude_from_search'=> true,
	'menu_position'      => null,
	'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'tags' ),
	'menu_icon'          => 'dashicons-portfolio',
);

$cpt_events_args = apply_filters( 'events_register_post_type',  $cpt_events_args );
register_post_type( 'events', $cpt_events_args );

/* Event Types taxonomy */
$labels = array(
	'name'                       => esc_html__( 'Event Types', 'events' ),
	'singular_name'              => esc_html__( 'Event Type', 'events' ),
	'search_items'               => esc_html__( 'Search Event Type', 'events' ),
	'popular_items'              => esc_html__( 'Popular Event Type', 'events' ),
	'all_items'                  => esc_html__( 'All Event Types', 'events' ),	
	'parent_item'       		 => esc_html__( 'Parent Event Types', 'events' ),
	'parent_item_colon' 		 => esc_html__( 'Parent Event Types:', 'events' ),
	'edit_item'                  => esc_html__( 'Edit Event Type', 'events' ),
	'update_item'                => esc_html__( 'Update Event Type', 'events' ),
	'add_new_item'               => esc_html__( 'Add New Event Type', 'events' ),
	'new_item_name'              => esc_html__( 'New Event Type Name', 'events' ),
	'separate_items_with_commas' => esc_html__( 'Separate Event Type with commas', 'events' ),
	'add_or_remove_items'        => esc_html__( 'Add or remove Event Type', 'events' ),
	'choose_from_most_used'      => esc_html__( 'Choose from the most used Event Type', 'events' ),
	'not_found'                  => esc_html__( 'No Event Type found.', 'events' ),
	'menu_name'                  => esc_html__( 'Event Types', 'events' ),
);

$events_types_args = array(
	'hierarchical'          => true,
	'labels'                => $labels,
	'show_ui'               => true,
	'show_admin_column'     => true,
	'public'                => true,
	'update_count_callback' => '_update_post_term_count',
	'query_var'             => true,
	'rewrite'               => array( 'slug' => 'event-types' ),
);	
$events_types_args = apply_filters( 'events_types_register_taxonomy', $events_types_args, 'events' );
register_taxonomy( 'event-types', 'events', $events_types_args );