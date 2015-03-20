<?php

function wfu_view_log() {
	global $wpdb;
	$siteurl = site_url();
	$table_name1 = $wpdb->prefix . "wfu_log";
	$table_name2 = $wpdb->prefix . "wfu_userdata";
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	if ( !current_user_can( 'manage_options' ) ) return;

	$echo_str = "\n".'<div class="wrap">';
	$echo_str .= "\n\t".'<h2>Wordpress File Upload Control Panel</h2>';
	$echo_str .= "\n\t".'<div style="margin-top:20px;">';
	$echo_str .= "\n\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=manage_mainmenu" class="button" title="go back">Go to Main Menu</a>';
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n\t".'<h2 style="margin-bottom: 10px; margin-top: 20px;">History Log</h2>';
	$echo_str .= "\n\t".'<div>';
	$echo_str .= "\n\t\t".'<table class="widefat">';
	$echo_str .= "\n\t\t\t".'<thead>';
	$echo_str .= "\n\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="5%" style="text-align:center;">';
	$echo_str .= "\n\t\t\t\t\t\t".'<label>#</label>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="15%" style="text-align:left;">';
	$echo_str .= "\n\t\t\t\t\t\t".'<label>Date</label>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="10%" style="text-align:center;">';
	$echo_str .= "\n\t\t\t\t\t\t".'<label>Action</label>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="30%" style="text-align:left;">';
	$echo_str .= "\n\t\t\t\t\t\t".'<label>File</label>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="15%" style="text-align:center;">';
	$echo_str .= "\n\t\t\t\t\t\t".'<label>User</label>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="25%" style="text-align:left;">';
	$echo_str .= "\n\t\t\t\t\t\t".'<label>Remarks</label>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t".'</thead>';
	$echo_str .= "\n\t\t\t".'<tbody>';

	$filerecs = $wpdb->get_results('SELECT * FROM '.$table_name1.' ORDER BY date_from DESC');
	$userdatarecs = $wpdb->get_results('SELECT * FROM '.$table_name2);
	$i = 0;
	foreach ( $filerecs as $filerec ) {
		$remarks = '';
		$filepath = ABSPATH;
		if ( substr($filepath, -1) == '/' ) $filepath = substr($filepath, 0, -1);
		$filepath .= $filerec->filepath;
		$enc_file = wfu_plugin_encode_string($filepath.'[[name]]');
		if ( $filerec->action == 'rename' ) {
			$prevfilepath = '';
			foreach ( $filerecs as $key => $prevfilerec ) {
				if ( $prevfilerec->idlog == $filerec->linkedto ) {
					$prevfilepath = $prevfilerec->filepath;
					break;
				}
			}
			if ( $prevfilepath != '' )
				$remarks = "\n\t\t\t\t\t\t".'<label>Previous filepath: '.$prevfilepath.'</label>';
		}
		elseif ( $filerec->action == 'upload' || $filerec->action == 'modify' ) {
			foreach ( $userdatarecs as $userdata ) {
				if ( $userdata->uploadid == $filerec->uploadid && $userdata->date_from == $filerec->date_from )
					$remarks .= "\n\t\t\t\t\t\t\t".'<option>'.$userdata->property.': '.$userdata->propvalue.'</option>';
			}
			if ( $remarks != '' ) {
				$remarks = "\n\t\t\t\t\t\t".'<select multiple="multiple" style="width:100%; height:40px; background:none; font-size:small;">'.$remarks;
				$remarks .= "\n\t\t\t\t\t\t".'</select>';
			}
		}
		elseif ( $filerec->action == 'other' ) {
			$info = $filerec->filepath;
			$filerec->filepath = '';
			$remarks = "\n\t\t\t\t\t\t".'<textarea style="width:100%; resize:vertical; background:none;" readonly="readonly">'.$info.'</textarea>';
		}
		$i ++;
		$otheraction = ( $filerec->action == 'other' );
		$echo_str .= "\n\t\t\t\t".'<tr>';
		$echo_str .= "\n\t\t\t\t\t".'<td style="padding: 5px 5px 5px 10px; text-align:center;">'.$i.'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td style="padding: 5px 5px 5px 10px; text-align:left;">'.$filerec->date_from.'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td style="padding: 5px 5px 5px 10px; text-align:center;">'.$filerec->action.'</td>';
		if ( !$otheraction ) {	
			$echo_str .= "\n\t\t\t\t\t".'<td style="padding: 5px 5px 5px 10px; text-align:left;">';
			$echo_str .= "\n\t\t\t\t\t\t".'<a class="row-title" href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_details&file='.$enc_file.'" title="View and edit file details" style="font-weight:normal;">'.$filerec->filepath.'</a>';
			$echo_str .= "\n\t\t\t\t\t".'</td>';
			$echo_str .= "\n\t\t\t\t\t".'<td style="padding: 5px 5px 5px 10px; text-align:center;">'.wfu_get_username_by_id($filerec->userid).'</td>';
		}
		$echo_str .= "\n\t\t\t\t\t".'<td style="padding: 5px 5px 5px 10px; text-align:left;"'.( $otheraction ? ' colspan="3"' : '' ).'>';
		$echo_str .= $remarks;
		$echo_str .= "\n\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t".'</tr>';
	}
	$echo_str .= "\n\t\t\t".'</tbody>';
	$echo_str .= "\n\t\t".'</table>';
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n".'</div>';

	return $echo_str;
}

?>
