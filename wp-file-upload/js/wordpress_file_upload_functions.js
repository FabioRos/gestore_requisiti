GlobalData = {};
UploadStates = {};
GlobalData.filestatematch = {};
GlobalData.filestatematch.success = [0, 1, 2, 2];
GlobalData.filestatematch.warning = [1, 1, 2, 2];
GlobalData.filestatematch.error1 = [3, 3, 2, 3];
GlobalData.filestatematch.error2 = [2, 2, 2, 3];
wfu_Check_Browser_Capabilities();
//console.log(wfu_BrowserCaps);

//wfu_Initialize_Consts: function to initialize constants passed from plugin to javascript
function wfu_Initialize_Consts(consts) {
	if (typeof GlobalData.consts != "undefined") return;
	GlobalData.consts = new Object();
	var consts_arr = consts.split(";");
	var const_arr;
	for (var i = 0; i < consts_arr.length; i++) {
		const_txt = consts_arr[i].split(":");
		GlobalData.consts[wfu_plugin_decode_string(const_txt[0])] = wfu_plugin_decode_string(const_txt[1]);
	}
}

//wfu_Check_Browser_Capabilities: function that checks if browser supports HTML5, iframes and AJAX
function wfu_Check_Browser_Capabilities() {
	if (typeof wfu_BrowserCaps != "undefined") return;
	wfu_BrowserCaps = new Object();
	//check AJAX
	var xmlhttp = wfu_GetHttpRequestObject();
	wfu_BrowserCaps.supportsAJAX = ( xmlhttp != null );
	//check Upload Progress
	wfu_BrowserCaps.supportsUploadProgress = !! (xmlhttp && ('upload' in xmlhttp) && ('onprogress' in xmlhttp.upload));
	//check HTML5
	var fd = null;
	try {
		var fd = new FormData();
	}
	catch(e) {}
	wfu_BrowserCaps.supportsHTML5 = ( fd != null );
	//check IFRAME
	var e = document.createElement("iframe");
	wfu_BrowserCaps.supportsIFRAME = ( e != null );
	//check Drag and Drop
	wfu_BrowserCaps.supportsDRAGDROP = (window.FileReader);
	//check animation
	wfu_BrowserCaps.supportsAnimation = wfu_check_animation();
	//check if browser is Safari
	wfu_BrowserCaps.isSafari = (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1);
}

//wfu_check_animation: function that checks if CSS3 animation is supported 
function wfu_check_animation() {
	var animation = false,
	animationstring = 'animation',
	keyframeprefix = '',
	domPrefixes = 'Webkit Moz O ms Khtml'.split(' '),
	pfx = '';
	
	var elm = document.createElement('DIV');

	if( elm.style.animationName ) { animation = true; } 

	if( animation === false ) {
		for( var i = 0; i < domPrefixes.length; i++ ) {
			if( elm.style[ domPrefixes[i] + 'AnimationName' ] !== undefined ) {
				pfx = domPrefixes[ i ];
				animationstring = pfx + 'Animation';
				keyframeprefix = '-' + pfx.toLowerCase() + '-';
				animation = true;
				break;
			}
		}
	}
	return animation;
}

/* function to join two or more strings using a delimeter */
function wfu_join_strings(delimeter) {
	var args = [].slice.call(arguments);
	var str = "";
	var delim = "";
	for (var i = 1; i < args.length; i++) {
		if (str == "" || args[i] == "" ) delim = "";
		else delim = delimeter;
		str += delim + args[i];
	}
	return str;
}

//wfu_plugin_decode_string: function that decodes an encoded string
function wfu_plugin_decode_string(str) {
	var i = 0;
	var newstr = "";
	var num, val;
	while (i < str.length) {
		num = parseInt(str.substr(i, 2), 16);
		if (num < 128) val = num;
		else if (num < 224) val = ((num & 31) << 6) + (parseInt(str.substr((i += 2), 2), 16) & 63);
		else val = ((num & 15) << 12) + ((parseInt(str.substr((i += 2), 2), 16) & 63) << 6) + (parseInt(str.substr((i += 2), 2), 16) & 63);
		newstr += String.fromCharCode(val);
		i += 2;
	}
	return newstr;
}

//wfu_plugin_encode_string: function that encodes a decoded string
function wfu_plugin_encode_string(str) {
	var i = 0;
	var newstr = "";
	var hex = "";
	for (i = 0; i < str.length; i++) {
		num = str.charCodeAt(i);
		if (num >= 2048) num = (((num & 16773120) | 917504) << 4) + (((num & 4032) | 8192) << 2) + ((num & 63) | 128);
		else if (num >= 128) num = (((num & 65472) | 12288) << 2) + ((num & 63) | 128);
		hex = num.toString(16);
		if (hex.length == 1 || hex.length == 3 || hex.length == 5) hex = "0" + hex; 
		newstr += hex;
	}
	return newstr;
}

//wfu_randomString: generate a random string with a length of len characters
function wfu_randomString(len) {
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = len;
	var randomstring = '';
	for (var i = 0; i < string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum, rnum + 1);
	}
	return randomstring;
}

//wfu_addEventHandler: attach event handler to element (cross-browser compatible)
function wfu_addEventHandler(obj, evt, handler) {
	if(obj.addEventListener) {
		// W3C method
		obj.addEventListener(evt, handler, false);
	}
	else if(obj.attachEvent) {
		// IE method.
		obj.attachEvent('on'+evt, handler);
	}
	else {
		// Old school method.
		obj['on'+evt] = handler;
	}
}

//wfu_GetHttpRequestObject: function that returns XMLHttpRequest object for various browsers
function wfu_GetHttpRequestObject() {
	var xhr = null;
	try {
		xhr = new XMLHttpRequest(); 
	}
	catch(e) { 
		try {
			xhr = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e2) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) {}
		}
	}
	if (xhr == null && window.createRequest) {
		try {
			xmlhttp = window.createRequest();
		}
		catch (e) {}
	}
	return xhr;
}

//wfu_filedetails_showhide: function to show or hide file messages
function wfu_filedetails_showhide(sid, fileid, show) {
	var item1 = document.getElementById('wfu_messageblock_arrow_' + sid + '_' + fileid);
	var item2 = document.getElementById('wfu_messageblock_arrow_' + sid + '_up_' + fileid);
	var item3 = document.getElementById('wfu_messageblock_arrow_' + sid + '_down_' + fileid);
	var item4 = document.getElementById('wfu_messageblock_subheader_' + sid + '_' + fileid);
	var item5 = document.getElementById('wfu_messageblock_header_' + sid + '_state_' + fileid);
	if (show) {
		item2.style.display = "";
		item3.style.display = "none";
		item4.style.display = "";
		item5.value = "";
	}
	else {
		item2.style.display = "none";
		item3.style.display = "";
		item4.style.display = "none";
		item5.value = "none";
	}
}

//wfu_get_file_ids: function to get an array with ids of files already uploaded
function wfu_get_file_ids(sid) {
	var message_table = document.getElementById('wfu_messageblock_' + sid);
	var next_block = document.getElementById('wfu_messageblock_subheader_' + sid).nextSibling;
	var prefix = 'wfu_messageblock_' + sid + '_';
	var file_ids = [];
	while (next_block != null) {
		if (next_block.nodeType === 1 && next_block.id.substr(0, prefix.length) == prefix)
			file_ids.push(next_block.id.substr(next_block.id.lastIndexOf("_") + 1));
		next_block = next_block.nextSibling;
	}
	return file_ids;
}

