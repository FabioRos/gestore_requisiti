<?php

function wfu_process_files($params, $method) {
	$sid = $params["uploadid"];
	$user = wp_get_current_user();
	if ( 0 == $user->ID ) {
		$user_login = "guest";
		$user_email = "";
		$is_admin = false;
	}
	else {
		$user_login = $user->user_login;
		$user_email = $user->user_email;
		$is_admin = current_user_can('manage_options');
	}
	$uniqueuploadid = ( isset($_POST['uniqueuploadid_'.$sid]) ? $_POST['uniqueuploadid_'.$sid] : "" );
	// determine if this routine is only for checking the file
	$only_check = ( isset($_POST['only_check']) ? ( $_POST['only_check'] == "1" ) : false );

	$suppress_admin_messages = ( $params["adminmessages"] != "true" || !$is_admin );
	$success_count = 0;
	$warning_count = 0;
	$error_count = 0;
	$default_colors = wfu_prepare_message_colors(WFU_DEFAULTMESSAGECOLORS);
	$notify_only_filename_list = "";
	$notify_target_path_list = "";
	$notify_attachment_list = "";
	$uploadedfile = 'uploadedfile_'.$sid;
	$hiddeninput = 'hiddeninput_'.$sid;
	$unique_id = ( isset($_POST['unique_id']) ? $_POST['unique_id'] : "" );
	$allowed_patterns = explode(",",$params["uploadpatterns"]);
	foreach ($allowed_patterns as $key => $allowed_pattern) {
		$allowed_patterns[$key] = trim($allowed_pattern);
	}
	$userdata_fields = $params["userdata_fields"]; 
	foreach ( $userdata_fields as $userdata_key => $userdata_field ) 
		$userdata_fields[$userdata_key]["value"] = ( isset($_POST[$hiddeninput.'_userdata_'.$userdata_key]) ? $_POST[$hiddeninput.'_userdata_'.$userdata_key] : "" );
	$params_output_array["version"] = "full";
	$params_output_array["general"]['shortcode_id'] = $sid;
	$params_output_array["general"]['unique_id'] = $unique_id;
	$params_output_array["general"]['state'] = 0;
	$params_output_array["general"]['files_count'] = 0;
	$params_output_array["general"]['update_wpfilebase'] = "";
	$params_output_array["general"]['redirect_link'] = ( $params["redirect"] == "true" ? $params["redirectlink"] : "" );
	$params_output_array["general"]['upload_finish_time'] = 0;
	$params_output_array["general"]['message'] = "";
	$params_output_array["general"]['message_type'] = "";
	$params_output_array["general"]['admin_messages']['wpfilebase'] = "";
	$params_output_array["general"]['admin_messages']['notify'] = "";
	$params_output_array["general"]['admin_messages']['redirect'] = "";
	$params_output_array["general"]['admin_messages']['other'] = "";
	$params_output_array["general"]['errors']['wpfilebase'] = "";
	$params_output_array["general"]['errors']['notify'] = "";
	$params_output_array["general"]['errors']['redirect'] = "";
	$params_output_array["general"]['color'] = $default_colors['color'];
	$params_output_array["general"]['bgcolor'] = $default_colors['bgcolor'];
	$params_output_array["general"]['borcolor'] = $default_colors['borcolor'];
	$params_output_array["general"]['notify_only_filename_list'] = "";
	$params_output_array["general"]['notify_target_path_list'] = "";
	$params_output_array["general"]['notify_attachment_list'] = "";
	$params_output_array["general"]['fail_message'] = "";
	$params_output_array["general"]['fail_admin_message'] = "";
	/* safe_output is a minimized version of params_output_array, that is passed as text, in case JSON parse fails
	   its data are separated by semicolon (;) and are the following:
		upload state: the upload state number
		default colors: the default color, bgcolor and borcolor values, separated by comma(,)
		file_count: the number of files processed
		filedata: message type, header, message and admin message of each file, encoded and separated by comma (,) */
	$params_output_array["general"]['safe_output'] = "";

	/* adjust $uploadedfile variable (holding file data) if this is a redirection caused because the browser of the user could not handle AJAX upload */
	if ( isset($_FILES[$uploadedfile.'_redirected']) ) $uploadedfile .= '_redirected';
	/* notify admin if this is a redirection caused because the browser of the user could not handle AJAX upload */
	$params_output_array["general"]['admin_messages']['other'] = $params['adminerrors'];

	if ( isset($_FILES[$uploadedfile]['error']) || $only_check ) {
		$files_count = 1;
		// in case of checking of file, then the $_FILES variable has not been set because no file has been uploaded,
		// so we set it manually in order to allow the routine to continue
		if ( $only_check ) {
			$_FILES[$uploadedfile]['name'] = wfu_plugin_decode_string($_POST[$uploadedfile.'_name']);
			$_FILES[$uploadedfile]['type'] = 'any';
			$_FILES[$uploadedfile]['tmp_name'] = 'any';
			$_FILES[$uploadedfile]['error'] = '';
			$_FILES[$uploadedfile]['size'] = $_POST[$uploadedfile.'_size'];
		}
	}
	else $files_count = 0;
	$params_output_array["general"]['files_count'] = $files_count;
	// index of uploaded file in case of ajax uploads (in ajax uploads only one file is uploaded in every ajax call)
	// the index is used to store any file data in session variables, in case the file is uploaded in two or more passes
	// (like the case were in the first pass it is only checked) 
	$single_file_index = ( isset($_POST[$uploadedfile.'_index']) ? $_POST[$uploadedfile.'_index'] : -1 );

	/* append userdata fields to upload path */
	$search = array ( );	 
	$replace = array ( );
	foreach ( $userdata_fields as $userdata_key => $userdata_field ) { 
		$ind = 1 + $userdata_key;
		array_push($search, '/%userdata'.$ind.'%/');  
		array_push($replace, $userdata_field["value"]);
	}   
	$params["uploadpath"] =  preg_replace($search, $replace, $params["uploadpath"]);

	/* append subfolder name to upload path */
	if ( $params["askforsubfolders"] == "true" && $params['subdir_selection_index'] >= 1 ) {
		if ( substr($params["uploadpath"], -1, 1) == "/" ) $params["uploadpath"] .= $params['subfoldersarray'][$params['subdir_selection_index']];
		else $params["uploadpath"] .= '/'.$params['subfoldersarray'][$params['subdir_selection_index']];
	}

	if ( $files_count == 1 ) {

		foreach ( $_FILES[$uploadedfile] as $key => $prop )
			$fileprops[$key] = $prop;

		$upload_path_ok = false;
		$allowed_file_ok = false;
		$size_file_ok = false;
		$file_output['color'] = $default_colors['color'];
		$file_output['bgcolor'] = $default_colors['bgcolor'];
		$file_output['borcolor'] = $default_colors['borcolor'];
		$file_output['header'] = "";
		$file_output['message'] = "";
		$file_output['message_type'] = "";
		$file_output['admin_messages'] = "";

		// determine if file data have been saved to session variables, due to a previous pass of this file
		$file_map = "filedata_".$unique_id."_".$single_file_index;
	  // retrieve unique id of the file, used in filter actions for identifying each separate file
		$file_unique_id = ( isset($_SESSION[$file_map]) ? $_SESSION[$file_map]['file_unique_id'] : '' );
		$filedata_previously_defined = ( $file_unique_id != '' );
		/* generate unique id for each file for use in filter actions if it has not been previously defined */
		if ( !$filedata_previously_defined )
			$file_unique_id = wfu_create_random_string(20);

		/* Get uploaded file size in Mbytes */
		$upload_file_size = filesize($fileprops['tmp_name']) / 1024 / 1024;
		// correct file size in case of checking of file otherwise $upload_file_size will be zero and the routine will fail
		if ( $only_check ) $upload_file_size = $fileprops['size'] / 1024 / 1024;

		if ( $upload_file_size > 0 ) {

			/* Section to perform filter action wfu_before_file_check before file is checked in order to perform
			   any filename or userdata modifications or reject the upload of the file by setting error_message item
			   of $ret_data array to a non-empty value */
			$filter_error_message = '';
			if ( $file_unique_id != '' && !$filedata_previously_defined ) {
				$target_path = wfu_upload_plugin_full_path($params).$fileprops['name'];
				$changable_data['file_path'] = $target_path;
				$changable_data['user_data'] = $userdata_fields;
				$changable_data['error_message'] = $filter_error_message;
				$additional_data['file_unique_id'] = $file_unique_id;
				$additional_data['file_size'] = filesize($fileprops['tmp_name']);
				// correct file size in case of checking of file
				if ( $only_check ) $additional_data['file_size'] = $fileprops['size'];
				$additional_data['user_id'] = $user->ID;
				$additional_data['page_id'] = $params["pageid"];
				$ret_data = apply_filters('wfu_before_file_check', $changable_data, $additional_data);
				$fileprops['name'] = str_replace(wfu_upload_plugin_full_path($params), '', $ret_data['file_path']);
				$userdata_fields = $ret_data['user_data'];
				$filter_error_message = $ret_data['error_message'];
				// if this is a file check, which means that a second pass of the file will follow, then we do not want to
				// apply the filters again, so we store the changable data to session variables for this specific file
				if ( $only_check ) {
					$_SESSION[$file_map]['file_unique_id'] = $file_unique_id;
					$_SESSION[$file_map]['filename'] = $fileprops['name'];
					$_SESSION[$file_map]['userdata'] = $userdata_fields;
				}
			}
			// if this is a second pass of the file, because a first pass with file checking was done before, then retrieve
			// file data that may have previously changed because of application of filters
			if ( $filedata_previously_defined ) {
				$fileprops['name'] = $_SESSION[$file_map]['filename'];
				$userdata_fields = $_SESSION[$file_map]['userdata'];
			}
			if ( $filter_error_message != '' ) {
				$file_output['message_type'] = "error";
				$file_output['message'] = wfu_join_strings("<br />", $file_output['message'], $filter_error_message);
			}
			else {

				/* Check if upload path exist */
				if ( is_dir( wfu_upload_plugin_full_path($params) ) ) {		
					$upload_path_ok = true;
				}
				/* Attempt to create path if user has selected to do so */ 
				else if ( $params["createpath"] == "true" ) {
					$wfu_create_directory_ret = wfu_create_directory(wfu_upload_plugin_full_path($params), $params["accessmethod"], $params["ftpinfo"]);
					if ( $wfu_create_directory_ret != "" ) {
						$file_output['admin_messages'] = wfu_join_strings("<br />", $file_output['admin_messages'], $wfu_create_directory_ret);
					}
					if ( is_dir( wfu_upload_plugin_full_path($params) ) ) {		
						$upload_path_ok = true;
					}
				}

				/* File name control, reject files with .php extension for security reasons */
				if ( strtolower(substr($fileprops['name'], -4)) != ".php" )
					foreach ($allowed_patterns as $allowed_pattern) {
						if ( wfu_upload_plugin_wildcard_match( $allowed_pattern, $fileprops['name']) ) {
							$allowed_file_ok = true;
							break ;
						}
					}

				/* File size control */
				if ( $upload_file_size <= $params["maxsize"] ) {
					$size_file_ok = true;
				}
	
				if ( !$upload_path_ok or !$allowed_file_ok or !$size_file_ok ) {
					$file_output['message_type'] = "error";
					$file_output['message'] = wfu_join_strings("<br />", $file_output['message'], WFU_ERROR_UPLOAD_FAILED);

					if ( !$upload_path_ok ) $file_output['message'] = wfu_join_strings("<br />", $file_output['message'], WFU_ERROR_DIR_EXIST);
					if ( !$allowed_file_ok ) $file_output['message'] = wfu_join_strings("<br />", $file_output['message'], WFU_ERROR_FILE_ALLOW);
					if ( !$size_file_ok ) $file_output['message'] = wfu_join_strings("<br />", $file_output['message'], WFU_ERROR_FILE_PLUGIN_SIZE);
				}
			}
		}
		else {
			// This block is executed when there is an error
			$upload_error = $fileprops['error'];
			if ( $upload_error == 1 ) {
				$message_text = WFU_ERROR_FILE_PHP_SIZE;
				$file_output['admin_messages'] = wfu_join_strings("<br />", $file_output['admin_messages'], WFU_ERROR_ADMIN_FILE_PHP_SIZE);
			}
			elseif ( $upload_error == 2 ) $message_text = WFU_ERROR_FILE_HTML_SIZE;
			elseif ( $upload_error == 3 ) $message_text = WFU_ERROR_FILE_PARTIAL;
			elseif ( $upload_error == 4 ) $message_text = WFU_ERROR_FILE_NOTHING;
			elseif ( $upload_error == 6 ) $message_text = WFU_ERROR_DIR_NOTEMP;
			elseif ( $upload_error == 7 ) $message_text = WFU_ERROR_FILE_WRITE;
			elseif ( $upload_error == 8 ) $message_text = WFU_ERROR_UPLOAD_STOPPED;
			else {
				$upload_time_limit = ini_get("max_input_time");
				$params_output_array["general"]['upload_finish_time'] = $params["upload_start_time"] + $upload_time_limit * 1000;
				$message_text = WFU_ERROR_FILE_PHP_TIME;
				$file_output['admin_messages'] = wfu_join_strings("<br />", $file_output['admin_messages'], WFU_ERROR_ADMIN_FILE_PHP_TIME);
			}
			$file_output['message_type'] = "error";
			$file_output['message'] = wfu_join_strings("<br />", $file_output['message'], $message_text);
		}

//		if ( $upload_path_ok and $allowed_file_ok and $size_file_ok ) {
		if ( $file_output['message_type'] != "error" ) {

			if ( is_uploaded_file($fileprops['tmp_name']) || $only_check ) {
				if ( $only_check ) $file_copied = true;
				else {
					$file_copied = false;
					$message_processed = false;
					$source_path = $fileprops['tmp_name'];
					$only_filename = wfu_upload_plugin_clean( $fileprops['name'] );
					$target_path = wfu_upload_plugin_full_path($params).$only_filename;

					$search = array ('/%filename%/', '/%filepath%/');	 
					$replace = array ($only_filename, $target_path);
					$success_message =  preg_replace($search, $replace, $params["successmessage"]);

					if ($source_path) {
						$file_exists = file_exists($target_path);
						if ( !$file_exists || $params["dublicatespolicy"] == "" || $params["dublicatespolicy"] == "overwrite" ) {
							//redirect echo in internal buffer to receive and process any unwanted warning messages from wfu_upload_file
							ob_start();
							ob_clean();
							/* Apply wfu_before_file_upload filter right before the upload, in order to allow the user to change the file name.
							   If additional data are required, such as user_id or userdata values, they can be retrieved by implementing the
							   previous filter wfu_before_file_check, corresponding them to the unique file id */
							if ( $file_unique_id != '' ) $target_path = apply_filters('wfu_before_file_upload', $target_path, $file_unique_id);
							//recalculate $only_filename in case it changed with wfu_before_file_upload filter
							$only_filename = basename($target_path);
							//move the uploaded file to its final destination
							$wfu_upload_file_ret = wfu_upload_file($source_path, $target_path, $params["accessmethod"], $params["ftpinfo"]);
							$file_copied = $wfu_upload_file_ret["uploaded"];
							//process warning messages from wfu_upload_file
							$echo_message = ob_get_contents();
							//finish redirecting of echo to internal buffer
							ob_end_clean();
							if ( $echo_message != "" && !$file_copied ) {
								$file_output['message_type'] = "error";
								if ( stristr($echo_message, "warning") && stristr($echo_message, "permission denied") && stristr($echo_message, "unable to move") ) {
									$file_output['message'] = wfu_join_strings("<br />", $file_output['message'], WFU_ERROR_DIR_PERMISSION);
									$file_output['admin_messages'] = wfu_join_strings("<br />", $file_output['admin_messages'], WFU_ERROR_ADMIN_DIR_PERMISSION);
								}
								else { 
									$file_output['message'] = wfu_join_strings("<br />", $file_output['message'], WFU_ERROR_FILE_MOVE);
									$file_output['admin_messages'] = wfu_join_strings("<br />", $file_output['admin_messages'], strip_tags($echo_message));
								}
								$message_processed = true;
							}
							if ( $wfu_upload_file_ret["admin_message"] != "" ) {
								$file_output['admin_messages'] = wfu_join_strings("<br />", $file_output['admin_messages'], $wfu_upload_file_ret["admin_message"]);
							}
						}
						else if ( $file_exists && $params["dublicatespolicy"] == "maintain both" ) {
							$full_path = wfu_upload_plugin_full_path($params);
							$name_part = $only_filename;
							$ext_part = "";
							$dot_pos = strrpos($name_part, ".");
							if ( $dot_pos ) {
								$ext_part = substr($name_part, $dot_pos);
								$name_part = substr($name_part, 0, $dot_pos);
							}
							if ( $params["uniquepattern"] != "datetimestamp" ) {
								$unique_ind = 1;
								do {
									$unique_ind += 1;
									$only_filename = $name_part . "(" . $unique_ind . ")" . $ext_part;
									$target_path = $full_path . $only_filename;
								}
								while ( file_exists($target_path) );
							}
							else {
								$current_datetime = gmdate("U") - 1;
								do {
									$current_datetime += 1;
									$only_filename = $name_part . "-" . gmdate("YmdHis", $current_datetime) . $ext_part;
									$target_path = $full_path . $only_filename;
								}
								while ( file_exists($target_path) );
							}
							//redirect echo in internal buffer to receive and process any unwanted warning messages from move_uploaded_file
							ob_start();
							ob_clean();
							/* Apply wfu_before_file_upload filter right before the upload, in order to allow the user to change the file name.
							   If additional data are required, such as user_id or userdata values, they can be retrieved by implementing the
							   previous filter wfu_before_file_check, corresponding them to the unique file id */
							if ( $file_unique_id != '' ) $target_path = apply_filters('wfu_before_file_upload', $target_path, $file_unique_id);
							//recalculate $only_filename in case it changed with wfu_before_file_upload filter
							$only_filename = basename($target_path);
							//move the uploaded file to its final destination
							$wfu_upload_file_ret = wfu_upload_file($source_path, $target_path, $params["accessmethod"], $params["ftpinfo"]);
							$file_copied = $wfu_upload_file_ret["uploaded"];
							//process warning messages from move_uploaded_file
							$echo_message = ob_get_contents();
							//finish redirecting of echo to internal buffer
							ob_end_clean();
							if ( $echo_message != "" && !$file_copied ) {
								$file_output['message_type'] = "error";
								if ( stristr($echo_message, "warning") && stristr($echo_message, "permission denied") && stristr($echo_message, "unable to move") ) {
									$file_output['message'] = wfu_join_strings("<br />", $file_output['message'], WFU_ERROR_DIR_PERMISSION);
									$file_output['admin_messages'] = wfu_join_strings("<br />", $file_output['admin_messages'], WFU_ERROR_ADMIN_DIR_PERMISSION);
								}
								else { 
									$file_output['message'] = wfu_join_strings("<br />", $file_output['message'], WFU_ERROR_FILE_MOVE);
									$file_output['admin_messages'] = wfu_join_strings("<br />n", $file_output['admin_messages'], strip_tags($echo_message));
								}
								$message_processed = true;
							}
							if ( $wfu_upload_file_ret["admin_message"] != "" ) {
								$file_output['admin_messages'] = wfu_join_strings("<br />", $file_output['admin_messages'], $wfu_upload_file_ret["admin_message"]);
							}
						}
						else {
							$file_output['message_type'] = "error";
							$file_output['message'] = wfu_join_strings("<br />", $file_output['message'], WFU_WARNING_FILE_EXISTS);
							$message_processed = true;
							$file_copied = false;
						}
					}
				}

				if ( $file_copied ) {
					/* prepare email notification parameters if email notification is enabled */
					if ( $params["notify"] == "true" && !$only_check ) {
						$notify_only_filename_list .= ( $notify_only_filename_list == "" ? "" : ", " ).$only_filename;
						$notify_target_path_list .= ( $notify_target_path_list == "" ? "" : ", " ).$target_path;
						if ( $params["attachfile"] == "true" )
							$notify_attachment_list .= ( $notify_attachment_list == "" ? "" : "," ).$target_path;
					} 

					/* prepare redirect link if redirection is enabled */
					if ( $params["redirect"] == "true" ) {
						/* Define dynamic redirect link from variables */
						$search = array ('/%filename%/');	 
						$replace = array ($only_filename);
						$params_output_array["general"]['redirect_link'] =  trim(preg_replace($search, $replace, $params["redirectlink"]));
					}
					
					if ( !$message_processed ) {
						$file_output['message_type'] = "success";
					}
				}
				else if ( !$message_processed ) {
					$file_output['message_type'] = "error";
					$file_output['message'] = wfu_join_strings("<br />", $file_output['message'], WFU_ERROR_UNKNOWN);
				}

				/* Delete temporary file (in tmp directory) */
//				unlink($source_path);			
			}
			else {
				$file_output['message_type'] = "error";
				$file_output['message'] = wfu_join_strings("<br />", $file_output['message'], WFU_ERROR_UNKNOWN);
			}
		}

		/* last check of output file status */
		if ( $file_output['message_type'] == "" ) {
			if ( $file_copied ) $file_output['message_type'] = "success";
			else {
				$file_output['message_type'] = "error";
				$file_output['message'] = wfu_join_strings("<br />", $file_output['message'], WFU_ERROR_UNKNOWN);
			}
		}

		/* suppress any admin messages if user is not administrator or adminmessages is not activated */		
		if ( $suppress_admin_messages ) $file_output['admin_messages'] = "";

		/* set file status to "warning" if the file has been uploaded but there are messages */
		if ( $file_output['message_type'] == "success" ) {
			if ( $file_output['message'] != "" || $file_output['admin_messages'] != "" )
				$file_output['message_type'] = "warning";
		}

		/* adjust message details and colors according to file result */
		/* FileResult: A */
		$search = array ('/%username%/', '/%useremail%/', '/%filename%/', '/%filepath%/');	 
		$replace = array ($user_login, ( $user_email == "" ? "no email" : $user_email ), $only_filename, $target_path);
		if ( $file_output['message_type'] == "success" ) {
			$success_count ++;
			$color_array = explode(",", $params['successmessagecolors']);
			$file_output['color'] = $color_array[0];
			$file_output['bgcolor'] = $color_array[1];
			$file_output['borcolor'] = $color_array[2];
			$file_output['header'] = preg_replace($search, $replace, $params['successmessage']);
			/* prepare details of successful file upload, visible only to administrator */
			$file_output['admin_messages'] = wfu_join_strings("<br />", preg_replace($search, $replace, WFU_SUCCESSMESSAGE_DETAILS), $file_output['admin_messages']);
		}
		/* FileResult: B */
		elseif ( $file_output['message_type'] == "warning" ) {
			$warning_count ++;
			$color_array = explode(",", $params['warningmessagecolors']);
			$file_output['color'] = $color_array[0];
			$file_output['bgcolor'] = $color_array[1];
			$file_output['borcolor'] = $color_array[2];
			$file_output['header'] = preg_replace($search, $replace, $params['warningmessage']);
			/* prepare and prepend details of successful file upload, visible only to administrator */
			$file_output['admin_messages'] = wfu_join_strings("<br />", preg_replace($search, $replace, WFU_SUCCESSMESSAGE_DETAILS), $file_output['admin_messages']);
		}
		/* FileResult: C */
		elseif ( $file_output['message_type'] == "error" ) {
			$error_count ++;
			$color_array = explode(",", $params['failmessagecolors']);
			$file_output['color'] = $color_array[0];
			$file_output['bgcolor'] = $color_array[1];
			$file_output['borcolor'] = $color_array[2];
			/* define variables that were not defined before due to error */
			$only_filename = wfu_upload_plugin_clean( $fileprops['name'] );
			$target_path = wfu_upload_plugin_full_path($params).$only_filename;
			$replace = array ($user_login, ( $user_email == "" ? "no email" : $user_email ), $only_filename, $target_path);
			$file_output['header'] = preg_replace($search, $replace, $params['errormessage']);
			/* prepare and prepend details of failed file upload, visible only to administrator */
			$file_output['admin_messages'] = wfu_join_strings("<br />", preg_replace($search, $replace, WFU_FAILMESSAGE_DETAILS), $file_output['admin_messages']);
		}

		/* suppress again any admin messages if user is not administrator or adminmessages is not activated */		
		if ( $suppress_admin_messages ) $file_output['admin_messages'] = "";

		/* set success status of the file, to be used for medialink and post actions */
		$file_finished_successfully = ( !$only_check && ( $file_output['message_type'] == "success" || $file_output['message_type'] == "warning" ) );

		$params_output_array[0] = $file_output;

		/* Apply wfu_after_file_upload action after failed upload, in order to allow the user to perform any post-upload actions.
		   If additional data are required, such as user_id or userdata values or filepath, they can be retrieved by implementing
		   the previous filters wfu_before_file_check and wfu_before_file_upload, corresponding them to the unique file id */
		if ( $file_unique_id != '' && $file_output['message_type'] == "error" ) {
			do_action('wfu_after_file_upload', $file_unique_id, $file_output['message_type'], $file_output['message'], $file_output['admin_messages']);
		}

		/* log file upload action if file has finished uploading successfully */
		if ( $file_finished_successfully ) {
			wfu_log_action('upload', $target_path, $user->ID, $uniqueuploadid, $params['pageid'], $sid, $userdata_fields);
			/* Apply wfu_after_file_upload action after successfull upload, in order to allow the user to perform any post-upload actions.
			   If additional data are required, such as user_id or userdata values or filepath, they can be retrieved by implementing
			   the previous filters wfu_before_file_check and wfu_before_file_upload, corresponding them to the unique file id */
			do_action('wfu_after_file_upload', $file_unique_id, $file_output['message_type'], $file_output['message'], $file_output['admin_messages']);
		}

		/* add file to Media or attach file to current post if any of these options is activated and the file has finished uploading successfully */
		if ( ( $params["medialink"] == "true" || $params["postlink"] == "true" ) && $file_finished_successfully ) {
			$pageid = ( $params["postlink"] == "true" ? $params['pageid'] : 0 );
			wfu_process_media_insert($target_path, $pageid);
		}
	}

	// in case of file check set files_count to 0 in order to denote that the file was not really uploaded
	if ( $only_check ) $params_output_array["general"]['files_count'] = 0;

	$somefiles_Ok = ( ( $warning_count + $success_count ) > 0 );
	$allfiles_Ok = ( $somefiles_Ok && ( $error_count == 0 ) );

	/* Prepare WPFileBase Plugin update url, if this option has been selected and only if at least one file has been successfully uploaded.
	   Execution will happen only if accumulated $params_output_array["general"]['update_wpfilebase'] is not empty */
	if ( $params["filebaselink"] == "true" ) {
		if ( $somefiles_Ok ) {		
			$filebaseurl = site_url();
			if ( substr($filebaseurl, -1, 1) == "/" ) $filebaseurl = substr($filebaseurl, 0, strlen($filebaseurl) - 1);
			/* if the following variable is not empty, then WPFileBase Plugin update must be executed
			   and any admin messages must be suppressed */
			$params_output_array["general"]['update_wpfilebase'] = $filebaseurl;
		}
		else {
			$params_output_array["general"]['admin_messages']['wpfilebase'] = WFU_WARNING_WPFILEBASE_NOTUPDATED_NOFILES;
			$params_output_array["general"]['errors']['wpfilebase'] = "error";
		}
	} 

	/* Prepare email notification parameters if email notification is enabled and only if at least one file has been successfully uploaded
	   	if $method = "no-ajax" then send the email to the recipients 
	   	if $method = "ajax" then return the notification parameters to the handler for further processing
	   In case of ajax, execution will happen only if accumulated notify_only_filename_list is not empty */
	if ( $params["notify"] == "true" ) {
		/* verify that there are recipients */
		$notifyrecipients =  trim(preg_replace('/%useremail%/', $user_email, $params["notifyrecipients"]));
		if ( $notifyrecipients != "" ) {
			if ( $somefiles_Ok ) {	
				if ( $method == 'no_ajax' ) {
					$send_error = wfu_send_notification_email($user, $notify_only_filename_list, $notify_target_path_list, $notify_attachment_list, $userdata_fields, $params);
					if ( $send_error != "" ) {
						$params_output_array["general"]['admin_messages']['notify'] = $send_error;
						$params_output_array["general"]['errors']['notify'] = "error";
					}
				}
				else {
					/* if the following variable is not empty, then email notification must be sent
					   and any admin messages must be suppressed */
					$params_output_array["general"]['notify_only_filename_list'] = $notify_only_filename_list;
					$params_output_array["general"]['notify_target_path_list'] = $notify_target_path_list;
					$params_output_array["general"]['notify_attachment_list'] = $notify_attachment_list;
				}
			}
			else {
				$params_output_array["general"]['admin_messages']['notify'] = WFU_WARNING_NOTIFY_NOTSENT_NOFILES;
				$params_output_array["general"]['errors']['notify'] = "error";
			}
		}
		else {
			$params_output_array["general"]['admin_messages']['notify'] = WFU_WARNING_NOTIFY_NOTSENT_NORECIPIENTS;
			$params_output_array["general"]['errors']['notify'] = "error";
		}
	} 

	/* Prepare redirect link if redirection is enabled and only if all files have been successfully uploaded
	   Execution will happen only if accumulated redirect_link is not empty and accumulated redirect errors are empty */
	if ( $params["redirect"] == "true" ) {
		if ( $params_output_array["general"]['redirect_link'] == "" ) {
			$params_output_array["general"]['admin_messages']['redirect'] = WFU_WARNING_REDIRECT_NOTEXECUTED_EMPTY;
			$params_output_array["general"]['errors']['redirect'] = "error";
		}
		elseif ( !$allfiles_Ok ) {
			$params_output_array["general"]['admin_messages']['redirect'] = WFU_WARNING_REDIRECT_NOTEXECUTED_FILESFAILED;
			$params_output_array["general"]['errors']['redirect'] = "error";
		}
	}

	/* suppress any admin messages if user is not administrator or adminmessages is not activated */		
	if ( $suppress_admin_messages ) {
		$params_output_array["general"]['admin_messages']['wpfilebase'] = "";
		$params_output_array["general"]['admin_messages']['notify'] = "";
		$params_output_array["general"]['admin_messages']['redirect'] = "";
		$params_output_array["general"]['admin_messages']['other'] = "";
	}

	/* Calculate upload state from file results */
	if ( $allfiles_Ok && ( $warning_count == 0 ) ) $params_output_array["general"]['state'] = 4;
	else if ( $allfiles_Ok ) $params_output_array["general"]['state'] = 5;
	else if ( $somefiles_Ok ) $params_output_array["general"]['state'] = 6;   //only valid in no-ajax method
	else if ( !$somefiles_Ok && $error_count > 0 ) $params_output_array["general"]['state'] = 7;
	else $params_output_array["general"]['state'] = 8;

	/* construct safe output */
	$sout = $params_output_array["general"]['state'].";".WFU_DEFAULTMESSAGECOLORS.";".$files_count;
	for ($i = 0; $i < $files_count; $i++) {
		$sout .= ";".wfu_plugin_encode_string($file_output['message_type']);
		$sout .= ",".wfu_plugin_encode_string($file_output['header']);
		$sout .= ",".wfu_plugin_encode_string($file_output['message']);
		$sout .= ",".wfu_plugin_encode_string($file_output['admin_messages']);
	}
	$params_output_array["general"]['safe_output'] = $sout;

	return $params_output_array;
}

?>
