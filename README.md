# The Events Calendar Shortcode

__Adds shortcode functionality to The Events Calendar Plugin (Free Version) by Modern Tribe.__

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
  ```
3. limit - Total number of events to show. Default is 5.  
  ```
  [ecs-list-events limit='3']
  ```
4. order - Order of the events to be shown. Value can be `ASC` or `DESC`. Default is `ASC`. Order is based on event date.  
  ```
  [ecs-list-events order='DESC']
  ```
5. date - To show or hide date. Value can be `true` or `false`. Default is `true`.  
  ```
  [ecs-list-events eventdetails='false']
  ```
6. venue - To show or hide the venue. Value can be `true` or `false`. Default is `false`.  
  ```
  [ecs-list-events venue='true']
  ```
7. excerpt - To show or hide the excerpt and set excerpt length. Default is false.  
  ```
  [ecs-list-events excerpt='true'] //display excerpt with length 100  
  
  [ecs-list-events excerpt='300'] //display excerpt with length 300
  ```
8. thumb - To show or hide thumbnail image. Default is `false`.  
  ```
  //display post thumbnail in default thumbnail dimension from media settings.  
  
  [ecs-list-events thumb='true'] 
  ```  
  You can use 2 other attributes: thumbwidth and thumbheight to customize the thumbnail size  
  ```
  [ecs-list-events thumb='true' thumbwidth='150' thumbheight='150']
  ```
9. message - Message to show when there are no events. Defaults to 'There are no upcoming events at this time.'
10. viewall - Determines whether to show 'View all events' or not. Values can be `true` or `false`. Default to `true`.
  ```  
  [ecs-list-events cat='festival' limit='3' order='DESC' viewall='false']
  ```
