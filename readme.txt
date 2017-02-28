=== Badgearoo ===
Contributors: dpowney
Donate link: http://danielpowney.com/donate
Tags: badge, badges, credit, points, achievement, award, rewards, gamify, engagement, bbpress, buddpress, easy digital downloads, woocommerce
Requires at least: 4.0
Tested up to: 4.7
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create your own badges and points system for WordPress users.

== Description ==

Create your own badges and points system for WordPress users. You can configure automatic assignment or manually assign 
badges and points to users. Upload your own badge icons, use a badge theme inspired by Stack Overflow or use custom HTML.

* Setup conditions and steps that need to be satisfied for automatic assignment of badges and points to users
* BuddyPress, bbPress, WooCommerce and Easy Digital Downloads plugins supported with predefined actions that can be used in condition steps
* 4 badge themes including badge icon (upload your own image), light, dark and custom HTML (light and dark themes are based on Stack Overflow badges)
* Administrators can manually assign badges and points to users from within the WP-admin
* Administrators can moderate assignments of new badges and points to users including e-mail notifications
* A badges and points leaderboard shortcode [broo_leaderboard]
* A user dashboard of badges, points and their assignments [broo_user_dashboard]
* Assignments can expire (can be renewed) or re-occur (earn multiple times)
* Badges can be earned multiple times
* Recent assignments widget
* User badges & points summary widget for Post Author, displayed bbPress user or BuddyPress member pages
* Export user assignments to a CSV file
* Option to show a popup message on page load after a user is assigned badges and or points
* Fully WPML compatible (v3.2+)
* Developer API functions, hooks, filters and template tags to use in your theme
* In-built template system for customization

= The following shortcodes are available (see FAQ for usage): =
* [broo_user_badges] - Shows a list of badges assigned to a user e.g. [broo_user_badges username="johnsmith"]
* [broo_user_points] - Shows total points assigned to a user e.g. [broo_user_badges user_id=77]
* [broo_leaderboard] - Shows a leaderboard of user badges and points e.g. [broo_leaderboard show_avatar="true" sort_by="badges" show_filters="false"]
* [broo_badge] - Shows badge details e.g. [broo_badge badge_id="89" show_description="true" show_users="false" show_user_count="true"]
* [broo_condition] - Shows condition details e.g. [broo_condition condition_id="1" show_steps="true" show_badges="true" show_points="true"]
* [broo_user_dashboard] - Shows a dashboard of badges, points and assignents for a user e.g. [broo_user_dashboard show_assignments="true" limit="5" to_date="205-01-01" from_date="2015-12-12"]
* [broo_badge_list] - Shows a list of badge details e.g. [broo_badge_list badge_ids="34,55,56" layout="table"]

= The following widgets are available: =

* User Badges - Shows the post author details including any badges and points they have
* Recent Assignments - Shows recent user assignments of badges and points.

= The following step actions are available out-of-the-box: =
* User publishes a post.
* User logs in.
* User updates their profile.
* Register user.
* User submits a comment.
* Minimum points.
* Views post.
* User edits a post.
	
### BuddyPress (requires BuddyPress plugin)

* Add favorite.
* Comment on an activity.
* Post activity.
* Accept a friend request.
* Request a friend.
* Create Group.
* Join Group.
* Invite someone to join a group.
* Get promoted to group moderator/administrator.
* Promote another group member to group moderator/administrator.
* Activate your account.
* Change your profile avatar.
* Update your profile information.
* Send/reply to a private message.

Other features:

* Option to add assignment of badges and points to member activity streams.
* Option to add a summary of badges and points assigned to members in member header or new member tab
* Option to show 3 most recent assignments alongside each member in the members directory list
	
### bbPress (requires bbPress plugin)

* Closes a forum topic.
* Creates a new forum (outside wp-admin).
* Replies to forum topic (outside wp-admin).
* Adds a new forum topic (outside wp-admin).

Other features:

* Adds a summary of badges and points assigned to users in the reply author details
* Adds a summary of badges and points assigned to users in user profile page

### WooCommerce (requires WooCommerce plugin)

* Checkout order processed.

### Easy Digital Downloads (requires Easy Digital Downloads plugin)

* User completed purchase.

= GitHub =

https://github.com/danielpowney/badgearoo/

== Installation ==

1.  Install and activate the plugin.
2.  Go to the plugin Settings, check any predefined actions you need are enabled
3.  Go to the General settings tab and choose a badge theme: icon (shows a badge icon and badge title), light & dark (similar to Stack Overflow badges) or custom HTML.
4.  Add new badges (if you only intend to assign points, then skip this step)
    * Add a badge title, a long description in the content, a short description in the excerpt, choose a featured image and setup any badge theme styles you need.
