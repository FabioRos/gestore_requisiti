<?php

function wfu_create_directory($path, $method, $ftpdata) {
	$ret_message = "";
	if ( $method == "" || $method == "normal" ) {
		mkdir($path, 0777, true);
	}
	else if ( $method == "ftp" && $ftpdata != "" ) {
		$ftpdata_flat =  str_replace(array('\:', '\@'), array('\_', '\_'), $ftpdata);
		$pos1 = strpos($ftpdata_flat, ":");
		$pos2 = strpos($ftpdata_flat, "@");
		if ( $pos1 && $pos2 && $pos2 > $pos1 ) {
			$ftp_username = substr($ftpdata, 0, $pos1);
			$ftp_password = substr($ftpdata, $pos1 + 1, $pos2 - $pos1 - 1);
			$ftp_host = substr($ftpdata, $pos2 + 1);
			$conn_id = ftp_connect($ftp_host);
			$login_result = ftp_login($conn_id, $ftp_username, $ftp_password);
			if ( $conn_id && $login_result ) {
				$flat_host = preg_replace("/^(.*\.)?([^.]*\..*)$/", "$2", $ftp_host);
				$pos1 = strpos($path, $flat_host);
				if ( $pos1 ) {
					$path = substr($path, $pos1 + strlen($flat_host));
					ftp_mkdir($conn_id, $path);
					ftp_chmod($conn_id, 511, $path);
				}
				else {
					$ret_message = WFU_ERROR_ADMIN_FTPDIR_RESOLVE;
				}
			}
			else {
				$ret_message = WFU_ERROR_ADMIN_FTPINFO_INVALID;
			}
			ftp_quit($conn_id);
		}
		else {
			$ret_message = WFU_ERROR_ADMIN_FTPINFO_EXTRACT;
		}
	}
	else {
		$ret_message = WFU_ERROR_ADMIN_FTPINFO_INVALID;
	}
	return $ret_message;
}


function wfu_upload_file($source, $target, $method, $ftpdata) {
	$ret_array = "";
	$ret_array["uploaded"] = false;
	$ret_array["admin_message"] = "";
	$ret_message = "";
	$target_perms = substr(sprintf('%o', fileperms(dirname($target))), -4);
	if ( $method == "" || $method == "normal" ) {
		$ret_array["uploaded"] = move_uploaded_file($source, $target);
		if ( !$ret_array["uploaded"] && !is_writable(dirname($target)) ) {
			$ret_message = WFU_ERROR_ADMIN_DIR_PERMISSION;
		}
	}
	elseif ( $method == "ftp" &&  $ftpdata != "" ) {
		$result = false;
		$ftpdata_flat =  str_replace(array('\:', '\@'), array('\_', '\_'), $ftpdata);
		$pos1 = strpos($ftpdata_flat, ":");
		$pos2 = strpos($ftpdata_flat, "@");
		if ( $pos1 && $pos2 && $pos2 > $pos1 ) {
			$ftp_username = substr($ftpdata, 0, $pos1);
			$ftp_password = substr($ftpdata, $pos1 + 1, $pos2 - $pos1 - 1);
			$ftp_host = substr($ftpdata, $pos2 + 1);
			$conn_id = ftp_connect($ftp_host);
			$login_result = ftp_login($conn_id, $ftp_username, $ftp_password);
			if ( $conn_id && $login_result ) {
				$flat_host = preg_replace("/^(.*\.)?([^.]*\..*)$/", "$2", $ftp_host);
				$pos1 = strpos($target, $flat_host);
				if ( $pos1 ) {
//					$temp_fname = tempnam(dirname($target), "tmp");
//					move_uploaded_file($source, $temp_fname);
					$target = substr($target, $pos1 + strlen($flat_host));
					ftp_chmod($conn_id, 0755, dirname($target));
					$ret_array["uploaded"] = ftp_put($conn_id, $target, $source, FTP_BINARY);
					ftp_chmod($conn_id, 0755, $target);
					ftp_chmod($conn_id, $target_perms, dirname($target));
					unlink($source);
					if ( !$ret_array["uploaded"] ) {
						$ret_message = WFU_ERROR_ADMIN_DIR_PERMISSION;
					}
				}
				else {
					$ret_message = WFU_ERROR_ADMIN_FTPFILE_RESOLVE;
				}
			}
			else {
				$ret_message = WFU_ERROR_ADMIN_FTPINFO_INVALID;
			}
			ftp_quit($conn_id);
		}
		else {
			$ret_message = WFU_ERROR_ADMIN_FTPINFO_EXTRACT.$ftpdata_flat;
		}
	}		
	else {
		$ret_message = WFU_ERROR_ADMIN_FTPINFO_INVALID;
	}

	$ret_array["admin_message"] = $ret_message;
	return $ret_array;
}

?>
