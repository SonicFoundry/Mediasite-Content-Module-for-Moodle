<!-- BEGIN basiclti_callback.js -->
	// var g_mediasiteLtiPopup; 

	function mediasiteLtiCallBack(message) {
		// alert(message.data.Mode);
		setFormValue('id_siteid', message.data.ext_content_return_url);
		setFormValue('id_name', message.data.Title);
		setFormValue('id_description', message.data.Description);
		setFormValue('id_resourcetype', message.data.EntityType);
		setFormValue('id_resourceid', message.data.ResourceId);
		setFormValue('id_launchurl', message.data.LaunchURL); // base64 encoded
		setFormValue('id_displaymode', message.data.Mode);
		if (message.data.EntityType === 'Presentation') {
			setFormValue('id_recorddateutc', (new Date(message.data.RecordDateTimeUTC).getTime() / 1000).toFixed(0));
			if (message.data.Presenters != undefined && message.data.Presenters != null && message.data.Presenters.length > 0) {
				setFormValue('id_presenters', message.data.Presenters.join('~!~')); // collapse array to string??
				setFormValue('id_presenters_display', message.data.Presenters.join('\n\n'));
			}
			if (message.data.Tags != undefined && message.data.Tags != null && message.data.Tags.length > 0) {	
				setFormValue('id_sofotags', message.data.Tags.join('~!~'));
				setFormValue('id_sofotags_display', message.data.Tags.join(', '));
			}
		} else {
			setFormValue('id_recorddateutc', (new Date().getTime() / 1000).toFixed(0));
			setFormValue('id_presenters', ''); // collapse array to string??
			setFormValue('id_sofotags', '');
		}

		setFormValue('id_showdescription', (message.data.Mode == 'PresentationLink' || message.data.Mode == 'BasicLTI') ? '0' : '1');

		var iframeDiv = document.getElementById('mediasite_lti_content')
		if (iframeDiv != undefined && iframeDiv != null) {
			iframeDiv.style.display = 'none';
		}

		toggleEmbedModeChange(message.data.Mode);
	}
	function setFormValue(id, value) {
		var el = document.getElementById(id);
		if (el != null && el != undefined) {
			el.value = value;
		}
	}
	function toggleEmbedModeChange(mode) {
		// find all the nodes with sofo-embed class
		// hide them all
		var allEmbedControls = document.getElementsByClassName("sofo-embed");
		Array.prototype.forEach.call(allEmbedControls, function (currentControl, index) {
			toggleDisplayOfParentDiv(currentControl, false);
		});

		// find all the nodes with sofo-embed-type-<mode>
		// display them all
		var myEmbedControls = document.getElementsByClassName("sofo-embed-type-" + mode);
		Array.prototype.forEach.call(myEmbedControls, function (currentControl, index) {
			toggleDisplayOfParentDiv(currentControl, true);
		});

		setFormValue('id_showdescription', (mode === 'BasicLTI') ? '0' : '1');
	}
	function toggleDisplayOfParentDiv(currentControl, visible) {
		var parentDiv = document.getElementById('fitem_' + currentControl.id);
		if (parentDiv == null) {
			parentDiv = document.getElementById(currentControl.id);
		}
		parentDiv.style.display = visible ? 'block': 'none';
	}

	window.addEventListener("message", mediasiteLtiCallBack, false);

<!-- END basiclti_callback.js -->