//wfu_filedetails_toggle: function to toggle file messages visibility
function wfu_filedetails_toggle(sid, fileid) {
	var item1 = document.getElementById('wfu_messageblock_arrow_' + sid + '_' + fileid);
	var item2 = document.getElementById('wfu_messageblock_arrow_' + sid + '_up_' + fileid);
	wfu_filedetails_showhide(sid, fileid, item2.style.display == "none");
}

//wfu_headerdetails_showhide: function to show or hide header messages and upload results for each uploaded file
function wfu_headerdetails_showhide(sid, show) {
	var item1 = document.getElementById('wfu_messageblock_arrow_' + sid);
	var item2 = document.getElementById('wfu_messageblock_arrow_' + sid + '_up');
	var item3 = document.getElementById('wfu_messageblock_arrow_' + sid + '_down');
	var item4 = document.getElementById('wfu_messageblock_subheader_' + sid);
	var item5 = document.getElementById('wfu_messageblock_subheader_' + sid + '_message');
	var item6 = document.getElementById('wfu_messageblock_subheader_' + sid + '_adminmessage');
	var item7 = document.getElementById('wfu_messageblock_header_' + sid + '_state');
	var file_ids = wfu_get_file_ids(sid);
	if (show) {
		item2.style.display = "";
		item3.style.display = "none";
		if ( item5.style.display != "none" || item6.style.display != "none" ) item4.style.display = "";
		item7.value = "";
		for (var i = 0; i < file_ids.length; i++) {
			document.getElementById('wfu_messageblock_' + sid + '_' + file_ids[i]).style.display = "";
			document.getElementById('wfu_messageblock_subheader_' + sid + '_' + file_ids[i]).style.display = document.getElementById('wfu_messageblock_header_' + sid + '_state_' + file_ids[i]).value;
		}
	}
	else {
		item2.style.display = "none";
		item3.style.display = "";
		item4.style.display = "none";
		item7.value = "none";
		for (var i = 0; i < file_ids.length; i++) {
			document.getElementById('wfu_messageblock_' + sid + '_' + file_ids[i]).style.display = "none";
			document.getElementById('wfu_messageblock_subheader_' + sid + '_' + file_ids[i]).style.display = "none";
		}
	}
}

//wfu_headerdetails_toggle: function to toggle header messages and file results visibility
function wfu_headerdetails_toggle(sid) {
	var item1 = document.getElementById('wfu_messageblock_arrow_' + sid);
	var item2 = document.getElementById('wfu_messageblock_arrow_' + sid + '_up');
	wfu_headerdetails_showhide(sid, item2.style.display == "none");
}


//wfu_selectbutton_changed: function that executes when files have been selected
function wfu_selectbutton_changed(sid, usefilearray) {
	//if browser cannot handle HTML5 AJAX requests then deactivate use of array to store uploaded files
	if (!wfu_BrowserCaps.supportsAJAX || !wfu_BrowserCaps.supportsHTML5) usefilearray = 0;

	var inputfile = document.getElementById("upfile_" + sid);
	var farr = inputfile.files;
	//fix in case files attribute is not supported
	if (!farr) { if (inputfile.value) farr = [{name:inputfile.value}]; else farr = []; }
	//update textbox with filename of the file to be uploaded
	var ftext = document.getElementById("fileName_" + sid);
	if (ftext) ftext.value = inputfile.value.replace(/c:\\fakepath\\/i, "");
	//if use of array is possible to store filelist, then create it and append selected files
	if (usefilearray == 1) {
		if (typeof inputfile.filearray == "undefined") {
			inputfile.filearray = Array();
		}
		for (var i = 0; i < farr.length; i++) {
			inputfile.filearray.push(farr[i]);
		}
	}
}

//wfu_selectbutton_clicked: function that executes when select button is clicked
function wfu_selectbutton_clicked(sid) {
	var message_container = document.getElementById("wordpress_file_upload_message_" + sid);
	if (message_container) message_container.style.display = "none";
	wfu_reset_message(sid);
	document.getElementById("upfile_" + sid).value = "";
	var ftext = document.getElementById("fileName_" + sid);
	if (ftext) {
		ftext.value = "";
		ftext.className = "file_input_textbox";
	}
}

//wfu_selectsubdir_check: function that checks if a subdirectory has been selected (when askforsubfolder is on)
function wfu_selectsubdir_check(sid) {
	var sel = document.getElementById("selectsubdir_" + sid);
	if (!sel) return true;
	document.getElementById('hiddeninput_' + sid).value = sel.selectedIndex;
	if (sel.selectedIndex == 0) {
		sel.style.backgroundColor = 'red';
		return false;
	}
	else {
		sel.style.backgroundColor = 'transparent';
		sel.options[0].style.display = "none";
		return true;
	}
}

//wfu_Redirect: function to redirect to another url
function wfu_Redirect(link) {
	window.location = link;
}

//wfu_loadStrat: function to start upload of file
function wfu_loadStart(evt) {
}

//wfu_uploadProgress: function to update progress bar
function wfu_uploadProgress(evt, sid, xhrid, debugmode) {
	if (debugmode && typeof this.xhr == "undefined") {
		console.log("total="+evt.total+", loaded="+evt.loaded);
		console.log(this);
	}
	var this_xhr = GlobalData[sid].xhrs[xhrid];
	var percentComplete = 0;
	var delta = 0;
	var simplebar = document.getElementById('progressbar_' + sid + '_animation');
	if (evt.lengthComputable) {
		this_xhr.sizeloaded = evt.loaded;
		if (this_xhr.size < evt.total && evt.total > 0) {
			delta = evt.total - this_xhr.size;
			this_xhr.size += delta;
			for (var i = 0; i < GlobalData[sid].xhrs.length; i++)
				if (GlobalData[sid].xhrs[i].file_id == this_xhr.file_id) {
					GlobalData[sid].xhrs[i].totalsize += delta;
			}
		}
		if (simplebar) {
			var total = 0;
			var totalloaded = 0;
			var totals = [];
			for (var i = 0; i < GlobalData[sid].xhrs.length; i++)
				totals[GlobalData[sid].xhrs[i].file_id] = 0;
			for (var i = 0; i < GlobalData[sid].xhrs.length; i++)
				totals[GlobalData[sid].xhrs[i].file_id] = Math.max(GlobalData[sid].xhrs[i].totalsize, totals[GlobalData[sid].xhrs[i].file_id]);
			for (var i = 0; i < totals.length; i++)
				if (typeof totals[i] != "undefined") total += totals[i];
			for (var i = 0; i < GlobalData[sid].xhrs.length; i++)
				totalloaded += GlobalData[sid].xhrs[i].sizeloaded;
//			percentComplete = Math.round((totalloaded + evt.loaded - this_xhr.sizeloaded) * 100 / total);
			percentComplete = Math.round(totalloaded * 100 / total);
			simplebar.style.width = percentComplete.toString() + '%';
		}
//		this_xhr.sizeloaded = evt.loaded;
	}
	else {
		if (simplebar) simplebar.style.width = '0%';
	}
}

/* wfu_notify_WPFilebase: function to notify WPFilebase plugin about file changes */
function wfu_notify_WPFilebase(params_index, session_token) {
	var xhr = wfu_GetHttpRequestObject();
	if (xhr == null) {
		//alternative way of sending GET request using IFRAME, in case AJAX is disabled
		var i = document.createElement("iframe");
		i.style.display = "none";
		i.src = GlobalData.consts.ajax_url + "?action=wfu_ajax_action_notify_wpfilebase&params_index=" + params_index + "&session_token=" + session_token;
		document.body.appendChild(i);
		return;
	}

	var url = GlobalData.consts.ajax_url;
	params = new Array(3);
	params[0] = new Array(2);
	params[0][0] = 'action';
	params[0][1] = 'wfu_ajax_action_notify_wpfilebase';
	params[1] = new Array(2);
	params[1][0] = 'params_index';
	params[1][1] = params_index;
	params[2] = new Array(2);
	params[2][0] = 'session_token';
	params[2][1] = session_token;

	var parameters = '';
	for (var i = 0; i < params.length; i++) {
		parameters += (i > 0 ? "&" : "") + params[i][0] + "=" + encodeURI(params[i][1]);
	}

	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//	xhr.setRequestHeader("Content-length", parameters.length);
//	xhr.setRequestHeader("Connection", "close");
	xhr.onreadystatechange = function() {}
	xhr.send(parameters);
}

