<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Assignments table row template
 */
?>
<tr>
	<td>
		<?php 
		if ( $assignment['type'] == 'badge' && $assignment['badge'] ) {
			
			broo_get_template_part( 'badge', null, true, array(
					'badge_id' => $assignment['badge']->id,
					'show_title' => true,
					'badge_theme' => $badge_theme,
					'badge_icon' => $assignment['badge']->badge_icon,
					'badge_html' => $assignment['badge']->badge_html,
					'badge_color' => $assignment['badge']->badge_color,
					'excerpt' => $assignment['badge']->excerpt,
					'title' => $assignment['badge']->title,
					'content' => $assignment['badge']->content,
					'enable_badge_permalink' => $enable_badge_permalink
			) );

		} else if ( $assignment['points'] ) {
			broo_get_template_part( 'points', null, true, array(
					'points' => $assignment['points']
			) );
		}
		?>
	</td>
	<td>
		<?php 
		if ( $assignment['condition'] ) {
			echo $assignment['condition']->name;
		}
		?>
	</td>
	<td><?php echo date( 'F j, Y, g:ia', strtotime( $assignment['created_dt'] ) ); ?></td>
	<td>
		<?php 
		if ( $assignment['expiry_dt'] ) {
			echo date( 'F j, Y, g:ia', strtotime( $assignment['expiry_dt'] ) );
		}
		?>
	</td>
</tr>