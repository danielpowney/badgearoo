=== Badgearoo ===
Contributors: dpowey
Donate link: http://danielpowney.com/donate
Tags: badge, points, achievement, rewards, gamify
Requires at least: 3.0.1
Tested up to: 4.0
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create your own badges and points system for WordPress users.

== Description ==

Create your own badges and points system for WordPress users. You can configure automatic assignment or manually assign 
badges and points to users. 

The key features of the plugin are:

*	Setup conditions and steps that need to be satisfied for automatic assignment of badges and points to users
*	BuddyPress, bbPress, WooCommerce and Easy Digital Downloads plugins supported with predefined actions that can be used in condition steps
*	4 badge themes including badge icon, light, dark and custom HTML (light and dark themes are based on Stack Overflow badges)
*	Administrators can manually assign badges and points to users from within the WP-admin
*	Administrators can moderate assignments of new badges and points to users including e-mail notifications
*	A badges and points leaderboard shortcode [broo_leaderboard]
*	A user dashboard of badges, points and their assignments [broo_user_dashboard]
*	Assignments can expire (can be renewed) or re-occur (earn multiple times)
*	Badges can be earned multiple times
*	Recent assignments widget
*	Post author or user page badges & points widget
*	Export user assignments to a CSV file
*	Option to show a popup message on page load after a user is assigned badges and or points
*	Fully WPML compatible
*	Developer API functions, hooks, filters and template tags to use in your theme
*	In-built template system for customization

The following shortcodes are available:

    *   [broo_user_badges] - Shows a list of badges assigned to a user e.g. [broo_user_badges username="johnsmith"]
    *   [broo_user_points] - Shows total points assigned to a user e.g. [broo_user_badges user_id=77]
    *   [broo_leaderboard] - Shows a leaderboard of user badges and points e.g. [broo_leaderboard show_avatar="true" sort_by="badges" show_filters="false"]
    *   [broo_badge] - Shows badge details e.g. [broo_badge badge_id="89" show_description="true" show_users="false" show_user_count="true"]
    *   [broo_condition] - Shows condition details e.g. [broo_condition condition_id="1" show_steps="true" show_badges="true" show_points="true"]
    *   [broo_user_dashboard] - Shows a dashboard of badges, points and assignents for a user e.g. [broo_user_dashboard show_assignments="true" limit="5" to_date="205-01-01" from_date="2015-12-12"]
    *   [broo_badge_list] - Shows a list of badge details e.g. [broo_badge_list badge_ids="34,55,56" layout="table"]
    
The following widgets are available:

    *   User Badges - Shows the post author details including any badges and points they have
    *   Recent Assignments - Shows recent user assignments of badges and points.
    
= GitHub =

https://github.com/danielpowney/badgearoo/

== Installation ==

1.  Install and activate the plugin.
2.  Go to the plugin Settings, check any predefined actions you need are enabled
3.  Go to the General settings tab and choose a badge theme: icon (shows a badge icon and badge title), light & dark (similar to Stack Overflow badges) or custom HTML.
4.  Add new badges (if you only intend to assign points, then skip this step)
    1.  Add a badge title, a long description in the content, a short description in the excerpt, choose a featured image and setup any badge theme styles you need.
5.  Once badges have been added, you can start setting up conditions for automatic assignment of badges and points to user
    *   Add a name for the condition (if the [[broo_condition]] shortcode is used then this will be shown)
    *   Add steps with specific actions that need to be accomplished to satisfy the condition e.g. publishes a post
    *   Add the badges and points that are to be assigned to users. You can add more than one badge. Assignments can be earned multiple times (recurring) or only once which means it can be renewed.
    *   Assignments can expiry after a set period of time. You can leave this empty if assignments have no expiry. If assignments are not recurring, then the expiry date can be extended if assignments are renewed.
6.  Once all conditions are setup and if the actions are enabled, users will be automatically assigned badges and points when they satisfy your conditions.
7.  You can manually assign badges and points to users as well. Go the Assignments page, and click Add New.

== Frequently Asked Questions ==

= How do I upload a badge icon =

If you want to display icons for badges, you first need to make sure the badge icon theme is set in your settings. You can upload badge icons when editing a badge in the Badge Theme metabox.

== Screenshots ==

1. Manage and moderate user assignments of badges and points
2. Recent assignments widget using the badge icon theme. Badge icons can be uploaded on the edit badge page.
3. Setup conditions that need to be satisfied for automatic assignments of badges and points to users.
4. [broo_user_dashboard] shortcode using the dark badge theme
5. [broo_leaderboard] shortcode
6. [broo_badge_list] shortcode using the light badge theme
7. Moderation settings 1
8. Moderation settings 2

== Changelog ==

= 1.0 =
*Initial version