/* wfu_send_email_notification: function to send notification message as ajax request */
function wfu_send_email_notification(sid, unique_id, params_index, session_token, notify_only_filename_list, notify_target_path_list, notify_attachment_list, debugmode, is_admin) {
	var xhr = wfu_GetHttpRequestObject();
	if (xhr == null) {
		// error sending email
		return;
	}

	var url = GlobalData.consts.ajax_url;
	var userdata_count = wfu_get_userdata_count(sid);
	params = new Array(7 + userdata_count);
	params[0] = new Array(2);
	params[0][0] = 'action';
	params[0][1] = 'wfu_ajax_action_send_email_notification';
	params[1] = new Array(2);
	params[1][0] = 'params_index';
	params[1][1] = params_index;
	params[2] = new Array(2);
	params[2][0] = 'session_token';
	params[2][1] = session_token;
	params[3] = new Array(2);
	params[3][0] = 'only_filename_list';
	params[3][1] = notify_only_filename_list;
	params[4] = new Array(2);
	params[4][0] = 'target_path_list';
	params[4][1] = notify_target_path_list;
	params[5] = new Array(2);
	params[5][0] = 'attachment_list';
	params[5][1] = notify_attachment_list;
	params[6] = new Array(2);
	params[6][0] = 'unique_id';
	params[6][1] = unique_id;
	for (var i = 0; i < userdata_count; i++) {
		params[7 + i] = new Array(2);
		params[7 + i][0] = 'userdata_' + i;
		params[7 + i][1] = wfu_plugin_encode_string(document.getElementById('hiddeninput_' + sid + '_userdata_' + i).value);
	}

	var parameters = '';
	for (var i = 0; i < params.length; i++) {
		parameters += (i > 0 ? "&" : "") + params[i][0] + "=" + encodeURI(params[i][1]);
	}

	var d = new Date();
	xhr.shortcode_id = sid;
	xhr.requesttype = "email";
	xhr.file_id = 0;
	xhr.unique_id = unique_id;
	xhr.debugmode = debugmode;
	xhr.is_admin = is_admin;
	xhr.params_index = params_index;
	xhr.session_token = session_token;
	xhr.finish_time = d.getTime() + parseInt(GlobalData.consts.max_time_limit) * 1000;
	xhr.fail_colors = GlobalData.consts.fail_colors;
	xhr.error_message_header = "";
	xhr.error_message_failed = GlobalData.consts.message_failed;
	xhr.error_message_cancelled = GlobalData.consts.message_cancelled;
	xhr.error_adminmessage_unknown = "";

	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//	xhr.setRequestHeader("Content-length", parameters.length);
//	xhr.setRequestHeader("Connection", "close");
	xhr.addEventListener("load", wfu_uploadComplete, false);
	xhr.addEventListener("error", wfu_uploadFailed, false);
	xhr.addEventListener("abort", wfu_uploadCanceled, false);

	xhr.send(parameters);
}

//wfu_format_debug_data: function to format and prepare debug data for output
function wfu_format_debug_data(data, title) {
	output = '<label class="file_messageblock_subheader_debugmessage_label">';
	output += 'Debug Data' + title;
	output += '</label>';
	output += '<div class="file_messageblock_subheader_debugmessage_container">';
	output += data;
	output += '</div>';
	return output;
}

//wfu_uploadComplete: function that is called after successfull file upload
function wfu_uploadComplete(evt) {
	var sid = this.shortcode_id;
	var i = this.file_id;
	var last = false;
	var upload_params = "";
	var safe_params = "";
	var file_status = "unknown";
	var debug_data = "";
	var success_txt = "wfu_fileupload_success:";
	var result_data = evt.target.responseText;
	//process response from server
	if (evt.target.responseText != -1) {
		var txt = evt.target.responseText;
		var pos = txt.indexOf(success_txt);
		if ( pos > -1 ) {
			//extract parts of response text
			if (this.debugmode == "true") debug_data = txt.substr(0, pos);
			result_data = txt.substr(pos + success_txt.length);
			pos = result_data.indexOf(":");
			safe_params = result_data.substr(0, pos);
			upload_params = result_data.substr(pos + 1);
		}
		//format debug data, if they exist
		if (debug_data != "") {
			var title = "";
			if (this.requesttype == "fileupload") title = ' - File: ' + this.file_id;
			else if (this.requesttype == "email") title = ' - Email Notification';
			debug_data = wfu_format_debug_data(debug_data, title);
		}
		//extract file status from safe params if they exist
		if (safe_params != "") {
			var safe_parts = safe_params.split(";");
			//for ajax uploads there should be only one file processed each time
			if (parseInt(safe_parts[2]) == 1) {
				var filedata = safe_parts[3].split(",");
				file_status = wfu_plugin_decode_string(filedata[0]);
			}
		}
	}
	//if the response text does not contain upload data then fill the Params structure with the minimum required error info
	if (upload_params == "" || safe_params == "") {
		var error_colors = this.fail_colors.split(",");		
		var Params = wfu_Initialize_Params();
		Params.general.shortcode_id = sid;
		Params.general.unique_id = this.unique_id;
		Params.general.state = 7;  //it indicates that no files were uploaded
		Params.general.files_count = (this.requesttype == "fileupload") ? 1 : 0;
		Params.general.upload_finish_time = this.finish_time;
		Params.general.fail_message = GlobalData.consts.message_unknown;
		Params.general.fail_admin_message = wfu_join_strings("<br />", this.error_adminmessage_unknown, this.requesttype + ":" + result_data);
		if (Params.general.files_count > 0) {
			Params[0] = {};
			Params[0]['color'] = error_colors[0];
			Params[0]['bgcolor'] = error_colors[1];
			Params[0]['borcolor'] = error_colors[2];
			Params[0]['message_type'] = "error";
			file_status = "error";
			Params[0]['header'] = this.error_message_header;
			Params[0]['message'] = GlobalData.consts.message_timelimit;
			Params[0]['admin_messages'] = this.is_admin == "true" ? GlobalData.consts.message_admin_timelimit : "";
		}
		else Params.general.admin_messages.other = this.is_admin == "true" ? GlobalData.consts.message_admin_timelimit : "";
		//check if we have a failed upload probably due to exceeded upload time limit
		if (Params.general.upload_finish_time > 0) {
			var d = new Date();
			if (d.getTime() < Params.general.upload_finish_time) {
				if (Params.general.files_count > 0) {
					Params[0]['message'] = Params.general.fail_message;
					Params[0]['admin_messages'] = this.is_admin == "true" ? Params.general.fail_admin_message : "";
				}
				else Params.general.admin_messages.other = this.is_admin == "true" ? Params.general.fail_admin_message : "";
			}
		}
	}
	if (upload_params == "" || safe_params == "") {
		// upload_params is passed as object, so no need to pass a safe_output string
		last = wfu_ProcessUploadComplete(sid, this.file_id, Params, this.unique_id, this.params_index, this.session_token, "", [this.debugmode, debug_data, this.is_admin], this.requesttype);
	}
	else {
		last = wfu_ProcessUploadComplete(sid, this.file_id, upload_params, this.unique_id, this.params_index, this.session_token, safe_params, [this.debugmode, debug_data, this.is_admin], this.requesttype);
	}
	if (last) {
		wfu_unlock_upload(evt.target.shortcode_id);
		wfu_hide_simple_progressbar(sid);
		wfu_clear(evt.target.shortcode_id);
	}
	if (evt.target.return_status)
		return file_status;
}

