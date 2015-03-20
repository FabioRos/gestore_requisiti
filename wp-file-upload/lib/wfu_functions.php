<?php

//********************* String Functions ***************************************************************************************************

function wfu_upload_plugin_clean($label) {
	/**
	 * Regular expressions to change some characters.
	 */

	$search = array ('@[eeeeEE]@i','@[aaaAA]@i','@[iiII]@i','@[uuuUU]@i','@[ooOO]@i',
	'@[c]@i','@[^a-zA-Z0-9._]@');	 
	$replace = array ('e','a','i','u','o','c','-');
	$label =  preg_replace($search, $replace, $label);
	$label = strtolower($label); // Convert in lower case
//	$label =  preg_replace('/[\\/\?%\*:\|\"<>]/', '-', $label);
	return $label;
}

function wfu_upload_plugin_wildcard_to_preg($pattern) {
	return '/^' . str_replace(array('\*', '\?', '\[', '\]'), array('.*', '.', '[', ']+'), preg_quote($pattern)) . '$/is';
}

function wfu_upload_plugin_wildcard_match($pattern, $str) {
	$pattern = wfu_upload_plugin_wildcard_to_preg($pattern);
	return preg_match($pattern, $str);
}

function wfu_plugin_encode_string($string) {
	$array = unpack('H*', $string);
	return $array[1];

	$array = unpack('C*', $string);
	$new_string = "";	
	for ($i = 1; $i <= count($array); $i ++) {
		$new_string .= sprintf("%02X", $array[$i]);
	}
	return $new_string;
}

function wfu_plugin_decode_string($string) {
	return pack('H*', $string);

	$new_string = "";	
	for ($i = 0; $i < strlen($string); $i += 2 ) {
		$new_string .= sprintf("%c", hexdec(substr($string, $i ,2)));
	}
	return $new_string;
}

function wfu_create_random_string($len) {
	$base = 'ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz123456789';
	$max = strlen($base) - 1;
	$activatecode = '';
	mt_srand((double)microtime()*1000000);
	while (strlen($activatecode) < $len)
		$activatecode .= $base{mt_rand(0, $max)};
	return $activatecode;
}

function wfu_join_strings($delimeter) {
	$arr = func_get_args();
	unset($arr[0]);
	foreach ($arr as $key => $item)
		if ( $item == "" ) unset($arr[$key]);
	return join($delimeter, $arr);
}

function wfu_create_string($size) {
	$piece = str_repeat("0", 1024);
	$str = "";
	$reps = $size / 1024;
	$rem = $size - 1024 * $reps;
	for ( $i = 0; $i < $reps; $i++ ) $str .= $piece;
	$str .= substr($piece, 0, $rem);
	return $str;
}

//********************* Array Functions ****************************************************************************************************

function wfu_encode_array_to_string($arr) {
	$arr_str = json_encode($arr);
	$arr_str = wfu_plugin_encode_string($arr_str);
	return $arr_str;
}

function wfu_decode_array_from_string($arr_str) {
	$arr_str = wfu_plugin_decode_string($arr_str);
	$arr = json_decode($arr_str, true);
	return $arr;
}

function wfu_plugin_parse_array($source) {
	$keys = array_keys($source);
	$new_arr = array();
	for ($i = 0; $i < count($keys); $i ++) 
		$new_arr[$keys[$i]] = wp_specialchars_decode($source[$keys[$i]]);
	return $new_arr;
}

function wfu_array_remove_nulls(&$arr) {
	foreach ( $arr as $key => $arri )
		if ( $arri == null )
			array_splice($arr, $key, 1);
}

function wfu_safe_array($arr) {
	return array_map("htmlspecialchars", $arr);
}

function wfu_sanitize($var) {
	$typ = gettype($var);
	if ( $typ == "boolean" || $typ == "integer" || $typ == "double" || $typ == "resource" || $typ == "NULL" )
		return $var;
	elseif ( $typ == "string" )
		return htmlspecialchars($var);
	elseif ( $typ == "array" || $typ == "object" ) {
		foreach ( $var as &$item ) $item = wfu_sanitize($item);
		return $var;
	}
	else
		return $typ;
}

