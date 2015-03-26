<?php
/**
 * 
 */
function ub_actions_page() {
	
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
	<h2><?php _e( 'Actions', 'user-badges' ); ?><a class="add-new-h2" id="add-new-action" href="#"><?php _e( 'Add New', 'user-badges' ); ?></a>
	</h2>

	<form method="post" id="ub-actions-table-form">
			<?php 
			$actions_table = new UB_Actions_Table();
			$actions_table->prepare_items();
			$actions_table->display();
			?>
		</form>
</div>
<?php
}