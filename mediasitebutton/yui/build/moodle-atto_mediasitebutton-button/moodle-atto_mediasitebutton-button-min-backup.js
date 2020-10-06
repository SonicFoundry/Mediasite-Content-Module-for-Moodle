YUI.add("moodle-atto_mediasitebutton-button",function(o,t){var l="atto_mediasitebutton",s="",r="",d=1024,_="",u="",g="",c="",m="",p=!0,h=!1,b="atto_mediasitebutton",f='<div id="{{element_id}}_instruction_div" class="mediasite-instruction alert alert-info alert-block fade in" style="color: #0149bc;"><div><h2>{{instruction_title}}</h2><p>~~INSTRUCTIONAL_TEXT~~</p></div><button id="{{element_id}}_button_continue" class="mediasitebutton_continue btn btn-primary">{{continue}}</button></div><div id="{{element_id}}_iframe_div"><button id="{{element_id}}_button_top" class="mediasitebutton_submit btn btn-success" style="margin: 0 0 12px 20px; background-color: green;">{{insert}}</button><div id="{{element_id}}_div" class="mdl-align"><iframe id="{{element_id}}_iframe" src="{{launch_url}}" height="{{height}}" width="{{width}}" scrolling="auto"></iframe></div><button id="{{element_id}}_button_bottom" class="mediasitebutton_submit btn btn-success" style="margin: 0 0 12px 20px; background-color: green;">{{insert}}</button></div>';o.namespace("M.atto_mediasitebutton").Button=o.Base.create("button",o.M.editor_atto.EditorPlugin,[],{_lang:"en",initializer:function(t){if(this._log("Initializing atto_mediasitebutton in debug"),this._log("launch_url: "+t.launch_url),this._log("site_id: "+t.site_id),this._log("enabled: "+t.enabled),t.enabled){s=this._decodeUrl(t.launch_url),r=t.site_id,_="CustomIntegration",u=t.toolconsumerkey,g=t.newpage,c=t.extcontentreturnurl,m=this._decodeUrl(t.assignmentlaunchurl),p=1==t.isassignment;var e=o.DOM.winWidth();0<e&&(d=900<e?e-120:e-40),this._log("viewPortWidth: "+e+", WIDTH: "+d),this.addButton({title:"mediasitebutton",buttonName:"mediasitebutton",icon:"icon",iconComponent:"atto_mediasitebutton",callback:this._displayMediasiteUploadDialog})}else this._log("The atto_mediasitebutton is disabled, exiting.")},_decodeUrl:function(t){return decodeURIComponent(atob(t)).replace(/&amp;/g,"&")},_displayMediasiteUploadDialog:function(t,e){this._log("_displayMediasiteUploadDialog");var i=this.getDialogue({headerContent:M.util.get_string("dialogtitle",l),width:d+60+"px",height:"980px",focusAfterHide:e});h=!1,i.after("visibleChange",function(){!1===i.getAttrs().visible&&setTimeout(function(){i.reset()},5)}),i.set("bodyContent",this._buildMediasiteDialogContent()).show(),o.one("#{{element_id}}_iframe_div".replace("{{element_id}}",b)).hide(),this._resizeDialog(),this.markUpdated()},_buildMediasiteDialogContent:function(t){var e=p?M.util.get_string("insert",l):M.util.get_string("editor_insert",l),i=p?M.util.get_string("submission_instructions",l):M.util.get_string("editor_instructions",l),n=o.Handlebars.compile(f.replace("~~INSTRUCTIONAL_TEXT~~",i)),a=o.Node.create(n({launch_url:s,height:800,width:d,element_id:b,insert:e,continue:M.util.get_string("continue",l),instruction_title:M.util.get_string("dialogtitle",l)}));return this._form=a,this._form.one("#{{element_id}}_button_top".replace("{{element_id}}",b)).on("click",this._processMediasiteInsert,this),this._form.one("#{{element_id}}_button_bottom".replace("{{element_id}}",b)).on("click",this._processMediasiteInsert,this),this._form.one("#{{element_id}}_button_continue".replace("{{element_id}}",b)).on("click",this._showMediasiteIframe,this),a},_resizeDialog:function(t){this._log("_resizeDialog called");this._log("iframdeid: atto_mediasitebutton_iframe")},_showMediasiteIframe:function(t){this._log("Hide the instructions and show the iframe."),t.preventDefault(),o.one("#{{element_id}}_instruction_div".replace("{{element_id}}",b)).hide(),o.one("#{{element_id}}_iframe_div".replace("{{element_id}}",b)).show()},_processMediasiteInsert:function(t){var n=this;n._log("Starting to insert Mediasite content."),t.preventDefault();var e,i,a=document.getElementById(b+"_iframe").contentWindow,o=JSON.stringify({type:"embedrequest",Mode:_,NewPage:g,ExtContentReturnUrl:c,ToolConsumerKey:u});a.postMessage(o,"*"),window.addEventListener?(e="addEventListener",i="message"):(e="attachEvent",i="onmessage"),window[e](i,function(t){n._log("Received a postmessage"),n._log("SITEID: "+r);try{var e=JSON.parse(t.data);if("embedresponse"!==e.type)return void n._log("The message is of an unknown type");n._log("The message is type embedresponse");var i=n._layoutAbstractWithTemplate(e);n.getDialogue({focusAfterHide:null}).hide(),n.editor.focus(),h?n._log("The content has already been embedded once. Skip this event."):(n.get("host").insertContentAtFocusPoint(i),h=!0),n.markUpdated()}catch(t){return void n._log("There was an error parsing the message")}n._log("Finished processing postmessage")})},_layoutAbstractWithTemplate:function(t){this._log("_layoutAbstractWithTemplate called.");var e=p?o.Handlebars.compile('<div style="padding: 8px; background-color: white;"><div style="width: 100%; margin-bottom: 8px;"><a href="~~LAUNCHURL~~" target="_blank" /><div class="btn btn-secondary">{{grade_presentation}}</div></a></div><h3><a href="~~LAUNCHURL~~" target="_blank">{{title}}</a></h3><a href="~~LAUNCHURL~~" target="_blank"><img style="margin-left: 20px" align="right" src="{{thumbnail}}" alt="{{title}}"></a><p><span style="font-weight: bold;">{{record_date_label}}</span>: {{record_date}}</p><p><span style="font-weight: bold;">{{upload_date_label}}</span>: {{upload_date}}</p><p>{{description}}</p><p><span style="font-weight: bold;">{{tags_label}}</span><br /><ul style="list-style-type: none; margin: 0; padding: 0;">~~TAGS~~</ul></p><br clear="both" /><div style="text-align: center; width: 100%; margin-top: 8px;"><a href="~~LAUNCHURL~~" target="_blank" /><div class="btn btn-secondary">{{grade_presentation}}</div></a></div></div>'):o.Handlebars.compile('<div style="padding: 8px; background-color: white;"><div style="width: 100%; margin-bottom: 8px;"><a href="~~LAUNCHURL~~" target="_blank" /><h3><a href="~~LAUNCHURL~~" target="_blank">{{title}}</a></h3><a href="~~LAUNCHURL~~" target="_blank"><img style="margin-left: 20px" align="right" src="{{thumbnail}}" alt="{{title}}"></a><p><span style="font-weight: bold;">{{record_date_label}}</span>: {{record_date}}</p><p><span style="font-weight: bold;">{{upload_date_label}}</span>: {{upload_date}}</p><p>{{description}}</p><p><span style="font-weight: bold;">{{tags_label}}</span><br /><ul style="list-style-type: none; margin: 0; padding: 0;">~~TAGS~~</ul></p><br clear="both" /><div style="text-align: center; width: 100%; margin-top: 8px;"><a href="~~LAUNCHURL~~" target="_blank" /></div>'),i=this._buildListItemsFromArray(t.Tags),n="";1===t.Tags.length?n=M.util.get_string("tag",l):1<t.Tags.length&&(n=M.util.get_string("tags",l));var a=o.Node.create(e({title:t.Name,record_date:this._formatDate(t.Date,t.Culture),upload_date:this._formatDate(t.UploadDate,t.Culture),thumbnail:t.ThumbnailUrl,description:t.Description,grade_presentation:M.util.get_string("grade_presentation",l),record_date_label:M.util.get_string("record_date",l),upload_date_label:M.util.get_string("upload_date",l),tags_label:n}))._node.outerHTML.replace(new RegExp("~~LAUNCHURL~~","g"),m.replace("##ID##",t.ResourceId));return a=a.replace("~~TAGS~~",i)},_buildListItemsFromArray:function(t){for(var e="",i=0;i<t.length;i++)e+="<li>"+t[i]+"</li>";return e},_formatDate:function(t,e){if(void 0!==t){if(this._log("_formatDate: datestring = "+t+", culture = "+e),window.Intl&&"object"==typeof window.Intl){var i=new Date(t),n={year:"numeric",month:"numeric",day:"numeric",hour:"numeric",minute:"numeric",timeZoneName:"short"};return n.timeZone=Intl.DateTimeFormat().resolvedOptions().timeZone,new Intl.DateTimeFormat(e,n).format(i)}return i.toLocaleDateString()+" "+i.toLocaleTimeString()}},_log:function(t){console.log(l+" : "+t)}})},"@VERSION@",{requires:["moodle-editor_atto-plugin","get"]});