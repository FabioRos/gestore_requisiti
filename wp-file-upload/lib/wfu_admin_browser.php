<?php

function wfu_browse_files($basedir) {
	$siteurl = site_url();
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	if ( !current_user_can( 'manage_options' ) ) return;
	//first decode basedir
	$basedir = wfu_plugin_decode_string($basedir);
	//then extract sort info from basedir
	$ret = wfu_extract_sortdata_from_path($basedir);
	$basedir = $ret['path'];
	$sort = $ret['sort'];
	if ( $sort == "" ) $sort = 'name';
	if ( substr($sort, 0, 1) == '-' ) $order = SORT_DESC;
	else $order = SORT_ASC;

	//adjust basedir to have a standard format
	if ( $basedir != "" ) {
		if ( substr($basedir, -1) != '/' ) $basedir .= '/';
		if ( !file_exists($basedir) ) $basedir = "";
	}
	//set basedit to default value if empty
	if ( $basedir == "" ) {
		$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
		$basedir = $plugin_options['basedir'];
		$temp_params = array( 'uploadpath' => $basedir, 'accessmethod' => 'normal', 'ftpinfo' => '', 'useftpdomain' => 'false' );
		$basedir = wfu_upload_plugin_full_path($temp_params);
	}
	//find relative dir
	$reldir = str_replace(ABSPATH, "root/", $basedir);
	//save dir route to an array
	$parts = explode('/', $reldir);
	$route = array();
	$prev = "";
	foreach ( $parts as $part ) {
		$part = trim($part);
		if ( $part != "" ) {
			if ( $part == 'root' && $prev == "" ) $prev = ABSPATH;
			else $prev .= $part.'/';
			array_push($route, array( 'item' => $part, 'path' => $prev ));
		}
	}
	//calculate upper directory
	$updir = substr($basedir, 0, -1);
	$delim_pos = strrpos($updir, '/');
	if ( $delim_pos !== false ) $updir = substr($updir, 0, $delim_pos + 1);

	$echo_str = "\n".'<div class="wrap">';
	$echo_str .= "\n\t".'<h2>Wordpress File Upload Control Panel</h2>';
	$echo_str .= "\n\t".'<div style="margin-top:20px;">';
	$echo_str .= "\n\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=manage_mainmenu" class="button" title="go back">Go to Main Menu</a>';
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n\t".'<h2 style="margin-bottom: 10px; margin-top: 20px;">File Browser</h2>';
	$echo_str .= "\n\t".'<div>';
	$echo_str .= "\n\t\t".'<span><strong>Location:</strong> </span>';
	foreach ( $route as $item ) {
		$echo_str .= '<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_browser&dir='.wfu_plugin_encode_string($item['path']).'">'.$item['item'].'</a>';
		$echo_str .= '<span>/</span>';
	}
	//file browser header
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=create_dir&dir='.wfu_plugin_encode_string($basedir.'[['.$sort.']]').'" class="button" title="create folder" style="margin-top:6px">Create folder</a>';
	$echo_str .= "\n\t".'<div style="margin-top:10px;">';
	$echo_str .= "\n\t\t".'<table class="widefat">';
	$echo_str .= "\n\t\t\t".'<thead>';
	$echo_str .= "\n\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="30%" style="text-align:left;">';
	$enc_dir = wfu_plugin_encode_string($basedir.'[['.( substr($sort, -4) == 'name' ? ( $order == SORT_ASC ? '-name' : 'name' ) : 'name' ).']]');
	$echo_str .= "\n\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_browser&dir='.$enc_dir.'">Name'.( substr($sort, -4) == 'name' ? ( $order == SORT_ASC ? ' &uarr;' : ' &darr;' ) : '' ).'</a>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="10%" style="text-align:right;">';
	$enc_dir = wfu_plugin_encode_string($basedir.'[['.( substr($sort, -4) == 'size' ? ( $order == SORT_ASC ? '-size' : 'size' ) : 'size' ).']]');
	$echo_str .= "\n\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_browser&dir='.$enc_dir.'">Size'.( substr($sort, -4) == 'size' ? ( $order == SORT_ASC ? ' &uarr;' : ' &darr;' ) : '' ).'</a>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="20%" style="text-align:left;">';
	$enc_dir = wfu_plugin_encode_string($basedir.'[['.( substr($sort, -4) == 'date' ? ( $order == SORT_ASC ? '-date' : 'date' ) : 'date' ).']]');
	$echo_str .= "\n\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_browser&dir='.$enc_dir.'">Date'.( substr($sort, -4) == 'date' ? ( $order == SORT_ASC ? ' &uarr;' : ' &darr;' ) : '' ).'</a>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="10%" style="text-align:center;">';
	$enc_dir = wfu_plugin_encode_string($basedir.'[['.( substr($sort, -4) == 'user' ? ( $order == SORT_ASC ? '-user' : 'user' ) : 'user' ).']]');
	$echo_str .= "\n\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_browser&dir='.$enc_dir.'">Uploaded By'.( substr($sort, -4) == 'user' ? ( $order == SORT_ASC ? ' &uarr;' : ' &darr;' ) : '' ).'</a>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="30%" style="text-align:left;">';
	$echo_str .= "\n\t\t\t\t\t\t".'<label>User Data</label>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t".'</thead>';
	$echo_str .= "\n\t\t\t".'<tbody>';

	//find contents of current folder
	$dirlist = array();
	$filelist = array();
	if ( $handle = opendir($basedir) ) {
		$blacklist = array('.', '..');
		while ( false !== ($file = readdir($handle)) )
			if ( !in_array($file, $blacklist) ) {
				$filepath = $basedir.$file;
				$stat = stat($filepath);
				if ( is_dir($filepath) ) {
					array_push($dirlist, array( 'name' => $file, 'fullpath' => $filepath, 'mdate' => $stat['mtime'] ));
				}
				else {
					//find relative file record in database together with user data
					$filerec = wfu_get_file_rec($filepath, true);
					//find user who uploaded the file
					$username = '';
					if ( $filerec != null ) $username = wfu_get_username_by_id($filerec->userid);
					array_push($filelist, array( 'name' => $file, 'fullpath' => $filepath, 'size' => $stat['size'], 'mdate' => $stat['mtime'], 'user' => $username, 'filedata' => $filerec ));
				}
			}
		closedir($handle);
	}
	$dirsort = ( substr($sort, -4) == 'date' ? 'mdate' : substr($sort, -4) );
	$filesort = $dirsort;
	$dirorder = $order;
	if ( $dirsort == 'size' ) { $dirsort = 'name'; $dirorder = SORT_ASC; }
	if ( $dirsort == 'user' ) { $dirsort = 'name'; $dirorder = SORT_ASC; }
	$dirlist = wfu_array_sort($dirlist, $dirsort, $dirorder);
	$filelist = wfu_array_sort($filelist, $filesort, $order);

	//show subfolders first
	if ( $reldir != "root/" ) {
		$enc_dir = wfu_plugin_encode_string($updir);
		$echo_str .= "\n\t\t\t\t".'<tr>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="30%" style="padding: 5px 5px 5px 10px; text-align:left;">';
		$echo_str .= "\n\t\t\t\t\t\t".'<a class="row-title" href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_browser&dir='.$enc_dir.'" title="go up">..</a>';
		$echo_str .= "\n\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="10%" style="padding: 5px 5px 5px 10px; text-align:right;"> </td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="20%" style="padding: 5px 5px 5px 10px; text-align:left;"> </td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="10%" style="padding: 5px 5px 5px 10px; text-align:center;"> </td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="30%" style="padding: 5px 5px 5px 10px; text-align:left;"> </td>';
		$echo_str .= "\n\t\t\t\t".'</tr>';
	}
	$ii = 1;
	foreach ( $dirlist as $dir ) {
		$enc_dir = wfu_plugin_encode_string($dir['fullpath'].'[['.$sort.']]');
		$echo_str .= "\n\t\t\t\t".'<tr onmouseover="for (i in document.getElementsByName(\'wfu_dir_actions\')){document.getElementsByName(\'wfu_dir_actions\').item(i).style.visibility=\'hidden\';} document.getElementById(\'wfu_dir_actions_'.$ii.'\').style.visibility=\'visible\'" onmouseout="for (i in document.getElementsByName(\'wfu_dir_actions\')){document.getElementsByName(\'wfu_dir_actions\').item(i).style.visibility=\'hidden\';}">';
		$echo_str .= "\n\t\t\t\t\t".'<td width="30%" style="padding: 5px 5px 5px 10px; text-align:left;">';
		$echo_str .= "\n\t\t\t\t\t\t".'<a class="row-title" href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_browser&dir='.$enc_dir.'" title="'.$dir['name'].'">'.$dir['name'].'</a>';
		$echo_str .= "\n\t\t\t\t\t\t".'<div id="wfu_dir_actions_'.$ii.'" name="wfu_dir_actions" style="visibility:hidden;">';
		$echo_str .= "\n\t\t\t\t\t\t\t".'<span>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=rename_dir&file='.$enc_dir.'" title="Rename this folder">Rename</a>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t".' | ';
		$echo_str .= "\n\t\t\t\t\t\t\t".'</span>';
		$echo_str .= "\n\t\t\t\t\t\t\t".'<span>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=delete_dir&file='.$enc_dir.'" title="Delete this folder">Delete</a>';
		$echo_str .= "\n\t\t\t\t\t\t\t".'</span>';
		$echo_str .= "\n\t\t\t\t\t\t".'</div>';
		$echo_str .= "\n\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="10%" style="padding: 5px 5px 5px 10px; text-align:right;"> </td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="20%" style="padding: 5px 5px 5px 10px; text-align:left;">'.date("d/m/Y H:i:s", $dir['mdate']).'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="10%" style="padding: 5px 5px 5px 10px; text-align:center;"> </td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="30%" style="padding: 5px 5px 5px 10px; text-align:left;"> </td>';
		$echo_str .= "\n\t\t\t\t".'</tr>';
		$ii ++;
	}
	//show contained files
	foreach ( $filelist as $file ) {
		$enc_file = wfu_plugin_encode_string($file['fullpath'].'[['.$sort.']]');
		$echo_str .= "\n\t\t\t\t".'<tr onmouseover="for (i in document.getElementsByName(\'wfu_file_actions\')){document.getElementsByName(\'wfu_file_actions\').item(i).style.visibility=\'hidden\';} document.getElementById(\'wfu_file_actions_'.$ii.'\').style.visibility=\'visible\'" onmouseout="for (i in document.getElementsByName(\'wfu_file_actions\')){document.getElementsByName(\'wfu_file_actions\').item(i).style.visibility=\'hidden\';}">';
		$echo_str .= "\n\t\t\t\t\t".'<td width="30%" style="padding: 5px 5px 5px 10px; text-align:left;">';
		if ( $file['filedata'] != null )
			$echo_str .= "\n\t\t\t\t\t\t".'<a class="row-title" href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_details&file='.$enc_file.'" title="View and edit file details" style="font-weight:normal;">'.$file['name'].'</a>';
		else
			$echo_str .= "\n\t\t\t\t\t\t".'<span>'.$file['name'].'</span>';
		$echo_str .= "\n\t\t\t\t\t\t".'<div id="wfu_file_actions_'.$ii.'" name="wfu_file_actions" style="visibility:hidden;">';
		if ( $file['filedata'] != null ) {
			$echo_str .= "\n\t\t\t\t\t\t\t".'<span>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_details&file='.$enc_file.'" title="View and edit file details">Details</a>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".' | ';
			$echo_str .= "\n\t\t\t\t\t\t\t".'</span>';
		}
		$echo_str .= "\n\t\t\t\t\t\t\t".'<span>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=rename_file&file='.$enc_file.'" title="Rename this file">Rename</a>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t".' | ';
		$echo_str .= "\n\t\t\t\t\t\t\t".'</span>';
		$echo_str .= "\n\t\t\t\t\t\t\t".'<span>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=delete_file&file='.$enc_file.'" title="Delete this file">Delete</a>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t".' | ';
		$echo_str .= "\n\t\t\t\t\t\t\t".'</span>';
		$echo_str .= "\n\t\t\t\t\t\t\t".'<span>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t".'<a href="javascript:wfu_download_file(\''.wfu_plugin_encode_string(WFU_AJAX_URL).'\', \''.wfu_plugin_encode_string($file['fullpath']).'\', '.( $file['filedata'] != null ? $file['filedata']->idlog : '0' ).');" title="Download this file">Download</a>';
		$echo_str .= "\n\t\t\t\t\t\t\t".'</span>';
		$echo_str .= "\n\t\t\t\t\t\t".'</div>';
		$echo_str .= "\n\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="10%" style="padding: 5px 5px 5px 10px; text-align:right;">'.$file['size'].'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="20%" style="padding: 5px 5px 5px 10px; text-align:left;">'.date("d/m/Y H:i:s", $file['mdate']).'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="10%" style="padding: 5px 5px 5px 10px; text-align:center;">'.$file['user'].'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="30%" style="padding: 5px 5px 5px 10px; text-align:left;">';
		if ( $file['filedata'] != null ) {
			if ( count($file['filedata']->userdata) > 0 ) {
				$echo_str .= "\n\t\t\t\t\t\t".'<select multiple="multiple" style="width:100%; height:40px; background:none; font-size:small;">';
				foreach ( $file['filedata']->userdata as $userdata )
					$echo_str .= "\n\t\t\t\t\t\t\t".'<option>'.$userdata->property.': '.$userdata->propvalue.'</option>';
				$echo_str .= "\n\t\t\t\t\t\t".'</select>';
			}
		}
		$echo_str .= "\n\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t".'</tr>';
		$ii ++;
	}
	$echo_str .= "\n\t\t\t".'</tbody>';
	$echo_str .= "\n\t\t".'</table>';
	$echo_str .= "\n\t\t".'<iframe id="wfu_download_frame" style="display: none;"></iframe>';
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n".'</div>';

	return $echo_str;
}

