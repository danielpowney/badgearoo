<?php 
/**
 * Assignments table row template
 */
?>
<tr>
	<td>
		<?php 
		if ( $assignment['type'] == 'badge' && $assignment['badge'] ) {
			?>
			<div class="ub-badge" title="<?php echo $assignment['badge']->excerpt; ?>"><?php echo $assignment['badge']->title; ?></div>
			<?php
		} else if ( $assignment['points'] ) {
			ub_get_template_part( 'points', null, true, array(
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