<!-- BEGIN course_settings.js -->
function siteChange(siteControl, data) {
	var newSiteId = siteControl.value;
	data.sites.forEach(function (element, index, array) {
		if (element.id == newSiteId) {
			// coursesTitle
			// id
			// name
			// showIntegrationCatalog
			var chkbox = document.getElementById('id_mediasite_courses_enabled');
			chkbox.checked = (element.showIntegrationCatalog > 1);
			chkbox.disabled = (element.showIntegrationCatalog == 0 || element.showIntegrationCatalog == 3);

			// find the label control
			for (var i = 0; i < chkbox.parentNode.children.length; i++) {
				console.log(i + ' : ' + chkbox.parentNode.children[i].id + ' : ' + chkbox.parentNode.children[i].tagName);
				if (chkbox.parentNode.children[i].tagName === 'LABEL') {
					chkbox.parentNode.children[i].innerHTML = (element.showIntegrationCatalog > 0) ? element.coursesTitle : '';
				}
			}
		}
	});
}

<!-- END course_settings.js -->