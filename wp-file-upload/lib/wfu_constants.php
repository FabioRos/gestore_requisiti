<?php

$siteurl = site_url();

//define plugin defaults
DEFINE("WFU_UPLOADID", "1");
DEFINE("WFU_UPLOADTITLE", __('Upload files', 'wordpress-file-upload'));
DEFINE("WFU_SELECTBUTTON", __('Select File', 'wordpress-file-upload'));
DEFINE("WFU_UPLOADBUTTON", __('Upload File', 'wordpress-file-upload'));
DEFINE("WFU_SINGLEBUTTON", "false");
DEFINE("WFU_UPLOADROLE", "all");
DEFINE("WFU_UPLOADPATH", 'uploads');
DEFINE("WFU_CREATEPATH", "false");
DEFINE("WFU_UPLOADPATTERNS", "*.*");
DEFINE("WFU_MAXSIZE", "50");
DEFINE("WFU_ACCESSMETHOD", "normal");
DEFINE("WFU_FTPINFO", "");
DEFINE("WFU_USEFTPDOMAIN", "false");
DEFINE("WFU_DUBLICATESPOLICY", "overwrite");
DEFINE("WFU_UNIQUEPATTERN", "index");
DEFINE("WFU_FILEBASELINK", "false");
DEFINE("WFU_NOTIFY", "false");
DEFINE("WFU_NOTIFYRECIPIENTS", "");
DEFINE("WFU_NOTIFYSUBJECT", __('File Upload Notification', 'wordpress-file-upload'));
DEFINE("WFU_NOTIFYMESSAGE", __("Dear Recipient,%n%%n%   This is an automatic delivery message to notify you that a new file has been uploaded.%n%%n%Best Regards", 'wordpress-file-upload'));
DEFINE("WFU_NOTIFYHEADERS", "");    
DEFINE("WFU_ATTACHFILE", "false");
DEFINE("WFU_REDIRECT", "false");
DEFINE("WFU_REDIRECTLINK", "");
DEFINE("WFU_ADMINMESSAGES", "false");
DEFINE("WFU_SUCCESSMESSAGE", __('File %filename% uploaded successfully', 'wordpress-file-upload'));
DEFINE("WFU_WARNINGMESSAGE", __('File %filename% uploaded successfully but with warnings', 'wordpress-file-upload'));  
DEFINE("WFU_ERRORMESSAGE", __('File %filename% not uploaded', 'wordpress-file-upload'));
DEFINE("WFU_WAITMESSAGE", __('File %filename% is being uploaded', 'wordpress-file-upload'));  
DEFINE("WFU_SUCCESSMESSAGECOLOR", "green");
DEFINE("WFU_SUCCESSMESSAGECOLORS", "#006600,#EEFFEE,#006666");
DEFINE("WFU_WARNINGMESSAGECOLORS", "#F88017,#FEF2E7,#633309");
DEFINE("WFU_FAILMESSAGECOLORS", "#660000,#FFEEEE,#666600");
DEFINE("WFU_WAITMESSAGECOLORS", "#666666,#EEEEEE,#333333");  
DEFINE("WFU_SHOWTARGETFOLDER", "false");
DEFINE("WFU_TARGETFOLDERLABEL", "Upload Directory");
DEFINE("WFU_ASKFORSUBFOLDERS", "false");
DEFINE("WFU_SUBFOLDERLABEL", "Select Subfolder");
DEFINE("WFU_SUBFOLDERTREE", "");
DEFINE("WFU_FORCECLASSIC", "false");
DEFINE("WFU_TESTMODE", "false");
DEFINE("WFU_DEBUGMODE", "false");
DEFINE("WFU_WIDTHS", "");
DEFINE("WFU_HEIGHTS", "");
DEFINE("WFU_PLACEMENTS", "title/filename+selectbutton+uploadbutton/subfolders"."/userdata"."/message");    
DEFINE("WFU_USERDATA", "false");               
DEFINE("WFU_USERDATALABEL", __('Your message', 'wordpress-file-upload'));   
DEFINE("WFU_MEDIALINK", "false");
DEFINE("WFU_POSTLINK", "false");

