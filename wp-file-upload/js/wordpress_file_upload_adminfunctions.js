var DraggedItem = null;
var ShortcodeNextSave = 0;
var ShortcodeTimeOut = null;
var ShortcodeString = "";

jQuery(document).ready(function($){
	$('.wfu_color_field').wpColorPicker({
		change: function(event, ui) {
			event.target.value = ui.color.toString();
			if (event.target.name == "wfu_text_elements") wfu_update_text_value(event);
			else if (event.target.name == "wfu_triplecolor_elements") wfu_update_triplecolor_value(event);
		}
	});
});

function wfu_admin_activate_tab(key) {
	var tabs = document.getElementById("wfu_tab_container");
	var tab, tabkey;
	for (var i = 0; i < tabs.childNodes.length; i++) {
		tab = tabs.childNodes[i];
		if (tab.nodeType === 1) {
			tabkey = tab.id.substr(8);
			if (tab.className.indexOf("nav-tab-active") > -1) {
				tab.className = "nav-tab";
				document.getElementById("wfu_container_" + tabkey).style.display = "none";
			}
		}
	}
	document.getElementById("wfu_tab_" + key).className = "nav-tab nav-tab-active";
	document.getElementById("wfu_container_" + key).style.display = "block";
}

function wfu_admin_onoff_clicked(key) {
	var onoff = document.getElementById("wfu_attribute_" + key);
	var container = document.getElementById("wfu_wrapper");
	var shadows = document.getElementsByClassName("wfu_shadow_" + key, "div", container);
	var shadows_inv = document.getElementsByClassName("wfu_shadow_" + key + "_inv", "div", container);
	var status = (onoff.className.substr(onoff.className.length - 2) == "on");
	status = !status;
	if (status) {
		document.getElementById("wfu_attribute_value_" + key).value = "true";
		onoff.className = "wfu_onoff_container_on";
		for (var i = 0; i < shadows.length; i++) shadows[i].style.display = "none";
		for (var i = 0; i < shadows_inv.length; i++) shadows_inv[i].style.display = "block";
	}
	else {
		document.getElementById("wfu_attribute_value_" + key).value = "false";
		onoff.className = "wfu_onoff_container_off";
		for (var i = 0; i < shadows.length; i++) shadows[i].style.display = "block";
		for (var i = 0; i < shadows_inv.length; i++) shadows_inv[i].style.display = "none";
	}
	wfu_generate_shortcode();
	if (key == "userdata") wfu_update_userfield_variables();
}

function wfu_admin_radio_clicked(key) {
	var radios = document.getElementsByName("wfu_radioattribute_" + key);
	var container = document.getElementById("wfu_wrapper");
	var shadows = document.getElementsByClassName("wfu_shadow_" + key, "div", container);
	var shadows_inv = document.getElementsByClassName("wfu_shadow_" + key + "_inv", "div", container);
	var val = "";
	for (i = 0; i < radios.length; i++)
		if (radios[i].checked) val = radios[i].value;
	var status = (val.substr(0, 1) == "*");
	if (status) {
		val = val.substr(1);
		for (var i = 0; i < shadows.length; i++) shadows[i].style.display = "none";
		for (var i = 0; i < shadows_inv.length; i++) shadows_inv[i].style.display = "block";
	}
	else {
		for (var i = 0; i < shadows.length; i++) shadows[i].style.display = "block";
		for (var i = 0; i < shadows_inv.length; i++) shadows_inv[i].style.display = "none";
	}
	document.getElementById("wfu_attribute_value_" + key).value = val;
	wfu_generate_shortcode();
}

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

function wfu_attach_separator_dragdrop_events() {
	var container = document.getElementById('wfu_placements_container');
	var item;
	for (var i = 0; i < container.childNodes.length; i++) {
		item = container.childNodes[i];
		if (item.className == "wfu_component_separator_hor" || item.className == "wfu_component_separator_ver") {
			wfu_addEventHandler(item, 'dragenter', wfu_separator_dragenter);
			wfu_addEventHandler(item, 'dragover', wfu_default_dragover);
			wfu_addEventHandler(item, 'dragleave', wfu_separator_dragleave);
			wfu_addEventHandler(item, 'drop', wfu_separator_drop);
		}
	}
}

function wfu_Attach_Admin_DragDrop_Events() {
	if (window.FileReader) {
		var container = document.getElementById('wfu_placements_container');
		var available_container = document.getElementById('wfu_componentlist_container');
		var item;
		for (var i = 0; i < container.childNodes.length; i++) {
			item = container.childNodes[i];
			if (item.className == "wfu_component_box") {
				wfu_addEventHandler(item, 'dragstart', wfu_component_dragstart);
				wfu_addEventHandler(item, 'dragend', wfu_component_dragend);
			}
		}
		for (var i = 0; i < available_container.childNodes.length; i++) {
			item = available_container.childNodes[i];
			if (item.className == "wfu_component_box_container") {
				for (var ii = 0; ii < item.childNodes.length; ii++) {
					if (item.childNodes[ii].className == "wfu_component_box wfu_inbase") {
						wfu_addEventHandler(item.childNodes[ii], 'dragstart', wfu_component_dragstart);
						wfu_addEventHandler(item.childNodes[ii], 'dragend', wfu_component_dragend);
					}
				}
			}
		}
		item = document.getElementById('wfu_componentlist_dragdrop');
		wfu_addEventHandler(item, 'dragenter', wfu_componentlist_dragenter);
		wfu_addEventHandler(item, 'dragover', wfu_default_dragover);
		wfu_addEventHandler(item, 'dragleave', wfu_componentlist_dragleave);
		wfu_addEventHandler(item, 'drop', wfu_componentlist_drop);
		wfu_attach_separator_dragdrop_events();
	}	
}

function wfu_componentlist_dragenter(e) {
	e = e || window.event;
	if (e.preventDefault) { e.preventDefault(); }
	if (!DraggedItem) return false;
	var item = document.getElementById('wfu_componentlist_dragdrop');
	if (item.className.indexOf("wfu_componentlist_dragdrop_dragover") == -1)
		item.className += " wfu_componentlist_dragdrop_dragover";
	return false;
}

function wfu_componentlist_dragleave(e) {
	e = e || window.event;
	if (e.preventDefault) { e.preventDefault(); }
	if (!DraggedItem) return false;
	var item = document.getElementById('wfu_componentlist_dragdrop');
	item.className = item.className.replace(" wfu_componentlist_dragdrop_dragover", "");
	return false;
}

function wfu_componentlist_drop(e) {
	e = e || window.event;
	if (e.preventDefault) { e.preventDefault(); }
	var component = e.dataTransfer.getData("Component");
	if (!component) return false;
	//move dragged component to base
	var item = document.getElementById('wfu_component_box_' + component);
	item.className = "wfu_component_box wfu_inbase";
	item.style.display = "block";
	document.getElementById('wfu_component_box_container_' + component).appendChild(item);
	//recreate placements panel
	var placements = wfu_admin_recreate_placements_text(null, "");
	wfu_admin_recreate_placements_panel(placements);
	document.getElementById("wfu_attribute_value_placements").value = placements;
	wfu_generate_shortcode();
	return false;
}

function wfu_separator_dragenter(e) {
	e = e || window.event;
	if (e.preventDefault) { e.preventDefault(); }
	if (!DraggedItem) return false;
	if (e.target.className == "wfu_component_separator_hor") {
		var bar = document.getElementById('wfu_component_bar_hor');
		bar.style.top = e.target.offsetTop + "px";
		bar.style.display = "block";
	}
	else if (e.target.className == "wfu_component_separator_ver") {
		var bar = document.getElementById('wfu_component_bar_ver');
		bar.style.top = e.target.offsetTop + "px";
		bar.style.left = e.target.offsetLeft + "px";
		bar.style.display = "block";
	}
	return false;
}

function wfu_default_dragover(e) {
	e = e || window.event;
	if (e.preventDefault) { e.preventDefault(); }
	return false;
}

function wfu_separator_dragleave(e) {
	e = e || window.event;
	if (e.preventDefault) { e.preventDefault(); }
	if (!DraggedItem) return false;
	if (e.target.className == "wfu_component_separator_hor") {
		var bar = document.getElementById('wfu_component_bar_hor');
		bar.style.display = "none";
	}
	else if (e.target.className == "wfu_component_separator_ver") {
		var bar = document.getElementById('wfu_component_bar_ver');
		bar.style.display = "none";
	}
	return false;
}

function wfu_separator_drop(e) {
	e = e || window.event;
	if (e.preventDefault) { e.preventDefault(); }
	var component = e.dataTransfer.getData("Component");
	if (!component) return false;
	//first move dragged component to base otherwise we may lose it during recreation of placements panel
	var item = document.getElementById('wfu_component_box_' + component);
	item.style.display = "none";
	item.className = "wfu_component_box wfu_inbase";
	document.getElementById('wfu_component_box_container_' + component).appendChild(item);
	//recreate placements panel
	var placements = wfu_admin_recreate_placements_text(e.target, component);
	wfu_admin_recreate_placements_panel(placements);
	document.getElementById("wfu_attribute_value_placements").value = placements;
	wfu_generate_shortcode();
	return false;
}