function wfu_current_user_allowed_action($action, $filepath) {
	//first get file data from database, if exist
	$filerec = wfu_get_file_rec($filepath, false);

	$user = wp_get_current_user();
	if ( 0 == $user->ID ) return null;
	else $is_admin = current_user_can('manage_options');
	if ( !$is_admin ) {
		return null;
	}
	return $user;
}

function wfu_rename_file_prompt($file, $type, $error) {
	$siteurl = site_url();

	$dec_file = wfu_plugin_decode_string($file);
	//first extract sort info from dec_file
	$ret = wfu_extract_sortdata_from_path($dec_file);
	$dec_file = $ret['path'];
	if ( $type == 'dir' && substr($dec_file, -1) == '/' ) $dec_file = substr($dec_file, 0, -1);

	//check if user is allowed to perform this action
	if ( wfu_current_user_allowed_action('rename', $dec_file) == null ) return;

	$parts = pathinfo($dec_file);
	$newname = $parts['basename'];
	$enc_dir = wfu_plugin_encode_string($parts['dirname'].'[['.$ret['sort'].']]');

	$echo_str = "\n".'<div class="wrap">';
	if ( $error ) {
		$newname = $_SESSION['wfu_rename_file']['newname'];
		$echo_str .= "\n\t".'<div class="error">';
		$echo_str .= "\n\t\t".'<p>'.$_SESSION['wfu_rename_file_error'].'</p>';
		$echo_str .= "\n\t".'</div>';
	}
	$echo_str .= "\n\t".'<div style="margin-top:20px;">';
	$echo_str .= "\n\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=file_browser&dir='.$enc_dir.'" class="button" title="go back">Go back</a>';
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n\t".'<h2 style="margin-bottom: 10px;">Rename '.( $type == 'dir' ? 'Folder' : 'File' ).'</h2>';
	$echo_str .= "\n\t".'<form enctype="multipart/form-data" name="renamefile" id="renamefile" method="post" action="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload" class="validate">';
	$echo_str .= "\n\t\t".'<input type="hidden" name="action" value="rename'.( $type == 'dir' ? 'dir' : 'file' ).'">';
	$echo_str .= "\n\t\t".'<input type="hidden" name="dir" value="'.$enc_dir.'">';
	$echo_str .= "\n\t\t".'<input type="hidden" name="file" value="'.$file.'">';
	if ( $type == 'dir' ) $echo_str .= "\n\t\t".'<label>Enter new name for folder <strong>'.$dec_file.'</strong></label><br/>';
	else $echo_str .= "\n\t\t".'<label>Enter new filename for file <strong>'.$dec_file.'</strong></label><br/>';
	$echo_str .= "\n\t\t".'<input name="wfu_newname" id="wfu_newname" type="text" value="'.$newname.'" style="width:50%;" />';
	$echo_str .= "\n\t\t".'<p class="submit">';
	$echo_str .= "\n\t\t\t".'<input type="submit" class="button-primary" name="submit" value="Rename">';
	$echo_str .= "\n\t\t\t".'<input type="submit" class="button-primary" name="submit" value="Cancel">';
	$echo_str .= "\n\t\t".'</p>';
	$echo_str .= "\n\t".'</form>';
	$echo_str .= "\n".'</div>';
	return $echo_str;
}