5.  Once badges have been added, you can start setting up conditions for automatic assignment of badges and points to user
    * Add a name for the condition (if the [[broo_condition]] shortcode is used then this will be shown)
    * Add steps with specific actions that need to be accomplished to satisfy the condition e.g. publishes a post
    * Add the badges and points that are to be assigned to users. You can add more than one badge. Assignments can be earned multiple times (recurring) or only once which means it can be renewed.
    * Assignments can expiry after a set period of time. You can leave this empty if assignments have no expiry. If assignments are not recurring, then the expiry date can be extended if assignments are renewed.
6.  Once all conditions are setup and if the actions are enabled, users will be automatically assigned badges and points when they satisfy your conditions.
7.  You can manually assign badges and points to users as well. Go the Assignments page, and click Add New.

== Frequently Asked Questions ==

= How do I create a badge? =

Badges are a custom post type. You can create a new badge by clicking the Add New menu option under the Badges menu.

= How do I setup automatic assignment of badges and points to users? =

You need to setup conditions. Go to the Conditions menu option under the Badges menu. Here you can setup and manage conditions that need to be satisfied in order for users to be automatically assigned basdges and points. 

= How do I manually assigned a badge or points to users? =

Go to the Assignments menu option under the Badges menu and then click Add New.

= How do I upload a badge icon =

If you want to display icons for badges, you first need to make sure the badge icon theme is set in your settings. You can upload badge icons when editing a badge in the Badge Theme metabox.

= How do I create my own step actions or custom assign badges or points in my plugin or theme =

Sample code and developer notes are documented in the plugin-sample.php file.

The API function add_user_assignment() can be used to assign badges or points using PHP code. See the class-api.php file.

`$user_id = get_current_user_id(); 
$condition_id = null; // optional to have a condition otherwise null
$expiry_dt = null; // date format "Y-m-d H:i:s" or null
$points = 100;
Badgearoo::instance()->api->add_user_assignment( $condition_id, $user_id, 'points', $points, $expiry_dt );` 

= [broo_user_badges] - Shows a list of badges assigned to a user =

Attributes:

* user_id - User id of user. If not set, the default is the current logged in user id.
* username - Username of user. This will override user_id if set. Default empty.

e.g. [broo_user_badges username="johnsmith"]

= [broo_user_points] - Shows total points assigned to a user =

Attributes:

* user_id - User id of user. If not set, the default is the current logged in user id.
* username - Username of user. This will override user_id if set. Default empty.

e.g. [broo_user_points username="johnsmith"]

= [broo_leaderboard] - Shows a leaderboard of user badges and points =

Attributes:

* show_avatar - Show user's avatar. true or false. Default is true.
* before_name -
* after_name -
* show_badges - Include badges in the leaderboard. true or false. Default is true.
* show_points - Include points in the leaderboard. true or false. Default is true.
* sort_by - Sort leaderboard. points or badges. Default is points.
* show_filters - Show leaderboard filters i.e. sort by, type, date range etc... true or false. Default is true.
* from_date - yyyy-mm-dd format. Default empty.
* to_date - yyyy-mm-dd format. Default empty.
* include_no_assignments - whether to include users who do not have any assignments (i.e. points or badges)
* limit - How many users to limit in the leaderboard. Default is null which means show all.
* offset - Start offset for user leaderboard. This can be used for paging. Default is 0.

e.g. [broo_leaderboard show_filters="false" show_avatar="false"]

= [broo_badge] - Shows badge details =

Attributes:

* badge_id - The badge post id. This is required otherwise no badge can be shown.
* show_description - Show badge post content. true or false. Default is true.
* before_name -
* after_name -
* show_users - show users who have been assigned this bade. true or false. Default is true.
* show_users_count - show count of users who have been assigned this badge. Default is true.

e.g. [broo_badge badge_id="10"]

= [broo_badge_list] - Shows a list of badge details =

Attributes:

* badge_ids - A list of badge post id's to show. Default is empty which means all badges are shown.
* show_description - Show badge excerpt. Default is true.
* before_name -
* after_name -
* show_users - show users who have been assigned this bade. true or false. Default is true.
* show_users_count - show count of users who have been assigned this badge. Default is true.
* layout - Layout for displaying badges. summary or table. Default is table.
* taxonomy - Taxonomy slug to filter badges.
* terms - comma separated of taxonomy term slugs to filter badges. Default is empty which means all terms.
* terms_operator - Taxonomy query operator to test. Possible values are "IN", "NOT IN", "AND", "EXISTS" and "NOT EXISTS". Default value is "IN".

e.g. [broo_badge_list layout="table" show_users="false" badge_ids="10,11,12"]

e.g. [broo_badge_list taxonomy="category" terms="test2,test3" tax_operator="NOT IN"]

= [broo_condition] - Shows condition details =

Attributes:

* condition_id - The condition id to show. This is required otherwise no condition is shown.
* show_steps - Show the step details that need to be completed. Default is true.
* show_badges - Show the badges that are assigned when condition is satisfied. Default is true.
* show_points - Show the points that are assigned when condition is satisfied. Default is true.

e.g. [broo_condition condition_id="1"]