function wfu_shortcode_string_to_array($shortcode) {
	function _wfu_preg_replace_callback_alt($contents, $token) {
		$in_block = false;
		$prev_pos = 0;
		$new_contents = '';
		$ret['items'] = array();
		$ret['tokens'] = array();
		$ii = 0;
		while ( ($pos = strpos($contents, '"', $prev_pos)) !== false ) {
			if ( !$in_block ) {
				$new_contents .= substr($contents, $prev_pos, $pos - $prev_pos + 1);
				$in_block = true;
			}
			else {
				$ret['items'][$ii] = substr($contents, $prev_pos, $pos - $prev_pos);
				$ret['tokens'][$ii] = $token.sprintf('%03d', $ii);
				$new_contents .= $token.sprintf('%03d', $ii).'"';
				$ii ++;
				$in_block = false;
			}
			$prev_pos = $pos + 1;
		}
		if ( $in_block ) {
			$ret['items'][$ii] = substr($contents, $prev_pos);
			$ret['tokens'][$ii] = $token.sprintf('%03d', $ii);
			$new_contents .= $token.sprintf('%03d', $ii).'"';
		}
		else
			$new_contents .= substr($contents, $prev_pos);
		$ret['contents'] = $new_contents;
		return $ret;
	}

	$i = 0;
	$m1 = array();
	$m2 = array();
	//for some reason preg_replace_callback does not work in all cases, so it has been replaced by a similar custom inline routine
//	$mm = preg_replace_callback('/"([^"]*)"/', function ($matches) use(&$i, &$m1, &$m2) {array_push($m1, $matches[1]); array_push($m2, "attr".$i); return "attr".$i++;}, $shortcode);
	$ret = _wfu_preg_replace_callback_alt($shortcode, "attr");
	$mm = $ret['contents'];
	$m1 = $ret['items'];
	$m2 = $ret['tokens'];
	$arr = explode(" ", $mm);
	$attrs = array();
	foreach ( $arr as $attr ) {
		if ( trim($attr) != "" ) {
			$attr_arr = explode("=", $attr, 2);
			$key = "";
			if ( count($attr_arr) > 0 ) $key = $attr_arr[0];
			$val = "";
			if ( count($attr_arr) > 1 ) $val = $attr_arr[1];
			if ( trim($key) != "" ) $attrs[trim($key)] = str_replace('"', '', $val);
		}
	}
	$attrs2 = str_replace($m2, $m1, $attrs);
	return $attrs2;
}

function wfu_array_sort($array, $on, $order=SORT_ASC) {
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

//********************* Plugin Options Functions *******************************************************************************************

function wfu_encode_plugin_options($plugin_options) {
	$encoded_options = 'version='.$plugin_options['version'].';';
	$encoded_options .= 'shortcode='.wfu_plugin_encode_string($plugin_options['shortcode']).';';
	$encoded_options .= 'hashfiles='.$plugin_options['hashfiles'].';';
	$encoded_options .= 'basedir='.wfu_plugin_encode_string($plugin_options['basedir']);
	return $encoded_options;
}

function wfu_decode_plugin_options($encoded_options) {
	$decoded_array = explode(';', $encoded_options);
	$plugin_options = array();
	foreach ($decoded_array as $decoded_item) {
		list($item_key, $item_value) = explode("=", $decoded_item, 2);
		if ( $item_key == 'shortcode' || $item_key == 'basedir' )
			$plugin_options[$item_key] = wfu_plugin_decode_string($item_value);
		else
			$plugin_options[$item_key] = $item_value;
		
	}
	return $plugin_options;
}

function wfu_get_plugin_version() {
	$plugin_data = get_plugin_data(WPFILEUPLOAD_PLUGINFILE);
	return $plugin_data['Version'];
}

function wfu_get_latest_version() {
	include( ABSPATH . WPINC . '/version.php' );
	$plugin_name = plugin_basename( WPFILEUPLOAD_PLUGINFILE );
	$plugins = array( $plugin_name => get_plugin_data(WPFILEUPLOAD_PLUGINFILE, false, false) );
	$plugins[$plugin_name]['Version'] = '1.0.0';
	$active = array( $plugin_name );
	$to_send = (object) compact('plugins', 'active');
	$options = array(
		'timeout' 	=> 30,
		'body' => array( 'plugins' => serialize( $to_send ) ),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);
	$url = 'http://api.wordpress.org/plugins/update-check/1.0/';
	$raw_response = wp_remote_post( $url, $options );
	if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ) return '';
	$response = unserialize( wp_remote_retrieve_body( $raw_response ) );
	if ( false === $response ) return '';
	if ( !is_array($response) ) return '';
	if ( count($response) < 1 ) return '';
	if ( !isset($response[$plugin_name]->new_version) ) return '';
	return $response[$plugin_name]->new_version;
}