function wfu_rename_file($file, $type) {
	$dec_file = wfu_plugin_decode_string($file);
	$dec_file = wfu_flatten_path($dec_file);
	if ( $type == 'dir' && substr($dec_file, -1) == '/' ) $dec_file = substr($dec_file, 0, -1);
	if ( !file_exists($dec_file) ) return wfu_browse_files();

	//check if user is allowed to perform this action
	$user = wfu_current_user_allowed_action('rename', $dec_file);
	if ( $user == null ) return;

	$parts = pathinfo($dec_file);
	$error = "";
	if ( isset($_POST['wfu_newname'])  && isset($_POST['submit']) ) {
		if ( $_POST['submit'] == "Rename" && $_POST['wfu_newname'] != $parts['basename'] ) {
			$new_file = $parts['dirname'].'/'.$_POST['wfu_newname'];
			$relativepath = str_replace(ABSPATH, '', $new_file);
			if ( substr($relativepath, 0, 1) != '/' ) $relativepath = '/'.$relativepath;
			if ( $_POST['wfu_newname'] == "" ) $error = 'Error: New '.( $type == 'dir' ? 'folder ' : 'file' ).'name cannot be empty!';
			elseif ( preg_match("/[^A-Za-z0-9.#\-$]/", $_POST['wfu_newname']) ) $error = 'Error: name contains invalid characters! Please correct.';
			elseif ( file_exists($new_file) ) $error = 'Error: The '.( $type == 'dir' ? 'folder' : 'file' ).' <strong>'.$_POST['wfu_newname'].'</strong> already exists! Please choose another one.';
			else {
				//pre-log rename action
				if ( $type == 'file' ) $retid = wfu_log_action('rename:'.$new_file, $dec_file, $user->ID, '', 0, '', null);
				//perform rename action
				if ( rename($dec_file, $new_file) == false ) $error = 'Error: Rename of '.( $type == 'dir' ? 'folder' : 'file' ).' <strong>'.$parts['basename'].'</strong> failed!';
				//revert log action if file was not renamed
				if ( $type == 'file' && !file_exists($new_file) ) wfu_revert_log_action($retid);
			}
		}
	}
	if ( $error != "" ) {
		$_SESSION['wfu_rename_file_error'] = $error;
		$_SESSION['wfu_rename_file']['newname'] = $_POST['wfu_newname'];
	}
	return ( $error == "" );
}

