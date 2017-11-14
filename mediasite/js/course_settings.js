<!-- BEGIN course_settings.js -->
/**
 * Provides client side update when a different Mediasite site is selected in the course settings.
 *
 * The Moodle plugin precheck incorrectly reports 'SiteChange' is defined but never used.
 * SiteChange is called from /mediasite/site/mod_course_settings_form.php when the course's
 * selected site is changed.
 *
 * @class SiteChange
 * @param {control} siteControl
 * @param {object} data
 */
function SiteChange(siteControl, data) {
    var newSiteId = siteControl.value;
    data.sites.forEach(function(element) {
        if (element.id == newSiteId) {
            var chkbox = document.getElementById('id_mediasite_courses_enabled');
            chkbox.checked = (element.showIntegrationCatalog > 1);
            chkbox.disabled = (element.showIntegrationCatalog == 0 || element.showIntegrationCatalog == 3);

            var i = 0;
            if (chkbox.parentNode.tagName.toUpperCase() === 'LABEL') {
                var objectstore = new Array();
                var parent = chkbox.parentNode;
                for (i = 0; i < chkbox.parentNode.children.length; i++) {
                    objectstore[i] = chkbox.parentNode.children[i].cloneNode();
                }
                while (parent.firstChild) {
                    parent.removeChild(parent.firstChild);
                }
                for (i = 0; i < objectstore.length; i++) {
                    parent.appendChild(objectstore[i]);
                }
                var label = document.createElement('span');
                label.innerHTML = (element.showIntegrationCatalog > 0) ? element.coursesTitle : '';
                parent.appendChild(label);
             } else {
                for (i = 0; i < chkbox.parentNode.children.length; i++) {
                    if (chkbox.parentNode.children[i].tagName.toUpperCase() === 'LABEL') {
                        chkbox.parentNode.children[i].innerHTML = (element.showIntegrationCatalog > 0) ? element.coursesTitle : '';
                    }
                }
            }
        }
    });
}

<!-- END course_settings.js -->