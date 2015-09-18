# Badgearoo

Create your own badges and points system for WordPress users. You can configure automatic assignment or manually assign 
badges and points to users. 

* Setup conditions and steps that need to be satisfied for automatic assignment of badges and points to users
* BuddyPress, bbPress, WooCommerce and Easy Digital Downloads plugins supported with predefined actions that can be used in condition steps
* 4 badge themes including badge icon (upload your own image), light, dark and custom HTML (light and dark themes are based on Stack Overflow badges)
* Administrators can manually assign badges and points to users from within the WP-admin
* Administrators can moderate assignments of new badges and points to users including e-mail notifications
* A badges and points leaderboard shortcode [broo\_leaderboard]
* A user dashboard of badges, points and their assignments [broo\_user\_dashboard]
* Assignments can expire (can be renewed) or re-occur (earn multiple times)
* Badges can be earned multiple times
* Recent assignments widget
* User badges & points summary widget for Post Author, displayed bbPress user or BuddyPress member pages
* Export user assignments to a CSV file
* Option to show a popup message on page load after a user is assigned badges and or points
* Fully WPML compatible (v3.2+)
* Developer API functions, hooks, filters and template tags to use in your theme
* In-built template system for customization

The following shortcodes are available:
* [broo\_user\_badges] - Shows a list of badges assigned to a user e.g. [broo\_user\_badges username="johnsmith"]
* [broo\_user\_points] - Shows total points assigned to a user e.g. [broo\_user\_badges user\_id=77]
* [broo\_leaderboard] - Shows a leaderboard of user badges and points e.g. [broo\_leaderboard show\_avatar="true" sort\_by="badges" show\_filters="false"]
* [broo\_badge] - Shows badge details e.g. [broo\_badge badge\_id="89" show\_description="true" show\_users="false" show\_user\_count="true"]
* [broo\_condition] - Shows condition details e.g. [broo\_condition condition\_id="1" show\_steps="true" show\_badges="true" show\_points="true"]
* [broo\_user\_dashboard] - Shows a dashboard of badges, points and assignents for a user e.g. [broo\_user\_dashboard show\_assignments="true" limit="5" to\_date="205-01-01" from\_date="2015-12-12"]
* [broo\_badge\_list] - Shows a list of badge details e.g. [broo\_badge\_list badge\_ids="34,55,56" layout="table"]
    
The following widgets are available:
* User Badges - Shows the post author details including any badges and points they have
* Recent Assignments - Shows recent user assignments of badges and points.

The following step actions are available out-of-the-box:
* User publishes a post.
* User logs in.
* User updates their profile.
* Register user.
* User submits a comment.
* Minimum points.
	
## BuddyPress (requires BuddyPress plugin)

Step actions:
* Add favorite.
* Comment on an activity.
* Post activity.
* Accept a friend request.
* Request a friend.
* Create Group.
* Join Group.
* Invite someone to join a group. (coming soon)
* Get promoted to group moderator/administrator. (coming soon)
* Promote another group member to group moderator/administrator. (coming soon)
* Activate your account. (coming soon)
* Change your profile avatar. (coming soon)
* Update your profile information. (coming soon)
* Send/reply to a private message. (coming soon)

Other features:
* Option to add assignment of badges and points to member activity streams.
* Option to add a summary of badges and points assigned to members in member header or new member tab
	
## bbPress (requires bbPress plugin)

Step actions:
* Closes a forum topic.
* Creates a new forum (outside wp-admin).
* Replies to forum topic (outside wp-admin).
* Adds a new forum topic (outside wp-admin).

Other features:
* Adds a summary of badges and points assigned to users in the reply author details
* Adds a summary of badges and points assigned to users in user profile page

## WooCommerce (requires WooCommerce plugin)

Step actions:
* Checkout order processed.

## Easy Digital Downloads (requires Easy Digital Downloads plugin)

Step actions:
* User completed purchase.