function wfu_delete_file_prompt($file, $type) {
	$siteurl = site_url();

	$dec_file = wfu_plugin_decode_string($file);
	//first extract sort info from dec_file
	$ret = wfu_extract_sortdata_from_path($dec_file);
	$dec_file = $ret['path'];
	if ( $type == 'dir' && substr($dec_file, -1) == '/' ) $dec_file = substr($dec_file, 0, -1);

	//check if user is allowed to perform this action
	if ( wfu_current_user_allowed_action('delete', $dec_file) == null ) return;

	$parts = pathinfo($dec_file);
	$enc_dir = wfu_plugin_encode_string($parts['dirname'].'[['.$ret['sort'].']]');

	$echo_str = "\n".'<div class="wrap">';
	$echo_str .= "\n\t".'<div style="margin-top:20px;">';
	$echo_str .= "\n\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=file_browser&dir='.$enc_dir.'" class="button" title="go back">Go back</a>';
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n\t".'<h2 style="margin-bottom: 10px;">Delete '.( $type == 'dir' ? 'Folder' : 'File' ).'</h2>';
	$echo_str .= "\n\t".'<form enctype="multipart/form-data" name="deletefile" id="deletefile" method="post" action="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload" class="validate">';
	$echo_str .= "\n\t\t".'<input type="hidden" name="action" value="delete'.( $type == 'dir' ? 'dir' : 'file' ).'">';
	$echo_str .= "\n\t\t".'<input type="hidden" name="dir" value="'.$enc_dir.'">';
	$echo_str .= "\n\t\t".'<input type="hidden" name="file" value="'.$file.'">';
	$echo_str .= "\n\t\t".'<label>Are you sure that you want to delete '.( $type == 'dir' ? 'folder' : 'file' ).' <strong>'.$parts['basename'].'</strong>?</label><br/>';
	$echo_str .= "\n\t\t".'<p class="submit">';
	$echo_str .= "\n\t\t\t".'<input type="submit" class="button-primary" name="submit" value="Delete">';
	$echo_str .= "\n\t\t\t".'<input type="submit" class="button-primary" name="submit" value="Cancel">';
	$echo_str .= "\n\t\t".'</p>';
	$echo_str .= "\n\t".'</form>';
	$echo_str .= "\n".'</div>';
	return $echo_str;
}