function wfu_compare_versions($current, $latest) {
	$ret['status'] = true;
	$ret['custom'] = false;
	$ret['result'] = 'equal';
	$res = preg_match('/^([0-9]*)\.([0-9]*)\.([0-9]*)(.*)/', $current, $cur_data);
	if ( !$res || count($cur_data) < 5 )
		return array( 'status' => false, 'custom' => false, 'result' => 'current version invalid' );
	if ( $cur_data[1] == '' || $cur_data[2] == '' || $cur_data[3] == '' )
		return array( 'status' => false, 'custom' => false, 'result' => 'current version invalid' );
	$custom = ( $cur_data[4] != '' );
	$res = preg_match('/^([0-9]*)\.([0-9]*)\.([0-9]*)/', $latest, $lat_data);
	if ( !$res || count($lat_data) < 4 )
		return array( 'status' => false, 'custom' => $custom, 'result' => 'latest version invalid' );
	if ( $lat_data[1] == '' || $lat_data[2] == '' || $lat_data[3] == '' )
		return array( 'status' => false, 'custom' => $custom, 'result' => 'latest version invalid' );
	if ( intval($cur_data[1]) < intval($lat_data[1]) )
		return array( 'status' => true, 'custom' => $custom, 'result' => 'lower' );
	elseif ( intval($cur_data[1]) > intval($lat_data[1]) )
		return array( 'status' => false, 'custom' => $custom, 'result' => 'current version invalid' );
	if ( intval($cur_data[2]) < intval($lat_data[2]) )
		return array( 'status' => true, 'custom' => $custom, 'result' => 'lower' );
	elseif ( intval($cur_data[2]) > intval($lat_data[2]) )
		return array( 'status' => false, 'custom' => $custom, 'result' => 'current version invalid' );
	if ( intval($cur_data[3]) < intval($lat_data[3]) )
		return array( 'status' => true, 'custom' => $custom, 'result' => 'lower' );
	elseif ( intval($cur_data[3]) > intval($lat_data[3]) )
		return array( 'status' => false, 'custom' => $custom, 'result' => 'current version invalid' );
	return array( 'status' => true, 'custom' => $custom, 'result' => 'equal' );	
}

function wfu_debug_log($message) {
	$logpath = WP_CONTENT_DIR.'/debug_log.txt';
	file_put_contents($logpath, $message, FILE_APPEND);
}

//********************* Directory Functions ************************************************************************************************

function wfu_upload_plugin_full_path( $params ) {
	$path = $params["uploadpath"];
	if ( $params["accessmethod"]=='ftp' && $params["ftpinfo"] != '' && $params["useftpdomain"] == "true" ) {
		$ftpdata_flat =  str_replace(array('\:', '\@'), array('\_', '\_'), $params["ftpinfo"]);
		$pos1 = strpos($ftpdata_flat, ":");
		$pos2 = strpos($ftpdata_flat, "@");
		if ( $pos1 && $pos2 && $pos2 > $pos1 ) {
			$ftp_username = substr($params["ftpinfo"], 0, $pos1);
			$ftp_password = substr($params["ftpinfo"], $pos1 + 1, $pos2 - $pos1 - 1);
			$ftp_host = substr($params["ftpinfo"], $pos2 + 1);
			$ftp_username = str_replace('@', '%40', $ftp_username);   //if username contains @ character then convert it to %40
			$ftp_password = str_replace('@', '%40', $ftp_password);   //if password contains @ character then convert it to %40
			$start_folder = 'ftp://'.$ftp_username.':'.$ftp_password."@".$ftp_host.'/';
		}
		else $start_folder = 'ftp://'.$params["ftpinfo"].'/';
	}
	else $start_folder = WP_CONTENT_DIR.'/';
	if ($path) {
		if ( $path == ".." || substr($path, 0, 3) == "../" ) {
			$start_folder = ABSPATH;
			$path = substr($path, 2, strlen($path) - 2);
		}
		if ( substr($path, 0, 1) == "/" ) $path = substr($path, 1, strlen($path) - 1);
		if ( substr($path, -1, 1) == "/" ) $path = substr($path, 0, strlen($path) - 1);
		$full_upload_path = $start_folder;
		if ( $path != "" ) $full_upload_path .= $path.'/';
	}
	else {
		$full_upload_path = $start_folder;
	}
	return $full_upload_path;
}

function wfu_upload_plugin_directory( $path ) {
	$dirparts = explode("/", $path);
	return $dirparts[count($dirparts) - 1];
}

//function to extract sort information from path, which is stored as [[-sort]] inside the path
function wfu_extract_sortdata_from_path($path) {
	$pos1 = strpos($path, '[[');
	$pos2 = strpos($path, ']]');
	$ret['path'] = $path;
	$ret['sort'] = "";
	if ( $pos1 !== false && $pos2 !== false )
		if ( $pos2 > $pos1 ) {
			$ret['sort'] = substr($path, $pos1 + 2, $pos2 - $pos1 - 2);
			$ret['path'] = str_replace('[['.$ret['sort'].']]', '', $path);
		}
	return $ret;
}

//extract sort information from path and return the flatten path
function wfu_flatten_path($path) {
	$ret = wfu_extract_sortdata_from_path($path);
	return $ret['path'];
}

