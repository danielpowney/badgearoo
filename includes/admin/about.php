<?php
/**
 * Shows the about screen
 */
function ub_about_page() {
			
	// if version is less than 3.8 then manually add the necessary css missing from about.css
	if ( ! version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) {
		?>
		<style type="text/css">
			.about-wrap .changelog .feature-section {
			    overflow: hidden;
			}
			.about-wrap .feature-section {
			    margin-top: 20px;
			}
			.about-wrap .feature-section.two-col > div {
			    position: relative;
			    width: 47.5%;
			    margin-right: 4.999999999%;
			    float: left;
			}
			.about-wrap .feature-section.col .last-feature {
			    margin-right: 0;
			}
			 .about-wrap hr {
			  	border: 0;
				border-top: 1px solid #DFDFDF;
			}
			.about-wrap {
				position: relative;
				margin: 25px 40px 0 20px;
				max-width: 1050px;
				font-size: 15px;
			}
			.about-wrap img {
				margin: 0;
				max-width: 100%;
				vertical-align: middle;
			}
			.about-wrap .changelog h2.about-headline-callout {
				margin: 1.1em 0 0.2em;
				font-size: 2.4em;
				font-weight: 300;
				line-height: 1.3;
				text-align: center;
			}
			.about-wrap .feature-section img {
			    margin-bottom: 20px !important;
			}
			.about-wrap h3 {
				margin: 1em 0 .6em;
				font-size: 1.5em;
				line-height: 1.5em;
			}
			.about-wrap .feature-section.three-col div {
				width: 29.75%;
			}
			.about-wrap .feature-section.two-col > div {
				margin-right: 4.8%;
			}
		</style>
	<?php 
	}
	?>
	
	<div class="wrap about-wrap">
			<h1><?php printf( __( 'Badgearoo v%s', 'user-badges' ), User_Badges::VERSION ); ?></h1>
		
		<div class="about-text"><?php _e( 'Create your own badges and points system for WordPress users.', 'user-badges' ); ?></div>
			<h2 class="nav-tab-wrapper">
			<?php
			$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'getting_started';
			$page = User_Badges::ABOUT_PAGE_SLUG;
			$tabs = array (
					'getting_started' => __( 'Getting Started', 'user-badges' ),
			);
			
			foreach ( $tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				
				echo '<a class="nav-tab ' . $active . '" href="?page=' . $page . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			} ?>
		</h2>
		
		<?php 
		if ( $current_tab == 'getting_started' ) { ?>	
		
			<div class="changelog">
					
				<p class="about-description"><?php _e( 'Create your own badges and points system for WordPress users. You can configure automatic assignment or manually assign badges and points to users.', 'user-badges' ); ?></p>
				
				<div class="feature-section col two-col">
					<div class="col-1">
						<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'about-assignments.png' , __FILE__ ); ?>" />
						<h4><?php _e( 'Assignments', 'user-badges' ); ?></h4>
						<p><?php _e( 'Manage user assignments of badges and points including moderation and e-mail notifications.', 'user-badges' ); ?></p>
					</div>
					<div class="col-2 last-feature">
						<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'about-conditions.png' , __FILE__ ); ?>" />
						<h4><?php _e( 'Conditions', 'user-badges' ); ?></h4>
						<p><?php _e( 'Setup conditions with steps that need to be accomplished for badges and or points to be automatically assigned to users.', 'user-badges' ); ?></p>
					</div>
				</div>
				
				<div class="feature-section col two-col">
					<br />
					<br />
					<div class="col-1">
					
						<h4><?php _e( 'Shortcodes', 'user-badges' ); ?></h4>
						<ul>
							<li>[broo_user_badges] - <?php _e( 'Shows a list of badges assigned to a user.', 'user-badges' ); ?></li>
							<li>[broo_user_points] - <?php _e( 'Shows total points assigned to a user.', 'user-badges' ); ?></li>
							<li>[broo_leaderboard] - <?php _e( 'Shows a leaderboard of user badges and points', 'user-badges' ); ?></li>
							<li>[broo_badge] - <?php _e( 'Shows badge details.', 'user-badges' ); ?></li>
							<li>[broo_condition] - <?php _e( 'Shows condition details.', 'user-badges' ); ?></li>
							<li>[broo_user_dashboard] - <?php _e( 'Shows a dashboard of badges, points and assignents for a user.', 'user-badges' ); ?></li>
							<li>[broo_badge_list] - <?php _e( 'Shows a list of badge details.', 'user-badges' ); ?></li>
						</ul>
						
						<h4><?php _e( 'Widgets', 'user-badges' ); ?></h4>
						<ul>
							<li><?php _e( 'User Badges - Shows the post author details including any badges and points they have.', 'user-badges' ); ?></li>
							<li><?php _e( 'Recent Assignments -  Shows recent user assignments of badges and points.', 'user-badges' ); ?>
						</ul>
						
						<h4><?php _e( 'Moderation', 'user-badges' ); ?></h4>
						<p><?php _e( 'You can turn on moderation of new user assignments. E-mail notifications can be setup to notify moderations to approve or unapprove new user assignments', 'user-badges' ); ?></p>
				
					</div>
					
					<div class="col-2 last-feature">
						<h4><?php _e( 'Badge Themes', 'user-badges' ); ?></h4>
						<p><?php _e( 'There are four themes currently available for displaying badges: dark, light, badge icon and custom HTML.', 'user-badges' ); ?></p>
						<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'about-theme-dark.PNG' , __FILE__ ); ?>" />
						<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'about-theme-light.PNG' , __FILE__ ); ?>" />
						<img src="<?php echo plugins_url( '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'about-theme-icon.PNG' , __FILE__ ); ?>" />
						
						<h4><?php _e( 'Reports', 'user-badges' ); ?></h4>
						<p><?php _e( 'You can export user assignments to a CSV file from the plugin tools page.', 'user-badges' ); ?></p>
						
						<h4><?php _e( 'Developer Friendly', 'user-badges' ); ?></h4>
						<p><?php _e( 'The Badgearoo plugin has been built with extensibility in mind providing an API in one place and plenty of action hooks and filters for customization.', 'user-badges' ); ?></p>
						<p><?php _e( 'Please see the developer guide for instructions on how to add your own predefined actions that can be used in conditions.', 'user-badges' ); ?>
					</div>
					
				</div>
			</div>
						
			<div class="changelog under-the-hood">
				<div class="feature-section col three-col">
				
					<h3><?php _e( 'Steps Actions', 'user-badges' ); ?></h3>
					<p><?php _e( 'The following actions are available out-of-the-box and can be used in condition steps. It\'s easy to add more actions through add-ons.', 'user-badges' ); ?>
					<div>
						
						<h4><?php _e( 'BuddyPress', 'user-badges' ); ?></h4>
						<ul>
							<li><?php _e( 'Add favorite.', 'user-badges' ); ?></li>
							<li><?php _e( 'Comment on an activity. ', 'user-badges' ); ?></li>
							<li><?php _e( 'Post activity. ', 'user-badges' ); ?></li>
							<li><?php _e( 'Accept a friend request.', 'user-badges' ); ?></li>
							<li><?php _e( 'Request a friend.', 'user-badges' ); ?></li>
							<li><?php _e( 'Create Group.', 'user-badges' ); ?></li>
							<li><?php _e( 'Join Group.', 'user-badges' ); ?></li>
						</ul>
						
					</div>
					<div>
						<h4><?php _e( 'bbPress', 'user-badges' ); ?></h4>
						<ul>
							<li><?php _e( 'Closes a forum topic.', 'user-badges' ); ?></li>
							<li><?php _e( 'Creates a new forum (outside wp-admin). ', 'user-badges' ); ?></li>
							<li><?php _e( 'Replies to forum topic (outside wp-admin). ', 'user-badges' ); ?></li>
							<li><?php _e( 'Adds a new forum topic (outside wp-admin).', 'user-badges' ); ?></li>
						</ul>
						
						<h4><?php _e( 'WooCommerce', 'user-badges' ); ?></h4>
						<ul>
							<li><?php _e( 'Checkout order processed.', 'user-badges' ); ?></li>
							
						</ul>
						
						<h4><?php _e( 'Easy Digital Downloads', 'user-badges' ); ?></h4>
						<ul>
							<li><?php _e( 'User completed purchase.', 'user-badges' ); ?></li>
							
						</ul>
						 
						
					</div>
					<div class="last-feature">	
						<h4><?php _e( 'WordPress', 'user-badges' ); ?></h4>
						<ul>
							<li><?php _e( 'User logs in.', 'user-badges' ); ?></li>
							<li><?php _e( 'User updates their profile. ', 'user-badges' ); ?></li>
							<li><?php _e( 'Register user. ', 'user-badges' ); ?></li>
							<li><?php _e( ' User submits a comment..', 'user-badges' ); ?></li>
						</ul>
						
						<h4><?php _e( 'Custom', 'user-badges' ); ?></h4>
						<ul>
							<li><?php _e( 'Minimum points.', 'user-badges' ); ?></li>
						</ul>
										
					</div>
			</div>
		
			
		</div>
	<?php }
}