function wfu_delete_file($file, $type) {
	$dec_file = wfu_plugin_decode_string($file);
	$dec_file = wfu_flatten_path($dec_file);
	if ( $type == 'dir' && substr($dec_file, -1) == '/' ) $dec_file = substr($dec_file, 0, -1);

	//check if user is allowed to perform this action
	$user = wfu_current_user_allowed_action('delete', $dec_file);
	if ( $user == null ) return;

	if ( isset($_POST['submit']) ) {
		if ( $_POST['submit'] == "Delete" ) {
			//pre-log delete action
			if ( $type == 'file' ) $retid = wfu_log_action('delete', $dec_file, $user->ID, '', 0, '', null);
			if ( $type == 'dir' && $dec_file != "" ) wfu_delTree($dec_file);
			else unlink($dec_file);
			//revert log action if file has not been deleted
			if ( $type == 'file' && file_exists($dec_file) ) wfu_revert_log_action($retid);
		}
	}
	return true;
}

function wfu_create_dir_prompt($dir, $error) {
	$siteurl = site_url();

	if ( !current_user_can( 'manage_options' ) ) return;

	$dec_dir = wfu_plugin_decode_string($dir);
	//first extract sort info from dec_dir
	$ret = wfu_extract_sortdata_from_path($dec_dir);
	$dec_dir = $ret['path'];
	if ( substr($dec_dir, -1) != '/' ) $dec_dir .= '/';
	$newname = '';

	$echo_str = "\n".'<div class="wrap">';
	if ( $error ) {
		$newname = $_SESSION['wfu_create_dir']['newname'];
		$echo_str .= "\n\t".'<div class="error">';
		$echo_str .= "\n\t\t".'<p>'.$_SESSION['wfu_create_dir_error'].'</p>';
		$echo_str .= "\n\t".'</div>';
	}
	$echo_str .= "\n\t".'<div style="margin-top:20px;">';
	$echo_str .= "\n\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=file_browser&dir='.$dir.'" class="button" title="go back">Go back</a>';
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n\t".'<h2 style="margin-bottom: 10px;">Create Folder</h2>';
	$echo_str .= "\n\t".'<form enctype="multipart/form-data" name="createdir" id="createdir" method="post" action="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload" class="validate">';
	$echo_str .= "\n\t\t".'<input type="hidden" name="action" value="createdir">';
	$echo_str .= "\n\t\t".'<input type="hidden" name="dir" value="'.$dir.'">';
	$echo_str .= "\n\t\t".'<label>Enter the name of the new folder inside <strong>'.$dec_dir.'</strong></label><br/>';
	$echo_str .= "\n\t\t".'<input name="wfu_newname" id="wfu_newname" type="text" value="'.$newname.'" style="width:50%;" />';
	$echo_str .= "\n\t\t".'<p class="submit">';
	$echo_str .= "\n\t\t\t".'<input type="submit" class="button-primary" name="submit" value="Create">';
	$echo_str .= "\n\t\t\t".'<input type="submit" class="button-primary" name="submit" value="Cancel">';
	$echo_str .= "\n\t\t".'</p>';
	$echo_str .= "\n\t".'</form>';
	$echo_str .= "\n".'</div>';
	return $echo_str;
}