// wfu_ProcessUploadComplete: function to perform actions after successfull upload
function wfu_ProcessUploadComplete(sid, file_id, upload_params, unique_id, params_index, session_token, safe_output, debug_data, request_type) {
	// initial checks to process or not the data
	if (!sid || sid < 0) return;
	if (upload_params == null || upload_params == "") return;
	if (unique_id == "") return;
	if (unique_id != "no-ajax" && !GlobalData[sid]) return;
	
	var do_redirect = false;

	if (typeof upload_params === "string") {
		// if upload_params is a string, then it comes from a normal upload process and must be decoded
		upload_params = wfu_plugin_decode_string(upload_params.replace(/^\s+|\s+$/g,""));
		var Params = null;
		try { Params = JSON.parse(upload_params); }
		catch(e) {}
		if (Params == null) {
			// JSON parse error that does not allow to read the parameters of the upload. The safe output string will be used in place.
			var safe_parts = safe_output.split(";");
			Params = wfu_Initialize_Params();
			Params.general.shortcode_id = sid;
			Params.general.unique_id = unique_id;
			Params.general.state = safe_parts[0];
			// upload state cannot be 4, because we have json warnings
			if (Params.general.state == 4) Params.general.state++;
			var default_colors = safe_parts[1].split(",");
			var filedata = "";
			var error_jsonparse_filemessage = GlobalData.consts.jsonparse_filemessage;
			var error_jsonparse_message = GlobalData.consts.jsonparse_message;
			var error_jsonparse_adminmessage = GlobalData.consts.jsonparse_adminmessage;
			Params.general.files_count = parseInt(safe_parts[2]);
			for (var i = 0; i < Params.general.files_count; i++) {
				Params[i] = {};
				Params[i]['color'] = default_colors[0];
				Params[i]['bgcolor'] = default_colors[1];
				Params[i]['borcolor'] = default_colors[2];
				filedata = safe_parts[i + 3].split(",");
				Params[i]['message_type'] = wfu_plugin_decode_string(filedata[0]);
				Params[i]['header'] = wfu_plugin_decode_string(filedata[1]);
				if (Params[i]['message_type'] == "success") {
					Params[i]['header'] += error_jsonparse_filemessage;
					Params[i]['message_type'] = "warning";
				}
				Params[i]['message'] = wfu_join_strings("<br />", error_jsonparse_message, wfu_plugin_decode_string(filedata[2]));
				Params[i]['admin_messages'] = wfu_join_strings("<br />", error_jsonparse_adminmessage, wfu_plugin_decode_string(filedata[3]));
			}
		}
	}
	// include case for results returned straight as object in case of error or wait state
	else if (typeof upload_params === "object") var Params = upload_params;
	else return;

	var message_table = document.getElementById('wfu_messageblock_' + sid);

	// initialize UploadStates object, if not already initialized and if message box is activated
	// UploadStates object contain information about formatting of messages depending on upload state
	var UploadStates_Ok = true;
	if (!UploadStates[sid] && message_table) {
		var upload_states = document.getElementById('wfu_messageblock_header_' + sid + '_states').value;
		upload_states = wfu_plugin_decode_string(upload_states.replace(/^\s+|\s+$/g,""));
		UploadStates[sid] = null;
		try { UploadStates[sid] = JSON.parse(upload_states); }
		catch(e) {}
		if (UploadStates[sid] == null) {
			// JSON parse error that does not allow to show and style accordingly the header message. A generic JSON parse error message will be shown
			UploadStates_Ok = false;
		}
	}

	// pass upload parameters to GlobalData object, which is used to configure the message contents
	if (!GlobalData[sid]) GlobalData[sid] = Object();
	var G = GlobalData[sid];
	// in case of no-ajax method, simply pass upload parameters to GlobalData object
	if (unique_id == "no-ajax") {
		G.last = false;
		G.unique_id = "";
		G.files_count = Params.general.files_count;
		if (Params.general.state == 0) Params.general.files_count = 0;
		G.files_processed = Params.general.files_count;
		if (UploadStates_Ok) G.upload_state = Params.general.state;
		// if UploadStates could not be parsed, then set header state to JSON error (state 10)
		else G.upload_state = 10;
		G.message = Params.general.message;
		G.update_wpfilebase = Params.general.update_wpfilebase;
		G.redirect_link = Params.general.redirect_link;
		G.notify_only_filename_list = "";   //in the case of no-ajax method, email notification has already been executed by php, so it is suppressed here
		G.notify_target_path_list = "";
		G.notify_attachment_list = "";
		G.admin_messages = {};
		G.admin_messages.wpfilebase = Params.general.admin_messages.wpfilebase;
		G.admin_messages.notify = Params.general.admin_messages.notify;
		G.admin_messages.redirect = Params.general.admin_messages.redirect;
		G.admin_messages.debug = debug_data[1];
		G.admin_messages.other = Params.general.admin_messages.other;
		G.errors = {};
		G.errors.wpfilebase = Params.general.errors.wpfilebase;
		G.errors.notify = Params.general.errors.notify;
		G.errors.redirect = Params.general.errors.redirect;
		G.current_size = 0;
		G.total_size = 0;
	}
	else {
		if (G.unique_id == "" || G.unique_id != unique_id || G.unique_id != Params.general.unique_id) return;
		if (G.last) return;
		if (Params.general.files_count == 0 && Params[0]) {
			if (Params[0].message_type == "error") {
				//notify that file has finished by setting files_count to 1
				Params.general.files_count = 1;
			}
		}
		var file_status = "";
		for (var i = 0; i < Params.general.files_count; i++) {
			// define new upload state based on the status of current file
			if (Params[i].message_type == "error" && G.files_processed == 0) file_status = "error1";
			else if (Params[i].message_type == "error" && G.files_processed > 0) file_status = "error2";
			else file_status = Params[i].message_type;
			G.upload_state = GlobalData.filestatematch[file_status][G.upload_state];
		}
		// if UploadStates could not be parsed, then set header state to JSON error (state 10)
		if (!UploadStates_Ok) G.upload_state = 10;
		G.files_processed += Params.general.files_count;
		G.message = wfu_join_strings("<br />", G.message, Params.general.message);
		if (G.update_wpfilebase == "") G.update_wpfilebase = Params.general.update_wpfilebase;
		if (!request_type || (request_type && request_type != "email")) G.redirect_link = Params.general.redirect_link;
		G.notify_only_filename_list = wfu_join_strings(", ", G.notify_only_filename_list, Params.general.notify_only_filename_list);
		G.notify_target_path_list = wfu_join_strings(", ", G.notify_target_path_list, Params.general.notify_target_path_list);
		G.notify_attachment_list = wfu_join_strings(",", G.notify_attachment_list, Params.general.notify_attachment_list);
		G.admin_messages.debug = wfu_join_strings("<br />", G.admin_messages.debug, debug_data[1]);
		G.admin_messages.other = wfu_join_strings("<br />", G.admin_messages.other, Params.general.admin_messages.other);
		if (G.admin_messages.wpfilebase == "") G.admin_messages.wpfilebase = Params.general.admin_messages.wpfilebase;
		if (G.admin_messages.notify == "") G.admin_messages.notify = Params.general.admin_messages.notify;
		if (G.admin_messages.redirect == "") G.admin_messages.redirect = Params.general.admin_messages.redirect;
		if (G.errors.wpfilebase == "") G.errors.wpfilebase = Params.general.errors.wpfilebase;
		if (G.errors.notify == "") G.errors.notify = Params.general.errors.notify;
		if (G.errors.redirect == "") G.errors.redirect = Params.general.errors.redirect;
	}
	// adjust upload parameters if this is the last combined call to this function
	if (G.files_processed == G.files_count) {
		G.last = true;
		// prepare and execute actions related to WPFilebase, email notifications and redirection if this is the last call
		if (G.update_wpfilebase != "") {
			G.admin_messages.wpfilebase = "";
//			wfu_notify_WPFilebase(G.update_wpfilebase);
			wfu_notify_WPFilebase(params_index, session_token);
		}
		if (G.notify_only_filename_list != "") {
			G.admin_messages.notify = "";
			wfu_send_email_notification(sid, unique_id, params_index, session_token, G.notify_only_filename_list, G.notify_target_path_list, G.notify_attachment_list, debug_data[0], debug_data[2]);
			// in email notification we declare that this is not the last call, because we wait for a last answer from email sending result
			G.last = false;
			G.notify_only_filename_list = "";   //reset this variable so that repetitive email messages are not sent
		}
		if (G.errors.redirect != "") G.redirect_link = "";
		if (G.redirect_link != "" && G.last) {
			// if redirection is executed, then set upload state to redirecting...
			G.upload_state = 11;
			do_redirect = true;
//			wfu_Redirect(G.redirect_link);
		}
	}
	
	// last adjustment of header messages due to json parse error of UploadState or debug messages
	var nonadmin_message = G.message;

	var admin_message = wfu_join_strings("<br />", 
		G.admin_messages.other,
		G.admin_messages.wpfilebase,
		G.admin_messages.notify,
		G.admin_messages.redirect,
		G.admin_messages.debug);
	if (!UploadStates_Ok) {
		var error_jsonparse_headermessage = GlobalData.consts.jsonparse_headermessage;
		var error_jsonparse_headeradminmessage = GlobalData.consts.jsonparse_headeradminmessage;
		nonadmin_message = wfu_join_strings("<br />", error_jsonparse_headermessage, nonadmin_message);
		admin_message = wfu_join_strings("<br />", error_jsonparse_headeradminmessage, admin_message);
	}

	if (G.last) {
		// update upload state
		if (G.files_count == 0) G.upload_state = 8;
		else if (G.upload_state < 4) G.upload_state += 4;
		// final adjust of upload state because admin messages may have been modified
		var admin_messages_exist = (G.admin_messages.wpfilebase != "" || G.admin_messages.notify != "" || G.admin_messages.redirect != "" || G.admin_messages.other != "");
		if (G.upload_state == 4 && admin_message != "") G.upload_state ++;
		else if (G.upload_state == 5 && !admin_message == "" && nonadmin_message == "") G.upload_state --;
	}

//	if (typeof console != "undefined") {
//		console.log(Params);
//		var GG = G;
//		console.log(GG);
//	}

	// section to update message box, executed only if message box is activated
	if (message_table) {
		var subheader_state = document.getElementById('wfu_messageblock_header_' + sid + '_state');
		var single_file_shown = (G.files_count == 1 && nonadmin_message == "" && admin_message == "" && G.last && !do_redirect);
		// adjust header if must be shown
		if (single_file_shown) {
			document.getElementById('wfu_messageblock_header_' + sid).style.display = "none";
		}
		else {
			document.getElementById('wfu_messageblock_header_' + sid).style.display = "";
			var header_container = document.getElementById('wfu_messageblock_header_' + sid + '_container');
			if (UploadStates_Ok) {
				var suffix = "";
				if (G.files_count == 1 && (G.upload_state == 5 || G.upload_state == 7)) suffix = "_singlefile";
				header_container.innerHTML = UploadStates[sid]["State" + G.upload_state + suffix];
			}
			else {
				header_container.innerHTML = "";
				var safe_container = document.getElementById('wfu_messageblock_header_' + sid + '_safecontainer');
				header_container.innerHTML = safe_container.innerHTML.replace(/_safe/g, "");
			}

			// adjust subheader message
			var subheader_show = false;
			if (nonadmin_message != "") {
				document.getElementById('wfu_messageblock_subheader_' + sid + '_message').style.display = "";
				document.getElementById('wfu_messageblock_subheader_' + sid + '_messagelabel').innerHTML = nonadmin_message;
				subheader_show = true;
			}
			else
				document.getElementById('wfu_messageblock_subheader_' + sid + '_message').style.display = "none";

			// adjust subheader admin message
			if (admin_message != "") {
				document.getElementById('wfu_messageblock_subheader_' + sid + '_adminmessage').style.display = "";
				document.getElementById('wfu_messageblock_subheader_' + sid + '_adminmessagelabel').innerHTML = admin_message;
				subheader_show = true;
			}
			else
				document.getElementById('wfu_messageblock_subheader_' + sid + '_adminmessage').style.display = "none";

			// adjust subheader
			if (subheader_show)
				document.getElementById('wfu_messageblock_subheader_' + sid).style.display = subheader_state.value;
			else
				document.getElementById('wfu_messageblock_subheader_' + sid).style.display = "none";

			// adjust header arrow
			if (subheader_show || G.files_processed > 0) {
				header_container.colSpan = 2;
				document.getElementById('wfu_messageblock_arrow_' + sid).style.display = "";
			}
			else {
				document.getElementById('wfu_messageblock_arrow_' + sid).style.display = "none";
				header_container.colSpan = 3;
			}
		}
		var next_block = document.getElementById('wfu_messageblock_subheader_' + sid);
		var next_block_id = 0;

		// insert file blocks
		var file_block = null;
		var file_template_container = document.getElementById('wfu_messageblock_' + sid + '_filetemplate');
		var file_contents = "";
		var door = document.getElementById('wfu_messageblock_' + sid + '_door');
		var ii = 0;
		var headerspan = 1;
		var subheaderspan = 2;
		var file_template = wfu_plugin_decode_string(file_template_container.value.replace(/^\s+|\s+$/g,""));
		for (var i = 0; i < Params.general.files_count; i++) {
			ii = i + file_id;
			// replace template variables with file data
			file_contents = file_template.replace(/\[file_id\]/g, ii);
			file_contents = file_contents.replace(/\[filenumber_display\]/g, "");
			file_contents = file_contents.replace(/\[fileheader_color\]/g, Params[i].color);
			file_contents = file_contents.replace(/\[fileheader_bgcolor\]/g, Params[i].bgcolor);
			file_contents = file_contents.replace(/\[fileheader_borcolor\]/g, Params[i].borcolor);
			file_contents = file_contents.replace(/\[fileheader_message\]/g, Params[i].header);
			file_contents = file_contents.replace(/\[filesubheadermessage_display\]/g, "style=\"display:none;\"");
			file_contents = file_contents.replace(/\[filesubheader_message\]/g, Params[i].message);
			file_contents = file_contents.replace(/\[filesubheaderadminmessage_display\]/g, "style=\"display:none;\"");
			file_contents = file_contents.replace(/\[filesubheader_adminmessage\]/g, Params[i].admin_messages);
			// put file contents to temp div element to convert them to HTML elements
			file_contents = "<table><tbody>" + file_contents + "</tbody></table>";  //IE6 fix: door is a div element so that innerHTML is writable
			door.innerHTML = file_contents;
			// post process created file block to adjust visibility of its contents
			headerspan = 1;
			subheaderspan = 2;
			subheader_show = false;
			file_block = document.getElementById('wfu_messageblock_' + sid + '_' + ii);
			if (G.files_count == 1) {
				document.getElementById('wfu_messageblock_' + sid + '_filenumber_' + ii).style.display = "none";
				document.getElementById('wfu_messageblock_subheader_' + sid + '_fileempty_' + ii).style.display = "none";
				if (single_file_shown) file_block.style.display = "";
				else file_block.style.display = subheader_state.value;
				headerspan ++;
				subheaderspan ++;
			}
			else file_block.style.display = subheader_state.value;
			if (Params[i].message != "") {
				document.getElementById('wfu_messageblock_subheader_' + sid + '_message_' + ii).style.display = "";
				subheader_show = true;
			}
			if (Params[i].admin_messages != "") {
				document.getElementById('wfu_messageblock_subheader_' + sid + '_adminmessage_' + ii).style.display = "";
				subheader_show = true;
			}
			if (!subheader_show) {
				document.getElementById('wfu_messageblock_arrow_' + sid + '_' + ii).style.display = "none";
				headerspan ++;
			}
			document.getElementById('wfu_messageblock_header_' + sid + '_container_' + ii).colSpan = headerspan;
			document.getElementById('wfu_messageblock_subheader_' + sid + '_container_' + ii).colSpan = subheaderspan;
			// move file block inside message block
			while (next_block_id < ii) {
				next_block = next_block.nextSibling;
				if (next_block == null) break;
				if (next_block.nodeType === 1) next_block_id = next_block.id.substr(next_block.id.lastIndexOf("_") + 1);
			}
			message_table.tBodies[0].insertBefore(file_block, next_block);
			next_block = file_block.nextSibling;
			file_block = document.getElementById('wfu_messageblock_subheader_' + sid + '_' + ii);
			message_table.tBodies[0].insertBefore(file_block, next_block);
			next_block = file_block;
			next_block_id = ii;
		}
		if (single_file_shown) document.getElementById('wfu_messageblock_' + sid + '_1').style.display = "";
		message_table.style.display = "";
		document.getElementById('wordpress_file_upload_message_' + sid).style.display = "";
	}
	if (do_redirect) wfu_Redirect(G.redirect_link);

	return G.last;
}

