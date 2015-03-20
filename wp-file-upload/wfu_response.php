<?php
/*
This script runs every time the user presses the upload button in order to inform the page that
it should process the file upload data (stored in $_FILES variable) when the page reloads 
*/
session_start();

if ( isset($_GET['shortcode_id']) && isset($_GET['session_token']) ) {
	//check referer using server sessions to avoid CSRF attacks
	if ( $_SESSION["wfu_token_".$_GET['shortcode_id']] != $_GET['session_token'] ) die();

	if ( isset($_GET['start_time']) ) {
		$_SESSION['wfu_check_refresh_'.$_GET['shortcode_id']] = 'form button pressed';
		$_SESSION['wfu_start_time_'.$_GET['shortcode_id']] = $_GET['start_time'];

		die("wfu_response_success:");
	}
}
die();
?>