function wfu_create_dir($dir) {
	if ( !current_user_can( 'manage_options' ) ) return;

	$dec_dir = wfu_plugin_decode_string($dir);
	$dec_dir = wfu_flatten_path($dec_dir);
	if ( substr($dec_dir, -1) != '/' ) $dec_dir .= '/';
	if ( !file_exists($dec_dir) ) return wfu_browse_files();
	$error = "";
	if ( isset($_POST['wfu_newname'])  && isset($_POST['submit']) ) {
		if ( $_POST['submit'] == "Create" ) {
			$new_dir = $dec_dir.$_POST['wfu_newname'];
			if ( $_POST['wfu_newname'] == "" ) $error = 'Error: New folder name cannot be empty!';
			elseif ( preg_match("/[^A-Za-z0-9.#\-$]/", $_POST['wfu_newname']) ) $error = 'Error: name contains invalid characters! Please correct.';
			elseif ( file_exists($new_dir) ) $error = 'Error: The folder <strong>'.$_POST['wfu_newname'].'</strong> already exists! Please choose another one.';
			elseif ( mkdir($new_dir) == false ) $error = 'Error: Creation of folder <strong>'.$_POST['wfu_newname'].'</strong> failed!';
		}
	}
	if ( $error != "" ) {
		$_SESSION['wfu_create_dir_error'] = $error;
		$_SESSION['wfu_create_dir']['newname'] = $_POST['wfu_newname'];
	}
	return ( $error == "" );
}

