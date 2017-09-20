<!-- BEGIN mod_mediasite_site_form.js -->

function mediasite_add_menu_placement_hint() {
	// find the id_my_mediasite_placement
	var placementSelect = document.getElementById('id_my_mediasite_placement');
	var hintText = null;
	try {
		hintText = document.getElementsByName('my_mediasite_placement_hint')[0];
	} catch (e) {
		alert(e);
	}
	if (placementSelect != null && hintText != null) {
		var parent = placementSelect.parentNode;
		// append some goodness
		var hint = document.createTextNode(hintText.value);
		var hintNode = document.createElement("div");
		hintNode.className = 'mediasite-add-menu-placement-hint';
		hintNode.appendChild(hint);
		parent.appendChild(hintNode);
	}
}

// mediasite_add_menu_placement_hint();
window.setTimeout(mediasite_add_menu_placement_hint, 500);

<!-- END mod_mediasite_site_form.js -->