function wfu_delTree($dir) {
	$files = array_diff(scandir($dir), array('.','..'));
	foreach ($files as $file) {
		is_dir("$dir/$file") ? wfu_delTree("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}

function wfu_parse_folderlist($subfoldertree) {
	$ret['path'] = array();
	$ret['label'] = array();
	$ret['level'] = array();
	$ret['default'] = array();

	$subfolders = explode(",", $subfoldertree);
	if ( count($subfolders) == 0 ) return $ret;
	if ( count($subfolders) == 1 && trim($subfolders[0]) == "" ) return $ret;
	$dir_levels = array ( "root" );
	$prev_level = 0;
	$level0_count = 0;
	$default = -1;
	foreach ($subfolders as $subfolder) {
		$subfolder = trim($subfolder);			
		$star_count = 0;
		$start_spaces = "";
		$is_default = false;
		//check for folder level
		while ( $star_count < strlen($subfolder) ) {
			if ( substr($subfolder, $star_count, 1) == "*" ) {
				$star_count ++;
				$start_spaces .= "&nbsp;&nbsp;&nbsp;";
			}
			else break;
		}
		if ( $star_count - $prev_level <= 1 && ( $star_count > 0 || $level0_count == 0 ) ) {
			$subfolder = substr($subfolder, $star_count, strlen($subfolder) - $star_count);
			// check for default value
			if ( substr($subfolder, 0, 1) == '&' ) {
				$subfolder = substr($subfolder, 1);
				$is_default = true;
			}
			//split item in folder path and folder name
			$subfolder_items = explode('/', $subfolder);
			if ( $subfolder_items[1] != "" ) {
				$subfolder_dir = $subfolder_items[0];
				$subfolder_label = $subfolder_items[1];
			}
			else {
				$subfolder_dir = $subfolder;
				$subfolder_label = $subfolder;
			}
			if ( $subfolder_dir != "" ) {
				// set is_default flag to true only for the first default item
				if ( $is_default && $default == -1 ) $default = count($ret['path']);
				else $is_default = false;
				// set flag that root folder has been included (so that it is not included it again)
				if ( $star_count == 0 ) $level0_count = 1;
				if ( count($dir_levels) > $star_count ) $dir_levels[$star_count] = $subfolder_dir;
				else array_push($dir_levels, $subfolder_dir);
				$subfolder_path = "";
				for ( $i_count = 1; $i_count <= $star_count; $i_count++) {
					$subfolder_path .= $dir_levels[$i_count].'/';
				}
				array_push($ret['path'], $subfolder_path);
				array_push($ret['label'], $subfolder_label);
				array_push($ret['level'], $star_count);
				array_push($ret['default'], $is_default);
				$prev_level = $star_count;
			}
		}
	}

	return $ret;
}

//********************* User Functions *****************************************************************************************************

function wfu_get_user_role($user, $param_roles) {
	if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
		/* Go through the array of the roles of the current user */
		foreach ( $user->roles as $user_role ) {
			$user_role = strtolower($user_role);
			/* If one role of the current user matches to the roles allowed to upload */
			if ( in_array($user_role, $param_roles) || $user_role == 'administrator' ) {
				/*  We affect this role to current user */
				$result_role = $user_role;
				break;
			}
			else {
				/* We affect the 'visitor' role to current user */
				$result_role = 'visitor';
			}
		}
	}
	else {
		$result_role = 'visitor';
	}
	return $result_role;		
}

//*********************** DB Functions *****************************************************************************************************

//log action to database
function wfu_log_action($action, $filepath, $userid, $uploadid, $pageid, $sid, $userdata) {
	global $wpdb;
	$table_name1 = $wpdb->prefix . "wfu_log";
	$table_name2 = $wpdb->prefix . "wfu_userdata";
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	if ( !file_exists($filepath) && substr($action, 0, 5) != 'other' ) return;
	$parts = pathinfo($filepath);
	$relativepath = str_replace(ABSPATH, '', $filepath);
	if ( substr($relativepath, 0, 1) != '/' ) $relativepath = '/'.$relativepath;
	
	$retid = 0;
	if ( $action == 'upload' ) {
		// calculate and store file hash if this setting is enabled from Settings
		$filehash = '';
		if ( $plugin_options['hashfiles'] == '1' ) $filehash = md5_file($filepath);
		// calculate file size
		$filesize = filesize($filepath);
		// first make obsolete records having the same file path because the old file has been replaced
		$wpdb->update($table_name1,
			array( 'date_to' => date('Y-m-d H:i:s') ),
			array( 'filepath' => $relativepath ),
			array( '%s'),
			array( '%s')
		);
		// attempt to create new log record
		$now_date = date('Y-m-d H:i:s');
		if ( $wpdb->insert($table_name1,
			array(
				'userid' 	=> $userid,
				'uploaduserid' 	=> $userid,
				'filepath' 	=> $relativepath,
				'filehash' 	=> $filehash,
				'filesize' 	=> $filesize,
				'uploadid' 	=> $uploadid,
				'pageid' 	=> $pageid,
				'sid' 		=> $sid,
				'date_from' 	=> $now_date,
				'date_to' 	=> 0,
				'action' 	=> 'upload'
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s',
				'%d',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s'
			)) !== false ) {
			$retid = $wpdb->insert_id;
			// if new log record has been created, also create user data records
			if ( $userdata != null && $uploadid != '' ) {
				foreach ( $userdata as $userdata_key => $userdata_field ) {
					$existing = $wpdb->get_row('SELECT * FROM '.$table_name2.' WHERE uploadid = \''.$uploadid.'\' AND property = \''.$userdata_key.'\'');
					if ($existing == null)
						$wpdb->insert($table_name2,
							array(
								'uploadid' 	=> $uploadid,
								'property' 	=> $userdata_field['label'],
								'propkey' 	=> $userdata_key,
								'propvalue' 	=> $userdata_field['value'],
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
			}
		}
	}
	//for rename action the $action variable is of the form: $action = 'rename:'.$newfilepath; in order to pass the new file path
	elseif ( substr($action, 0, 6) == 'rename' ) {
		//get new filepath
		$newfilepath = substr($action, 7);
		$relativepath = str_replace(ABSPATH, '', $newfilepath);
		if ( substr($relativepath, 0, 1) != '/' ) $relativepath = '/'.$relativepath;
		//get stored file data from database without user data
		$filerec = wfu_get_file_rec($filepath, false);
		//log action only if there are previous stored file data
		if ( $filerec != null ) {
			$now_date = date('Y-m-d H:i:s');
			//make previous record obsolete
			$wpdb->update($table_name1,
				array( 'date_to' => $now_date ),
				array( 'idlog' => $filerec->idlog ),
				array( '%s' ),
				array( '%d' )
			);
			//insert new rename record
			if ( $wpdb->insert($table_name1,
				array(
					'userid' 	=> $userid,
					'uploaduserid' 	=> $filerec->uploaduserid,
					'filepath' 	=> $relativepath,
					'filehash' 	=> $filerec->filehash,
					'filesize' 	=> $filerec->filesize,
					'uploadid' 	=> $filerec->uploadid,
					'pageid' 	=> $filerec->pageid,
					'sid' 		=> $filerec->sid,
					'date_from' 	=> $now_date,
					'date_to' 	=> 0,
					'action' 	=> 'rename',
					'linkedto' 	=> $filerec->idlog
				),
				array( '%d','%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%d' ) ) !== false )
				$retid = $wpdb->insert_id;
		}
	}
	elseif ( $action == 'delete' ) {
		//get stored file data from database without user data
		$filerec = wfu_get_file_rec($filepath, false);
		//log action only if there are previous stored file data
		if ( $filerec != null ) {
			$now_date = date('Y-m-d H:i:s');
			//make previous record obsolete
			$wpdb->update($table_name1,
				array( 'date_to' => $now_date ),
				array( 'idlog' => $filerec->idlog ),
				array( '%s' ),
				array( '%d' )
			);
			//insert new delete record
			if ( $wpdb->insert($table_name1,
				array(
					'userid' 	=> $userid,
					'uploaduserid' 	=> $filerec->uploaduserid,
					'filepath' 	=> $filerec->filepath,
					'filehash' 	=> $filerec->filehash,
					'filesize' 	=> $filerec->filesize,
					'uploadid' 	=> $filerec->uploadid,
					'pageid' 	=> $filerec->pageid,
					'sid' 		=> $filerec->sid,
					'date_from' 	=> $now_date,
					'date_to' 	=> $now_date,
					'action' 	=> 'delete',
					'linkedto' 	=> $filerec->idlog
				),
				array( '%d','%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%d' )) != false )
				$retid = $wpdb->insert_id;
		}
	}
	elseif ( $action == 'download' ) {
		//get stored file data from database without user data
		$filerec = wfu_get_file_rec($filepath, false);
		//log action only if there are previous stored file data
		if ( $filerec != null ) {
			$now_date = date('Y-m-d H:i:s');
			//make previous record obsolete
			$wpdb->update($table_name1,
				array( 'date_to' => $now_date ),
				array( 'idlog' => $filerec->idlog ),
				array( '%s' ),
				array( '%d' )
			);
			//insert new download record
			if ( $wpdb->insert($table_name1,
				array(
					'userid' 	=> $userid,
					'uploaduserid' 	=> $filerec->uploaduserid,
					'filepath' 	=> $filerec->filepath,
					'filehash' 	=> $filerec->filehash,
					'filesize' 	=> $filerec->filesize,
					'uploadid' 	=> $filerec->uploadid,
					'pageid' 	=> $filerec->pageid,
					'sid' 		=> $filerec->sid,
					'date_from' 	=> $now_date,
					'date_to' 	=> 0,
					'action' 	=> 'download',
					'linkedto' 	=> $filerec->idlog
				),
				array( '%d','%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%d' )) != false )
				$retid = $wpdb->insert_id;
		}
	}
	//for modify action the $action variable is of the form: $action = 'modify:'.$now_date; in order to pass the exact modify date
	elseif ( substr($action, 0, 6) == 'modify' ) {
		$now_date = substr($action, 7);
		//get stored file data from database without user data
		$filerec = wfu_get_file_rec($filepath, false);
		//log action only if there are previous stored file data
		if ( $filerec != null ) {
			//make previous record obsolete
			$wpdb->update($table_name1,
				array( 'date_to' => $now_date ),
				array( 'idlog' => $filerec->idlog ),
				array( '%s' ),
				array( '%d' )
			);
			//insert new modify record
			if ( $wpdb->insert($table_name1,
				array(
					'userid' 	=> $userid,
					'uploaduserid' 	=> $filerec->uploaduserid,
					'filepath' 	=> $filerec->filepath,
					'filehash' 	=> $filerec->filehash,
					'filesize' 	=> $filerec->filesize,
					'uploadid' 	=> $filerec->uploadid,
					'pageid' 	=> $filerec->pageid,
					'sid' 		=> $filerec->sid,
					'date_from' 	=> $now_date,
					'date_to' 	=> 0,
					'action' 	=> 'modify',
					'linkedto' 	=> $filerec->idlog
				),
				array( '%d','%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%d' )) != false )
				$retid = $wpdb->insert_id;
		}
	}
	elseif ( substr($action, 0, 5) == 'other' ) {
		$info = substr($action, 6);
		$now_date = date('Y-m-d H:i:s');
		//insert new download record
		if ( $wpdb->insert($table_name1,
			array(
				'userid' 	=> $userid,
				'uploaduserid' 	=> -1,
				'filepath' 	=> $info,
				'filehash' 	=> '',
				'filesize' 	=> 0,
				'uploadid' 	=> '',
				'pageid' 	=> 0,
				'sid' 		=> '',
				'date_from' 	=> $now_date,
				'date_to' 	=> $now_date,
				'action' 	=> 'other',
				'linkedto' 	=> -1
			),
			array( '%d','%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%d' )) != false )
			$retid = $wpdb->insert_id;
	}
	return $retid;
}

//revert previously saved action
function wfu_revert_log_action($idlog) {
	global $wpdb;
	$table_name1 = $wpdb->prefix . "wfu_log";

	$filerec = $wpdb->get_row('SELECT * FROM '.$table_name1.' WHERE idlog = '.$idlog);
	if ( $filerec != null ) {
		$prevfilerec = $wpdb->get_row('SELECT * FROM '.$table_name1.' WHERE idlog = '.$filerec->idlog);
		if ( $prevfilerec != null ) {
			$wpdb->update($table_name1,
				array( 'date_to' => date('Y-m-d H:i:s') ),
				array( 'idlog' => $filerec->idlog ),
				array( '%s' ),
				array( '%d' )
			);
			$wpdb->update($table_name1,
				array( 'date_to' => 0 ),
				array( 'idlog' => $prevfilerec->idlog ),
				array( '%s' ),
				array( '%d' )
			);
		}
	}
}

//find user by its id and return a non-empty username
function wfu_get_username_by_id($id) {
	$user = get_user_by('id', $id);
	if ( $user == false && $id > 0 ) $username = 'unknown';
	elseif ( $user == false ) $username = 'guest';
	else $username = $user->user_login;
	return $username;
}

//get the most current database record for file $filepath and also include any userdata if $include_userdata is true
function wfu_get_file_rec($filepath, $include_userdata) {
	global $wpdb;
	$table_name1 = $wpdb->prefix . "wfu_log";
	$table_name2 = $wpdb->prefix . "wfu_userdata";
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	if ( !file_exists($filepath) ) return null;

	$relativepath = str_replace(ABSPATH, '', $filepath);
	if ( substr($relativepath, 0, 1) != '/' ) $relativepath = '/'.$relativepath;
	//if file hash is enabled, then search file based on its path and hash, otherwise find file based on its path and size
	if ( $plugin_options['hashfiles'] == '1' ) {
		$filehash = md5_file($filepath);
		$filerec = $wpdb->get_row('SELECT * FROM '.$table_name1.' WHERE filepath = \''.$relativepath.'\' AND filehash = \''.$filehash.'\' AND date_to = 0 ORDER BY date_from DESC');
	}
	else {
		$stat = stat($filepath);
		$filerec = $wpdb->get_row('SELECT * FROM '.$table_name1.' WHERE filepath = \''.$relativepath.'\' AND filesize = '.$stat['size'].' AND date_to = 0 ORDER BY date_from DESC');
	}
	//get user data
	if ( $filerec != null && $include_userdata ) {
		$filerec->userdata = null;
		if ( $filerec->uploadid != '' ) {
			$filerec->userdata = $wpdb->get_results('SELECT * FROM '.$table_name2.' WHERE uploadid = \''.$filerec->uploadid.'\' AND date_to = 0');
		}
	}
	return $filerec;
}

//reassign file hashes for all valid files in the database
function wfu_reassign_hashes() {
	global $wpdb;
	$table_name1 = $wpdb->prefix . "wfu_log";
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	if ( $plugin_options['hashfiles'] == '1' ) {
		$filerecs = $wpdb->get_results('SELECT * FROM '.$table_name1.' WHERE filehash = \'\' AND date_to = 0');
		foreach( $filerecs as $filerec ) {
			//calculate full file path
			$filepath = ABSPATH;
			if ( substr($filepath, -1) == '/' ) $filepath = substr($filepath, 0, -1);
			$filepath .= $filerec->filepath;
			if ( file_exists($filepath) ) {
				$filehash = md5_file($filepath);
				$wpdb->update($table_name1,
					array( 'filehash' => $filehash ),
					array( 'idlog' => $filerec->idlog ),
					array( '%s' ),
					array( '%d' )
				);
			}
		}
	}
}

//update database to reflect the current status of files
function wfu_sync_database() {
	global $wpdb;
	$table_name1 = $wpdb->prefix . "wfu_log";
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	$filerecs = $wpdb->get_results('SELECT * FROM '.$table_name1.' WHERE action <> \'other\' AND date_to = 0');
	$obsolete_count = 0;
	foreach( $filerecs as $filerec ) {
		$obsolete = true;
		//calculate full file path
		$filepath = ABSPATH;
		if ( substr($filepath, -1) == '/' ) $filepath = substr($filepath, 0, -1);
		$filepath .= $filerec->filepath;
		if ( file_exists($filepath) ) {
			if ( $plugin_options['hashfiles'] == '1' ) {
				$filehash = md5_file($filepath);
				if ( $filehash == $filerec->filehash ) $obsolete = false;
			}
			else {
				$filesize = filesize($filepath);
				if ( $filesize == $filerec->filesize ) $obsolete = false;
			}
		}
		if ( $obsolete ) {
			$now_date = date('Y-m-d H:i:s');
			//make previous record obsolete
			$wpdb->update($table_name1,
				array( 'date_to' => $now_date ),
				array( 'idlog' => $filerec->idlog ),
				array( '%s' ),
				array( '%d' )
			);
			$obsolete_count ++;
		}
	}
	return $obsolete_count;
}

//********************* Shortcode Options Functions ****************************************************************************************

function wfu_generate_current_params_index($shortcode_id, $user_login) {
	global $post;
	$cur_index_str = '||'.$post->ID.'||'.$shortcode_id.'||'.$user_login;
	$cur_index_str_search = '\|\|'.$post->ID.'\|\|'.$shortcode_id.'\|\|'.$user_login;
	$index_str = get_option('wfu_params_index');
	$index = explode("&&", $index_str);
	foreach ($index as $key => $value) if ($value == "") unset($index[$key]);
	$index_match = preg_grep("/".$cur_index_str_search."$/", $index);
	if ( count($index_match) == 1 )
		foreach ( $index_match as $key => $value )
			if ( $value == "" ) unset($index_match[$key]);
	if ( count($index_match) <= 0 ) {
		$cur_index_rand = wfu_create_random_string(16);
		array_push($index, $cur_index_rand.$cur_index_str);
	}
	else {
		reset($index_match);
		$cur_index_rand = substr(current($index_match), 0, 16);
		if ( count($index_match) > 1 ) {
			$index_match_keys = array_keys($index_match);
			for ($i = 1; $i < count($index_match); $i++) {
				$ii = $index_match_keys[$i];
				unset($index[array_search($index_match[$ii], $index, true)]);
			}
		}
	}
	if ( count($index_match) != 1 ) {
		$index_str = implode("&&", $index);
		update_option('wfu_params_index', $index_str);
	}
	return $cur_index_rand;
}

function wfu_get_params_fields_from_index($params_index) {
	$fields = array();
	$index_str = get_option('wfu_params_index');
	$index = explode("&&", $index_str);
	$index_match = preg_grep("/^".$params_index."/", $index);
	if ( count($index_match) == 1 )
		foreach ( $index_match as $key => $value )
			if ( $value == "" ) unset($index_match[$key]);
	if ( count($index_match) > 0 ) {
		reset($index_match);
		list($fields['unique_id'], $fields['page_id'], $fields['shortcode_id'], $fields['user_login']) = explode("||", current($index_match));
	}
	return $fields; 
}

function wfu_decode_dimensions($dimensions_str) {
	$components = wfu_component_definitions();
	$dimensions = array();

	foreach ( $components as $comp ) {
		if ( $comp['dimensions'] == null ) $dimensions[$comp['id']] = "";
		else foreach ( $comp['dimensions'] as $dimraw ) {
			list($dim_id, $dim_name) = explode("/", $dimraw);
			$dimensions[$dim_id] = "";
		}
	}
	$dimensions_raw = explode(",", $dimensions_str);
	foreach ( $dimensions_raw as $dimension_str ) {
		$dimension_raw = explode(":", $dimension_str);
		$item = strtolower(trim($dimension_raw[0]));
		foreach ( array_keys($dimensions) as $key ) {
			if ( $item == $key ) $dimensions[$key] = trim($dimension_raw[1]);
		}
	}
	return $dimensions;
}

//********************* Plugin Design Functions ********************************************************************************************

function wfu_add_div() {
	$items_count = func_num_args();
	if ( $items_count == 0 ) return "";
	$items_raw = func_get_args();
	$items = array( );
	foreach ( $items_raw as $item_raw ) {
		if ( is_array($item_raw) ) array_push($items, $item_raw);
	}
	$items_count = count($items);
	if ( $items_count == 0 ) return "";
	$div = "";
	$div .= "\n\t".'<div class="file_div_clean">';  
	$div .= "\n\t\t".'<table class="file_table_clean">';
	$div .= "\n\t\t\t".'<tbody>';
	$div .= "\n\t\t\t\t".'<tr>';  
	for ( $i = 0; $i < $items_count; $i++ ) {
		$div .= "\n\t\t\t\t\t".'<td class="file_td_clean"';  
		if ( $i < $items_count - 1 ) $div .= ' style="padding: 0 4px 0 0"';
		$div .= '>';
		$div .= "\n\t\t\t\t\t\t".'<div id="'.$items[$i]["title"].'" class="file_div_clean"';  
		if ( $items[$i]["hidden"] ) $div .= ' style="display: none"';
		$div .= '>';
		$item_lines_count = count($items[$i]) - 2;
		for ( $k = 1; $k <= $item_lines_count; $k++ ) {
			if ( $items[$i]["line".$k] != "" ) $div .= "\n\t\t\t\t\t\t\t".$items[$i]["line".$k];
		}
		$div .= "\n\t\t\t\t\t\t\t".'<div class="file_space_clean" />';  
		$div .= "\n\t\t\t\t\t\t".'</div>';  
		$div .= "\n\t\t\t\t\t".'</td>';  
	}
	$div .= "\n\t\t\t\t".'</tr>';  
	$div .= "\n\t\t\t".'</tbody>';
	$div .= "\n\t\t".'</table>';
	$div .= "\n\t".'</div>';  
	return $div;
}

//********************* Email Functions ****************************************************************************************************

function wfu_send_notification_email($user, $only_filename_list, $target_path_list, $attachment_list, $userdata_fields, $params) {
	if ( 0 == $user->ID ) {
		$user_login = "guest";
		$user_email = "";
	}
	else {
		$user_login = $user->user_login;
		$user_email = $user->user_email;
	}
	$search = array ('/%useremail%/');	 
	$replace = array ($user_email);
	foreach ( $userdata_fields as $userdata_key => $userdata_field ) { 
		$ind = 1 + $userdata_key;
		array_push($search, '/%userdata'.$ind.'%/');  
		array_push($replace, $userdata_field["value"]);
	}   
//	$notifyrecipients =  trim(preg_replace('/%useremail%/', $user_email, $params["notifyrecipients"]));
	$notifyrecipients =  preg_replace($search, $replace, $params["notifyrecipients"]);
	$search = array ('/%n%/');	 
	$replace = array ("\n");
	$notifyheaders =  preg_replace($search, $replace, $params["notifyheaders"]);
	$search = array ('/%username%/', '/%useremail%/', '/%filename%/', '/%filepath%/', '/%n%/');	 
	$replace = array ($user_login, ( $user_email == "" ? "no email" : $user_email ), $only_filename_list, $target_path_list, "\n");
	foreach ( $userdata_fields as $userdata_key => $userdata_field ) { 
		$ind = 1 + $userdata_key;
		array_push($search, '/%userdata'.$ind.'%/');  
		array_push($replace, $userdata_field["value"]);
	}   
	$notifysubject = preg_replace($search, $replace, $params["notifysubject"]);
	$notifymessage = preg_replace($search, $replace, $params["notifymessage"]);
	if ( $params["attachfile"] == "true" ) {
		$attachments = explode(",", $attachment_list);
		$notify_sent = wp_mail($notifyrecipients, $notifysubject, $notifymessage, $notifyheaders, $attachments); 
	}
	else {
		$notify_sent = wp_mail($notifyrecipients, $notifysubject, $notifymessage, $notifyheaders); 
	}
	return ( $notify_sent ? "" : WFU_WARNING_NOTIFY_NOTSENT_UNKNOWNERROR );
}

//********************* Media Functions ****************************************************************************************************

// function wfu_process_media_insert contribution from Aaron Olin
function wfu_process_media_insert($file_path, $page_id){   
	$filetype = wp_check_filetype( basename( $file_path ), null );

	$attachment = array(
		'post_mime_type' => $filetype['type'],
		'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_path ) ),
		'post_content'   => '',
		'post_status'    => 'inherit'
	);

	$attach_id = wp_insert_attachment( $attachment, $file_path, $page_id ); 
	
	// If file is an image, process the default thumbnails for previews
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
	$update_attach = wp_update_attachment_metadata( $attach_id, $attach_data );

	return $attach_id;	
}

?>
