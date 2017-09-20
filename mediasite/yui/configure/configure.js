YUI.add('moodle-mod_mediasite-configure', function (Y, NAME) {

    M.mod_mediasite = M.mod_mediasite || {};
    M.mod_mediasite.configure = {
        init: function (formid, courseid) {
            var self = this;
            Y.on("click", function () {
                //console.log('Add site');
                //var addSiteUrl = M.cfg.wwwroot + '/mod/mediasite/site/add.php';
                //window.location = addSiteUrl;
                var addSiteUrl = 'add.php';
                document.location = addSiteUrl;
            }, "#id_siteaddbutton", self);
        }
    };
}, '@VERSION@', {
    "requires": [
        "base",
        "event",
        "event-delegate",
        "node",
        "node-event-delegate",
        "node-base"
    ]
});
