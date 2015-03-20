<?php

function wfu_prepare_message_block_skeleton($sid, $styles, $test) {
	/* Prepare header styles for all upload states */
	$header_styles["State0"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE0);
	$header_styles["State0"]['message'] = WFU_UPLOAD_STATE0;
	$header_styles["State1"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE1);
	$header_styles["State1"]['message'] = WFU_UPLOAD_STATE1;
	$header_styles["State2"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE2);
	$header_styles["State2"]['message'] = WFU_UPLOAD_STATE2;
	$header_styles["State3"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE3);
	$header_styles["State3"]['message'] = WFU_UPLOAD_STATE3;
	$header_styles["State4"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE4);
	$header_styles["State4"]['message'] = WFU_UPLOAD_STATE4;
	$header_styles["State5"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE5);
	$header_styles["State5"]['message'] = WFU_UPLOAD_STATE5;
	$header_styles["State5_singlefile"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE5);
	$header_styles["State5_singlefile"]['message'] = WFU_UPLOAD_STATE5_SINGLEFILE;
	$header_styles["State6"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE6);
	$header_styles["State6"]['message'] = WFU_UPLOAD_STATE6;
	$header_styles["State7"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE7);
	$header_styles["State7"]['message'] = WFU_UPLOAD_STATE7;
	$header_styles["State7_singlefile"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE7);
	$header_styles["State7_singlefile"]['message'] = WFU_UPLOAD_STATE7_SINGLEFILE;
	$header_styles["State8"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE8);
	$header_styles["State8"]['message'] = WFU_UPLOAD_STATE8;
	$header_styles["State9"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE9);
	$header_styles["State9"]['message'] = WFU_UPLOAD_STATE9;
	$header_styles["State10"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE10);
	$header_styles["State10"]['message'] = WFU_UPLOAD_STATE10;
	$header_styles["State11"] = wfu_prepare_message_colors(WFU_HEADERMESSAGECOLORS_STATE11);
	$header_styles["State11"]['message'] = WFU_UPLOAD_STATE11;
	$ExposedStateIndex = array("0", "1", "2", "3", "4", "5", "5_singlefile", "6", "7", "7_singlefile", "8", "11");

	/* set general variables */
	$messageblock_main = 'wfu_messageblock_'.$sid;
	$messageblock_header = 'wfu_messageblock_header_'.$sid;
	$messageblock_arrow = 'wfu_messageblock_arrow_'.$sid;
	$messageblock_subheader = 'wfu_messageblock_subheader_'.$sid;
	$dlp = "\n\t\t\t\t\t\t\t";

	/* Prepare header HTML template
	   Variables:
		[header_safe]: suffix having the value "_safe" in case of State10 upload state, or empty otherwise,
		[header_color]: text color of header,
		[header_bgcolor]: background color of header,
		[header_borcolor]: border color of header,
		[header_message]: text shown in header */
	$i = 1;
	$messageblock_headers[$i++] = "\t\t\t".'<div id="'.$messageblock_header.'[header_safe]" class="file_messageblock_header" style="color:[header_color]; background-color:[header_bgcolor]; border:1px solid [header_borcolor];">';
	$messageblock_headers[$i++] = "\t\t\t\t".'<label id="'.$messageblock_header.'_label[header_safe]" class="file_messageblock_header_label">[header_message]</label>';
	$messageblock_headers[$i++] = "\t\t\t".'</div>';

	/* Prepare the file block HTML template
	   Variables:
		[file_id]: replaced by the id of the file (1, 2, ...),
		[filenumber_display]: display:none if single file upload, otherwise empty,
		[fileheader_color], [fileheader_bgcolor], [fileheader_borcolor], [fileheader_message]: replaced by the returned values,
		[filesubheadermessage_display]: display:none if there is no message, otherwise empty,
		[filesubheader_message]: replaced by the returned value,
		[filesubheaderadminmessage_display]: display:none if there is no admin message, otherwise empty,
		[filesubheader_adminmessage]: replaced by the returned value */
	/* Prepare the files header block HTML template */
	$i = 1;
	$file_count = ( $test ? 2 : 1);
	for ($ii = 1; $ii <= $file_count; $ii++) {
		if ( $test ) {
			$file_props = wfu_prepare_message_colors(WFU_TESTMESSAGECOLORS);
			$file_id = $ii;
			$filenumber_display = "";
			$fileheader_color = $file_props['color'];
			$fileheader_bgcolor = $file_props['bgcolor'];
			$fileheader_borcolor = $file_props['borcolor'];
			$fileheader_message = constant('WFU_TESTMESSAGE_FILE'.$ii.'_HEADER');
			$filesubheadermessage_display = "";
			$filesubheader_message = constant('WFU_TESTMESSAGE_FILE'.$ii.'_MESSAGE');
			$filesubheaderadminmessage_display = "";
			$filesubheader_adminmessage = constant('WFU_TESTMESSAGE_FILE'.$ii.'_ADMINMESSAGE');
		}
		else {
			$file_id = '[file_id]';
			$filenumber_display = '[filenumber_display]';
			$fileheader_color = '[fileheader_color]';
			$fileheader_bgcolor = '[fileheader_bgcolor]';
			$fileheader_borcolor = '[fileheader_borcolor]';
			$fileheader_message = '[fileheader_message]';
			$filesubheadermessage_display = '[filesubheadermessage_display]';
			$filesubheader_message = '[filesubheader_message]';
			$filesubheaderadminmessage_display = '[filesubheaderadminmessage_display]';
			$filesubheader_adminmessage = '[filesubheader_adminmessage]';
		}
		$messageblock_file[$i++] = "\t".'<tr id="'.$messageblock_main.'_'.$file_id.'" class="file_messageblock_fileheader_tr" style="display:none;">';
		$messageblock_file[$i++] = "\t\t".'<td id="'.$messageblock_main.'_filenumber_'.$file_id.'" class="file_messageblock_filenumber_td"'.$filenumber_display.'>'.$file_id.'</td>';
		$messageblock_file[$i++] = "\t\t".'<td id="'.$messageblock_header.'_container_'.$file_id.'" class="file_messageblock_fileheader_td">';
		$messageblock_file[$i++] = "\t\t\t".'<div id="'.$messageblock_header.'_'.$file_id.'" class="file_messageblock_fileheader" style="color:'.$fileheader_color.'; background-color:'.$fileheader_bgcolor.'; border:1px solid '.$fileheader_borcolor.';">';
		$messageblock_file[$i++] = "\t\t\t\t".'<label id="'.$messageblock_header.'_label_'.$file_id.'" class="file_messageblock_fileheader_label">'.$fileheader_message.'</label>';
		/* The following hidden input holds state of arrow (open or close) */
		$messageblock_file[$i++] = "\t\t\t\t".'<input id="'.$messageblock_header.'_state_'.$file_id.'" type="hidden" value="none" />';
		$messageblock_file[$i++] = "\t\t\t".'</div>';
		$messageblock_file[$i++] = "\t\t".'</td>';
		/* Add a drop down arrow to the file header (file has always details to be shown) */
		$messageblock_file[$i++] = "\t\t".'<td id="'.$messageblock_arrow.'_'.$file_id.'" class="file_messageblock_filearrow_td" onclick="wfu_filedetails_toggle('.$sid.', '.$file_id.');">';
		$messageblock_file[$i++] = "\t\t\t".'<div id="'.$messageblock_arrow.'_up_'.$file_id.'" class="file_messageblock_file_arrow_up" style="display:none;"></div>';
		$messageblock_file[$i++] = "\t\t\t".'<div id="'.$messageblock_arrow.'_down_'.$file_id.'" class="file_messageblock_file_arrow_down"></div>';
		$messageblock_file[$i++] = "\t\t".'</td>';
		$messageblock_file[$i++] = "\t".'</tr>';
		/* Prepare the files subheader block HTML template */
		$messageblock_file[$i++] = "\t".'<tr id="'.$messageblock_subheader.'_'.$file_id.'" class="file_messageblock_filesubheader_tr" style="display:none;">';
		$messageblock_file[$i++] = "\t\t".'<td id="'.$messageblock_subheader.'_fileempty_'.$file_id.'" class="file_messageblock_filesubheaderempty_td"'.$filenumber_display.'></td>';
		$messageblock_file[$i++] = "\t\t".'<td colspan="2" id="'.$messageblock_subheader.'_container_'.$file_id.'" class="file_messageblock_filesubheader_td">';
		$messageblock_file[$i++] = "\t\t\t".'<div id="'.$messageblock_subheader.'_message_'.$file_id.'" class="file_messageblock_filesubheader_message"'.$filesubheadermessage_display.'>';
		$messageblock_file[$i++] = "\t\t\t\t".'<label id="'.$messageblock_subheader.'_messagelabel_'.$file_id.'" class="file_messageblock_filesubheader_messagelabel">'.$filesubheader_message.'</label>';
		$messageblock_file[$i++] = "\t\t\t".'</div>';
		$messageblock_file[$i++] = "\t\t\t".'<div id="'.$messageblock_subheader.'_adminmessage_'.$file_id.'" class="file_messageblock_filesubheader_adminmessage"'.$filesubheaderadminmessage_display.'>';
		$messageblock_file[$i++] = "\t\t\t\t".'<label id="'.$messageblock_subheader.'_adminmessagelabel_'.$file_id.'" class="file_messageblock_filesubheader_adminmessagelabel">'.$filesubheader_adminmessage.'</label>';
		$messageblock_file[$i++] = "\t\t\t".'</div>';
		$messageblock_file[$i++] = "\t\t".'</td>';
		$messageblock_file[$i++] = "\t".'</tr>';
	}

	/* Construct the main header block HTML text
	/* Construct the header block HTML text */
	$i = 1;
	$messageblock["msgblock"]["line".$i++] = '<table id="'.$messageblock_main.'" class="file_messageblock_table"'.$styles.'><tbody>';
	$messageblock["msgblock"]["line".$i++] = "\t".'<tr id="'.$messageblock_header.'" class="file_messageblock_header_tr"'.( $test ? '' : 'style="display:none;"' ).'>';
	$messageblock["msgblock"]["line".$i++] = "\t\t".'<td colspan="2" id="'.$messageblock_header.'_container" class="file_messageblock_header_td">';
	/* Inside this td element the appropriate upload state HTML block is going to be inserted using Javascript 
	   If the plugin is in test mode, then State9 HTML block is inserted now */
	if ( $test ) {
		foreach ( $messageblock_headers as $messageblock_header_part )
			$messageblock["msgblock"]["line".$i++] = strtr($messageblock_header_part, array(
				"[header_safe]" => "",
				"[header_color]" => $header_styles["State9"]["color"],
				"[header_bgcolor]" => $header_styles["State9"]["bgcolor"],
				"[header_borcolor]" => $header_styles["State9"]["borcolor"],
				"[header_message]" => $header_styles["State9"]["message"]
			));
	}
	$messageblock["msgblock"]["line".$i++] = "\t\t".'</td>';
	/* Add a drop down arrow to the header */
	$messageblock["msgblock"]["line".$i++] = "\t\t".'<td id="'.$messageblock_arrow.'" class="file_messageblock_arrow_td"'.( $test ? '' : 'style="display:none;"' ).' onclick="wfu_headerdetails_toggle('.$sid.');">';
	$messageblock["msgblock"]["line".$i++] = "\t\t\t".'<input id="'.$messageblock_header.'_state" type="hidden" value="none" />';
	$messageblock["msgblock"]["line".$i++] = "\t\t\t".'<div id="'.$messageblock_arrow.'_up" class="file_messageblock_header_arrow_up" style="display:none;"></div>';
	$messageblock["msgblock"]["line".$i++] = "\t\t\t".'<div id="'.$messageblock_arrow.'_down" class="file_messageblock_header_arrow_down"></div>';
	$messageblock["msgblock"]["line".$i++] = "\t\t".'</td>';
	$messageblock["msgblock"]["line".$i++] = "\t".'</tr>';
	/* Construct the subheader block HTML text if exists */
	$messageblock["msgblock"]["line".$i++] = "\t".'<tr id="'.$messageblock_subheader.'" class="file_messageblock_subheader_tr" style="display:none;">';
	$messageblock["msgblock"]["line".$i++] = "\t\t".'<td colspan="3" id="'.$messageblock_subheader.'_td" class="file_messageblock_subheader_td">';
	$messageblock["msgblock"]["line".$i++] = "\t\t\t".'<div id="'.$messageblock_subheader.'_message" class="file_messageblock_subheader_message"'.( $test ? '' : 'style="display:none;"' ).'>';
	$messageblock["msgblock"]["line".$i++] = "\t\t\t\t".'<label id="'.$messageblock_subheader.'_messagelabel" class="file_messageblock_subheader_messagelabel">'.( $test ? WFU_TESTMESSAGE_MESSAGE : '' ).'</label>';
	$messageblock["msgblock"]["line".$i++] = "\t\t\t".'</div>';
	$messageblock["msgblock"]["line".$i++] = "\t\t\t".'<div id="'.$messageblock_subheader.'_adminmessage" class="file_messageblock_subheader_adminmessage"'.( $test ? '' : 'style="display:none;"' ).'>';
	$messageblock["msgblock"]["line".$i++] = "\t\t\t\t".'<label id="'.$messageblock_subheader.'_adminmessagelabel" class="file_messageblock_subheader_adminmessagelabel">'.( $test ? WFU_TESTMESSAGE_ADMINMESSAGE : '' ).'</label>';
	$messageblock["msgblock"]["line".$i++] = "\t\t\t".'</div>';
	$messageblock["msgblock"]["line".$i++] = "\t\t".'</td>';
	$messageblock["msgblock"]["line".$i++] = "\t".'</tr>';
	/* After the above tr the file blocks are appended by Javascript dynamically as additional tr elements
	   If the plugin is in test mode, then two test file blocks are appended now */
	if ( $test ) {
		foreach ( $messageblock_file as $messageblock_file_part )
			$messageblock["msgblock"]["line".$i++] = $messageblock_file_part;
	}
	$messageblock["msgblock"]["line".$i++] = '</tbody></table>';
	/* Construct a div element that will hold the State10 header and use it in case JSON parse fails and upload results cannot be decoded */
	$messageblock["msgblock"]["line".$i++] = '<div id="'.$messageblock_header.'_safecontainer" style="display:none;">';
	foreach ( $messageblock_headers as $messageblock_header_part )
		$messageblock["msgblock"]["line".$i++] = strtr($messageblock_header_part, array(
			"[header_safe]" => "_safe",
			"[header_color]" => $header_styles["State10"]["color"],
			"[header_bgcolor]" => $header_styles["State10"]["bgcolor"],
			"[header_borcolor]" => $header_styles["State10"]["borcolor"],
			"[header_message]" => $header_styles["State10"]["message"]
		));
	$messageblock["msgblock"]["line".$i++] = '</div>';

	/* Construct header HTML text for all upload states and save it to hidden input, to be used later on by Javascript to adjust the upload state dynamically */
	$messageblock_header_template = "";
	foreach ( $messageblock_headers as $messageblock_header_part )
		$messageblock_header_template .= $dlp.$messageblock_header_part;
	foreach ($ExposedStateIndex as $ii)
		$messageblock["header"]["State".$ii] = strtr($messageblock_header_template, array(
			"[header_safe]" => "",
			"[header_color]" => $header_styles["State".$ii]["color"],
			"[header_bgcolor]" => $header_styles["State".$ii]["bgcolor"],
			"[header_borcolor]" => $header_styles["State".$ii]["borcolor"],
			"[header_message]" => $header_styles["State".$ii]["message"]
		));
	$messageblock_header_str = wfu_encode_array_to_string($messageblock["header"]);
	$messageblock["msgblock"]["line".$i++] = '<input id="'.$messageblock_header.'_states" type="hidden" value="'.$messageblock_header_str.'" />';

	/* Construct file HTML block template and save it to hidden div to be used later on by Javascript to add file results to the upload message dynamically */
	$messageblock_file_str = "";
	foreach ( $messageblock_file as $messageblock_file_part )
		$messageblock_file_str .= $dlp.$messageblock_file_part;
	$messageblock_file_str = wfu_plugin_encode_string($messageblock_file_str);
	$messageblock["msgblock"]["line".$i++] = '<input id="'.$messageblock_main.'_filetemplate" type="hidden" value="'.$messageblock_file_str.'" />';
	$messageblock["msgblock"]["line".$i++] = '<div id="'.$messageblock_main.'_door" style="display:none;"></div>';

	return $messageblock;
}

function wfu_prepare_message_colors($template) {
	$color_array = explode(",", $template);
	$colors['color'] = $color_array[0];
	$colors['bgcolor'] = $color_array[1];
	$colors['borcolor'] = $color_array[2];
	return $colors;
}

?>