function wfu_file_details($file, $errorstatus) {
	global $wpdb;
	$table_name1 = $wpdb->prefix . "wfu_log";
	$siteurl = site_url();

	//extract file browser data from $file variable
	$dec_file = wfu_plugin_decode_string($file);
	$ret = wfu_extract_sortdata_from_path($dec_file);
	$filepath = $ret['path'];

	//get file data from database with user data
	$filedata = wfu_get_file_rec($filepath, true);
	if ( $filedata == null ) return;

	//check if user is allowed to perform this action
	$user = wfu_current_user_allowed_action('details', $filepath);
	if ( $user == null ) return;

	//get the username of the uploader
	$uploadername = wfu_get_username_by_id($filedata->uploaduserid);

	//extract sort info and construct contained dir
	$parts = pathinfo($filepath);
	$enc_dir = wfu_plugin_encode_string($parts['dirname'].'[['.$ret['sort'].']]');

	$stat = stat($filepath);

	$echo_str = '<div class="regev_wrap">';
	if ( $errorstatus == 'error' ) {
		$echo_str .= "\n\t".'<div class="error">';
		$echo_str .= "\n\t\t".'<p>'.$_SESSION['wfu_filedetails_error'].'</p>';
		$echo_str .= "\n\t".'</div>';
	}
	//show file detais
	$echo_str .= "\n\t".'<h2>Detais of File: '.$parts['basename'].'</h2>';
	$echo_str .= "\n\t".'<div style="margin-top:10px;">';
	$echo_str .= "\n\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=file_browser&dir='.$enc_dir.'" class="button" title="go back">Go back</a>';
	$echo_str .= "\n\t\t".'<form enctype="multipart/form-data" name="editfiledetails" id="editfiledetails" method="post" action="/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=edit_filedetails" class="validate">';
	$echo_str .= "\n\t\t\t".'<h3 style="margin-bottom: 10px; margin-top: 40px;">Upload Details</h3>';
	$echo_str .= "\n\t\t\t".'<input type="hidden" name="action" value="edit_filedetails" />';
	$echo_str .= "\n\t\t\t".'<input type="hidden" name="dir" value="'.$enc_dir.'">';
	$echo_str .= "\n\t\t\t".'<input type="hidden" name="file" value="'.$file.'">';
	$echo_str .= "\n\t\t\t".'<table class="form-table">';
	$echo_str .= "\n\t\t\t\t".'<tbody>';
	$echo_str .= "\n\t\t\t\t\t".'<tr class="form-field">';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Full Path</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input type="text" value="'.$filepath.'" readonly="readonly" />';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr class="form-field">';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Uploaded From User</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input type="text" value="'.$uploadername.'" readonly="readonly" style="width:auto;" />';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr class="form-field">';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>File Size</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input type="text" value="'.$filedata->filesize.'" readonly="readonly" style="width:auto;" />';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr class="form-field">';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>File Date</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input type="text" value="'.date("d/m/Y H:i:s", $stat['mtime']).'" readonly="readonly" style="width:auto;" />';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr class="form-field">';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Uploaded From Page</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input type="text" value="'.get_the_title($filedata->pageid).' ('.$filedata->pageid.')'.'" readonly="readonly" style="width:50%;" />';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr class="form-field">';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Upload Plugin ID</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input type="text" value="'.$filedata->sid.'" readonly="readonly" style="width:auto;" />';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t".'</tbody>';
	$echo_str .= "\n\t\t\t".'</table>';
	//show history details
	$echo_str .= "\n\t\t\t".'<h3 style="margin-bottom: 10px; margin-top: 40px;">File History</h3>';
	$echo_str .= "\n\t\t\t".'<table class="form-table">';
	$echo_str .= "\n\t\t\t\t".'<tbody>';
	$echo_str .= "\n\t\t\t\t\t".'<tr class="form-field">';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label></label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	//read all linked records
	$filerecs = array();
	array_push($filerecs, $filedata);
	$currec = $filedata;
	while ( $currec->linkedto > 0 ) {
		$currec = $wpdb->get_row('SELECT * FROM '.$table_name1.' WHERE idlog = '.$currec->linkedto);
		if ( $currec != null ) array_push($filerecs, $currec);
		else break;
	}
	//construct report from db records
	$rep = '';
	foreach ( $filerecs as $filerec ) {
		$username = wfu_get_username_by_id($filerec->userid);
		$fileparts = pathinfo($filerec->filepath);
		if ( $rep != '' ) $rep .= "<br />";
		$rep .= '<strong>['.$filerec->date_from.']</strong> ';
		if ( $filerec->action == 'upload' )
			$rep .= 'File uploaded with name <strong>'.$fileparts['basename'].'</strong> by user <strong>'.$username.'</strong>';
		elseif ( $filerec->action == 'download' )
			$rep .= 'File downloaded by user <strong>'.$username.'</strong>';
		elseif ( $filerec->action == 'rename' )
			$rep .= 'File renamed to <strong>'.$fileparts['basename'].'</strong> by user <strong>'.$username.'</strong>';
		elseif ( $filerec->action == 'delete' )
			$rep .= 'File deleted by user <strong>'.$username.'</strong>';
		elseif ( $filerec->action == 'modify' )
			$rep .= 'File userdata modified by user <strong>'.$username.'</strong>';
	}
	$echo_str .= "\n\t\t\t\t\t\t\t".'<div style="border:1px solid #dfdfdf; border-radius:3px; width:50%; overflow:scroll; padding:6px; height:100px; background-color:#eee;">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<span style="white-space:nowrap;">'.$rep.'</span>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'</div>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t".'</tbody>';
	$echo_str .= "\n\t\t\t".'</table>';

	$echo_str .= "\n\t\t\t".'<h3 style="margin-bottom: 10px; margin-top: 40px;">User Data Details</h3>';
	$echo_str .= "\n\t\t\t".'<table class="form-table">';
	$echo_str .= "\n\t\t\t\t".'<tbody>';
	if ( count($filedata->userdata) > 0 ) {
		foreach ( $filedata->userdata as $userdata ) {
			$echo_str .= "\n\t\t\t\t\t".'<tr class="form-field">';
			$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<label>'.$userdata->property.'</label>';
			$echo_str .= "\n\t\t\t\t\t\t".'</th>';
			$echo_str .= "\n\t\t\t\t\t\t".'<td>';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<input id="wfu_filedetails_userdata_value_'.$userdata->propkey.'" name="wfu_filedetails_userdata" type="text" value="'.$userdata->propvalue.'" />';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<input id="wfu_filedetails_userdata_default_'.$userdata->propkey.'" type="hidden" value="'.$userdata->propvalue.'" />';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<input id="wfu_filedetails_userdata_'.$userdata->propkey.'" name="wfu_filedetails_userdata_'.$userdata->propkey.'" type="hidden" value="'.$userdata->propvalue.'" />';
			$echo_str .= "\n\t\t\t\t\t\t".'</td>';
			$echo_str .= "\n\t\t\t\t\t".'</tr>';
		}
	}
	else {
		$echo_str .= "\n\t\t\t\t\t".'<tr class="form-field">';
		$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
		$echo_str .= "\n\t\t\t\t\t\t\t".'<label>No user data</label>';
		$echo_str .= "\n\t\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t\t".'<td></td>';
		$echo_str .= "\n\t\t\t\t\t".'</tr>';
	}
	$echo_str .= "\n\t\t\t\t".'</tbody>';
	$echo_str .= "\n\t\t\t".'</table>';
	$echo_str .= "\n\t\t\t".'<p class="submit">';
	$echo_str .= "\n\t\t\t\t".'<input id="dp_filedetails_submit_fields" type="submit" class="button-primary" name="submit" value="Update" disabled="disabled" />';
	$echo_str .= "\n\t\t\t".'</p>';
	$echo_str .= "\n\t\t".'</form>';
	$echo_str .= "\n\t".'</div>';
	$handler = 'function() { wfu_Attach_FileDetails_Admin_Events(); }';
	$echo_str .= "\n\t".'<script type="text/javascript">if(window.addEventListener) { window.addEventListener("load", '.$handler.', false); } else if(window.attachEvent) { window.attachEvent("onload", '.$handler.'); } else { window["onload"] = '.$handler.'; }</script>';
	$echo_str .= '</div>';
    
	return $echo_str;
}

