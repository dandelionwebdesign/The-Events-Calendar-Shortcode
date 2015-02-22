<?php
/***
 Plugin Name: The Events Calendar Shortcode
 Plugin URI: http://dandelionwebdesign.com/downloads/shortcode-modern-tribe/
 Description: An addon to add shortcode functionality for <a href="http://wordpress.org/plugins/the-events-calendar/">The Events Calendar Plugin (Free Version) by Modern Tribe</a>.
 Version: 1.0.6
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
 * @version 1.0.0
 */
class Events_Calendar_Shortcode
{
	/**
	 * Current version of the plugin.
	 *
	 * @since 1.0.0
	 */
	const VERSION = '1.0.6';

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
			'limit' => 5,
			'eventdetails' => 'true',
			'venue' => 'false',
			'message' => 'There are no upcoming events at this time.',
			'order' => 'ASC',
			'viewall' => 'false',
			'excerpt' => 'false',
			'thumb' => 'false',
			'thumbwidth' => '',
			'thumbheight' => ''
		), $atts, 'ecs-list-events' ), EXTR_PREFIX_ALL, 'ecs' );

		if ($ecs_cat) {
			$ecs_event_tax = array(
				array(
					'taxonomy' => 'tribe_events_cat',
					'field' => 'slug',
					'terms' => $ecs_cat
				)
			);
		}

		$posts = get_posts( array(
				'post_type' => 'tribe_events',
				'posts_per_page' => $ecs_limit,
				'tax_query'=> $ecs_event_tax,
				'meta_key' => '_EventEndDate',
				'orderby' => 'meta_value',
				'order' => $ecs_order,
				'meta_query' => array(
									array(
										'key' => '_EventEndDate',
										'value' => date('Y-m-d'),
										'compare' => '>=',
										'type' => 'DATETIME'
									)
								)
		) );

		if ($posts) {

			$output .= '<ul class="ecs-event-list">';
			foreach( $posts as $post ) :
				setup_postdata( $post );
				$output .= '<li class="ecs-event">';
					$output .= '<h4 class="entry-title summary">' .
									'<a href="' . tribe_get_event_link() . '" rel="bookmark">' . get_the_title() . '</a>
								</h4>';
			
					if( self::isValid($ecs_thumb) ) {
						$thumbWidth = is_numeric($ecs_thumbwidth) ? $ecs_thumbwidth : '';
						$thumbHeight = is_numeric($ecs_thumbheight) ? $ecs_thumbheight : '';
						if( !empty($thumbWidth) && !empty($thumbHeight) ) {
							$output .= get_the_post_thumbnail(get_the_ID(), array($thumbWidth, $thumbHeight) );
						} else {
							$output .= get_the_post_thumbnail(get_the_ID(), 'medium');
						}
					}
			
					if( self::isValid($ecs_excerpt) ) {
						$excerptLength = is_numeric($ecs_excerpt) ? $ecs_excerpt : 100;
						$output .= '<p class="ecs-excerpt">' . 
										self::get_excerpt($excerptLength) . 
									'</p>';
					}
			
					if( self::isValid($ecs_eventdetails) ) {	
						$output .= '<span class="duration time">' . tribe_events_event_schedule_details() . '</span>';
					}
			
					if( self::isValid($ecs_venue) ) {
						$output .= '<span class="duration venue"><em> at </em>' . tribe_get_venue() . '</span>';	
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
