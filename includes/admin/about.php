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
					
				<p class="about-description"><?php _e( 'Create your own badges and points system for WordPress users. You can manually assign badges or configure automatic assignment of predefined badges to to users.', 'user-badges' ); ?></p>
				
				<h4>Shortcodes</h4>
				<ul>
					<li>[broo_user_badges]</li>
					<li>[broo_user_points]</li>
					<li>[broo_leaderboard]</li>
					<li>[broo_badge]</li>
					<li>[broo_condition]</li>
					<li>[broo_user_dashboard] - TODO</li>
					<li>[broo_badge_list] - TODO</li>
					<li>[broo_condition_list] - TODO</li>
				</ul>
				
				<div class="feature-section col three-col">
					<div class="col-1">
						<img />
						<h4><?php _e( 'Assignments', 'user-badges' ); ?></h4>
						<p><?php _e( 'Manage user assignments of badges and points including moderation and e-mail notifications.', 'user-badges' ); ?></p>
					</div>
					<div class="col-2">
						<img />
						<h4><?php _e( 'Actions.', 'user-badges' ); ?></h4>
						<p><?php _e( 'Out-of-the-box support for BuddyPress, bbPress, WooCommerce and Easy Digital Downloads plugins.', 'user-badges' ); ?></p>
					</div>
					<div class="col-3 last-feature">
						<img />
						<h4><?php _e( 'Conditions', 'user-badges' ); ?></h4>
						<p><?php _e( 'Setup conditions with steps that need to be accomplished for badges and or points to be assigned to users.', 'user-badges' ); ?></p>
					</div>
				</div>
			
				<hr>
			
				<div class="feature-section col two-col">
					<div class="col-1">
						<img />
						<h4><?php _e( 'TODO', 'user-badges' ); ?></h4>
						<p><?php _e( 'TODO', 'user-badges' ); ?></p>
					</div>
					<div class="col-2 last-feature">
						<img />
						<h4><?php _e( 'TODO', 'user-badges' ); ?></h4>
						<p><?php _e( 'TODO', 'user-badges' ); ?></p>
					</div>
				</div>
			</div>
			
			<hr>
			
			<div class="changelog under-the-hood">
				<div class="feature-section col three-col">
					<div>
						
						<h4><?php _e( 'TODO', 'user-badges' ); ?></h4>
						<p><?php _e( 'TODO', 'user-badges' ); ?></p>
						
					</div>
					<div>
					
						<h4><?php _e( 'TODO', 'user-badges' ); ?></h4>
						<p><?php _e( 'TODO', 'user-badges' ); ?></p>
						
					</div>
					<div class="last-feature">	
					
						<h4><?php _e( 'TODO', 'user-badges' ); ?></h4>
						<p><?php _e( 'TODO', 'user-badges' ); ?></p>
										
					</div>
			</div>
			
		</div>
	<?php }
}