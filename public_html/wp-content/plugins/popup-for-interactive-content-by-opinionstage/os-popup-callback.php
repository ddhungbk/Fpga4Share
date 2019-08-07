<?php
	$success = $_GET['success'];
	$uid = $_GET['uid'];
	$token = $_GET['token'];
	$email = $_GET['email'];
	$fly_id = $_GET['fly_id'];
	$article_placement_id = $_GET['article_placement_id'];
	$sidebar_placement_id = $_GET['sidebar_placement_id'];
	os_popup_uninstall();
	os_popup_parse_client_data(compact(
		'success',
		'uid',
		'token',
		'email',
		'fly_id',
		'article_placement_id',
		'sidebar_placement_id'));	
		
	$redirect_url = get_admin_url('', '', 'admin').'admin.php?page='.OS_POPUP_WIDGET_UNIQUE_ID.'/os-popup.php';	
?>
<script type="text/javascript">
	window.location = "<?php echo($redirect_url) ?>";	
</script>