function wfu_component_dragstart(e) {
	e = e || window.event;
	e.dataTransfer.setData("Component", e.target.id.replace("wfu_component_box_", ""));
	if (e.target.className.indexOf("wfu_component_box_dragged") == -1) {
		e.target.className += " wfu_component_box_dragged";
		DraggedItem = e.target;
	}
	e.target.style.zIndex = 3;
	var item = document.getElementById('wfu_componentlist_dragdrop');
	item.className = "wfu_componentlist_dragdrop wfu_componentlist_dragdrop_dragover";
	item.style.display = "block";
	return false;
}

function wfu_component_dragend(e) {
	e = e || window.event;
	DraggedItem = null;
	e.target.style.zIndex = 1;
	var item = document.getElementById('wfu_componentlist_dragdrop');
	item.style.display = "none";
	item.className = "wfu_componentlist_dragdrop";
	e.target.className = e.target.className.replace(" wfu_component_box_dragged", "");
	document.getElementById('wfu_component_bar_ver').style.display = "none";
	document.getElementById('wfu_component_bar_hor').style.display = "none";
	return false;
}

function wfu_admin_recreate_placements_text(place, new_component) {
	function add_item(component) {
		if (placements != "") placements += delim;
		placements += component;
		delim = "";
	}

	var container = document.getElementById('wfu_placements_container');
	var delim = "";
	var placements = "";
	var component = "";
	for (var i = 0; i < container.childNodes.length; i++) {
		item = container.childNodes[i];
		if (item.className == "wfu_component_separator_ver") {
			if (delim == "" ) delim = "+";
			if (item == place) { add_item(new_component); delim = "+"; }
		}
		else if (item.className == "wfu_component_separator_hor") {
			delim = "/";
			if (item == place) { add_item(new_component); delim = "/"; } 
		}
		else if (item.className == "wfu_component_box") add_item(item.id.replace("wfu_component_box_", ""));
	}
	return placements;
}

function wfu_admin_recreate_placements_panel(placements_text) {
	var container = document.getElementById('wfu_placements_container');
	var item, placements, sections;
	var itemname = "";
	for (var i = 0; i < container.childNodes.length; i++) {
		item = container.childNodes[i];
		if (item.className == "wfu_component_box") {
			itemname = item.id.replace("wfu_component_box_", "");
			item.style.display = "inline-block";
			item.className = "wfu_component_box wfu_inbase";
			document.getElementById('wfu_component_box_container_' + itemname).appendChild(item);
		}
	}
	container.innerHTML = "";
	placements = placements_text.split("/");
	for (var i = 0; i < placements.length; i++) {
		item = document.createElement("DIV");
		item.className = "wfu_component_separator_hor";
		item.setAttribute("draggable", true);
		container.appendChild(item);
		item = document.createElement("DIV");
		item.className = "wfu_component_separator_ver";
		item.setAttribute("draggable", true);
		container.appendChild(item);
		sections = placements[i].split("+");
		for (var ii = 0; ii < sections.length; ii++) {
			item = document.getElementById('wfu_component_box_' + sections[ii]);
			if (item) {
				container.appendChild(item);
				item.className = "wfu_component_box";
				item.style.display = "inline-block";
				item = document.createElement("DIV");
				item.className = "wfu_component_separator_ver";
				item.setAttribute("draggable", true);
				container.appendChild(item);
			}
		}
	}
	item = document.createElement("DIV");
	item.className = "wfu_component_separator_hor";
	item.setAttribute("draggable", true);
	container.appendChild(item);
	item = document.createElement("DIV");
	item.id = "wfu_component_bar_hor";
	item.className = "wfu_component_bar_hor";
	container.appendChild(item);
	item = document.createElement("DIV");
	item.id = "wfu_component_bar_ver";
	item.className = "wfu_component_bar_ver";
	container.appendChild(item);
	wfu_attach_separator_dragdrop_events();
}

function wfu_subfolders_input_changed(e) {
	e = e || window.event;
	var item = e.target;
	var key = item.id.replace("wfu_subfolders_path_", "");
	key = key.replace("wfu_subfolders_label_", "");

	var list = document.getElementById('wfu_attribute_' + key);
	if (list.selectedIndex < 0 ) return;
	var tools_path = document.getElementById('wfu_subfolders_path_' + key);
	var tools_label = document.getElementById('wfu_subfolders_label_' + key);
	var tools_ok = document.getElementById('wfu_subfolders_ok_' + key);
	var old_path_value, old_label_value;
	var isnewitem = (document.getElementById('wfu_subfolders_isnewitem_' + key).value == "1");
	if (isnewitem) {
		old_path_value = "";
		old_label_value = "";
	}
	else {
		var items = list.data;	
		item = items[list.selectedIndex];
		old_path_value = item.path;
		old_label_value = item.label;
	}
	if (tools_path.value == old_path_value && tools_label.value == old_label_value) {
		tools_ok.disabled = true;
		if (!isnewitem) wfu_subfolders_update_nav(key);
	}
	else {
		tools_ok.disabled = false;
		var navs = document.getElementsByName('wfu_subfolder_nav_' + key);
		for (var i = 0; i < navs.length; i++) navs[i].disabled = true;
	}
}

function wfu_subfolders_up_clicked(key) {
	var list = document.getElementById('wfu_attribute_' + key);
	if (list.selectedIndex < 0 ) return;
	var items = list.data;	
	item = items[list.selectedIndex];
	// find previous sibling
	var prevind = item.index - 1;
	if (prevind < 0) return;
	var prevpos = -1;
	var curind = list.selectedIndex - 1;
	while (curind >= 0) {
		if (items[curind].level == item.level && items[curind].index == prevind) {
			prevpos = curind;
			break;
		}
		else curind --;
	}
	if (prevpos == -1) return;
	// find number of children
	var children_count = 0;
	curind = list.selectedIndex + 1;
	while (curind < items.length) {
		if (items[curind].level > item.level) {
			children_count ++;
			curind ++;
		}
		else break;
	}
	// update list indices
	items[prevpos].index = item.index;
	item.index = prevind;
	// restructure data list
	list.data = items.slice(0, prevpos).concat(items.slice(list.selectedIndex, list.selectedIndex + 1 + children_count)).
		concat(items.slice(prevpos, list.selectedIndex)).concat(items.slice(list.selectedIndex + 1 + children_count));
	// update option contents to match list
	var val = wfu_update_subfolder_list(key);
	// move current selection to new position
	list.selectedIndex = prevpos;
	// update tool and nav items
	wfu_subfolders_update_toolnav(key);
	// update shortcode
	item = list;
	if (val !== item.oldVal) {
		item.oldVal = val;
		document.getElementById("wfu_attribute_value_" + key).value = val;
		wfu_generate_shortcode();
	}
}

function wfu_subfolders_down_clicked(key) {
	var list = document.getElementById('wfu_attribute_' + key);
	if (list.selectedIndex < 0 ) return;
	var items = list.data;	
	item = items[list.selectedIndex];
	// find next sibling
	var nextind = item.index + 1;
	var nextpos = -1;
	curind = list.selectedIndex + 1;
	while (curind < items.length) {
		if (items[curind].level == item.level) {
			nextpos = curind;
			break;
		}
		else if (items[curind].level < item.level) break;
		else curind ++;
	}
	if (nextpos == -1) return;
	// find number of children of next
	var next_children_count = 0;
	curind = nextpos + 1;
	while (curind < items.length) {
		if (items[curind].level > item.level) {
			next_children_count ++;
			curind ++;
		}
		else break;
	}
	// update list indices
	items[nextpos].index = item.index;
	item.index = nextind;
	// restructure data list
	list.data = items.slice(0, list.selectedIndex).concat(items.slice(nextpos, nextpos + 1 + next_children_count)).
		concat(items.slice(list.selectedIndex, nextpos)).concat(items.slice(nextpos + 1 + next_children_count));
	// update option contents to match list
	var val = wfu_update_subfolder_list(key);
	// move current selection to new position
	list.selectedIndex = list.selectedIndex + next_children_count + 1;
	// update tool and nav items
	wfu_subfolders_update_toolnav(key);
	// update shortcode
	item = list;
	if (val !== item.oldVal) {
		item.oldVal = val;
		document.getElementById("wfu_attribute_value_" + key).value = val;
		wfu_generate_shortcode();
	}
}