//define plugin errors
DEFINE("WFU_ERROR_ADMIN_FTPDIR_RESOLVE", __("Error. Could not resolve ftp target filedir. Check the domain in 'ftpinfo' attribute.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_ADMIN_FTPINFO_INVALID", __("Error. Invalid ftp information. Check 'ftpinfo' attribute.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_ADMIN_FTPINFO_EXTRACT", __("Error. Could not extract ftp information from 'ftpinfo' attribute. Check its syntax.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_ADMIN_FTPFILE_RESOLVE", __("Error. Could not resolve ftp target filename. Check the domain in 'ftpinfo' attribute.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_ADMIN_FILE_PHP_SIZE", __("Error. The upload size limit of PHP directive upload_max_filesize is preventing the upload of big files.\nPHP directive upload_max_filesize limit is: ".ini_get("upload_max_filesize").".\nTo increase the limit change the value of the directive from php.ini.\nIf you don't have access to php.ini, then try adding the following line to your .htaccess file:\n\nphp_value upload_max_filesize 10M\n\n(adjust the size according to your needs)\n\nThe file .htaccess is found in your website root directory (where index.php is found).\nIf your don't have this file, then create it.\nIf this does not work either, then contact your domain provider.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_ADMIN_FILE_PHP_TIME", __("The upload time limit of PHP directive max_input_time is preventing the upload of big files.\nPHP directive max_input_time limit is: ".ini_get("max_input_time")." seconds.\nTo increase the limit change the value of the directive from php.ini.\nIf you don't have access to php.ini, then add the following line to your .htaccess file:\n\nphp_value max_input_time 500\n\n(adjust the time according to your needs)\n\nThe file .htaccess is found in your website root directory (where index.php is found).\nIf your don't have this file, then create it.\nIf this does not work either, then contact your domain provider.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_ADMIN_DIR_PERMISSION", __("Error. Permission denied to write to target folder.\nCheck and correct read/write permissions of target folder.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_DIR_EXIST", __("Targer folder doesn't exist.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_DIR_NOTEMP", __("Upload failed! Missing a temporary folder.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_DIR_PERMISSION", __("Upload failed! Permission denied to write to target folder.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_FILE_ALLOW", __("File not allowed.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_FILE_PLUGIN_SIZE", __("The uploaded file exceeds the file size limit.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_FILE_PHP_SIZE", __("Upload failed! The uploaded file exceeds the file size limit of the server. Please contact the administrator.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_FILE_PHP_TIME", __("Upload failed! The duration of the upload exceeded the time limit of the server. Please contact the administrator.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_FILE_HTML_SIZE", __("Upload failed! The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_FILE_PARTIAL", __("Upload failed! The uploaded file was only partially uploaded.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_FILE_NOTHING", __("Upload failed! No file was uploaded.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_FILE_WRITE", __("Upload failed! Failed to write file to disk.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_FILE_MOVE", __("Upload failed! Error occured while moving temporary file. Please contact administrator.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_UPLOAD_STOPPED", __("Upload failed! A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_UPLOAD_FAILED_WHILE", __("Upload failed! Error occured while attemting to upload the file.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_UPLOAD_FAILED", __("Upload failed!", "wordpress-file-upload"));
DEFINE("WFU_ERROR_UPLOAD_NOFILESELECTED", __("No file!", "wordpress-file-upload"));
DEFINE("WFU_ERROR_UPLOAD_CANCELLED", __("Upload failed! The upload has been canceled by the user or the browser dropped the connection.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_UNKNOWN", __("Upload failed! Unknown error.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_CONTACT_ADMIN", __("Please contact the administrator.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_REMOTESERVER_NORESULT", __("No result from remote server!", "wordpress-file-upload"));
DEFINE("WFU_ERROR_JSONPARSE_FILEMESSAGE", __(" but with warnings", "wordpress-file-upload"));
DEFINE("WFU_ERROR_JSONPARSE_MESSAGE", __("Warning: JSON parse error.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_JSONPARSE_ADMINMESSAGE", __("Upload parameters of this file, passed as JSON string to the handler, could not be parsed.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_JSONPARSE_HEADERMESSAGE", __("Warning: JSON parse error.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_JSONPARSE_HEADERADMINMESSAGE", __("UploadStates, passed as JSON string to the handler, could not be parsed.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_REDIRECTION_ERRORCODE0", __("Redirection to classic form functionality occurred due to unknown error.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_REDIRECTION_ERRORCODE1", __("Redirection to classic form functionality occurred because AJAX is not supported.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_REDIRECTION_ERRORCODE2", __("Redirection to classic form functionality occurred because HTML5 is not supported.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_REDIRECTION_ERRORCODE3", __("Redirection to classic form functionality occurred due to JSON parse error.", "wordpress-file-upload"));
DEFINE("WFU_ERROR_USERDATA_EMPTY", __("cannot be empty!", "wordpress-file-upload"));

//define plugin warnings
DEFINE("WFU_WARNING_FILE_EXISTS", __("Upload skipped! File already exists.", "wordpress-file-upload"));
DEFINE("WFU_WARNING_NOFILES_SELECTED", __("No files have been selected!", "wordpress-file-upload"));
DEFINE("WFU_WARNING_WPFILEBASE_NOTUPDATED_NOFILES", __("WPFilebase Plugin not updated because there were no files uploaded.", "wordpress-file-upload"));
DEFINE("WFU_WARNING_NOTIFY_NOTSENT_NOFILES", __("Notification email was not sent because there were no files uploaded.", "wordpress-file-upload"));
DEFINE("WFU_WARNING_NOTIFY_NOTSENT_NORECIPIENTS", __("Notification email was not sent because no recipients were defined. Please check notifyrecipients attribute in the shortcode.", "wordpress-file-upload"));
DEFINE("WFU_WARNING_NOTIFY_NOTSENT_UNKNOWNERROR", __("Notification email was not sent due to an error. Please check notifyrecipients, notifysubject and notifymessage attributes for errors.", "wordpress-file-upload"));
DEFINE("WFU_WARNING_REDIRECT_NOTEXECUTED_EMPTY", __("Redirection not executed because redirection link is empty. Please check redirectlink attribute.", "wordpress-file-upload"));
DEFINE("WFU_WARNING_REDIRECT_NOTEXECUTED_FILESFAILED", __("Redirection not executed because not all files were successfully uploaded.", "wordpress-file-upload"));

//define plugin messages
DEFINE("WFU_NOTIFY_TESTMODE", __("Test Mode", "wordpress-file-upload"));
DEFINE("WFU_SUBDIR_SELECTDIR", __("select dir...", "wordpress-file-upload"));
DEFINE("WFU_SUCCESSMESSAGE_DETAILS", __('Upload path: %filepath%', 'wordpress-file-upload'));
DEFINE("WFU_FAILMESSAGE_DETAILS", __('Failed upload path: %filepath%', 'wordpress-file-upload'));
DEFINE("WFU_USERDATA_REQUIREDLABEL", __(' (required)', 'wordpress-file-upload'));

//define plugin test messages
DEFINE("WFU_TESTMESSAGE_MESSAGE", __('This is a test message', 'wordpress-file-upload'));
DEFINE("WFU_TESTMESSAGE_ADMINMESSAGE", __('This is a test administrator message', 'wordpress-file-upload'));
DEFINE("WFU_TESTMESSAGE_FILE1_HEADER", __('File testfile 1 under test', 'wordpress-file-upload'));
DEFINE("WFU_TESTMESSAGE_FILE1_MESSAGE", __('File testfile 1 message', 'wordpress-file-upload'));
DEFINE("WFU_TESTMESSAGE_FILE1_ADMINMESSAGE", __('File testfile 1 administrator message', 'wordpress-file-upload'));
DEFINE("WFU_TESTMESSAGE_FILE2_HEADER", __('File testfile 2 under test', 'wordpress-file-upload'));
DEFINE("WFU_TESTMESSAGE_FILE2_MESSAGE", __('File testfile 2 message', 'wordpress-file-upload'));
DEFINE("WFU_TESTMESSAGE_FILE2_ADMINMESSAGE", __('File testfile 2 administrator message', 'wordpress-file-upload'));

//define tool tip constants
DEFINE("WFU_VARIABLE_TITLE_USERID", __("Insert variable %userid% inside text. It will be replaced by the id of the current user.", "wordpress-file-upload"));
DEFINE("WFU_VARIABLE_TITLE_USERNAME", __("Insert variable %username% inside text. It will be replaced by the username of the current user.", "wordpress-file-upload"));
DEFINE("WFU_VARIABLE_TITLE_USEREMAIL", __("Insert variable %useremail% inside text. It will be replaced by the email of the current user.", "wordpress-file-upload"));
DEFINE("WFU_VARIABLE_TITLE_FILENAME", __("Insert variable %filename% inside text. It will be replaced by the filename of the uploaded file.", "wordpress-file-upload"));
DEFINE("WFU_VARIABLE_TITLE_FILEPATH", __("Insert variable %filepath% inside text. It will be replaced by the full filepath of the uploaded file.", "wordpress-file-upload"));
DEFINE("WFU_VARIABLE_TITLE_BLOGID", __("Insert variable %blogid% inside text. It will be replaced by the blog id of the website.", "wordpress-file-upload"));
DEFINE("WFU_VARIABLE_TITLE_PAGEID", __("Insert variable %pageid% inside text. It will be replaced by the id of the current page.", "wordpress-file-upload"));
DEFINE("WFU_VARIABLE_TITLE_PAGETITLE", __("Insert variable %pagetitle% inside text. It will be replaced by the title of the current page.", "wordpress-file-upload"));
DEFINE("WFU_VARIABLE_TITLE_USERDATAXXX", __("Insert variable %userdataXXX% inside text. Select the user field from the drop-down list. It will be replaced by the value that the user entered in this field.", "wordpress-file-upload"));
DEFINE("WFU_VARIABLE_TITLE_N", __("Insert variable %n% inside text to denote a line change.", "wordpress-file-upload"));

//define plugin other constants
DEFINE("WFU_UPLOAD_STATE0", __("Upload in progress", "wordpress-file-upload"));
DEFINE("WFU_UPLOAD_STATE1", __("Upload in progress with warnings!", "wordpress-file-upload"));
DEFINE("WFU_UPLOAD_STATE2", __("Upload in progress but some files already failed!", "wordpress-file-upload"));
DEFINE("WFU_UPLOAD_STATE3", __("Upload in progress but no files uploaded so far!", "wordpress-file-upload"));
DEFINE("WFU_UPLOAD_STATE4", __("All files uploaded successfully", "wordpress-file-upload"));
DEFINE("WFU_UPLOAD_STATE5", __("All files uploaded successfully but there are warnings!", "wordpress-file-upload"));
DEFINE("WFU_UPLOAD_STATE5_SINGLEFILE", __("File uploaded successfully but there are warnings!", "wordpress-file-upload"));
DEFINE("WFU_UPLOAD_STATE6", __("Some files failed to upload!", "wordpress-file-upload"));
DEFINE("WFU_UPLOAD_STATE7", __("All files failed to upload", "wordpress-file-upload"));
DEFINE("WFU_UPLOAD_STATE7_SINGLEFILE", __("File failed to upload", "wordpress-file-upload"));
DEFINE("WFU_UPLOAD_STATE8", __("There are no files to upload!", "wordpress-file-upload"));
DEFINE("WFU_UPLOAD_STATE9", __("Test upload message", "wordpress-file-upload"));
DEFINE("WFU_UPLOAD_STATE10", __("JSON parse warning!", "wordpress-file-upload"));
DEFINE("WFU_UPLOAD_STATE11", __("please wait while redirecting...", "wordpress-file-upload"));
DEFINE("WFU_MAX_TIME_LIMIT", ini_get("max_input_time"));
DEFINE("WFU_RESPONSE_URL", $siteurl.WPFILEUPLOAD_DIR."wfu_response.php");
DEFINE("WFU_AJAX_URL", $siteurl."/wp-admin/admin-ajax.php");
DEFINE("WFU_PRO_VERSION_URL", 'http://www.iptanus.com/product/wordpress-file-upload-pro/');

//define colors
DEFINE("WFU_TESTMESSAGECOLORS", "#666666,#EEEEEE,#333333");  
DEFINE("WFU_DEFAULTMESSAGECOLORS", "#666666,#EEEEEE,#333333");  
DEFINE("WFU_HEADERMESSAGECOLORS_STATE0", "#666666,#EEEEEE,#333333");  
DEFINE("WFU_HEADERMESSAGECOLORS_STATE1", "#F88017,#FEF2E7,#633309");  
DEFINE("WFU_HEADERMESSAGECOLORS_STATE2", "#660000,#FFEEEE,#666600");  
DEFINE("WFU_HEADERMESSAGECOLORS_STATE3", "#660000,#FFEEEE,#666600");  
DEFINE("WFU_HEADERMESSAGECOLORS_STATE4", "#006600,#EEFFEE,#006666");  
DEFINE("WFU_HEADERMESSAGECOLORS_STATE5", "#F88017,#FEF2E7,#633309");  
DEFINE("WFU_HEADERMESSAGECOLORS_STATE6", "#660000,#FFEEEE,#666600");  
DEFINE("WFU_HEADERMESSAGECOLORS_STATE7", "#660000,#FFEEEE,#666600");  
DEFINE("WFU_HEADERMESSAGECOLORS_STATE8", "#660000,#FFEEEE,#666600");  
DEFINE("WFU_HEADERMESSAGECOLORS_STATE9", "#666666,#EEEEEE,#333333");  
DEFINE("WFU_HEADERMESSAGECOLORS_STATE10", "#F88017,#FEF2E7,#633309"); 
DEFINE("WFU_HEADERMESSAGECOLORS_STATE11", "#666666,#EEEEEE,#333333"); 

//define images
DEFINE("WFU_IMAGE_ADMIN_HELP", $siteurl.WPFILEUPLOAD_DIR.'images/help_16.png');
DEFINE("WFU_IMAGE_ADMIN_RESTOREDEFAULT", $siteurl.WPFILEUPLOAD_DIR.'images/restore_16.png');
DEFINE("WFU_IMAGE_ADMIN_USERDATA_ADD", $siteurl.WPFILEUPLOAD_DIR.'images/add_12.png');
DEFINE("WFU_IMAGE_ADMIN_USERDATA_REMOVE", $siteurl.WPFILEUPLOAD_DIR.'images/remove_12.png');
DEFINE("WFU_IMAGE_ADMIN_SUBFOLDER_BROWSE", $siteurl.WPFILEUPLOAD_DIR.'images/tree_16.gif');
DEFINE("WFU_IMAGE_ADMIN_SUBFOLDER_OK", $siteurl.WPFILEUPLOAD_DIR.'images/ok_12.gif');
DEFINE("WFU_IMAGE_ADMIN_SUBFOLDER_CANCEL", $siteurl.WPFILEUPLOAD_DIR.'images/cancel_12.gif');
DEFINE("WFU_IMAGE_ADMIN_SUBFOLDER_LOADING", $siteurl.WPFILEUPLOAD_DIR.'images/refresh_16.gif');
DEFINE("WFU_IMAGE_SIMPLE_PROGBAR", $siteurl.WPFILEUPLOAD_DIR.'images/progbar.gif');
DEFINE("WFU_IMAGE_VERSION_COMPARISON", $siteurl.WPFILEUPLOAD_DIR.'images/Version Comparison.png');

function wfu_set_javascript_constants() {
	$consts = array(
		"nofilemessage" => WFU_ERROR_UPLOAD_NOFILESELECTED,
		"userdata_empty" => WFU_ERROR_USERDATA_EMPTY,
		"remoteserver_noresult" => WFU_ERROR_REMOTESERVER_NORESULT,
		"message_header" => WFU_ERRORMESSAGE,
		"message_failed" => WFU_ERROR_UPLOAD_FAILED_WHILE,
		"message_cancelled" => WFU_ERROR_UPLOAD_CANCELLED,
		"message_unknown" => WFU_ERROR_UNKNOWN,
		"adminmessage_unknown" => WFU_FAILMESSAGE_DETAILS,
		"message_timelimit" => WFU_ERROR_FILE_PHP_TIME,
		"message_admin_timelimit" => WFU_ERROR_ADMIN_FILE_PHP_TIME,
		"jsonparse_filemessage" => WFU_ERROR_JSONPARSE_FILEMESSAGE,
		"jsonparse_message" => WFU_ERROR_JSONPARSE_MESSAGE,
		"jsonparse_adminmessage" => WFU_ERROR_JSONPARSE_ADMINMESSAGE,
		"jsonparse_headermessage" => WFU_ERROR_JSONPARSE_HEADERMESSAGE,
		"jsonparse_headeradminmessage" => WFU_ERROR_JSONPARSE_HEADERADMINMESSAGE,
		"default_colors" => WFU_DEFAULTMESSAGECOLORS,
		"fail_colors" => WFU_FAILMESSAGECOLORS,
		"max_time_limit" => WFU_MAX_TIME_LIMIT,
		"response_url" => WFU_RESPONSE_URL,
		"ajax_url" => WFU_AJAX_URL
	);
	$consts_txt = "";
	foreach ( $consts as $key => $val )
		$consts_txt .= ( $consts_txt == "" ? "" : ";" ).wfu_plugin_encode_string($key).":".wfu_plugin_encode_string($val);

	return $consts_txt;
}

?>
