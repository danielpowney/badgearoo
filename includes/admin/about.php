<?php 
/**
 * 
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function broo_about_page() {
	?>
	<div class="wrap about-wrap broo-about-wrap">
		<h1><?php printf( __( 'Badgearoo v%s', 'badgearoo' ), Badgearoo::VERSION ); ?></h1>
		
		<div class="about-text"><?php _e( 'Create your own badges and points system for WordPress users. You can configure automatic assignment or manually assign badges and points to users.', 'badgearoo' ); ?></div>
		<div class="broo-badge"><!-- Version here --></div>

		<h2 class="nav-tab-wrapper">
			<?php
			$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'getting_started';
			$page = Badgearoo::ABOUT_PAGE_SLUG;
			$tabs = array (
					'getting_started' => __( 'Getting Started', 'badgearoo' ),
			);
			
			foreach ( $tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				
				echo '<a class="nav-tab ' . $active . '" href="?page=' . $page . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			} ?>
		</h2>
		
		<?php 
		if ( $current_tab == 'getting_started' ) { ?>	
		
		

			<!-- <div class="headline-feature feature-video">
				<iframe width="1050" height="591" src="https://videopress.com/embed/T54Iy7Tw" frameborder="0" allowfullscreen=""></iframe>
				<script src="https://videopress.com/videopress-iframe.js"></script>
			</div>
	
			<hr> -->
	
			<div class="feature-section two-col">
				
				<div class="col">
					<!-- div class="media-container">
						<img src="//s.w.org/images/core/4.3/better-passwords.png">
					</div> -->
					<h3><?php _e( 'Assignments', 'badgearoo' ); ?></h3>
					<p><?php _e( 'Manage user assignments of badges and points including moderation and e-mail notifications.', 'badgearoo' ); ?></p>
				</div>
				
				<div class="col">
					<!-- <div class="media-container">
						<img src="//s.w.org/images/core/4.3/better-passwords.png">
					</div> -->
					<h3><?php _e( 'Conditions', 'badgearoo' ); ?></h3>
					<p><?php _e( 'Setup conditions with steps that need to be accomplished for badges and or points to be automatically assigned to users.', 'badgearoo' ); ?></p>
				</div>
				
				<div class="col">
					<h3><?php _e( 'Shortcodes', 'badgearoo' ); ?></h3>
					<ul>
						<li>[broo_user_badges] - <?php _e( 'Shows a list of badges assigned to a user.', 'badgearoo' ); ?></li>
						<li>[broo_user_points] - <?php _e( 'Shows total points assigned to a user.', 'badgearoo' ); ?></li>
						<li>[broo_leaderboard] - <?php _e( 'Shows a leaderboard of user badges and points', 'badgearoo' ); ?></li>
						<li>[broo_badge] - <?php _e( 'Shows badge details.', 'badgearoo' ); ?></li>
						<li>[broo_condition] - <?php _e( 'Shows condition details.', 'badgearoo' ); ?></li>
						<li>[broo_user_dashboard] - <?php _e( 'Shows a dashboard of badges, points and assignents for a user.', 'badgearoo' ); ?></li>
						<li>[broo_badge_list] - <?php _e( 'Shows a list of badge details.', 'badgearoo' ); ?></li>
					</ul>
				</div>
				
				<div class="col">
					<h3><?php _e( 'Widgets', 'badgearoo' ); ?></h3>
					<ul>
						<li><?php _e( 'User Badges - Shows the post author details including any badges and points they have.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Recent Assignments -  Shows recent user assignments of badges and points.', 'badgearoo' ); ?>
					</ul>
				</div>
				
				<div class="col">
					<h3><?php _e( 'Badges', 'badgearoo' ); ?></h3>
					<p><?php _e( 'There are four themes currently available for displaying badges: dark, light, badge icon and custom HTML.', 'badgearoo' ); ?></p>
				</div>
					
				<div class="col">
					<h3><?php _e( 'Moderation', 'badgearoo' ); ?></h3>
					<p><?php _e( 'You can turn on moderation of new user assignments. E-mail notifications can be setup to notify moderations to approve or unapprove new user assignments', 'badgearoo' ); ?></p>
				</div>
				
			</div>

			<div class="feature-section under-the-hood three-col">
				
				<h3><?php _e( 'Steps Actions', 'badgearoo' ); ?></h3>
				<p style="margin-left: 0px"><?php _e( 'The following actions are available out-of-the-box and can be used in condition steps. It\'s easy to add more actions through add-ons.', 'badgearoo' ); ?>
				
				<div class="col" style="margin-top: 0px">	
					<h4><?php _e( 'BuddyPress', 'badgearoo' ); ?></h4>
					<ul style="list-style: disc; padding-left: 30px;">
						<li><?php _e( 'Add favorite.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Comment on an activity. ', 'badgearoo' ); ?></li>
						<li><?php _e( 'Post activity. ', 'badgearoo' ); ?></li>
						<li><?php _e( 'Accept a friend request.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Request a friend.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Create Group.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Join Group.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Invite someone to join a group.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Get promoted to group moderator/administrator.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Promote another group member to group moderator/administrator.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Activate your account.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Change your profile avatar.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Update your profile information.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Send/reply to a private message.', 'badgearoo' ); ?></li>
					</ul>
					
				</div>
				
				<div class="col" style="margin-top: 0px">
					<h4><?php _e( 'bbPress', 'badgearoo' ); ?></h4>
					<ul style="list-style: disc; padding-left: 30px;">
						<li><?php _e( 'Closes a forum topic.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Creates a new forum (outside wp-admin). ', 'badgearoo' ); ?></li>
						<li><?php _e( 'Replies to forum topic (outside wp-admin). ', 'badgearoo' ); ?></li>
						<li><?php _e( 'Adds a new forum topic (outside wp-admin).', 'badgearoo' ); ?></li>
					</ul>
					
					<h4><?php _e( 'WooCommerce', 'badgearoo' ); ?></h4>
					<ul style="list-style: disc; padding-left: 30px;">
						<li><?php _e( 'Checkout order processed.', 'badgearoo' ); ?></li>
						
						</ul>
					
					<h4><?php _e( 'Easy Digital Downloads', 'badgearoo' ); ?></h4>
					<ul style="list-style: disc; padding-left: 30px;">
						<li><?php _e( 'User completed purchase.', 'badgearoo' ); ?></li>
					</ul>
					
				</div>
				
				<div class="col" style="margin-top: 0px">	
					<h4><?php _e( 'WordPress', 'badgearoo' ); ?></h4>
					<ul style="list-style: disc; padding-left: 30px;">
						<li><?php _e( 'User publishes a post.', 'badgearoo' ); ?></li>
						<li><?php _e( 'User edits a post.', 'badgearoo' ); ?></li>
						<li><?php _e( 'User logs in.', 'badgearoo' ); ?></li>
						<li><?php _e( 'User updates their profile. ', 'badgearoo' ); ?></li>
						<li><?php _e( 'Register user.', 'badgearoo' ); ?></li>
						<li><?php _e( 'User submits a comment.', 'badgearoo' ); ?></li>
					</ul>
					
					<h4><?php _e( 'Custom', 'badgearoo' ); ?></h4>
					<ul style="list-style: disc; padding-left: 30px;">
						<li><?php _e( 'Minimum points.', 'badgearoo' ); ?></li>
						<li><?php _e( 'Views post.', 'badgearoo' ); ?></li>
					</ul>
									
				</div>
			</div>
			
			<div class="feature-section">
				<h3><?php _e( 'Developers', 'badgearoo' ); ?></h3>
				<p style="margin-left: 0px"><?php _e( 'Sample code and developer notes are documented in the plugin-sample.php file. The API function add_user_assignment() can be used to assign badges or points using PHP code. See the class-api.php file.', 'badgearoo' ); ?></p>
						
<code>$user_id = get_current_user_id(); <br />
$condition_id = null; // optional to have a condition otherwise null<br />
$expiry_dt = null; // date format "Y-m-d H:i:s" or null<br />
$points = 100;<br />
Badgearoo::instance()->api->add_user_assignment( $condition_id, $user_id, 'points', $points, $expiry_dt );` ', 'badgearoo' );</code>

			</div>
			
		<?php } ?>
	</div>
	<?php
}