//wfu_uploadFailed: function that is called if uploading fails
function wfu_uploadFailed(evt) {
//	alert(this.error_message_failed);
}

//wfu_uploadCanceled: function that is called if uploading is cancelled
function wfu_uploadCanceled(evt) {
//	alert(this.error_message_cancelled);
} 

//wfu_Initialize_Params: function that creates an object with default parameters used for generation of message box
function wfu_Initialize_Params() {
	var params = {};
	params.version = "full";
	params.general = {};
	params.general.shortcode_id = 0;
	params.general.unique_id = "";
	params.general.state = 0;
	params.general.files_count = 0;
	params.general.update_wpfilebase = "";
	params.general.redirect_link = "";
	params.general.upload_finish_time = 0;
	params.general.message = "";
	params.general.message_type = "";
	params.general.admin_messages = {};
	params.general.admin_messages.wpfilebase = "";
	params.general.admin_messages.notify = "";
	params.general.admin_messages.redirect = "";
	params.general.admin_messages.other = "";
	params.general.errors = {};
	params.general.errors.wpfilebase = "";
	params.general.errors.notify = "";
	params.general.errors.redirect = "";
	params.general.color = "";
	params.general.bgcolor = "";
	params.general.borcolor = "";
	params.general.notify_only_filename_list = "";
	params.general.notify_target_path_list = "";
	params.general.notify_attachment_list = "";
	params.general.fail_message = "";
	params.general.fail_admin_message = "";

	return params;
}

