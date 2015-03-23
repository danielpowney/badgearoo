<?php
/**
 * 
 */
function ub_badges_page() {
	
	if ( isset( $_POST['form-submitted'] ) && $_POST['form-submitted'] === "true" ) {
		
		// TODO check permission
		
		$name = isset( $_POST['name'] ) ? trim( $_POST['name'] ) : null;
		$description = isset( $_POST['description'] ) ? trim( $_POST['description'] ) : null;
		$enabled = true;
		$url = isset( $_POST['url'] ) ? $_POST['url'] : null;
		
		if ( $name == null || $description == null || $url == null ) {
			echo '<div class="error"><p>' . __( 'Name, description and URL are requied', 'user-badges' ) . '</p></div>';
		} else {
			User_Badges::instance()->api->add_new_badge( $name, $description, $url, $enabled );
			echo '<div class="updated"><p>' . __( 'Badge added successfully', 'user-badges' ) . '</p></div>';
		}
	}
	
	?>
	<div class="wrap">
		<h2><?php _e( 'Badges', 'user-badges' ); ?><a class="add-new-h2" id="add-new-badge-header" href="#"><?php _e( 'Add New', 'user-badges' ); ?></a></h2>
		
		<form method="post" id="add-edit-badge-form" style="display: none;">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e( 'Name', 'user-badges' ); ?></th>
						<td>
							<input type="text" class="regular-text" id="name" name="name" value="" placeholder="Enter a name..." required maxlength="100" />	
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Description', 'user-badges' ); ?></th>
						<td>
							<input type="text" class="regular-text" id="description" name="description" value="" placeholder="Enter a description..." required maxlength="400" />	
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Badge Icon', 'user-badges' ); ?></th>
						<td>
							<input type="url" id="url" name="url" value="" class="regular-text" />
							<input type="submit" name="upload-btn" id="upload-btn" class="button" value="<?php _e('Upload', 'user-badges' ); ?>">
						</td>
					</tr>
					
				</tbody>
			</table>
				
			<p>
				<input style="display: none;" id="add-new-badge-btn" class="button button-primary" value="<?php _e( 'Add New Badge', 'user-badges' ); ?>" type="submit" />
				<input style="display: none;" id="update-badge-btn" class="button button-primary" value="<?php _e( 'Update Badge', 'user-badges' ); ?>" type="submit" />
			</p>
				
			<input type="hidden" id="form-submitted" name="form-submitted" value="false" />
			<input type="hidden" id="previous-badge-name" name="previous-badge-name" value="" />
		</form>

		<form method="post" id="ub-badges-table-form">
			<?php 
			$badges_table = new UB_Badges_Table();
			$badges_table->prepare_items();
			$badges_table->display();
			?>
		</form>
	</div>
	<?php
}