function wfu_edit_filedetails($file) {
	global $wpdb;
	$table_name2 = $wpdb->prefix . "wfu_userdata";

	$dec_file = wfu_plugin_decode_string($file);
	$dec_file = wfu_flatten_path($dec_file);

	//check if user is allowed to perform this action
	$user = wfu_current_user_allowed_action('modify', $dec_file);
	if ( $user == null ) return;

	//get file data from database with user data
	$filedata = wfu_get_file_rec($dec_file, true);
	if ( $filedata == null ) return;

	if ( isset($_POST['submit']) ) {
		if ( $_POST['submit'] == "Update" ) {
			//check for errors
			$is_error = false;
			foreach ( $filedata->userdata as $userdata ) {
				if ( !isset($_POST['wfu_filedetails_userdata_'.$userdata->propkey]) ) {
					$is_error = true;
					break;
				}
			}
			if ( !$is_error ) {
				$now_date = date('Y-m-d H:i:s');
				$userdata_count = 0;
				foreach ( $filedata->userdata as $userdata ) {
					$userdata_count ++;
					//make existing userdata record obsolete
					$wpdb->update($table_name2,
						array( 'date_to' => $now_date ),
						array( 'uploadid' => $userdata->uploadid, 'propkey'  => $userdata->propkey ),
						array( '%s' ),
						array( '%s', '%s' )
					);
					//insert new userdata record
					$wpdb->insert($table_name2,
						array(
							'uploadid' 	=> $userdata->uploadid,
							'property' 	=> $userdata->property,
							'propkey' 	=> $userdata->propkey,
							'propvalue' 	=> $_POST['wfu_filedetails_userdata_'.$userdata->propkey],
							'date_from' 	=> $now_date,
							'date_to' 	=> 0
						),
						array(
							'%s',
							'%s',
							'%d',
							'%s',
							'%s',
							'%s'
						)
					);
				}
				if ( $userdata_count > 0 ) wfu_log_action('modify:'.$now_date, $dec_file, $user->ID, '', 0, '', null);
			}
		}
	}
	return true;
}

?>
