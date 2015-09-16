[![Latest Stable Version](https://img.shields.io/wordpress/plugin/v/the-events-calendar-shortcode.svg?style=flat-square)](https://packagist.org/packages/ankitpokhrel/Dynamic-Featured-Image)
[![WordPress](https://img.shields.io/wordpress/v/the-events-calendar-shortcode.svg?style=flat-square)](https://wordpress.org/plugins/dynamic-featured-image/)
[![WordPress Rating](https://img.shields.io/wordpress/plugin/r/the-events-calendar-shortcode.svg?style=flat-square)](https://wordpress.org/plugins/dynamic-featured-image/)
[![Downloads](https://img.shields.io/wordpress/plugin/dt/the-events-calendar-shortcode.svg?style=flat-square)](https://wordpress.org/plugins/the-events-calendar-shortcode/)

# The Events Calendar Shortcode

_Adds shortcode functionality to The Events Calendar Plugin (Free Version) by Modern Tribe._

## Overview
The shortcode displays lists of your events. You can control the event display with the shortcode options. Example shortcode to show next 8 events in the category festival in ASC order with date showing [ecs-list-events cat='festival' limit='8'].

## Shortcode Options
1. Basic shortcode:  
  ```
  [ecs-list-events]
  ```
  
2. cat - Represents event category.  
  ```
  [ecs-list-events cat='festival']
  [ecs-list-events cat='festival, personal']
  ```
  
3. limit - Total number of events to show. Default is 5.  
  ```
  [ecs-list-events limit='3']
  ```
  
4. order - Order of the events to be shown. Value can be `ASC` or `DESC`. Default is `ASC`. Order is based on event date.  
  ```
  [ecs-list-events order='DESC']
  ```

5. author - To only show events posted by particular authors. Value should be a user ID, or several delimited by commas.  
  ```
  [ecs-list-events author='2'] or [ecs-list-events author='2,3,6,42'].
  ```
  
6. date - To show or hide date. Value can be `true` or `false`. Default is `true`.  
  ```
  [ecs-list-events eventdetails='false']
  ```
  
7. venue - To show or hide the venue. Value can be `true` or `false`. Default is `false`.  
  ```
  [ecs-list-events venue='true']
  ```
  
8. excerpt - To show or hide the excerpt and set excerpt length. Default is false.  
  ```
  [ecs-list-events excerpt='true'] //display excerpt with length 100  
  
  [ecs-list-events excerpt='300'] //display excerpt with length 300
  ```
  
9. thumb - To show or hide thumbnail image. Default is `false`.  
  ```
  //display post thumbnail in default thumbnail dimension from media settings.  
  
  [ecs-list-events thumb='true'] 
  ```  
  You can use 2 other attributes: thumbwidth and thumbheight to customize the thumbnail size  
  ```
  [ecs-list-events thumb='true' thumbwidth='150' thumbheight='150']
  ```
  
10. message - Message to show when there are no events. Defaults to 'There are no upcoming events at this time.'

11. viewall - Determines whether to show 'View all events' or not. Values can be `true` or `false`. Default to `true`.
  ```  
  [ecs-list-events cat='festival' limit='3' order='DESC' viewall='false']
  ```

12. contentorder - Manage the order of content with commas. Default to `title, thumbnail, excerpt, date, venue`.
  ```  
  [ecs-list-events cat='festival' limit='3' order='DESC' viewall='false' contentorder='title, thumbnail, excerpt, date, venue']
  ```

13. month identifier - Show only specific Month. Type 'current' for displaying current month only.
  ```  
  [ecs-list-events cat='festival' month='2015-06']
  ```

14. past events - Show Outdated Events.
  ```  
  [ecs-list-events cat='festival' past='yes']
  ```

15. order key - Order with Start Date.
  ```  
  [ecs-list-events cat='festival' key='start date']
  ```

## Shortcode Filters With Examples

**ecs_event_list_title**

    add_filter( 'ecs_event_list_title', 'filter_category_name_from_title', 10, 2 );
    function filter_category_name_from_title( $title, $atts ) {

	    if ( ( isset( $atts['cat'] ) ) && ( $atts['cat'] !== '' ) && ( strpos( $atts['cat'], ',' ) === false ) ) { // If multiple Categories specified, bail

		    $atts['cat'] = trim( $atts['cat'] );

		    $term = get_term_by( 'name', $atts['cat'], 'tribe_events_cat', 'OBJECT' );

		    ( $term === false ? $term = get_term_by( 'slug', $atts['cat'], 'tribe_events_cat', 'OBJECT' ) : '' ); // If the slug was strangely provided

		    $title = str_replace( $term->name . ' &#8211; ', '', $title ); // Filter "<CATEGORY NAME> - " from title. Must be HTML Entity in the str_replace().

		    $title = trim( $title );

	    }

	    return $title;

    }

**ecs_event_list_details**

    add_filter( 'ecs_event_list_details', 'change_events_time_to_link', 10, 2 );
    function change_events_time_to_link( $text, $atts ) {

	    if ( ( isset( $atts['contentorder'] ) ) && ( ! in_array( 'title', $atts['contentorder'] ) ) ) {

            // If we are not showing the Title, then wrap the Date/Time in a Link to the Event
		    $text = '<a href = "' . tribe_get_event_link() . '" rel = "bookmark">' . $text . '</a>';

	    }

	    return $text;

    }

**ecs_event_list_venue**

    add_filter( 'ecs_event_list_venue', 'change_events_venue_prefix', 10, 2 );
    function change_events_venue_prefix( $text, $atts ) {

        // Replace <em>at</em> with whatever you would like via str_replace()
	    $text = str_replace( '<em>at</em>', 'Location:', $text );

	    return $text;

    }

**ecs_event_list_viewall_link**

    add_filter( 'ecs_event_list_viewall_link', 'change_events_links_to_category', 10, 2 );
    function change_events_links_to_category( $link, $atts ) {

	    if ( ( isset( $atts['cat'] ) ) && ( $atts['cat'] !== '' ) && ( strpos( $atts['cat'], ',' ) === false ) ) { // If multiple Categories specified, bail

		    $atts['cat'] = trim( $atts['cat'] );

		    $term = get_term_by( 'name', $atts['cat'], 'tribe_events_cat', 'OBJECT' );

		    ( $term === false ? $term = get_term_by( 'slug', $atts['cat'], 'tribe_events_cat', 'OBJECT' ) : '' ); // If the slug was strangely provided

		    $link = get_term_link( $term->term_id, 'tribe_events_cat' );

	    }

	    return $link;

    }

**ecs_event_list_viewall_text**

    add_filter( 'ecs_event_list_viewall_text', 'change_events_viewall_text_to_category', 10, 2 );
    function change_events_viewall_text_to_category( $text, $atts ) {

	    if ( ( isset( $atts['cat'] ) ) && ( $atts['cat'] !== '' ) && ( strpos( $atts['cat'], ',' ) === false ) ) { // If multiple Categories specified, bail

		    $atts['cat'] = trim( $atts['cat'] );

		    $term = get_term_by( 'name', $atts['cat'], 'tribe_events_cat', 'OBJECT' );

		    ( $term === false ? $term = get_term_by( 'slug', $atts['cat'], 'tribe_events_cat', 'OBJECT' ) : '' ); // If the slug was strangely provided

            $text = 'View All ' . $term->name . ' Events';

	    }

	    return $text;

    }

## Questions about this project?

Please feel free to report any bug found. Pull requests, issues, and plugin recommendations are more than welcome!
