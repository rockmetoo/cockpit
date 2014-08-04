(function($) {
	$.fn.formValidate = function(elements) {
	
		// Japanese registration/ie6 bug: disable for ie6.
		if( $.browser.msie && $.browser.version < 7 ){
			return false;
		}

		var RPC = document.location.protocol + "//" + document.location.host + "/json.php";
	
		function initValidation(el) {
			$el = $(el);
			var addblur = true;
			$.each(["field_date"], function(i,v) { if($el.hasClass(v)) addblur = false; });
			if(addblur) {
				$el.blur(function() {
					$(document).data("html_form_elements")[this.id].cache_val = $(this).val();
					$(document).data("html_form_elements")[this.id].cache_time = -1;
					result(this);
				});
			}
		}
	
		//mark error messages for display
		function markError(el, error, value) {
			var id = $(el).attr("id");
			if(value) {
				$(document).data("html_form_elements")[id].triggered[error] = true;
			}
			else {
				delete $(document).data("html_form_elements")[id].triggered[error];
			}
		}
		
		//display the error decoration
		function displayError(el) {
			var id = $(el).attr("id");
			var $list = $("#list_of_error_" + id).eq(0);
			
			// from the triggered errors and the available messages, generate a list that can be displayed
			var show_items = {};
			var show_default = false;
			$.each($(document).data("html_form_elements")[id].triggered, function(error, value) {
				var $item = $("#error_item_" + id + "_" + error).eq(0);
				if($item.length && value) show_items[$item.attr("id")] = true;
				else show_default = true; 
			});
			if(show_default) show_items["error_item_" + id + "_default"] = true;
			
			//display one error from list.
			if($list.length) {
				var i = 0;
				$("li", $list).each(function() {
					if(show_items[this.id] && i < 1) {
						$(this).removeClass("hidden");
						i = 1;
					}
					else {
						$(this).addClass("hidden");
					}
				});
				if(i && $list.hasClass("hidden")) {
					$list.removeClass("hidden");
				}
				else if(!i) {
					$list.addClass("hidden");
				}
			}
			
			//display the element decoration
			var j = 0;
			for(var k in $(document).data("html_form_elements")[id].triggered) { j++; } 	
			if(j) {
				$(el).addClass("invalid");
			}
			else {
				$(el).removeClass("invalid");
			}
		}
		
		// tests whether the value of given element is valid
		function result(el, branched) {
			if(el) {
				//get element rules
				var options = $(document).data("html_form_elements")[el.id].rules;
				
				//get value
				var val = $(el).val();
				//if checkbox, then set val to 1 if checked, "" if not.
				//This will make it compatible with the mandatory check.
				if($(el)[0].getAttribute("type") == "checkbox") {
					if($(el)[0].checked) val = "true";
					else val = "";
				}
				
				//loop through rules and check each one.
				var opt_total = 0;
				var opt_checked = 0;
				for(var opt in options) {
					opt_total++;
					var fail = false;
					//check special rules
					if(options[opt].mandatory && val == "") fail = true;
					if(options[opt].regexp) {
						var re = options[opt].regexp.split(options[opt].regexp.charAt(0));
						var re_str = re[1].replace(/\\/g, "\\\\");
						if(!val.match(RegExp(re_str, re[2]))) fail = true;
					}
					if(options[opt].mustmatch && val != $("#" + options[opt].mustmatch).val()) fail = true;
					if(options[opt].eitheror && val == "" && $("#" + options[opt].eitheror).val() == "") fail = true;
					if(options[opt].eitheror && !branched) result($("#" + options[opt].eitheror).get(0), true);
					if(options[opt].maxvalue && parseFloat(val) > parseFloat(options[opt].maxvalue)) fail = true;	
					if(options[opt].maxlength && val.length > parseInt(options[opt].maxlength)) fail = true;	
					
					//check custom callback function rules
					if(options[opt].callback && options[opt].callback != "") {
						var id = $(el).attr("id");
						$.rpcJSON(RPC, "formValidate" , {"callback": options[opt].callback, "value": val, "params": options[opt].params}, {"fail":fail, "id":id, "name":options[opt].name, "eitheror":options[opt].eitheror}, function(data) {
							var el = $("#" + data.passthrough.id);
							if(!data.result || data.passthrough.fail) {
								if(data.passthrough.eitheror) markError($("#" + data.passthrough.eitheror), data.passthrough.name, true);
								markError(el, data.passthrough.name, true);
							}
							else {
								if(data.passthrough.eitheror) markError($("#" + data.passthrough.eitheror), data.passthrough.name, false);
								markError(el, data.passthrough.name, false);
							}
							opt_checked++;
						});
					}
					else {
						if(options[opt].eitheror) markError($("#" + options[opt].eitheror), options[opt].name, fail);
						markError(el, options[opt].name, fail);
						opt_checked++;
					}
				}
				//only display changes when all checks have been completed.
				var iv = setInterval(function() {
					if(opt_checked == opt_total) {
						clearInterval(iv);
						displayError(el);
					}
				}, 300);
			}
		}

		//if no rules are given, attempt to use already set rules
		if(!elements) {
			$("input,textarea,select", this).each(function(i) {
				initValidation(this);
			});
			return;
		}
	
		//get page form element list
		if(!$(document).data("html_form_elements")) $(document).data("html_form_elements",{});
		
		for(var el in elements) {
			var $el = $("#" + el, this);
			if($el.length > 0) {
				$(document).data("html_form_elements")[el] = {cache_val: $el.val(), cache_time: -1, rules: elements[el], triggered: {}};
				initValidation($el);
			}
		}
		
		if(!$(document).data("html_form_check")) {
			var interval = setInterval(function() {
				$.each($(document).data("html_form_elements"), function(el, el_data) {
					$el = $("#" + el);
					if($el.val() != el_data.cache_val) {
						$(document).data("html_form_elements")[el].cache_val = $el.val();
						$(document).data("html_form_elements")[el].cache_time = 0;
					}
					else if($(document).data("html_form_elements")[el].cache_time >= 0) {
						$(document).data("html_form_elements")[el].cache_time++;
					}
					if($(document).data("html_form_elements")[el].cache_time >= 2) {
						result($el.get(0), false);
						$(document).data("html_form_elements")[el].cache_time = -1;
					}
				}); 
			}, 500);
			$(document).data("html_form_check", interval);
		}
		return this;
	};
})(jQuery);