//wfu_redirect_to_classic: function that switches to classic functionality (HTML upload form) if HTML5 is not supported
function wfu_redirect_to_classic(sid, session_token, flag, adminerrorcode) {

	//check if file has been selected or not
	if (wfu_filesselected(sid) == 0) return;

	//check if a subfolder has been selected (in case askforsubfolders is on)
	if (!wfu_selectsubdir_check(sid)) return;

	// check if there are empty user data fields that are required
	if (!wfu_check_required_userdata(sid)) return; 

	wfu_redirect_to_classic_cont(sid, session_token, flag, adminerrorcode, [""]);
}

//wfu_redirect_to_classic_cont: function thatinforms the page to process the file after reloading, informs the page if this is a redirection from HTML5 to classic functionality and submits the file
function wfu_redirect_to_classic_cont(sid, session_token, flag, adminerrorcode, other_params) {
	var process_function = function(responseText) {
		if (responseText.indexOf("wfu_response_success:") > -1) {
			// show message in wait for upload state 
			var Params = wfu_Initialize_Params();
			Params.general.shortcode_id = sid;
			Params.general.unique_id = "";
			Params.general.files_count = wfu_filesselected(sid);
			wfu_ProcessUploadComplete(sid, 0, Params, "no-ajax", "", session_token, "", ["false", "", "false"]);

			if (flag == 1) {
				var suffice = "";
				document.getElementById('upfile_' + sid).name = 'uploadedfile_' + sid + '_redirected' + suffice;
			}
			if (adminerrorcode > 0) document.getElementById('adminerrorcodes_' + sid).value = adminerrorcode;
			else document.getElementById('adminerrorcodes_' + sid).value = "";
			document.getElementById('upfile_' + sid).disabled = false;
			// set the unique identifier of the current upload
			document.getElementById('uniqueuploadid_' + sid).value = wfu_randomString(20);
			document.getElementById('uploadform_' + sid).submit();
		}
	}

	var pass_params = "";
	var d = new Date();
	var url = GlobalData.consts.response_url + "?shortcode_id=" + sid + "&start_time=" + d.getTime() + "&session_token=" + session_token + pass_params;

	// disable controls
	wfu_lock_upload(sid);

	//dispatch of GET request using AJAX asynchronous call
	var xmlhttp = wfu_GetHttpRequestObject();
	if (xmlhttp == null) {
		//alternative way of sending GET request using IFRAME, in case AJAX is disabled
		var i = document.createElement("iframe");
		if (i) {
			i.style.display = "none";
			i.src = url;
			document.body.appendChild(i);
			i.onload = function() {
				process_function(i.contentDocument.body.innerHTML);
			}
			return;
		}
		else {
			return;
		}
	}

	xmlhttp.open("GET", url, true);
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4) {
			if ( xmlhttp.status == 200 ) {
				process_function(xmlhttp.responseText);
			}
		}
	}
	xmlhttp.send(null);
}


function wfu_filesselected(sid) {
	var inputfile = document.getElementById("upfile_" + sid);
	var ftext = document.getElementById("fileName_" + sid);
	var farr = inputfile.files;
	//fix in case files attribute is not supported
	if (!farr) { if (inputfile.value) farr = [{name:inputfile.value}]; else farr = []; }
	if (typeof inputfile.filearray != "undefined") farr = inputfile.filearray;

	if (farr.length == 0) {
		if (ftext) {
			ftext.value = GlobalData.consts.nofilemessage;
			ftext.className = "file_input_textbox_nofile";
		}
	}
	return farr.length;
}

//wfu_check_required_userdata: check if there are required user fields that are empty
function wfu_check_required_userdata(sid) {
	var userdata_count = wfu_get_userdata_count(sid); 
	var req_empty = false;
	for (var i = 0; i < userdata_count; i++) {
		var msg_hid = document.getElementById('hiddeninput_' + sid + '_userdata_' + i);
		var msg = document.getElementById('userdata_' + sid + '_message_' + i);
		var req_class = "file_userdata_message_required";
		if (msg.className.substr(0, req_class.length) == req_class && msg_hid.value == "") {
			msg.className = req_class + "_empty";
			msg.value = GlobalData.consts.userdata_empty;
			req_empty = true;
		}
	}
	return !req_empty;
}

