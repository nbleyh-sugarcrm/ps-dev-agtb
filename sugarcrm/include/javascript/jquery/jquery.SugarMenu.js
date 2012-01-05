/* This is a simple plugin to render action dropdown menus from html.
 * John Barlow - SugarCRM
 * 
 * The html structure it expects is as follows:
 * 
 * <ul> 						- Menu root
 * 		<li></li> 				- First element in menu (visible)
 * 		<ul class="subnav">		- Popout menu (should start hidden)
 * 			<li></li>			- \
 * 			...					-  Elements in popout menu 
 * 			<li></li>			- /
 * 		</ul>
 * </ul>
 * 
 * By adding a class of "fancymenu" to the menu root, the plugin adds an additional "ab" class to the
 * dropdown handle, allowing you to make the menu "fancy" with additional css if you would like :)
 * 
 * Functions:
 * 
 * 		init: initializes things (called by default)... currently no options are passed
 * 		
 * 		addItem: (item, index)
 * 			item - created dom element or string that represents one
 * 			index(optional) - the position you want your new menuitem. If you leave this off,
 * 				the item is appended to the end of the list.
 */
(function($){
	var methods = {
		init: function(options){
			
			menuNode = this;
			if(!this.hasClass("SugarActionMenu")){
				//tag this element as a sugarmenu
				this.addClass("SugarActionMenu");
				
				//Fix custom code buttons programatically to prevent metadata edits
				this.find("input[type='submit']").each(function(index, node){
					var jNode = $(node);
					var parent = jNode.parent();
				
					if(parent.is("ul") && parent.hasClass("subnav") && jNode.css("display") != "none"){
						var newItem = $(document.createElement("li"));
						var newItemA = $(document.createElement("a"));
						newItemA.html(jNode.val());
						newItemA.click(function(event){
							jNode.click();
						});
							
						newItem.append(newItemA);
						jNode.before(newItem);
						jNode.css("display", "none");
					}
				});
				
				
				//look for all subnavs and set them up
				this.find("ul.subnav").each(function(index, node){
					var jNode = $(node);
					var parent = jNode.parent();
					var fancymenu = "";
					var slideUpSpeed = "fast";
					var slideDownSpeed = "fast";
					
					//if the dropdown handle doesn't exist, lets create it and 
					//add it to the dom
					if(parent.find("span").length == 0){
					
						//create dropdown handle
						var dropDownHandle = $(document.createElement("span"));
						if(menuNode.hasClass("fancymenu")){
							dropDownHandle.addClass("ab");
						}
					
						dropDownHandle.tipTip({maxWidth: "auto", 
											   edgeOffset: 10, 
						                       content: "More Actions", 
						                       defaultPosition: "top"});
						
						//add click handler to handle
						dropDownHandle.click(function(event){
							//close all other open menus
							$("ul.SugarActionMenu ul.subnav").each(function(subIndex, node){
								$(node).slideUp(slideUpSpeed);	
							});
							if(jNode.hasClass("ddopen")){
								jNode.slideUp(slideUpSpeed);
								jNode.removeClass("ddopen");
							}
							else{
								jNode.slideDown(slideDownSpeed).show();
								jNode.addClass("ddopen");	
							}
						});
					
						//add hover handler to handle
						dropDownHandle.hover(function(){
							dropDownHandle.addClass("subhover");
						}, function(){
							dropDownHandle.removeClass("subhover");
						});
					
						parent.append(dropDownHandle);
					}
					
					//when mouse hovers out of the subnav, slid it back up
					jNode.hover(function(){}, function(){
						jNode.slideUp(slideUpSpeed);
						jNode.removeClass("ddopen");
					});
					
					//bind click event to submenu items to hide the menu on click
					jNode.find("li").each(function(index, subnode){
						$(subnode).bind("click", function(){
							jNode.slideUp(slideUpSpeed);
							jNode.removeClass("ddopen");
						});
					});
				});
			}
			return this;
		},
		addItem : function(args){
			if(args.index == null){
				this.find("ul.subnav").each(function(index, node){
					$(node).append(args.item);
				})
			}
			else{
				this.find("ul.subnav").find("li").each(function(index, node){
					if(args.index == index+1){
						$(node).before(args.item);
					}
				});
			}
			return this;
		}
	}
		
	$.fn.sugarActionMenu = function(method) {
		
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(
					arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' + method + ' does not exist on jQuery.tooltip');
		}
	}
})(jQuery);  	