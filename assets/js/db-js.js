/**************************************************************
 *
 * General javascript modules
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 assets/js/db-js.js
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url 	 http://www.gnu.org/licenses/gpl-3.0.html
 * @created			 1/27/2016
 **************************************************************/


/*
 * Global Functions
 */

// remove doc from list
var removeDocOfList = function(msg, url){
	if(confirm(msg)){
		window.location.href = url;
	} else {
		return false;
	}
	return false;
};


/*
 * Global Varialbles
 */

// documentation menus & contents
var docInfo = new Array;

// selected menu
var selectedMenu = 0;


/*
 * Functions
 */
(function($){

	$(document).ready(function(){


		//////////////////////////////////////////////////////
		// update doc info
		var getDocInfo = function(selectedItemID){
			var menusInfo = $("#documentation-menus ul.sortable").nestedSortable('toHierarchy');

			// tree object array to flattened one level object array
			var indexedDocInfo = new Array;
			var convertIndexed = function(indexingArray){
				for(var i in indexingArray){

					if(indexingArray[i].children){
						convertIndexed(indexingArray[i].children);
					}
					
					// push item
					indexedDocInfo[indexingArray[i].id] = {
						id:					indexingArray[i].id,
						label:				indexingArray[i].label,
						chapter_title:		indexingArray[i].chapter_title,
						chapter_desc:		indexingArray[i].chapter_desc,
						chapter_contents:	indexingArray[i].chapter_contents
					};					
				}
			};
			if(docInfo){
				convertIndexed(docInfo);
			}

			// if exist original selected menu, and then update the documentation array
			if(selectedMenu){

				// update function
				var updateDocInfo = function(docData){
					
					if(!docData.length){
						return;
					}

					for(var i in docData){

						// update item info with current editor
						if(docData[i].id == selectedMenu){
							
							docData[i].id = selectedMenu;
							docData[i].label = $("#doc-menu-name").val();
							docData[i].chapter_title = $("#doc-chapter-title").val();
							docData[i].chapter_desc = $("#doc-chapter-desc").val();
							docData[i].chapter_contents = tinymce.editors['doc-chapter-contents'].getContent();

						} else {

							if(indexedDocInfo[docData[i].id]){
								// add detail doc info to menu object array 
								docData[i] = $.extend({}, docData[i], indexedDocInfo[docData[i].id]);
							}
						}

						// push new doc info
						indexedDocInfo[docData[i].id] = {
							id:					docData[i].id,
							label:				docData[i].label,
							chapter_title:		docData[i].chapter_title,
							chapter_desc:		docData[i].chapter_desc,
							chapter_contents:	docData[i].chapter_contents
						};
						
						// if the item have children objects, repeat to update for children objects
						if(docData[i].children){

							updateDocInfo(docData[i].children);
						}
					}
				};
				
				// update menu info
				updateDocInfo(menusInfo);

				/////////////////////////
			} else {

				// init doc info with new item if menu list have one new item only
				indexedDocInfo[selectedItemID] = menusInfo[0];
			}
			
			// update doc info from updated menu info
			docInfo = menusInfo;

			// update current selected item
			selectedMenu = selectedItemID;

			// init form
			initEditChapter(indexedDocInfo[selectedMenu]);
			
			/////////////////////
			return docInfo;
		};
		
		// init chapter editor
		var initEditChapter = function(formData){
			if(formData){
				if(formData.label){
					$("#doc-menu-name").val(formData.label);
				} else {
					$("#doc-menu-name").val("");
				}

				if(formData.chapter_title){
					$("#doc-chapter-title").val(formData.chapter_title);
				} else {
					$("#doc-chapter-title").val("");
				}

				if(formData.chapter_desc){
					$("#doc-chapter-desc").val(formData.chapter_desc);
				} else {
					$("#doc-chapter-desc").val("");
				}

				if(formData.chapter_contents){
					tinymce.editors['doc-chapter-contents'].setContent(formData.chapter_contents);
				} else {
					tinymce.editors['doc-chapter-contents'].setContent("");
				}
			}

			return;
		};
		
		////////////////////////////////////
		// init sortable
		$("<div></div>").insertBefore(".content-wrapper").addClass("load_form");

		$(window).load(function(){
			$(".load_form").fadeOut(300, function(){
				$(this).remove();
				$(".content-wrapper").css('visibility','visible').hide().fadeIn(300);
			});			

			// init form			
			$("#documentation-menus").initSortable(docInfo, function(selectedItem){
				getDocInfo(selectedItem.data("id"));
			});

			// init form with original data again if edited this documentation before
			if(docInfo.length){
				selectedMenu = docInfo[0].id;
				
				initEditChapter(docInfo[0]);
				$("#documentation-menus ul.sortable li[data-id=" + selectedMenu + "]").addClass("actived");
			}
			
			// init menu name as changed manu name
			$("#doc-menu-name").keyup(function(){
				var menuObj = $("#documentation-menus li[data-id=" + selectedMenu + "]");

				if(menuObj.length){
					menuObj.children("div").children("span:first-child").text($(this).val());
				}
			});
		});


		//////////////////////////////////////////////////////////////////////////////////////////////
		// upload logo
		$("#upload-doc-logo").click(function(e){
			e.preventDefault();

			var uploadBtn = $(this);

			var image = wp.media({ 
				title: uploadBtn.val(),
				// mutiple: true if you want to upload multiple files at once
				multiple: false
			}).open()
			.on("select", function(e){

				// This will return the selected image from the Media Uploader, the result is an object
				var uploaded_image = image.state().get("selection").first();

				// We convert uploaded_image to a JSON object to make accessing it easier
				// Output to the console uploaded_image
				var image_url = uploaded_image.toJSON().url;

				// Let's assign the url value to the input field
				$("#doc-logo").val(image_url);
				
				if($("#doc-logo-preview").length){
					$("#doc-logo-preview").attr({"src": image_url});
				} else {
					$("<img>").appendTo(uploadBtn.parent()).attr({
						id: "doc-logo-preview",
						src: image_url
					});
				}				
			});
		});
		
		///////////////////////////

		var processFlag = false; // block all functions while ajax call
		
		///////////////////////////////////////////////////////////////////////////////
		// save new doc
		$("#documentation-save").click(function(){
			if(processFlag){
				alert("Please wait while until current process is finished!");
				return;
			}
			
			processFlag = true;
			var loadingbar = $(this).preloader({right: true});			
			$.post(
				db_var.ajaxurl, 
				{
					action:			"new",
					the_issue_key:	db_var.the_issue_key,

					id:				"" + $("#doc-id").val(),
					skin:			"" + $("#doc-skin").val(),
					logo:			"" + $("#doc-logo").val(),
					title:			"" + $("#doc-title").val(),
					desc:			"" + tinymce.editors['doc-desc'].getContent(),
					doc:			"" + JSON.stringify(getDocInfo(selectedMenu))
				}, 
				function(response){
					processFlag = false;
					$(this).preloader({id: loadingbar});

					/////////////
					response = $.parseJSON(response);
					
					// success
					if(response.success){
						alert(response.success_txt);
						
						location.href = $("#doc-list-page").attr("href");

					} else {
						// error
						if(response.error){						
							alert(response.error_txt);
						} else {
							alert("AJAX ERROR!");
						}
					}
				}
			);
		});

		///////////////////////////////////////////////////////////////////////////////
		// update doc
		var autoTimer = false;

		var updateDocumentation = function(obj, autoFlag){
			if(processFlag){
				alert("Please wait while until current process is finished!");
				return;
			}

			// remove auto save timer
			if(autoTimer){
				autoSave(autoTimer);
			}
			
			// show preloader
			processFlag = true;
			var loadingbar = obj.preloader({right: true});	

			// ajax post
			$.post(
				db_var.ajaxurl, 
				{
					action:			"edit",
					the_issue_key:	db_var.the_issue_key,

					id:				"" + $("#doc-id").val(),
					skin:			"" + $("#doc-skin").val(),
					logo:			"" + $("#doc-logo").val(),
					title:			"" + $("#doc-title").val(),
					desc:			"" + tinymce.editors['doc-desc'].getContent(),
					doc:			"" + JSON.stringify(getDocInfo(selectedMenu))
				}, 
				function(response){
					// remove preloader
					processFlag = false;
					obj.preloader({id: loadingbar});
					
					// init auto save
					autoTimer = autoSave(null, obj);

					/////////////
					response = $.parseJSON(response);
					
					// success
					if(response.success){
						if(!autoFlag){
							alert(response.success_txt);
						}
					} else {
						// error
						if(response.error){						
							alert(response.error_txt);
						} else {
							alert("AJAX ERROR!");
						}
					}
				}
			);
		};

		var autoSave = function(timer, intervalObj){
			if(timer){
				clearInterval(timer);
			} else {
				var interval = parseInt(intervalObj.data("auto-save")) * 1000;

				if(interval > 0){

					return setInterval(function(){
						updateDocumentation(intervalObj, true);
					}, interval);
				}				
			}
		}
		
		//////
		var update_btn = $("#documentation-update");
		update_btn.click(function(){
			updateDocumentation($(this));
		});

		// init auto-save
		setTimeout(function(){
			autoTimer = autoSave(null, update_btn);
		}, parseInt(update_btn.data("auto-save")) * 1000);


		///////////////////////////////////////////////////////////////////////////////
		// remove the doc
		$("#documentation-remove").click(function(){
			if(processFlag){
				alert("Please wait while until current process is finished!");
				return;
			}

			if(confirm($(this).data("confirm-text"))){
				processFlag = true;
				var loadingbar = $(this).preloader({right: true});			
				$.post(
					db_var.ajaxurl, 
					{
						action:			"del",
						the_issue_key:	db_var.the_issue_key,

						id:				$("#doc-id").val()
					}, 
					function(response){
						processFlag = false;
						$(this).preloader({id: loadingbar});

						/////////////
						response = $.parseJSON(response);
						
						// success
						if(response.success){
							alert(response.success_txt);
							
							location.href = $("#doc-list-page").attr("href");

						} else {

							// error
							if(response.error){						
								alert(response.error_txt);
							} else {
								alert("AJAX ERROR!");
							}
						}
					}
				);
			}
		});

		///////////////////////////////////////////////////////////////////////////////
		// export the doc
		$("#documentation-export").click(function(){
			if(processFlag){
				alert("Please wait while until current process is finished!");
				return;
			}

			processFlag = true;
			var loadingbar = $(this).preloader();			
			$.post(
				db_var.ajaxurl, 
				{
					action:			"export",
					the_issue_key:	db_var.the_issue_key,

					id:				$(this).data("id"),
					type:			$("[name=export-type]:checked").val()
				}, 
				function(response){
					processFlag = false;
					$(this).preloader({id: loadingbar});

					/////////////
					response = $.parseJSON(response);
					
					// success
					if(response.success){
						window.open(response.success_txt, "_blank");
					} else {
						// error
						if(response.error){						
							alert(response.error_txt);
						} else {
							alert("AJAX ERROR!");
						}
					}
				}
			);
		});

		///////////////////////////////////////////////////////////////////////////////
		// reset settings
		$("#settings-reset").click(function(){
			if(processFlag){
				alert("Please wait while until current process is finished!");
				return;
			}

			if(!confirm($(this).data("confirm-text"))){
				return;
			}

			processFlag = true;
			var loadingbar = $(this).preloader();			
			$.post(
				db_var.ajaxurl, 
				{
					action:			"settings",
					the_issue_key:	db_var.the_issue_key,

					type:			"reset"
				}, 
				function(response){
					processFlag = false;
					$(this).preloader({id: loadingbar});

					/////////////
					response = $.parseJSON(response);
					
					// success
					if(response.success){
						// reset fields
						$("#rows_per_page").val($("#rows_per_page").data("default-value"));
						$("#auto_save").val($("#auto_save").data("default-value"));

						alert(response.success_txt);
					} else {
						// error
						if(response.error){						
							alert(response.error_txt);
						} else {
							alert("AJAX ERROR!");
						}
					}
				}
			);
		});

		///////////////////////////////////////////////////////////////////////////////
		// save settings
		$("#settings-update").click(function(){
			if(processFlag){
				alert("Please wait while until current process is finished!");
				return;
			}

			if(!confirm($(this).data("confirm-text"))){
				return;
			}

			processFlag = true;
			var loadingbar = $(this).preloader();			
			$.post(
				db_var.ajaxurl, 
				{
					action:			"settings",
					the_issue_key:	db_var.the_issue_key,

					type:			"save",
					rows_per_page:	$("#rows_per_page").val(),
					auto_save:		$("#auto_save").val(),
				}, 
				function(response){
					processFlag = false;
					$(this).preloader({id: loadingbar});

					/////////////
					response = $.parseJSON(response);
					
					// success
					if(response.success){
						alert(response.success_txt);
					} else {
						// error
						if(response.error){						
							alert(response.error_txt);
						} else {
							alert("AJAX ERROR!");
						}
					}
				}
			);
		});

		////////////////
		// import new skin
		$("#settings-skin-import").click(function(){
			if(!$.trim($("#new_skin").val())){
				alert($(this).data("empty-text"));
				return;
			}

			if(!confirm($(this).data("confirm-text"))){
				return;
			}

			$("#import-new-skin").submit();
		});

		/////////////////////
		// select skin
		$("#settings-skin-select").click(function(){
			if(!$("#sel-skin").val()){
				alert($(this).data("error-text"));
				return;
			}

			/////
			location.href = $(this).data("href") + $("#sel-skin").val();
			return;
		});

		// init code editor
		$("[data-code-editor=css]").ace({theme: "chrome", lang: "css"});

		
		//////////////////////////
		// delete selected skin
		$("#settings-skin-delete").click(function(){
			if(processFlag){
				alert("Please wait while until current process is finished!");
				return;
			}

			if(!$("#sel-skin").val()){
				alert($(this).data("error-text"));
				return;
			}
			
			if(!confirm($(this).data("confirm-text"))){
				return;
			}
			
			var redirect_url = $(this).data("redirect");
			processFlag = true;
			var loadingbar = $(this).preloader({right: true});			
			$.post(
				db_var.ajaxurl, 
				{
					action:			"skin",
					the_issue_key:	db_var.the_issue_key,

					type:			"delete",
					skin:			$("#sel-skin").val()
				}, 
				function(response){
					processFlag = false;
					$(this).preloader({id: loadingbar});

					/////////////
					response = $.parseJSON(response);
					
					// success
					if(response.success){
						alert(response.success_txt);

						location.href = redirect_url;
						return;
					} else {
						// error
						if(response.error){						
							alert(response.error_txt);
						} else {
							alert("AJAX ERROR!");
						}
					}
				}
			);
		});
		
		// update selected skin
		$("#settings-skin-update").click(function(){
			if(processFlag){
				alert("Please wait while until current process is finished!");
				return;
			}

			if(!$("#sel-skin").val()){
				alert($(this).data("error-text"));
				return;
			}

			if(!confirm($(this).data("confirm-text"))){
				return;
			}

			processFlag = true;
			var loadingbar = $(this).preloader({right: true});			
			$.post(
				db_var.ajaxurl, 
				{
					action:			"skin",
					the_issue_key:	db_var.the_issue_key,

					type:			"update",
					skin:			$("#sel-skin").val(),
					contents:		$("#code-editor").val()
				}, 
				function(response){
					processFlag = false;
					$(this).preloader({id: loadingbar});

					/////////////
					response = $.parseJSON(response);
					
					// success
					if(response.success){
						alert(response.success_txt);
					} else {
						// error
						if(response.error){						
							alert(response.error_txt);
						} else {
							alert("AJAX ERROR!");
						}
					}
				}
			);
		});

		////////////////
		// import new documentation
		$("#settings-doc-import").click(function(){
			if(!$.trim($("#new_doc").val())){
				alert($(this).data("empty-text"));
				return;
			}

			if(!confirm($(this).data("confirm-text"))){
				return;
			}

			$("#import-new-doc").submit();
		});
		
	});
})(jQuery);