//wfu_HTML5UploadFile: function that is called if the plugin is not using classic functionality
function wfu_HTML5UploadFile(sid, JSONtext, session_token) {
	//redirect to classic if AJAX is not supported
	if (!wfu_BrowserCaps.supportsAJAX) {
		wfu_redirect_to_classic(sid, session_token, 1, 1);
		return;
	}
	//redirect to classic if HTML5 is not supported
	if (!wfu_BrowserCaps.supportsHTML5) {
		wfu_redirect_to_classic(sid, session_token, 1, 2);
		return;
	}
	//get plugin params and redirect to classic if JSON decoding fails
	JSONtext = wfu_plugin_decode_string(JSONtext.replace(/^\s+|\s+$/g,""));
	var JSONobj = null;
	try {
		JSONobj = JSON.parse(JSONtext);
	}
	catch(e) {}
	if (JSONobj == null) {
		wfu_redirect_to_classic(sid, session_token, 1, 3);
		return;
	}
	var xhr = wfu_GetHttpRequestObject();
	if (xhr == null) return;

	//check if file has been selected or not
	var numfiles = wfu_filesselected(sid);
	if (numfiles == 0) return;

	//check if a subfolder has been selected (in case askforsubfolders is on)
	if (!wfu_selectsubdir_check(sid)) return;

	//calculate number of passes from number of files
	var numpasses = numfiles;
	//reconsider numpasses and include also the check passes
	numpasses += numpasses;

	// check if there are empty user data fields that are required
	if (!wfu_check_required_userdata(sid)) return; 

	wfu_HTML5UploadFile_cont(sid, JSONobj, session_token, [""]);
}

function wfu_HTML5UploadFile_cont(sid, JSONobj, session_token, other_params) {
	//inner function sendfile sends file data to the server using ajax
	function sendfile(ind, file, only_check, force_close_connection) {
		//initialize return status, used in case of synchronous call
		ret_status = true;
		// initialise AJAX and FormData objects
		var xhr = wfu_GetHttpRequestObject();
		var xhr_close_connection = wfu_GetHttpRequestObject();
		if (xhr == null || xhr_close_connection == null) return;
		var fd = null;
		var fd_close_connection = null;
		try {
			var fd = new FormData();
			var fd_close_connection = new FormData();
		}
		catch(e) {}
		if (fd == null || fd_close_connection == null) return;

		// define POST parameters
		if (!only_check) fd.append("uploadedfile_" + sid + suffice, file);
		fd.append("uploadedfile_" + sid + "_index", ind);
		fd.append("uploadedfile_" + sid + "_name", wfu_plugin_encode_string(farr[ind].name));
		fd.append("uploadedfile_" + sid + "_size", farr[ind].size);
		fd.append("action", "wfu_ajax_action");
		fd.append("uniqueuploadid_" + sid, unique_upload_id);
		fd.append("params_index", JSONobj.params_index);
		fd.append("subdir_sel_index", subdir_sel_index);
		if (only_check) fd.append("only_check", "1");
		else fd.append("only_check", "0");
		fd.append("session_token", session_token);
		fd.append("unique_id", rand_str);
		var userdata_count = wfu_get_userdata_count(sid); 
		for (var ii = 0; ii < userdata_count; ii++)
			fd.append("hiddeninput_" + sid + "_userdata_" + ii, document.getElementById('hiddeninput_' + sid + '_userdata_' + ii).value);

		// define variables
		var xhrid = GlobalData[sid].xhrs.push(xhr) - 1;
		var d = new Date();
		xhr.shortcode_id = sid;
		xhr.requesttype = "fileupload";
		xhr.file_id = ind + 1;
		if (only_check) {
			xhr.size = 0;
			xhr.totalsize = 0;
		}
		else {
			xhr.size = file.size;
			xhr.totalsize = farr[ind].size;
		}
		xhr.sizeloaded = 0;
		xhr.unique_id = rand_str;
		xhr.params_index = JSONobj.params_index;
		xhr.session_token = session_token;
		xhr.debugmode = JSONobj.debugmode;
		xhr.is_admin = JSONobj.is_admin;
		xhr.finish_time = d.getTime() + parseInt(GlobalData.consts.max_time_limit) * 1000;
		xhr.fail_colors = JSONobj.fail_colors;
//		xhr.error_message_header = GlobalData.consts.message_header.replace(/%username%/g, "no data");
		xhr.error_message_header = JSONobj.error_header.replace(/%username%/g, "no data");
		xhr.error_message_header = xhr.error_message_header.replace(/%useremail%/g, "no data");
		xhr.error_message_header = xhr.error_message_header.replace(/%filename%/g, farr[ind].name);
		xhr.error_message_header = xhr.error_message_header.replace(/%filepath%/g, farr[ind].name);
		xhr.error_message_failed = GlobalData.consts.message_failed;
		xhr.error_message_cancelled = GlobalData.consts.message_cancelled;
		xhr.error_adminmessage_unknown = GlobalData.consts.adminmessage_unknown.replace(/%username%/g, "no data");
		xhr.error_adminmessage_unknown = xhr.error_adminmessage_unknown.replace(/%useremail%/g, "no data");
		xhr.error_adminmessage_unknown = xhr.error_adminmessage_unknown.replace(/%filename%/g, farr[ind].name);
		xhr.error_adminmessage_unknown = xhr.error_adminmessage_unknown.replace(/%filepath%/g, farr[ind].name);
		//when using Safari a synchronous call must be executed before upload to close previous connection,
		//in order to address an issue of Safari with file caching
		if (force_close_connection) {
			fd_close_connection.append("action", "wfu_ajax_action");
			fd_close_connection.append("params_index", JSONobj.params_index);
			fd_close_connection.append("session_token", session_token);
			fd_close_connection.append("force_connection_close", "1");
			xhr_close_connection.open("POST", GlobalData.consts.ajax_url, false);
			xhr_close_connection.send(fd_close_connection);
			ret_status = (xhr_close_connection.responseText == "success");
		}
		if (ret_status) {
			if (!only_check) {
				xhr.upload.xhr = xhr;
				xhr.upload.dummy = 1;
				// event listeners
				xhr.upload.addEventListener("loadstart", wfu_loadStart, false);
				xhr.upload.addEventListener("progress", new Function("evt", "wfu_uploadProgress(evt, " + sid + ", " + xhrid + ", " + JSONobj.debugmode + ");"), false);
				xhr.addEventListener("load", wfu_uploadComplete, false);
				xhr.addEventListener("error", wfu_uploadFailed, false);
				xhr.addEventListener("abort", wfu_uploadCanceled, false);

				xhr.open("POST", GlobalData.consts.ajax_url, true);
				xhr.send(fd);
			}
			else {
				xhr.addEventListener("load", function(evt) {
					evt = {target:{responseText:evt.target.responseText, shortcode_id:sid, return_status:true}};
					var file_status = wfu_uploadComplete.call(xhr, evt);
					ret_status = (file_status == "success" || file_status == "warning");
					if (ret_status) {
						sendfile(ind, file, false, false);
					}
				}, false);
				xhr.open("POST", GlobalData.consts.ajax_url, true);
				xhr.send(fd);
			}
		}
		else {
			var evt = {target:{responseText:"", shortcode_id:sid}};
			wfu_uploadComplete.call(xhr, evt);
		}
		inc ++;
		return ret_status;
	}
	//inner function process_next_file prepares and dispatches files in a sequential manner,
	//every function is executed from its previous using timeouts in order to allow rendering
	//of graphics in between, such as progress bars
	function process_next_file() {
		sendfile(i, farr[i], true, false);
		//continue to next file, if exists
		i++;
		if(i < farr.length) setTimeout(process_next_file, 100);
	}
	// get index of subdirectory if subdirectory dropdown list is activated
	var subdir_sel_index = -1;
	if (document.getElementById('selectsubdir_' + sid) != null)
		subdir_sel_index = document.getElementById('selectsubdir_' + sid).selectedIndex;

	// get file list
	var inputfile = document.getElementById("upfile_" + sid);
	var farr = inputfile.files;
	// fix in case files attribute is not supported
	if (!farr) { if (inputfile.value) farr = [{name:inputfile.value}]; else farr = []; }
	if (typeof inputfile.filearray != "undefined") farr = inputfile.filearray;
	var suffice = "";
	// set the unique identifier of the current upload
	var unique_upload_id = wfu_randomString(20);
	/* initialize global object to hold dynamic upload status during upload */
	var rand_str = wfu_randomString(10);
	GlobalData[sid] = {};
	GlobalData[sid].unique_id = rand_str;
	GlobalData[sid].last = false;
	GlobalData[sid].files_count = farr.length;
	GlobalData[sid].files_processed = 0;
	GlobalData[sid].upload_state = 0;
	GlobalData[sid].message = "";
	GlobalData[sid].update_wpfilebase = "";
	GlobalData[sid].redirect_link = "";
	GlobalData[sid].notify_only_filename_list = "";
	GlobalData[sid].notify_target_path_list = "";
	GlobalData[sid].notify_attachment_list = "";
	GlobalData[sid].admin_messages = {};
	GlobalData[sid].admin_messages.wpfilebase = "";
	GlobalData[sid].admin_messages.notify = "";
	GlobalData[sid].admin_messages.redirect = "";
	GlobalData[sid].admin_messages.debug = "";
	GlobalData[sid].admin_messages.other = "";
	GlobalData[sid].errors = {};
	GlobalData[sid].errors.wpfilebase = "";
	GlobalData[sid].errors.notify = "";
	GlobalData[sid].errors.redirect = "";
	GlobalData[sid].xhrs = Array();

	wfu_show_simple_progressbar(JSONobj.shortcode_id, "progressive");

	// show message in wait for upload state 
	var Params = wfu_Initialize_Params();
	Params.general.shortcode_id = sid;
	Params.general.unique_id = rand_str;
	wfu_ProcessUploadComplete(sid, 0, Params, rand_str, JSONobj.params_index, session_token, "", ["false", "", "false"]);

	var inc = 0;
	var ret_status = true;
	var i = 0;
	var fprops = [];
	setTimeout(process_next_file, 100);
}