function wfu_subfolders_left_clicked(key) {
	var list = document.getElementById('wfu_attribute_' + key);
	if (list.selectedIndex < 0 ) return;
	var items = list.data;	
	item = items[list.selectedIndex];
	// find and reduce level of children
	curind = list.selectedIndex + 1;
	while (curind < items.length) {
		if (items[curind].level > item.level) {
			items[curind].level --;
			curind ++;
		}
		else break;
	}
	item.level --;
	// update option contents to match list
	var val = wfu_update_subfolder_list(key);
	// update list indices
	list.data = wfu_decode_subfolder_list(key);
	// update tool and nav items
	wfu_subfolders_update_toolnav(key);
	// update shortcode
	item = list;
	if (val !== item.oldVal) {
		item.oldVal = val;
		document.getElementById("wfu_attribute_value_" + key).value = val;
		wfu_generate_shortcode();
	}
}

function wfu_subfolders_right_clicked(key) {
	var list = document.getElementById('wfu_attribute_' + key);
	if (list.selectedIndex < 0 ) return;
	var items = list.data;	
	item = items[list.selectedIndex];
	// find and increase level of children
	curind = list.selectedIndex + 1;
	while (curind < items.length) {
		if (items[curind].level > item.level) {
			items[curind].level ++;
			curind ++;
		}
		else break;
	}
	item.level ++;
	// update option contents to match list
	var val = wfu_update_subfolder_list(key);
	// update list indices
	list.data = wfu_decode_subfolder_list(key);
	// update tool and nav items
	wfu_subfolders_update_toolnav(key);
	// update shortcode
	item = list;
	if (val !== item.oldVal) {
		item.oldVal = val;
		document.getElementById("wfu_attribute_value_" + key).value = val;
		wfu_generate_shortcode();
	}
}

function wfu_subfolders_def_clicked(key) {
	var list = document.getElementById('wfu_attribute_' + key);
	if (list.selectedIndex < 0 ) return;
	var items = list.data;	
	item = items[list.selectedIndex];
	if (item.default) item.default = false;
	else {
		for (var i = 0; i < items.length; i++)
			items[i].default = false;
		item.default = true;
	}
	// update option contents to match list
	var val = wfu_update_subfolder_list(key);
	// update tool and nav items
	wfu_subfolders_update_toolnav(key);
	// update shortcode
	item = list;
	if (val !== item.oldVal) {
		item.oldVal = val;
		document.getElementById("wfu_attribute_value_" + key).value = val;
		wfu_generate_shortcode();
	}
}

function wfu_subfolders_ok_clicked(key) {
	var list = document.getElementById('wfu_attribute_' + key);
	if (list.selectedIndex < 0 ) return;
	var tools_path = document.getElementById('wfu_subfolders_path_' + key);
	var tools_label = document.getElementById('wfu_subfolders_label_' + key);
	if (tools_path.value == "" || tools_label.value == "") {
		alert("Path or label cannot be empty!");
		return;
	}
	var items = list.data;	
	var isnewitem = (document.getElementById('wfu_subfolders_isnewitem_' + key).value == "1");
	if (isnewitem) {
		var newlevel = parseInt(document.getElementById('wfu_subfolders_newitemlevel_' + key).value);
		var newitem = {label:tools_label.value, path:tools_path.value, level:newlevel, default:false};
		var newpos = parseInt(document.getElementById('wfu_subfolders_newitemindex_' + key).value);
		if (newpos >= items.length) items.push(newitem);
		else items.splice(newpos, 0, newitem);
	}
	else {
		item = items[list.selectedIndex];
		item.path = tools_path.value;
		item.label = tools_label.value;
	}
	// update option contents to match list
	var val = wfu_update_subfolder_list(key);
	// update list indices
	list.data = wfu_decode_subfolder_list(key);
	// update tool and nav items
	wfu_subfolders_update_toolnav(key);
	// update shortcode
	item = list;
	if (val !== item.oldVal) {
		item.oldVal = val;
		document.getElementById("wfu_attribute_value_" + key).value = val;
		wfu_generate_shortcode();
	}
}

function wfu_subfolders_del_clicked(key) {
	var list = document.getElementById('wfu_attribute_' + key);
	if (list.selectedIndex < 0 ) return;
	var items = list.data;	
	item = items[list.selectedIndex];
	// find number of children
	var children_count = 0;
	curind = list.selectedIndex + 1;
	while (curind < items.length) {
		if (items[curind].level > item.level) {
			children_count ++;
			curind ++;
		}
		else break;
	}
	if (children_count > 0)
		if (!confirm("Children items will be deleted as well. Proceed?")) return;
	// remove items from list
	items.splice(list.selectedIndex, 1 + children_count);
	// update option contents to match list
	var val = wfu_update_subfolder_list(key);
	// update list indices
	list.data = wfu_decode_subfolder_list(key);
	// update tool and nav items
	wfu_subfolders_update_toolnav(key);
	// update shortcode
	item = list;
	if (val !== item.oldVal) {
		item.oldVal = val;
		document.getElementById("wfu_attribute_value_" + key).value = val;
		wfu_generate_shortcode();
	}
}

function wfu_subfolders_add_clicked(key) {
	var list = document.getElementById('wfu_attribute_' + key);
	if (list.selectedIndex < 0 ) return;
	var items = list.data;
	var curpos = list.selectedIndex;
	item = items[curpos];
	var opts = list.options;
	var opt = document.createElement("option");
	opt.value = "";
	opt.innerHTML = "";
	opts.add(opt, curpos);
	list.selectedIndex = curpos;
	
	var tools_container = document.getElementById('wfu_subfolder_tools_' + key);
	var tools_path = document.getElementById('wfu_subfolders_path_' + key);
	var tools_label = document.getElementById('wfu_subfolders_label_' + key);
	var tools_ok = document.getElementById('wfu_subfolders_ok_' + key);
	var tools_browse = document.getElementById('wfu_subfolders_browse_' + key);
	tools_container.className = "wfu_subfolder_tools_container";
	tools_label.disabled = false;
	tools_ok.disabled = true;
	document.getElementById('wfu_subfolders_isnewitem_' + key).value = "1";
	document.getElementById('wfu_subfolders_newitemindex_' + key).value = curpos;
	document.getElementById('wfu_subfolders_newitemlevel_' + key).value = item.level;
	document.getElementById('wfu_subfolders_newitemlevel2_' + key).value = "";
	tools_path.disabled = (item.level == 0);
	tools_browse.disabled = (item.level == 0);
	if (item.level == 0) {
		tools_path.value = "{root}";
		tools_label.value = "{upload folder}";
	}
	else {
		tools_path.value = "";
		tools_label.value = "";
	}
	var navs = document.getElementsByName('wfu_subfolder_nav_' + key);
	for (var i = 0; i < navs.length; i++) navs[i].disabled = true;
}

function wfu_subfolders_browse_clicked(key) {
	var xhr = wfu_GetHttpRequestObject();
	if (xhr == null) return;
	var fd = null;
	try {
		var fd = new FormData();
	}
	catch(e) {}
	if (fd == null) return;

	var container = document.getElementById('wfu_global_dialog_container');
	var dialog = document.getElementById('wfu_subfolders_browser_' + key);
	var btn = document.getElementById('wfu_subfolders_browse_' + key);
	var shadow = document.getElementById('wfu_subfolders_inner_shadow_' + key);
	var msgcont = document.getElementById('wfu_subfolders_browser_msgcont_' + key);
	var msg = document.getElementById('wfu_subfolders_browser_msg_' + key);
	var img = document.getElementById('wfu_subfolders_browser_img_' + key);
	var ok = document.getElementById('wfu_subfolders_browser_ok_' + key);
	var list = document.getElementById('wfu_subfolders_browser_list_' + key);

	while (list.options.length > 0) list.options.remove(0);
	ok.disabled = true;
	ok.onclick = function() {wfu_folder_browser_cancel_clicked(key);}
	msg.innerHTML = "loading folder contents...";
	img.style.display = "inline";
	msgcont.style.display = "block";
	container.style.display = "block";
	dialog.style.display = "block";
	dialog.style.left = (btn.offsetLeft + btn.offsetWidth - dialog.offsetWidth) + 'px';
	dialog.style.top = (btn.offsetTop + btn.offsetHeight - dialog.offsetHeight) + 'px';
	shadow.style.display = "block";
	container.onclick = function() {wfu_folder_browser_cancel_clicked(key)};

	var path = document.getElementById('wfu_attribute_uploadpath').value;
	if (path.substr(path.length - 1) == "/") path = path.substr(0, path.length - 1);
	var paths = wfu_get_relative_path(key).split(",");
	var path1 = path + paths[0];
	if (path1.substr(0) != "/") path1 = "/" + path1;
	var path2 = "";
	if (paths.length == 2) path2 = paths[1];
	
	fd.append("action", "wfu_ajax_action_read_subfolders");
	fd.append("folder1", wfu_plugin_encode_string(path1));
	fd.append("folder2", wfu_plugin_encode_string(path2));
	xhr.key = key;
	xhr.addEventListener("load", wfu_readfolderComplete, false);
	xhr.addEventListener("error", wfu_readfolderFailed, false);
	xhr.addEventListener("abort", wfu_readfolderCanceled, false);

	xhr.open("POST", AdminParams.wfu_ajax_url);
	xhr.send(fd);
}

