/**************************************************************
 *
 * Sortable jQuery Plugin for plugin
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 assets/js/sortable.js
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url 	 http://www.gnu.org/licenses/gpl-3.0.html
 * @created			 1/27/2016
 **************************************************************/


(function($){
	"use strict";
	
	// check collapsed status
	$.fn.sortableCheckCollaped = function(){
		var sortableObj = $(this);

		sortableObj.find("li").each(function(){
			var u = $(this).children("ul");
			var c = $(this).children("div");
			if(!u.length){
				// if.collapsed button and then remove it
				if(c.find(".collapsed").length){
					c.find(".collapsed").remove();
				}
				return;
			}

			// if exist child ul section, fix exteded
			if(u.css('display') == "none"){
				$(this).removeClass("sortable-expanded").addClass("sortable-collapsed");
			} else {
				$(this).removeClass("sortable-collapsed").addClass("sortable-expanded");
			}
							
			// if.collapsed button and then return
			if(c.find(".collapsed").length){
				return;
			}

			// add new collapsed button
			$("<span class = \"collapsed\"></span>").appendTo(c).click(function(){
				var wrapper = $(this).closest("li").children("ul");
				if(wrapper.css('display') == "none"){
					wrapper.slideDown();
					$(this).closest("li").removeClass("sortable-collapsed").addClass("sortable-expanded");
				} else {
					wrapper.slideUp();
					$(this).closest("li").removeClass("sortable-expanded").addClass("sortable-collapsed");
				}
			});
		});
	};
	
	// creat preloader
	$.fn.preloader = function(opt){
		if(opt && opt.id){
			$("#preloader-" + opt.id).remove();
			return;
		}
		
		/////
		var objID = new Date().getTime();
		if(opt && opt.right){
			$("<div></div>").insertAfter($(this)).addClass("preloader").addClass("alignright").attr("id", "preloader-" + objID);
		} else {
			$("<div></div>").insertAfter($(this)).addClass("preloader").attr("id", "preloader-" + objID);
		}

		return objID;
	};
			
	// search sortable
	$.fn.sortableSearch = function(keyElemObj, initSearch){
		if(!keyElemObj){
			return;
		}

		// search sortable list
		var search = initSearch? "": $.trim(keyElemObj.val()).toLowerCase();

		// init search form
		keyElemObj.val(search);
		$(this).find(".sortable-empty").remove();
		
		// search
		var results = 0;
		$(this).find("li").each(function(){
			if(search && $(this).text().toLowerCase().indexOf(search) == -1){
				$(this).hide();
			} else {
				results++;
				$(this).show();
			}
		});

		if(!results){
			$("<div></div>").appendTo($(this)).html($(this).data("empty")).attr({"class": "sortable-empty"});
		}

		return;
	};

	$.fn.selectItem = function(callback, initFlag){
		var selectedItem = $(this);
		
		// select callback
		var selectFunction = function(callback){
			if(selectedItem.hasClass("actived")){
				return;
			}

			selectedItem.parents("ul.sortable").find("li").removeClass("actived");
			selectedItem.addClass("actived");
			
			if(callback){
				callback(selectedItem);
			}
		};
		
		/////
		if(initFlag){
			selectFunction(callback);
		}
		
		// add click event
		selectedItem.children("div:first-child").children("span:first-child").click(function(){
			selectFunction(callback);
		});

	};

	// init sortable
	$.fn.initSortable = function(initDocInfo, selectCallback){
		var sortableWrapper = $(this);
		var sortableObj = $(this).children("ul.sortable");
		var newItemText = sortableObj.data("new-menu-text");
		
		// build sortable
		sortableObj.nestedSortable({
			forcePlaceholderSize: true,
			handle: "div",
			listType: "ul",
			helper:	"clone",
			items: "li",
			opacity: .6,
			placeholder: "placeholder",
			revert: 250,
			tabSize: 25,
			tolerance: "pointer",
			toleranceElement: "> div",
			maxLevels: 5,
			branchClass: "sortable-branch",
			collapsedClass: "sortable-collapsed",
			disableNestingClass: "sortable-no-nesting",
			errorClass: "sortable-error",
			expandedClass: "sortable-expanded",
			hoveringClass: "sortable-hovering",
			leafClass: "sortable-leaf",
			stop: function(){
				var obj = $(this);

				// run after ended animation
				setTimeout(function(){
					obj.sortableCheckCollaped();
				}, 100);
			}
		});

		// init sortable form with init datas
		if(initDocInfo){
			var addMenus = function(menusInfo){
				var htmlMenus = "";
				for(var i in menusInfo){
					htmlMenus += "<li data-id = \"" + menusInfo[i].id + "\">";
					htmlMenus += "	<div><span>" + menusInfo[i].label + "</span></div>";

					if(menusInfo[i].children){
						htmlMenus += "<ul>";
						
						////
						htmlMenus += addMenus(menusInfo[i].children);
						////

						htmlMenus += "</ul>";
					}
					htmlMenus += "</li>";
				}

				return htmlMenus;
			};

			//////
			sortableObj.html(addMenus(initDocInfo));
			sortableObj.children("li:first-children").selectItem(selectCallback);
		}
		/////////////////////////////

		// init collapsed status
		sortableObj.sortableCheckCollaped();

		// init select callback
		sortableObj.find("li").each(function(){
			$(this).selectItem(selectCallback);
		});

		///
		sortableWrapper.find("#sortable-search").keyup(function(){
			sortableObj.sortableSearch($(this));
		});

		// init search
		sortableObj.sortableSearch(sortableWrapper.find("#sortable-search"));

		// add new menu
		sortableWrapper.find("[data-action=sortable-new]").click(function(){
			////

			$("<li></li>").appendTo(sortableObj)
				   .attr({"data-id": Math.floor((Math.random() * 10000000) + 1)})
				   .html("<div><span>" + newItemText + " " + (sortableObj.find("li").length) + "</span></div>")
				   .selectItem(selectCallback, true);

			sortableObj.sortableSearch(sortableWrapper.find("#sortable-search"), true);
		});
	};

})(jQuery);