//wfu_get_userdata_count: get number of userdata fields
function wfu_get_userdata_count(sid) {
	var fields_count = 0;
	while (document.getElementById('userdata_' + sid + '_' + fields_count)) fields_count ++;
	return fields_count;
}

function wfu_lock_upload(sid) {
	var textbox = document.getElementById('fileName_' + sid);
	if (textbox) textbox.disabled = true;
	document.getElementById('input_' + sid).disabled = true;
	document.getElementById('upfile_' + sid).disabled = true;
	var subdir = document.getElementById('selectsubdir_' + sid);
	if (subdir) subdir.disabled = true;
	var upload = document.getElementById('upload_' + sid);
	if (upload) upload.disabled = true;
	var userdata_count = wfu_get_userdata_count(sid);
	for (var i = 0; i < userdata_count; i++)
		document.getElementById('userdata_' + sid + '_message_' + i).disabled = true;
}

function wfu_unlock_upload(sid) {
	var textbox = document.getElementById('fileName_' + sid);
	if (textbox) textbox.disabled = false;
	document.getElementById('input_' + sid).disabled = false;
	document.getElementById('upfile_' + sid).disabled = false;
	var subdir = document.getElementById('selectsubdir_' + sid);
	if (subdir) subdir.disabled = false;
	var upload = document.getElementById('upload_' + sid);
	if (upload) upload.disabled = false;
	var userdata_count = wfu_get_userdata_count(sid);
	for (var i = 0; i < userdata_count; i++)
		document.getElementById('userdata_' + sid + '_message_' + i).disabled = false;
}

function wfu_clear(sid) {
	document.getElementById("uploadform_" + sid).reset();
	var textbox = document.getElementById('fileName_' + sid);
	if (textbox) {
		textbox.value = '';
		textbox.className = 'file_input_textbox';
	}
	var subdir = document.getElementById('selectsubdir_' + sid);
	if (subdir) subdir.selectedIndex = parseInt(document.getElementById('selectsubdirdefault_' + sid).value);
	var userdata_count = wfu_get_userdata_count(sid);
	for (var i = 0; i < userdata_count; i++) {
		document.getElementById('userdata_' + sid + '_message_' + i).value = "";
		document.getElementById('hiddeninput_' + sid + '_userdata_' + i).value = "";
	}
}

function wfu_reset_message(sid) {
	var message_table = document.getElementById('wfu_messageblock_' + sid);
	if (message_table) {
		// reset header
		document.getElementById('wfu_messageblock_header_' + sid).style.display = "";
		var header_container = document.getElementById('wfu_messageblock_header_' + sid + '_container');
		if (UploadStates[sid]) header_container.innerHTML = UploadStates[sid]["State0"];
		document.getElementById('wfu_messageblock_header_' + sid + '_state').value = "none";
		document.getElementById('wfu_messageblock_arrow_' + sid).style.display = "none";
		header_container.colSpan = 3;
		// reset subheader
		document.getElementById('wfu_messageblock_subheader_' + sid + '_messagelabel').innerHTML = "";
		document.getElementById('wfu_messageblock_subheader_' + sid + '_adminmessagelabel').innerHTML = "";
		document.getElementById('wfu_messageblock_subheader_' + sid).style.display = "none";
		document.getElementById('wfu_messageblock_subheader_' + sid + '_message').style.display = "none";
		document.getElementById('wfu_messageblock_subheader_' + sid + '_adminmessage').style.display = "none";
		// reset files
		var file_array = wfu_get_file_ids(sid);
		for (var i = 1; i <= file_array.length; i++) {
			message_table.tBodies[0].removeChild(document.getElementById('wfu_messageblock_' + sid + '_' + i));
			message_table.tBodies[0].removeChild(document.getElementById('wfu_messageblock_subheader_' + sid + '_' + i));
		}
	}
}


function wfu_show_simple_progressbar(sid, effect) {

	var bar = document.getElementById('progressbar_' + sid + '_animation');
	var barsafe = document.getElementById('progressbar_' + sid + '_imagesafe');
	if (bar) {
		if (effect == "progressive") {
			bar.style.width = "0%";
			bar.className = "file_progress_progressive";
			barsafe.style.display = "none";
			bar.style.display = "block";
		}
		else if (wfu_BrowserCaps.supportsAnimation) {
			bar.style.width = "25%";
			bar.className = "file_progress_shuffle";
			barsafe.style.display = "none";
			bar.style.display = "block";
		}
		else {
			bar.style.width = "0%";
			bar.className = "file_progress_noanimation";
			bar.style.display = "none";
			barsafe.style.display = "block";
		}
		document.getElementById('wordpress_file_upload_progressbar_' + sid).style.display = "block";
	}
}

function wfu_hide_simple_progressbar(sid) {
	var bar = document.getElementById('progressbar_' + sid + '_animation');
	var barsafe = document.getElementById('progressbar_' + sid + '_imagesafe');
	if (bar) {
		document.getElementById('wordpress_file_upload_progressbar_' + sid).style.display = "none";
		bar.style.width = "0%";
		bar.className = "file_progress_noanimation";
		barsafe.style.display = "none";
		bar.style.display = "block";
	}
}