function wfu_readfolderComplete(evt) {
	var key = evt.target.key;
	var msgcont = document.getElementById('wfu_subfolders_browser_msgcont_' + key);
	var msg = document.getElementById('wfu_subfolders_browser_msg_' + key);
	var img = document.getElementById('wfu_subfolders_browser_img_' + key);
	var list = document.getElementById('wfu_subfolders_browser_list_' + key);
	var ok = document.getElementById('wfu_subfolders_browser_ok_' + key);
	var tools_path = document.getElementById('wfu_subfolders_path_' + key);
	var tools_label = document.getElementById('wfu_subfolders_label_' + key);

	var txt = evt.target.responseText;
	if (txt != -1) {
		var pos = txt.indexOf(":");
		var txt_header = txt.substr(0, pos);
		var txt_value = txt.substr(pos + 1, txt.length - pos - 1);
		if (txt_header == 'success') {
			var filelist = wfu_plugin_decode_string(txt_value);
			var flist = filelist.split(",");
			var fcount = 0;
			var opt;
			for (var i = 0; i < flist.length; i++) {
				if (flist[i] != "") {
					opt = document.createElement("option");
					opt.value = flist[i];
					opt.innerHTML = flist[i].replace("*", "&nbsp;&nbsp;&nbsp;");
					list.add(opt);
					fcount ++;
				}
			}
			if (fcount == 0) {
				opt = document.createElement("option");
				opt.value = "";
				opt.innerHTML = "{empty}";
				opt.disabled = true;
				list.add(opt);
			}
			list.selectedIndex = -1;
			ok.onclick = function() {
				var val = list.options[list.selectedIndex].value;
				var level = parseInt(document.getElementById('wfu_subfolders_newitemlevel_' + key).value);
				if (val.substr(0, 1) == "*" || level == 0) {
					document.getElementById('wfu_subfolders_newitemlevel_' + key).value = level + 1;
					if (level > 0) val = val.substr(1);
				}
				tools_path.value = val;
				tools_label.value = val;
				wfu_folder_browser_cancel_clicked(key);
				wfu_subfolders_ok_clicked(key);
			}
			msgcont.style.display = "none";
		}
		else if (txt_header == 'error') {
			msg.innerHTML = txt_value;
			img.style.display = "none";
			ok.disabled = false;
		}
		else {
			msg.innerHTML = 'Unknown error';
			img.style.display = "none";
			ok.disabled = false;
		}
	}
}

function wfu_readfolderFailed(evt) {
	var key = evt.target.key;
	var msg = document.getElementById('wfu_subfolders_browser_msg_' + key);
	var img = document.getElementById('wfu_subfolders_browser_img_' + key);
	var ok = document.getElementById('wfu_subfolders_browser_ok_' + key);
	msg.innerHTML = 'Unknown error';
	img.style.display = "none";
	ok.disabled = false;
}

function wfu_readfolderCanceled(evt) {
	var key = evt.target.key;
	var msg = document.getElementById('wfu_subfolders_browser_msg_' + key);
	var img = document.getElementById('wfu_subfolders_browser_img_' + key);
	var ok = document.getElementById('wfu_subfolders_browser_ok_' + key);
	msg.innerHTML = 'Unknown error';
	img.style.display = "none";
	ok.disabled = false;
}

function wfu_subfolders_browser_list_changed(key) {
	var list = document.getElementById('wfu_subfolders_browser_list_' + key);
	var ok = document.getElementById('wfu_subfolders_browser_ok_' + key);
	ok.disabled = (list.selectedIndex < 0);
}

function wfu_folder_browser_cancel_clicked(key) {
	var container = document.getElementById('wfu_global_dialog_container');
	var dialog = document.getElementById('wfu_subfolders_browser_' + key);
	var btn = document.getElementById('wfu_subfolders_browse_' + key);
	var shadow = document.getElementById('wfu_subfolders_inner_shadow_' + key);

	container.onclick = null;
	shadow.style.display = "none";
	dialog.style.display = "none";
	container.style.display = "none";
}

function wfu_get_relative_path(key) {
	var list = document.getElementById('wfu_attribute_' + key);
	if (list.selectedIndex < 0) return;
	var items = list.data;
	var isnewitem = (document.getElementById('wfu_subfolders_isnewitem_' + key).value == "1");
	var level;
	if (isnewitem) level = parseInt(document.getElementById('wfu_subfolders_newitemlevel_' + key).value);
	else level = items[list.selectedIndex].level;
	var relpath = "/";
	var curpos = list.selectedIndex - 1;
	var curlevel = level;
	while (curpos >= 0 && curlevel > 1) {
		if (items[curpos].level < curlevel) {
			relpath = "/" + items[curpos].path + relpath;
			curlevel = items[curpos].level;
		}
		curpos --;
	}
	if (isnewitem && document.getElementById('wfu_subfolders_newitemlevel2_' + key).value == "1" && level > 0 && list.selectedIndex > 0)
		relpath += "," + items[list.selectedIndex - 1].path;

	return relpath;
}

function wfu_subfolders_changed(key) {
	wfu_update_subfolder_list(key);
	wfu_subfolders_update_toolnav(key);
}

function wfu_subfolders_update_toolnav(key) {
	var list = document.getElementById('wfu_attribute_' + key);
	var items, item, ind, nextind, prevlevel;
	var tools_container = document.getElementById('wfu_subfolder_tools_' + key);
	var tools_path = document.getElementById('wfu_subfolders_path_' + key);
	var tools_label = document.getElementById('wfu_subfolders_label_' + key);
	var tools_ok = document.getElementById('wfu_subfolders_ok_' + key);
	var tools_browse = document.getElementById('wfu_subfolders_browse_' + key);
	document.getElementById('wfu_subfolders_isnewitem_' + key).value = "";
	document.getElementById('wfu_subfolders_newitemindex_' + key).value = "";
	document.getElementById('wfu_subfolders_newitemlevel_' + key).value = "";
	document.getElementById('wfu_subfolders_newitemlevel2_' + key).value = "";
	if (list.data == null) {
		items = wfu_decode_subfolder_list(key);
		list.data = items;
	}
	else items = list.data;
	if (list.selectedIndex < 0) {
		tools_container.className = "wfu_subfolder_tools_container wfu_subfolder_tools_disabled";
		tools_path.disabled = true;
		tools_label.disabled = true;
		tools_ok.disabled = true;
		tools_browse.disabled = true;
		tools_label.value = "";
		tools_path.value = "";
	}
	else if (list.selectedIndex >= list.options.length - 1) {
		tools_container.className = "wfu_subfolder_tools_container";
		tools_label.disabled = false;
		tools_ok.disabled = true;
		document.getElementById('wfu_subfolders_isnewitem_' + key).value = "1";
		document.getElementById('wfu_subfolders_newitemindex_' + key).value = items.length;
		var level;
		if (items.length == 0) level = 0;
		else if (items[items.length - 1].level == 0) level = 1;
		else level = items[items.length - 1].level;
		document.getElementById('wfu_subfolders_newitemlevel_' + key).value = level;
		document.getElementById('wfu_subfolders_newitemlevel2_' + key).value = "1";
		tools_path.disabled = (level == 0);
		tools_browse.disabled = false;
		if (level == 0) {
			tools_path.value = "{root}";
			tools_label.value = "{upload folder}";
		}
		else {
			tools_path.value = "";
			tools_label.value = "";
		}
	}
	else {
		tools_container.className = "wfu_subfolder_tools_container";
		tools_label.disabled = false;
		tools_ok.disabled = true;
		item = items[list.selectedIndex];
		tools_path.disabled = (item.level == 0);
		tools_browse.disabled = (item.level == 0);
		tools_label.value = item.label;
		tools_path.value = item.path;
	}
	var navs = document.getElementsByName('wfu_subfolder_nav_' + key);
	if (list.selectedIndex < 0 || list.selectedIndex >= list.options.length - 1) {
		for (var i = 0; i < navs.length; i++) navs[i].disabled = true;
	}
	else {
		wfu_subfolders_update_nav(key);
	}
}

