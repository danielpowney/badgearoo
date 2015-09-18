<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Badge list template
 */
?>

<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> broo-badge-list">
	
	<?php
	
	if ( count( $badges ) == 0 ) {
		_e( 'No badges', 'badgearoo' );
	} else {
		
		// layout: summary, table
		
		if ( $layout == 'summary' ) {

			foreach ( $badges as $badge ) {
				
				$users = array();
				foreach ( $badge->users as $user_id ) {
				
					$user = get_userdata( intval( $user_id ) );
				
					if ( $user ) {
						array_push( $users, $user );
					}
				}
			
				broo_get_template_part( 'badge', 'summary', true, array(
						'badge_id' => $badge->id,
						'badge_theme' => $badge_theme,
						'badge_icon' => $badge->badge_icon,
						'badge_html' => $badge->badge_html,
						'badge_color' => $badge->badge_color,
						'title' => $badge->title,
						'content'=> $badge->content,
						'excerpt'=> $badge->excerpt,
						'users' => $users,
						'users_count' => count( $badge->users ),
						'show_users' => $show_users,
						'show_users_count' => $show_users_count,
						'show_description' => $show_description,
						'enable_badge_permalink' => $enable_badge_permalink
				) );
			}
			
		} else {
			
			?>
			<table>
				<tr>
					<th><?php _e( 'Badge', 'badgearoo' ); ?></th>
					<th><?php _e( 'Description', 'badgearoo' ); ?></th>
					<th><?php _e( 'Awarded', 'badgearoo' ); ?></th>
				</tr>
				
				<?php 
				foreach ( $badges as $badge ) {
					?>
					<tr>
						<td>
							<?php
							broo_get_template_part( 'badge', null, true, array(
									'badge_id' => $badge->id,
									'show_title' => true,
									'badge_theme' => $badge_theme,
									'badge_icon' => $badge->badge_icon,
									'badge_html' => $badge->badge_html,
									'badge_color' => $badge->badge_color,
									'excerpt' => $badge->excerpt,
									'title' => $badge->title,
									'content' => $badge->content,
									'enable_badge_permalink' => $enable_badge_permalink
							) );
							?>
						</td>
						<td>
							<?php echo $badge->excerpt; ?>
						</td>
						<td>
							<?php 
							$users_count = count( $badge->users );
							
							if ( $users_count == 0 ) {
								_e( 'No users', 'badgearoo' );
							} else if ( $users_count == 1 ) {
								_e( '1 user', 'badgearoo' ); 
							} else {
								printf( __( '%d users', 'badgearoo' ), $users_count );
							}
							?>
						</td>
					</tr>
					<?php
					
				} ?>
			</table>
		<?php 
		}
	}
	?>
</div>