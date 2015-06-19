<?php
/***
 Plugin Name: The Events Calendar Shortcode
 Plugin URI: http://dandelionwebdesign.com/downloads/shortcode-modern-tribe/
 Description: An addon to add shortcode functionality for <a href="http://wordpress.org/plugins/the-events-calendar/">The Events Calendar Plugin (Free Version) by Modern Tribe</a>.
 Version: 1.0.7
 Author: Dandelion Web Design Inc.
 Author URI: http://dandelionwebdesign.com
 License: GPL2 or later
 License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Avoid direct calls to this file
if ( !defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Events calendar shortcode addon main class
 *
 * @package events-calendar-shortcode
 * @author Dandelion Web Design Inc.
 * @version 1.0.7
 */
class Events_Calendar_Shortcode
{
	/**
	 * Current version of the plugin.
	 *
	 * @since 1.0.0
	 */
	const VERSION = '1.0.7';

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see	 add_shortcode()
	 */
	public function __construct()
	{
		add_shortcode('ecs-list-events', array($this, 'ecs_fetch_events') ); // link new function to shortcode name
	} // END __construct()

	/**
	 * Fetch and return required events.
	 * @param  array $atts 	shortcode attributes
	 * @return string 	shortcode output
	 */
	public function ecs_fetch_events( $atts )
	{
		/**
		 * Check if events calendar plugin method exists
		 */
		if ( !function_exists( 'tribe_get_events' ) ) {
			return;
		}

		global $wp_query, $post;
		$output = '';
		$ecs_event_tax = '';

		extract( shortcode_atts( array(
			'cat' => '',
			'month' => '',
			'limit' => 5,
			'eventdetails' => 'true',
			'time' => null,
			'past' => null,
			'venue' => 'false',
			'author' => null,
			'message' => 'There are no upcoming events at this time.',
			'key' => 'End Date',
			'order' => 'ASC',
			'viewall' => 'false',
			'excerpt' => 'false',
			'thumb' => 'false',
			'thumbwidth' => '',
			'thumbheight' => '',
			'contentorder' => 'title, thumbnail, excerpt, date, venue'
		), $atts, 'ecs-list-events' ), EXTR_PREFIX_ALL, 'ecs' );

		// Category
		if ( $ecs_cat ) {
			$ecs_cats = explode( ",", $ecs_cat );
			$ecs_cats = array_map( 'trim', $ecs_cats );
			$ecs_event_tax = array(
				array(
					'taxonomy' => 'tribe_events_cat',
					'field' => 'slug',
					'terms' => $ecs_cat
				)
			);
		}

		// Past Event
		$meta_date_compare = '>=';
		$meta_date_date = date( 'Y-m-d' );

		if ( $ecs_time == 'past' || !empty( $ecs_past ) ) {
			$meta_date_compare = '<';
		}

		// Key
		if ( str_replace( ' ', '', trim( strtolower( $ecs_key ) ) ) == 'startdate' ) {
			$ecs_key = '_EventStartDate';
		} else {
			$ecs_key = '_EventEndDate';
		}
		// Date
		$ecs_meta_date = array(
			array(
				'key' => $ecs_key,
				'value' => $meta_date_date,
				'compare' => $meta_date_compare,
				'type' => 'DATETIME'
			)
		);

		// Specific Month
		if ($ecs_month) {
			$month_array = explode("-", $ecs_month);

			$month_yearstr = $month_array[0];
			$month_monthstr = $month_array[1];

			$month_startdate = date($month_yearstr . "-" . $month_monthstr . "-1");
			$month_enddate = date($month_yearstr . "-" . $month_monthstr . "-t");

			$ecs_meta_date = array(
				array(
					'key' => $ecs_key,
					'value' => array($month_startdate, $month_enddate),
					'compare' => 'BETWEEN',
					'type' => 'DATETIME'
				)
			);
		}

		$posts = get_posts( array(
			'post_type' => 'tribe_events',
			'posts_per_page' => $ecs_limit,
			'tax_query'=> $ecs_event_tax,
			'meta_key' => $ecs_key,
			'orderby' => 'meta_value',
			'author' => $ecs_author,
			'order' => $ecs_order,
			'meta_query' => array( $ecs_meta_date )
		) );

		if ($posts) {
			$output .= '<ul class="ecs-event-list">';
			$ecs_contentorder = explode( ',', $ecs_contentorder );

			foreach( $posts as $post ) :
				setup_postdata( $post );

				$output .= '<li class="ecs-event">';

				// Put Values into $output
				foreach ( $ecs_contentorder as $contentorder ) {
					switch ( trim( $contentorder ) ) {
						case 'title' :
							$output .= '<h4 class="entry-title summary">' .
											'<a href="' . tribe_get_event_link() . '" rel="bookmark">' . get_the_title() . '</a>
										</h4>';
							break;

						case 'thumbnail' :
							if( self::isValid($ecs_thumb) ) {
								$thumbWidth = is_numeric($ecs_thumbwidth) ? $ecs_thumbwidth : '';
								$thumbHeight = is_numeric($ecs_thumbheight) ? $ecs_thumbheight : '';
								if( !empty($thumbWidth) && !empty($thumbHeight) ) {
									$output .= get_the_post_thumbnail(get_the_ID(), array($thumbWidth, $thumbHeight) );
								} else {
									$output .= get_the_post_thumbnail(get_the_ID(), 'medium');
								}
							}
							break;

						case 'excerpt' :
							if( self::isValid($ecs_excerpt) ) {
								$excerptLength = is_numeric($ecs_excerpt) ? $ecs_excerpt : 100;
								$output .= '<p class="ecs-excerpt">' .
												self::get_excerpt($excerptLength) .
											'</p>';
							}
							break;

						case 'date' :
							if( self::isValid($ecs_eventdetails) ) {
								$output .= '<span class="duration time">' . tribe_events_event_schedule_details() . '</span>';
							}
							break;

						case 'venue' :
							if( self::isValid($ecs_venue) ) {
								$output .= '<span class="duration venue"><em> at </em>' . tribe_get_venue() . '</span>';
							}
							break;
					}
				}
				$output .= '</li>';
			endforeach;
			$output .= '</ul>';

			if( self::isValid($ecs_viewall) ) {
				$output .= '<span class="ecs-all-events"><a href="' . tribe_get_events_link() . '" rel="bookmark">' . translate( 'View All Events', 'tribe-events-calendar' ) . '</a></span>';
			}

		} else { //No Events were Found
			$output .= translate( $ecs_message, 'tribe-events-calendar' );
		} // endif

		wp_reset_query();

		return $output;
	}

	/**
	 * Checks if the plugin attribute is valid
	 *
	 * @since 1.0.5
	 *
	 * @param string $prop
	 * @return boolean
	 */
	private function isValid( $prop )
	{
		return ($prop !== 'false');
	}

	/**
	 * Fetch and trims the excerpt to specified length
	 *
	 * @param integer $limit Characters to show
	 * @param string $source  content or excerpt
	 *
	 * @return string
	 */
	private function get_excerpt( $limit, $source = null )
	{
		$excerpt = get_the_excerpt();
		if( $source == "content" ) {
			$excerpt = get_the_content();
		}

		$excerpt = preg_replace(" (\[.*?\])", '', $excerpt);
		$excerpt = strip_tags( strip_shortcodes($excerpt) );
		$excerpt = substr($excerpt, 0, $limit);
		$excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));
		$excerpt .= '...';

		return $excerpt;
	}
}

/**
 * Instantiate the main class
 *
 * @since 1.0.0
 * @access public
 *
 * @var	object	$events_calendar_shortcode holds the instantiated class {@uses Events_Calendar_Shortcode}
 */
global $events_calendar_shortcode;
$events_calendar_shortcode = new Events_Calendar_Shortcode();