function wfu_subfolders_update_nav(key) {
	var list = document.getElementById('wfu_attribute_' + key);
	var navs_up = document.getElementById('wfu_subfolders_up_' + key);
	var navs_down = document.getElementById('wfu_subfolders_down_' + key);
	var navs_left = document.getElementById('wfu_subfolders_left_' + key);
	var navs_right = document.getElementById('wfu_subfolders_right_' + key);
	var navs_add = document.getElementById('wfu_subfolders_add_' + key);
	var navs_def = document.getElementById('wfu_subfolders_def_' + key);
	var navs_del = document.getElementById('wfu_subfolders_del_' + key);
	var items = list.data;
	var item = items[list.selectedIndex];
	navs_up.disabled = (item.index <= 0);
	ind = list.selectedIndex + 1;
	nextind = 0;
	while (ind < items.length) {
		if (items[ind].level == item.level) {
			nextind = items[ind].index;
			break;
		}
		else if (items[ind].level < item.level) break;
		else ind ++;
	}
	navs_down.disabled = (item.level == 0 || nextind == 0);
	navs_left.disabled = ((list.selectedIndex == 0 && item.level < 1) || (list.selectedIndex > 0 && item.level <= 1));
	if (list.selectedIndex >= 1) prevlevel = items[list.selectedIndex - 1].level;
	else prevlevel = 0;
	navs_right.disabled = (item.level - prevlevel > 0);
	navs_add.disabled = (item.level == 0);
	navs_def.disabled = false;
	navs_def.className = "button" + (item.default ? " wfu_subfolder_nav_pressed" : "");
	navs_del.disabled = false;
}

function wfu_decode_subfolder(data) {
	var ret = {label:"", path:"", level:0, default:false};
	data = data.trim();
	var star_count = 0;
	var is_default = false;
	while (star_count < data.length) {
		if (data.substr(star_count, 1) == "*") star_count ++;
		else break;
	}
	data = data.substr(star_count, data.length - star_count);
	if (data.substr(0, 1) == '&') {
		data = data.substr(1);
		is_default = true;
	}
	ret.level = star_count;
	ret.default = is_default;
	var data_raw = data.split('/');
	if (data_raw.length == 1) {
		ret.path = data_raw[0];
		ret.label = data_raw[0];
	}
	else if (data_raw.length > 1) {
		ret.path = data_raw[0];
		ret.label = data_raw[1];
	}
	if (star_count == 0) {
		ret.path = "{root}";
		if (ret.label == "") ret.label = "{upload folder}";
	}
	return ret;
}

function wfu_decode_subfolder_list(key) {
	var opts = document.getElementById('wfu_attribute_' + key).options;
	var list = Array();
	var dir_levels = ['root'];
	var last_index = [0];
	var subfolder_path;
	var prev_level = -1;
	for (var i = 0; i < opts.length - 1; i++) {
		list.push(wfu_decode_subfolder(wfu_plugin_decode_string(opts[i].value)));
		if (dir_levels.length > list[i].level) dir_levels[list[i].level] = list[i].path;
		else dir_levels.push(list[i].path);
		subfolder_path = "";
		for ( j = 1; j <= list[i].level; j++) {
			subfolder_path += dir_levels[j] + '/';
		}
		list[i].fullpath = subfolder_path;
		if (last_index.length <= list[i].level) last_index.push(0);
		if (list[i].level > prev_level) list[i].index = 0;
		else list[i].index = last_index[list[i].level] + 1;
		last_index[list[i].level] = list[i].index;
		prev_level = list[i].level;
	}
	return list;
}

function wfu_update_subfolder_list(key) {
	var opts = document.getElementById('wfu_attribute_' + key).options;
	var list = document.getElementById('wfu_attribute_' + key);
	var items = list.data;
	if (items == null) return;
	var value_raw, text_raw;
	var global_raw = "";
	opts.length = items.length + 1;
	for (var i = 0; i < items.length; i ++) {
		value_raw = "";
		text_raw = "";
		for (j = 0; j < items[i].level; j ++) {
			value_raw += "*";
			text_raw += "&nbsp;&nbsp;&nbsp;";
		}
		if (items[i].default) {
			value_raw += "&";
			opts[i].className = "wfu_select_folders_option_default";
		}
		else opts[i].className = "";
		value_raw += items[i].path + '/' + items[i].label;
		text_raw += items[i].label;
		opts[i].value = wfu_plugin_encode_string(value_raw);
		opts[i].innerHTML = text_raw;
		if (global_raw != "") global_raw += ",";
		global_raw += value_raw;
	}
	opts[items.length].value = "";
	opts[items.length].innerHTML = "";
	return global_raw;
}

function wfu_userdata_edit_field(line, label, required) {
	var item;
	for (var i = 0; i < line.childNodes.length; i ++) {
		item = line.childNodes[i];
		if (item.tagName == "INPUT") {
			if (item.type == "text") {
				item.value = label;
				wfu_attach_element_handlers(item, wfu_update_userfield_value);
			}
			else if (item.type == "checkbox") {
				item.checked = required;
			}
		}
		else if (item.tagName == "DIV") item.className = "wfu_userdata_action";
	}
}

function wfu_userdata_add_field(obj) {
	var line = obj.parentNode;
	var newline = line.cloneNode(true);
	wfu_userdata_edit_field(newline, "", false);
	line.parentNode.insertBefore(newline, line.nextSibling);
}

function wfu_userdata_remove_field(obj) {
	var line = obj.parentNode;
	var container = line.parentNode;
	var first = null;
	for (var i = 0; i < container.childNodes.length; i++)
		if (container.childNodes[i].nodeType === 1) {
			first = container.childNodes[i];
			break;
		}
	if (line != first) {
		line.parentNode.removeChild(line);
		for (var i = 0; i < first.childNodes.length; i++)
			if (first.childNodes[i].nodeType === 1) {
				wfu_update_userfield_value({target:first.childNodes[i]});
				break;
			}
	}
}

function wfu_generate_shortcode() {
	var defaults = document.getElementById("wfu_attribute_defaults");
	var values = document.getElementById("wfu_attribute_values");
	var item;
	var attribute = "";
	var value = "";
	var shortcode_full = "[wordpress_file_upload";
	var shortcode = "";
	for (var i = 0; i < defaults.childNodes.length; i++) {
		item = defaults.childNodes[i];
		if (item.nodeType === 1) {
			attribute = item.id.replace("wfu_attribute_default_", "");
			value = document.getElementById("wfu_attribute_value_" + attribute).value;
			if (item.value != value)
				shortcode += " " + attribute + "=\"" + value + "\"";
		}
	}
	shortcode_full += shortcode + "]";

	document.getElementById("wfu_shortcode").value = shortcode_full;
	ShortcodeString = shortcode.substr(1);

	wfu_schedule_save_shortcode();
}