= [broo_user_dashboard] Shows a dashboard of badges, points and assignments for a user =

Attributes:

* show_badges - Include badges in the dashboard summary. true or false. Default is true.
* show_points - Include points in the dashboard summary. true or false. Default is true.
* show_assignments - Include assignments table in the dashboard. Default is true.
* limit - How many assignments to limit in the table. More can be retrieved using the Load more button. Default is 5.
* offset - Start offset for assignments. This can be used for paging. Default is 0.
* type - Type of assignments to show. empty "" for all, badges or points. Default is empty.
* show_filters - Show assignments filters i.e. sort by, type, date range etc... true or false. Default is true.
* from_date - yyyy-mm-dd format. Default empty.
* to_date - yyyy-mm-dd format. Default empty.

e.g. [broo_user_dashboard limit="10" show_filters="false"]

== Screenshots ==

1. Manage and moderate user assignments of badges and points
2. Recent assignments widget using the light theme.
3. Setup conditions that need to be satisfied for automatic assignments of badges and points to users.
4. [broo_user_dashboard] shortcode using the dark badge theme
5. [broo_leaderboard] shortcode
6. [broo_badge_list] shortcode using the badge icon theme. Badge icons can be uploaded on the edit badge page.
7. Moderation settings 1
8. Moderation settings 2
9. BuddyPress member tab showing badges and points assigned to member. You can change the settings to show assignments in the member header instead.

== Changelog ==

= 1.0.14 (28/02/2017)
* New: Added user edits a post action
* New: Added offset and limit shortcode attributes to [broo_leaderboard] shortcode
* New: When users are deleted, delete their associated user assignments
* Bug: If a user has been deleted, fixed SQL query to not include their assignments in the leaderboard
* Bug: Fixed show_avatar, before_name, after_name, show_badges and show_points not persisting when filtering the user leaderboard
* New: Added include_no_assignments shortcode attribute to the [broo_leaderboard] shortcode. This allows you to includes users without points or badges.
* Bug: Fixed fatal error on BuddyPress assignments tab for badges summary
* Tweak: Updated about page
* Tweak: Updated language translation files

= 1.0.13 (16/07/2016) =
* Bug: Fixed pagination with filters on assignments page
* Bug: Fixed user action for publishing a BuddyPress activity not being recorded
* Bug: Fixed db charset collate not sing WP config
* New: Added new action for BuddyPress post updates since publishing a new activity post does not seem to register for updates

= 1.0.12 (05/04/2016) =
* Bug: Removed deprecated jQuery disableSelection() to steps causing issues in some browsers

= 1.0.11 (31/03/2016) =
* Bug: Fixed missing argument to bp_activity_post_type_published

= 1.0.10 (31/03/2016) =
* Bug: Fixed incorrect assignment points/badges to wrong user for BuddyPress activity comments
* Bug: Fixed conditions page postbox toggle

* 1.0.9 (29/03/2016) =
* New: Added action broo_condition_other_assignment in conditions page
* Bug: Fixed type in recent assignments widget
* Bug: Removed usage of boolval() as this was added in PHP 5.5 and causing issues for older versions of PHP

= 1.0.8 (28/01/2016) =
* New: Added option to display badges inline (i.e. horizontally)

= 1.0.7 (20/11/2015) =
* Bug: Fixed [broo_badge] shortcode show_users and show_users_count attributes not working

= 1.0.6 (09/11/2015) =
* Tweak: Changed default showing each member's 3 most recent assignments in members directory list to false
* New: Added WP_Query tax_query support to get_badges() API function. See https://codex.wordpress.org/Class_Reference/WP_Query.
* New: Added taxonony, terms and tax_operator shortcode attributes to the [broo_get_badges] shortcode.

= 1.0.5 (08/11/2015) =
* New: Added option for BuddyPress to show each member's 3 most recent assignments in the members directory list. Added recent-assignments template as well.
* Tweak: Updated language files. Updates are only related to wp-admin text.
* Bug: Fixed API function get_badges() WP_Query usage to set posts_per_page argument to -1 so that it retrieves all badges.

= 1.0.4 (02/11/2015) =
* Bug: Removed usage of mysql_real_escape_string() in admin tables
* Bug: Fixed API declaration fatal error

= 1.0.3 (2/10/2015) =
* Bug: Fixed db tables not gettign created in some cases due to the plugin trying to default the datetime field to CURRENT_TIMESTAMP which only works in MySQL versions 5.6.5 and above.
* Tweak: Improved actions settings tab to easily see inactive plugins and actions

= 1.0.2 =
* Added 7 new BuddyPress step actions:
	1. Invite someone to join a group.
	2. Get promoted to group moderator/administrator.
	3. Promote another group member to group moderator/administrator.
	4. Activate your account.
	5. Change your profile avatar.
	6. Update your profile information.
	7. Send/reply to a private message.
* Added new view post step action

= 1.0.1 =
* Bug: Fixed expired badges and points included in the user leaderboard

= 1.0 =
* Initial version