function wfu_update_text_value(e) {
	e = e || window.event;
	var item = e.target;
	var attribute = item.id.replace("wfu_attribute_", "");
	var val = item.value;
	//encode some characters not allowed in shortcode, such as line breaks, double quotes (") and brackets ([])
	val = val.replace(/(\r\n|\n|\r)/gm,"%n%");
	val = val.replace(/\"/gm,"%dq%");
	val = val.replace(/\[/gm,"%brl%");
	val = val.replace(/\]/gm,"%brr%");
	if (val !== item.oldVal) {
		item.oldVal = val;
		document.getElementById("wfu_attribute_value_" + attribute).value = val;
		wfu_generate_shortcode();
	}
}

function wfu_update_triplecolor_value(e) {
	e = e || window.event;
	var item = e.target;
	var attribute = item.id.replace("wfu_attribute_", "");
	attribute = attribute.replace("_color", "");
	attribute = attribute.replace("_bgcolor", "");
	attribute = attribute.replace("_borcolor", "");	
	item = document.getElementById("wfu_attribute_" + attribute + "_color");
	var val = item.value + "," +
		document.getElementById("wfu_attribute_" + attribute + "_bgcolor").value + "," +
		document.getElementById("wfu_attribute_" + attribute + "_borcolor").value;
	if (val !== item.oldVal) {
		item.oldVal = val;
		document.getElementById("wfu_attribute_value_" + attribute).value = val;
		wfu_generate_shortcode();
	}
}

function wfu_update_dimension_value(e) {
	e = e || window.event;
	var item = e.target;
	var attribute = item.name.replace("wfu_dimension_elements_", "");
	var group = document.getElementsByName(item.name);
	item = group[0];
	var val = "";
	var dimname = "";
	for (var i = 0; i < group.length; i++) {
		dimname = group[i].id.replace("wfu_attribute_" + attribute + "_", "");
		if (val != "" && group[i].value != "") val += ", ";
		if (group[i].value != "") val += dimname + ":" + group[i].value;
	}
	if (val !== item.oldVal) {
		item.oldVal = val;
		document.getElementById("wfu_attribute_value_" + attribute).value = val;
		wfu_generate_shortcode();
	}
}

function wfu_update_ptext_value(e) {
	e = e || window.event;
	var item = e.target;
	var attribute = item.id.replace("wfu_attribute_", "");
	attribute = attribute.substr(2);
	var singular = document.getElementById("wfu_attribute_s_" + attribute).value;
	var plural = document.getElementById("wfu_attribute_p_" + attribute).value;
	var val = singular + "/" + plural;
	if (val !== item.oldVal) {
		item.oldVal = val;
		document.getElementById("wfu_attribute_value_" + attribute).value = val;
	}
	wfu_generate_shortcode();
}

function wfu_update_mchecklist_value(attribute) {
	var value = "";
	var mchecklist = document.getElementById("wfu_attribute_" + attribute);
	var checkall = document.getElementById("wfu_attribute_" + attribute + "_all");
	if (checkall.checked) {
		jQuery("#wfu_attribute_" + attribute + " input").prop('disabled', true);
		jQuery("#wfu_attribute_" + attribute + " input").prop('checked', true);
		value = "all";
	}
	else {
		jQuery("#wfu_attribute_" + attribute + " input").prop('disabled', false);
		jQuery("#wfu_attribute_" + attribute + " input").each(function() {
			if (jQuery(this).prop('checked'))
				value += "," + jQuery(this).next().html();
		});
		value = value.substr(1);
	}
	document.getElementById("wfu_attribute_value_" + attribute).value = value;
	wfu_generate_shortcode();
}

function wfu_update_rolelist_value(attribute) {
	var value = "";
	var rolelist = document.getElementById("wfu_attribute_" + attribute);
	var checkall = document.getElementById("wfu_attribute_" + attribute + "_all");
	if (checkall.checked) {
		rolelist.disabled = true;
		value = "all";
	}
	else {
		rolelist.disabled = false;
		var options = rolelist.options;
		for (var i = 0; i < options.length; i++)
			if (options[i].selected) {
				if (value != "") value += ",";
				value += options[i].value;
			}
	}
	document.getElementById("wfu_attribute_value_" + attribute).value = value;
	wfu_generate_shortcode();
}

function wfu_update_userfield_value(e) {
	e = e || window.event;
	var item = e.target;
	var line = item.parentNode;
	var container = line.parentNode;
	var fieldval = "";
	var fieldreq = false;
	var val = "";
	for (var i = 0; i < container.childNodes.length; i++) {
		line = container.childNodes[i];
		if (line.tagName === "DIV") {
			for (var j = 0; j < line.childNodes.length; j++)
				if (line.childNodes[j].tagName == "INPUT") {
					if (line.childNodes[j].type == "text") {
						fieldval = line.childNodes[j].value;
						if (i == 0) item = line.childNodes[j];
					}
					else if (line.childNodes[j].type == "checkbox")
						fieldreq = line.childNodes[j].checked;
				}
			if (val != "" && fieldval != "") val += "/";
			if (fieldval != "" && fieldreq) val += "*";
			if (fieldval != "") val += fieldval;
		}
	}
	if (val !== item.oldVal) {
		item.oldVal = val;
		document.getElementById("wfu_attribute_value_userdatalabel").value = val;
		wfu_generate_shortcode();
		wfu_update_userfield_variables();
	}
}

function wfu_update_userfield_variables() {
	var userdata = document.getElementById("wfu_attribute_value_userdatalabel").value;
	var container = document.getElementById("wfu_wrapper");
	var shadows = document.getElementsByClassName("wfu_shadow_userdata", "div", container);
	var selects = document.getElementsByName("wfu_userfield_select");
	for (var i = 0; i < selects.length; i++) selects[i].style.display = "none";
	if (shadows.length == 0) return;
	if (shadows[0].style.display == "block") return;

	var options_str = '<option style="display:none;">%userdataXXX%</option>';
	var userfields = userdata.split("/");
	var field = "";
	for (var i = 1; i <= userfields.length; i++) {
		field = userfields[i - 1];
		if (field[0] == "*") field = field.substr(1);
		options_str += '<option value="%userdata' + i + '%">' + i + ': ' + field + '</option>';
	}
	for (var i = 0; i < selects.length; i++) {
		selects[i].innerHTML = options_str;
		selects[i].style.display = "inline-block";
	}
}

function wfu_attach_element_handlers(item, handler) {
	var elem_events = ['DOMAttrModified', 'textInput', 'input', 'change', 'keypress', 'paste', 'focus', 'propertychange'];
	for (var i = 0; i < elem_events.length; i++)
		wfu_addEventHandler(item, elem_events[i], handler);
}

function wfu_Attach_Admin_Events() {
	wfu_generate_shortcode();
	wfu_update_userfield_variables();
	wfu_Attach_Admin_DragDrop_Events();
	var text_elements = document.getElementsByName("wfu_text_elements");
	for (var i = 0; i < text_elements.length; i++) wfu_attach_element_handlers(text_elements[i], wfu_update_text_value);
	var ptext_elements = document.getElementsByName("wfu_ptext_elements");
	for (var i = 0; i < ptext_elements.length; i++) wfu_attach_element_handlers(ptext_elements[i], wfu_update_ptext_value);
	var triplecolor_elements = document.getElementsByName("wfu_triplecolor_elements");
	for (var i = 0; i < triplecolor_elements.length; i++) wfu_attach_element_handlers(triplecolor_elements[i], wfu_update_triplecolor_value);
	var dimension_elements = document.getElementsByName("wfu_dimension_elements_widths");
	for (var i = 0; i < dimension_elements.length; i++) wfu_attach_element_handlers(dimension_elements[i], wfu_update_dimension_value);
	dimension_elements = document.getElementsByName("wfu_dimension_elements_heights");
	for (var i = 0; i < dimension_elements.length; i++) wfu_attach_element_handlers(dimension_elements[i], wfu_update_dimension_value);
	var userfield_elements = document.getElementsByName("wfu_userfield_elements");
	for (var i = 0; i < userfield_elements.length; i++) wfu_attach_element_handlers(userfield_elements[i], wfu_update_userfield_value);
	var subfolder_input_elements = document.getElementsByName("wfu_subfolder_tools_input");
	for (var i = 0; i < subfolder_input_elements.length; i++) wfu_attach_element_handlers(subfolder_input_elements[i], wfu_subfolders_input_changed);
}


function wfu_insert_variable(obj) {
	var attr = obj.className.replace("wfu_variable wfu_variable_", "");
	var inp = document.getElementById("wfu_attribute_" + attr);
	var pos = inp.selectionStart;
	var prevval = inp.value;
	inp.value = prevval.substr(0, pos) + obj.innerHTML + prevval.substr(pos);
	wfu_update_text_value({target:inp});
}

function wfu_insert_userfield_variable(obj) {
	var attr = obj.className.replace("wfu_variable wfu_variable_", "");
	var inp = document.getElementById("wfu_attribute_" + attr);
	var pos = inp.selectionStart;
	var prevval = inp.value;
	inp.value = prevval.substr(0, pos) + obj.value + prevval.substr(pos);
	obj.value = "%userdataXXX%";
	wfu_update_text_value({target:inp});
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

//wfu_plugin_encode_string: function that encodes a decoded string
function wfu_plugin_encode_string(str) {
	var i = 0;
	var newstr = "";
	var num;
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

function wfu_schedule_save_shortcode() {
	var d = new Date();
	var dt = ShortcodeNextSave - d.getTime();
	if (ShortcodeTimeOut != null) {
		clearTimeout(ShortcodeTimeOut);
		ShortcodeTimeOut = null;
	}
	if (dt <= 0) wfu_save_shortcode();
	else ShortcodeTimeOut = setTimeout(function() {wfu_save_shortcode();}, dt);
}

function wfu_save_shortcode() {
	var xhr = wfu_GetHttpRequestObject();
	if (xhr == null) return;

	//send request using AJAX
	var url = AdminParams.wfu_ajax_url;
	params = new Array(2);
	params[0] = new Array(2);
	params[0][0] = 'action';
	params[0][1] = 'wfu_ajax_action_save_shortcode';
	params[1] = new Array(2);
	params[1][0] = 'shortcode';
	params[1][1] = wfu_plugin_encode_string(ShortcodeString);

	var parameters = '';
	for (var i = 0; i < params.length; i++) {
		parameters += (i > 0 ? "&" : "") + params[i][0] + "=" + encodeURI(params[i][1]);
	}

	var d = new Date();
	ShortcodeNextSave = d.getTime() + 5000;

	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//	xhr.setRequestHeader("Content-length", parameters.length);
//	xhr.setRequestHeader("Connection", "close");
	xhr.onreadystatechange = function() {
		if ( xhr.readyState == 4 ) {
			if ( xhr.status == 200 ) {
				if (xhr.responseText.substr(0, 22) == "save_shortcode_success") {
					document.getElementById("wfu_save_label").innerHTML = "saved";
					document.getElementById("wfu_save_label").className = "wfu_save_label";
					document.getElementById("wfu_save_label").style.opacity = 1;
					wfu_fadeout_element(300);
					ShortcodeNextSave = d.getTime() + 1000;
					if (ShortcodeTimeOut != null) wfu_schedule_save_shortcode();
				}
				else {
					document.getElementById("wfu_save_label").innerHTML = "not saved";
					document.getElementById("wfu_save_label").className = "wfu_save_label_fail";
					document.getElementById("wfu_save_label").style.opacity = 1;
					wfu_fadeout_element(300);
				}
			}
		}
	};
	xhr.send(parameters);
}

function wfu_adjust_opacity(opacity) {
	document.getElementById("wfu_save_label").style.opacity = opacity;
}

function wfu_fadeout_element(interval) {
	var reps = 20.0;
	var op = 0.0;
	for (var i = 0; i < reps; i++) {
		op = 1.0 - i / reps;
		setTimeout('wfu_adjust_opacity("' + op.toString() + '")', i * interval / reps);
	}

	setTimeout('wfu_adjust_opacity("0.0")', i * interval / reps);
}

function wfu_apply_value(attribute, type, value) {
	if (type == "onoff") {
		document.getElementById("wfu_attribute_" + attribute).className = "wfu_onoff_container_" + (value != "true" ? "on" : "off");
		wfu_admin_onoff_clicked(attribute);
	}
	else if (type == "text" || type == "ltext" || type == "integer" || type == "float" || type == "mtext" || type == "color" ) {
		var item = document.getElementById("wfu_attribute_" + attribute);
		//decode some characters not allowed in shortcode, such as line breaks, double quotes (") and brackets ([])
		value = value.replace(/\%n\%/gm,"\n");
		value = value.replace(/\%dq\%/gm,"\"");
		value = value.replace(/\%brl\%/gm,"[");
		value = value.replace(/\%brr\%/gm,"]");
		if (type == "color") {
			var rgb = colourNameToHex(value);
			if (!rgb) rgb = value;
			jQuery('#wfu_attribute_' + attribute).wpColorPicker('color', rgb);
		}
		item.value = value;
		wfu_update_text_value({target:item});
	}
	else if (type == "placements") {
		wfu_admin_recreate_placements_panel(value);
		document.getElementById("wfu_attribute_value_placements").value = value;
		wfu_generate_shortcode();
	}
	else if (type == "radio") {
		var radios = document.getElementsByName("wfu_radioattribute_" + attribute);
		for (var i = 0; i < radios.length; i++)
			radios[i].checked = (radios[i].value == value || ("*" + radios[i].value) == value);
		wfu_admin_radio_clicked(attribute);
	}
	else if (type == "ptext" ) {
		//decode some characters not allowed in shortcode, such as line breaks, double quotes (") and brackets ([])
		value = value.replace(/\%n\%/gm,"\n");
		value = value.replace(/\%dq\%/gm,"\"");
		value = value.replace(/\%brl\%/gm,"[");
		value = value.replace(/\%brr\%/gm,"]");
		var parts = value.split("/");
		var singular = parts.length < 1 ? "" : parts[0];
		var plural = parts.length < 2 ? singular : parts[1];
		var item1 = document.getElementById("wfu_attribute_s_" + attribute);
		item1.value = singular;
		var item2 = document.getElementById("wfu_attribute_p_" + attribute);
		item2.value = plural;
		wfu_update_ptext_value({target:item1});
		wfu_update_ptext_value({target:item2});
	}
	else if (type == "mchecklist" ) {
		value = value.toLowerCase();
		if (value == "all") document.getElementById("wfu_attribute_" + attribute + "_all").checked = true;
		else {
			document.getElementById("wfu_attribute_" + attribute + "_all").checked = false;
			var items = value.split(",");
			for (var i = 0; i < items.length; i++) items[i] = items[i].trim();
			jQuery("#wfu_attribute_" + attribute + " input").each(function() {
				jQuery(this).prop('checked', (items.indexOf(jQuery(this).next().html()) > -1));
			});
		}
		wfu_update_mchecklist_value(attribute);
	}
	else if (type == "rolelist" ) {
		value = value.toLowerCase();
		if (value == "all") document.getElementById("wfu_attribute_" + attribute + "_all").checked = true;
		else {
			document.getElementById("wfu_attribute_" + attribute + "_all").checked = false;
			var roles = value.split(",");
			for (var i = 0; i < roles.length; i++) roles[i] = roles[i].trim();
			var item = document.getElementById("wfu_attribute_" + attribute);
			for (var i = 0; i < item.options.length; i++)
				item.options[i].selected = (roles.indexOf(item.options[i].value) > -1);
		}
		wfu_update_rolelist_value(attribute);
	}
	else if (type == "dimensions" ) {
		var dims = value.split(",");
		var details, nam, val, item;
		var group = document.getElementsByName("wfu_dimension_elements_" + attribute);
		for (var i = 0; i < group.length; i++) group[i].value = "";
		for (var i = 0; i < dims.length; i++) {
			details = dims[i].split(":", 2);
			nam = details.length < 1 ? "" : details[0];
			val = details.length < 2 ? nam : details[1];
			item = document.getElementById("wfu_attribute_" + attribute + "_" + nam.trim());
			if (item) item.value = val.trim();
		}
		item = group[0];
		wfu_update_dimension_value({target:item});
	}
	else if (type == "userfields") {
		var fields_arr = value.split("/");
		var is_req;
		var fields = Array();
		for (var i = 0; i < fields_arr.length; i++) {
			is_req = (fields_arr[i].substr(0, 1) == "*");
			if (is_req) fields_arr[i] = fields_arr[i].substr(1);
			if (fields_arr[i] != "") fields.push({name:fields_arr[i], required:is_req});
		}
		var container = document.getElementById("wfu_attribute_" + attribute);
		var first = null;
		var remove_array = Array();
		for (var i = 0; i < container.childNodes.length; i++)
			if (container.childNodes[i].nodeType === 1) {
				if (first == null) first = container.childNodes[i];
				else remove_array.push(container.childNodes[i]);
			}
		for (var i = 0; i < remove_array.length; i++) container.removeChild(remove_array[i]);
		wfu_userdata_edit_field(first, "", false);
		
		var newline;
		var prevline = first;
		for (var i = 0; i < fields.length; i++) {
			if (i == 0) wfu_userdata_edit_field(first, fields[i].name, fields[i].required);
			else {
				newline = prevline.cloneNode(true);
				wfu_userdata_edit_field(newline, fields[i].name, fields[i].required);
				container.insertBefore(newline, prevline.nextSibling);
				prevline = newline;
			}
		}
		var item;
		for (var i = 0; i < first.childNodes.length; i++) {
			item = first.childNodes[i];
			if (item.tagName == "INPUT") break;
		}
		wfu_update_userfield_value({target:item});
	}
	else if (type == "color-triplet") {
		var colors = value.split(",");
		for (var i = 0; i < colors.length; i++) colors[i] = colors[i].trim();
		if (colors.length == 2) colors = [colors[0], colors[1], "#000000"];
		else if (colors.length == 1) colors = [colors[0], "#FFFFFF", "#000000"];
		else if (colors.length < 3) colors = ["#000000", "#FFFFFF", "#000000"];
		var rgb = colourNameToHex(colors[0]);
		if (!rgb) rgb = colors[0];
		jQuery('#wfu_attribute_' + attribute + "_color").wpColorPicker('color', rgb);
		var item = document.getElementById("wfu_attribute_" + attribute + "_color");
		item.value = colors[0];
		rgb = colourNameToHex(colors[1]);
		if (!rgb) rgb = colors[1];
		jQuery('#wfu_attribute_' + attribute + "_bgcolor").wpColorPicker('color', rgb);
		document.getElementById("wfu_attribute_" + attribute + "_bgcolor").value = colors[1];
		rgb = colourNameToHex(colors[2]);
		if (!rgb) rgb = colors[2];
		jQuery('#wfu_attribute_' + attribute + "_borcolor").wpColorPicker('color', rgb);
		document.getElementById("wfu_attribute_" + attribute + "_borcolor").value = colors[2];
		wfu_update_triplecolor_value({target:item});
	}
	else if (type == "folderlist") {
		var items = wfu_parse_folderlist_js(value);
		var opts = document.getElementById('wfu_attribute_' + attribute).options;
		while (opts.length > 0) opts.remove(0);
		var opt, subfolder, subfolder_raw, text, stars, subvalue;
		for (var i = 0; i < items.path.length; i++) {
			subfolder = items.path[i];
			if (subfolder.substr(subfolder.length, 1) == '/') subfolder = subfolder.substr(0, subfolder.length - 1);
			subfolder_raw = subfolder.split("/");
			subfolder = subfolder_raw[subfolder_raw.length - 1];
			stars = parseInt(items.level[i]);
			text = "";
			subvalue = "";
			for (var j = 0; j < stars; j++) {
				text += "&nbsp;&nbsp;&nbsp;";
				subvalue += "*";
			}
			text += items.label[i];
			if (items.default[i]) subvalue += "&";
			if (subfolder == "") subvalue += "{root}/" + items.label[i];
			else subvalue += subfolder + items.label[i];

			opt = document.createElement("option");
			if (items.default[i]) opt.className = "wfu_select_folders_option_default";
			else opt.className = "";
			opt.value = wfu_plugin_encode_string(subvalue);
			opt.innerHTML = text;
			opts.add(opt);
		}
		opt = document.createElement("option");
		opt.value = "";
		opt.innerHTML = "";
		opts.add(opt);
		var list = document.getElementById('wfu_attribute_' + attribute);
		// update list indices
		list.data = wfu_decode_subfolder_list(attribute);
		// update tool and nav items
		wfu_subfolders_update_toolnav(attribute);
		// update shortcode
		item = list;
		if (value !== item.oldVal) {
			item.oldVal = value;
			document.getElementById("wfu_attribute_value_" + attribute).value = value;
			wfu_generate_shortcode();
		}
	}
}

function wfu_parse_folderlist_js(list) {
	var ret = Object();
	ret.path = Array();
	ret.label = Array();
	ret.level = Array();
	ret.default = Array();

	var subfolders = list.split(",");
	if (subfolders.length == 0) return ret;
	if (subfolders.length == 1 && subfolders[0].trim() == "") return ret;
	var dir_levels = ["root"];
	var prev_level = 0;
	var level0_count = 0;
	var _default = -1;
	var subfolder, star_count, start_spaces, is_default, subfolder_dir, subfolder_label, subfolder_path;
	for (var i = 0; i < subfolders.length; i++) {
		subfolder = subfolders[i].trim();
		star_count = 0;
		start_spaces = "";
		is_default = false;
		// check for folder level
		while (star_count < subfolder.length) {
			if ( subfolder.substr(star_count, 1) == "*" ) {
				star_count ++;
				start_spaces += "&nbsp;&nbsp;&nbsp;";
			}
			else break;
		}
		if (star_count - prev_level <= 1 && (star_count > 0 || level0_count == 0)) {
			subfolder = subfolder.substr(star_count, subfolder.length - star_count);
			// check for default value
			if (subfolder.substr(0, 1) == '&') {
				subfolder = subfolder.substr(1);
				is_default = true;
			}
			//split item in folder path and folder name
			subfolder_items = subfolder.split("/");
			if (subfolder_items.length < 2) subfolder_items.push("");
			if (subfolder_items[1] != "") {
				subfolder_dir = subfolder_items[0];
				subfolder_label = subfolder_items[1];
			}
			else {
				subfolder_dir = subfolder;
				subfolder_label = subfolder;
			}
			if (subfolder_dir != "") {
				// set is_default flag to true only for the first default item
				if (is_default && _default == -1) _default = ret.path.length;
				else is_default = false;
				// set flag that root folder has been included (so that it is not included it again)
				if (star_count == 0) level0_count = 1;
				if (dir_levels.length > star_count) dir_levels[star_count] = subfolder_dir;
				else dir_levels.push(subfolder_dir);
				subfolder_path = "";
				for (var i_count = 1; i_count <= star_count; i_count++) {
					subfolder_path += dir_levels[i_count] + '/';
				}
				ret.path.push(subfolder_path);
				ret.label.push(subfolder_label);
				ret.level.push(star_count);
				ret.default.push(is_default);
				prev_level = star_count;
			}
		}
	}

	return ret;
}

function colourNameToHex(colour)
{
	var colours = {"aliceblue":"#f0f8ff","antiquewhite":"#faebd7","aqua":"#00ffff","aquamarine":"#7fffd4","azure":"#f0ffff",
		"beige":"#f5f5dc","bisque":"#ffe4c4","black":"#000000","blanchedalmond":"#ffebcd","blue":"#0000ff","blueviolet":"#8a2be2","brown":"#a52a2a","burlywood":"#deb887",
		"cadetblue":"#5f9ea0","chartreuse":"#7fff00","chocolate":"#d2691e","coral":"#ff7f50","cornflowerblue":"#6495ed","cornsilk":"#fff8dc","crimson":"#dc143c","cyan":"#00ffff",
		"darkblue":"#00008b","darkcyan":"#008b8b","darkgoldenrod":"#b8860b","darkgray":"#a9a9a9","darkgreen":"#006400","darkkhaki":"#bdb76b","darkmagenta":"#8b008b","darkolivegreen":"#556b2f",
		"darkorange":"#ff8c00","darkorchid":"#9932cc","darkred":"#8b0000","darksalmon":"#e9967a","darkseagreen":"#8fbc8f","darkslateblue":"#483d8b","darkslategray":"#2f4f4f","darkturquoise":"#00ced1",
		"darkviolet":"#9400d3","deeppink":"#ff1493","deepskyblue":"#00bfff","dimgray":"#696969","dodgerblue":"#1e90ff",
		"firebrick":"#b22222","floralwhite":"#fffaf0","forestgreen":"#228b22","fuchsia":"#ff00ff",
		"gainsboro":"#dcdcdc","ghostwhite":"#f8f8ff","gold":"#ffd700","goldenrod":"#daa520","gray":"#808080","green":"#008000","greenyellow":"#adff2f",
		"honeydew":"#f0fff0","hotpink":"#ff69b4",
		"indianred ":"#cd5c5c","indigo ":"#4b0082","ivory":"#fffff0","khaki":"#f0e68c",
		"lavender":"#e6e6fa","lavenderblush":"#fff0f5","lawngreen":"#7cfc00","lemonchiffon":"#fffacd","lightblue":"#add8e6","lightcoral":"#f08080","lightcyan":"#e0ffff","lightgoldenrodyellow":"#fafad2",
		"lightgrey":"#d3d3d3","lightgreen":"#90ee90","lightpink":"#ffb6c1","lightsalmon":"#ffa07a","lightseagreen":"#20b2aa","lightskyblue":"#87cefa","lightslategray":"#778899","lightsteelblue":"#b0c4de",
		"lightyellow":"#ffffe0","lime":"#00ff00","limegreen":"#32cd32","linen":"#faf0e6",
		"magenta":"#ff00ff","maroon":"#800000","mediumaquamarine":"#66cdaa","mediumblue":"#0000cd","mediumorchid":"#ba55d3","mediumpurple":"#9370d8","mediumseagreen":"#3cb371","mediumslateblue":"#7b68ee",
		"mediumspringgreen":"#00fa9a","mediumturquoise":"#48d1cc","mediumvioletred":"#c71585","midnightblue":"#191970","mintcream":"#f5fffa","mistyrose":"#ffe4e1","moccasin":"#ffe4b5",
		"navajowhite":"#ffdead","navy":"#000080",
		"oldlace":"#fdf5e6","olive":"#808000","olivedrab":"#6b8e23","orange":"#ffa500","orangered":"#ff4500","orchid":"#da70d6",
		"palegoldenrod":"#eee8aa","palegreen":"#98fb98","paleturquoise":"#afeeee","palevioletred":"#d87093","papayawhip":"#ffefd5","peachpuff":"#ffdab9","peru":"#cd853f","pink":"#ffc0cb","plum":"#dda0dd","powderblue":"#b0e0e6","purple":"#800080",
		"red":"#ff0000","rosybrown":"#bc8f8f","royalblue":"#4169e1",
		"saddlebrown":"#8b4513","salmon":"#fa8072","sandybrown":"#f4a460","seagreen":"#2e8b57","seashell":"#fff5ee","sienna":"#a0522d","silver":"#c0c0c0","skyblue":"#87ceeb","slateblue":"#6a5acd","slategray":"#708090","snow":"#fffafa","springgreen":"#00ff7f","steelblue":"#4682b4",
		"tan":"#d2b48c","teal":"#008080","thistle":"#d8bfd8","tomato":"#ff6347","turquoise":"#40e0d0",
		"violet":"#ee82ee",
		"wheat":"#f5deb3","white":"#ffffff","whitesmoke":"#f5f5f5",
		"yellow":"#ffff00","yellowgreen":"#9acd32"
	};

	if (typeof colours[colour.toLowerCase()] != 'undefined')
	return colours[colour.toLowerCase()];

	return false;
}

function wfu_download_file(ajaxurl_enc, filepath_enc, dataid) {
	var url = wfu_plugin_decode_string(ajaxurl_enc) + '?action=wfu_ajax_action_download_file&file=' + filepath_enc + '&dataid=' + dataid;
	var IF = document.getElementById("wfu_download_frame"); 
	IF.src = url;
}

function wfu_filedetails_userdata_changed(e) {
	var userdata_elements = document.getElementsByName("wfu_filedetails_userdata");
	var def, subm;
	var changed = false;
	for (var i = 0; i < userdata_elements.length; i++) {
		def = document.getElementById(userdata_elements[i].id.replace("wfu_filedetails_userdata_value_", "wfu_filedetails_userdata_default_"));
		subm = document.getElementById(userdata_elements[i].id.replace("wfu_filedetails_userdata_value_", "wfu_filedetails_userdata_"));
		subm.value = userdata_elements[i].value;
		if (userdata_elements[i].value != def.value) {
			changed = true;
			break;
		}
	}
	document.getElementById("dp_filedetails_submit_fields").disabled = !changed;
}

function wfu_Attach_FileDetails_Admin_Events() {
	var userdata_elements = document.getElementsByName("wfu_filedetails_userdata");
	for (var i = 0; i < userdata_elements.length; i++) wfu_attach_element_handlers(userdata_elements[i], wfu_filedetails_userdata_changed);
}
