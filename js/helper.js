var myMessages = ['info', 'warning', 'error', 'success'];

function hideAllMessages()
{
	//this array will store height for each
	 var messagesHeights = new Array();
	 for(i=0; i<myMessages.length; i++)
	 {
		//fill array
		 messagesHeights[i] = $('.' + myMessages[i]).outerHeight();
		//move element outside viewport
		 $('.' + myMessages[i]).css('top', -messagesHeights[i]);
	 }
}

var _capsTriggered = false;

var capsDetector = function(event, message)
{		
	if(_capsTriggered == false)
	{
		if(getKeyCode(event) === 20)
		{
			var alertMessage = "<span id='capslock'>" + message + "</span>";
			$("#password").after(alertMessage);
			$("#capslock").fadeIn("slow");
			_capsTriggered = true;
		}		
	}
	else if((_capsTriggered == true) && (getKeyCode(event) === 20))
	{
		$("#capslock").remove();
		_capsTriggered = false;
	}	
}

$.fn.clearForm = function()
{
	return this.each(function()
	{
		var type = this.type, tag = this.tagName.toLowerCase();
		if(tag == 'form') return $(':input',this).clearForm();
		if(type == 'text' || type == 'password' || tag == 'textarea') this.value = '';
		else if(type == 'checkbox' || type == 'radio') this.checked = false;
		else if(tag == 'select') this.selectedIndex = -1;
	});
};

// Show loading div
function addLoadEvent()
{
	var pos = 0;

	if (window.innerHeight)
	{
		pos = window.pageYOffset
	}
	else if (document.documentElement && document.documentElement.scrollTop)
	{
		pos = document.documentElement.scrollTop
	}
	else if (document.body)
	{
		pos = document.body.scrollTop
	}
	
	$("<div id='loading_event' style='position:absolute;top:"+pos+"px;right:0px;background-color:#E60202;z-index:9999;color:#FFFFFF;font-size:14px;padding:5px;'>Loading...</div>").appendTo("body");
};

// Remove loading div
function removeLoadEvent()
{
	$("#loading_event").remove();
};

function httpTestPlotFormSubmit()
{
	$("#httpTriggerTestPlotForm").submit(function(event)
	{
		// prevent form to submit
		event.preventDefault();
		
		var selectedRadioValue	= $("input[name=plotName]:radio:checked").val();
		
		// XXX: IMPORTANT - remove space and dot from plot name
		selectedRadioValue		= selectedRadioValue.replace(/ /g, 'space');
		selectedRadioValue		= selectedRadioValue.replace(/\./g, 'dot');
		
		$.ajax(
		{
			url : "ajax/httpTriggerTestPlot.php",
			type: "POST",
			data : $("#httpTriggerTestPlotForm").serialize(),
			success: function(data, textStatus, jqXHR)
			{
				data = JSON.parse(data);
				
				if(data["error"] == 1)
				{
					$("#serverMsg").empty();
					var errorDiv = "<div class='error content_notice transparent'><img src='../images/unsuccessful.png'></img>" + data["message"] + "</div>"
					$("#serverMsg").html(errorDiv);
					
					setTimeout(function()
					{
						$(".error").fadeOut(1500);
				    }, 8000);
				}
				else if(data["error"] == 2)
				{
					$("#serverMsg").empty();
					var errorDiv = "<div class='error content_notice transparent'><img src='../images/unsuccessful.png'></img>" + data["message"] + "</div>"
					$("#serverMsg").html(errorDiv);
				}
				else
				{
					$("#serverMsg").empty();
					var successDiv = "<div class='success content_notice transparent'><img src='../images/successful.png'></img>" + data["message"] + "</div>"
					$("#serverMsg").html(successDiv);
					
					setTimeout(function()
					{
						$(".success").fadeOut(1500);
				    }, 8000);
					
					var statusTimer;
					
					(function statusChecker()
					{
						$.ajax({
							url: "ajax/httpTestPlotStatus.php",
							type: "POST",
							data : $("#httpTriggerTestPlotForm").serialize(),
						    success: function(data)
						    {
						    	data = JSON.parse(data);
						    	
						    	if(data["val"] == 0)
					    		{
						    		// not testing so need not to show busy icon
						    		$("#" + selectedRadioValue + "Status").html(data["message"]);
					    		}
						    	else if(data["val"] == 1)
					    		{
						    		// test is accepted so need to show busy icon
						    		$("#" + selectedRadioValue + "Status").html(data["message"] + "&nbsp;<img src='../images/busy.gif'></img>");
						    		statusTimer = setTimeout(statusChecker, 5000);
					    		}
						    	else if(data["val"] == 2)
					    		{
						    		// test is running so need to show busy icon
						    		$("#" + selectedRadioValue + "Status").html(data["message"] + "&nbsp;<img src='../images/busy.gif'></img>");
						    		statusTimer = setTimeout(statusChecker, 5000);
					    		}
						    	else if(data["val"] == 3)
					    		{
						    		// test is completed so need not to show busy icon
						    		$("#" + selectedRadioValue + "Status").html(data["message"]);
					    		}
						    },
						    error: function()
						    {
						    }
						});
					})();
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#serverMsg").empty();
				var errorDiv = "<div class='error content_notice transparent'><img src='../images/unsuccessful.png'></img>Communication error occured</div>"
				$("#serverMsg").html(errorDiv);
				
				setTimeout(function()
				{
					$(".error").fadeOut(1500);
			    }, 8000);				
			}
		});
	});
};

function httpDLTestPlotFormSubmit()
{
	$("#httpTriggerDLTestPlotForm").submit(function(event)
	{
		// prevent form to submit
		event.preventDefault();
		
		var selectedRadioValue	= $("input[name=plotName]:radio:checked").val();
		
		// XXX: IMPORTANT - remove space and dot from plot name
		selectedRadioValue		= selectedRadioValue.replace(/ /g, 'space');
		selectedRadioValue		= selectedRadioValue.replace(/\./g, 'dot');
		
		$.ajax(
		{
			url : "ajax/httpTriggerDLTestPlot.php",
			type: "POST",
			data : $("#httpTriggerDLTestPlotForm").serialize(),
			success: function(data, textStatus, jqXHR)
			{
				data = JSON.parse(data);
				
				if(data["error"] == 1)
				{
					$("#serverMsg").empty();
					var errorDiv = "<div class='error content_notice transparent'><img src='../images/unsuccessful.png'></img>" + data["message"] + "</div>"
					$("#serverMsg").html(errorDiv);
					
					setTimeout(function()
					{
						$(".error").fadeOut(1500);
				    }, 8000);
				}
				else if(data["error"] == 2)
				{
					$("#serverMsg").empty();
					var errorDiv = "<div class='error content_notice transparent'><img src='../images/unsuccessful.png'></img>" + data["message"] + "</div>"
					$("#serverMsg").html(errorDiv);
				}
				else
				{
					$("#serverMsg").empty();
					var successDiv = "<div class='success content_notice transparent'><img src='../images/successful.png'></img>" + data["message"] + "</div>"
					$("#serverMsg").html(successDiv);
					
					setTimeout(function()
					{
						$(".success").fadeOut(1500);
				    }, 8000);
					
					var statusTimer;
					
					(function statusChecker()
					{
						$.ajax({
							url: "ajax/httpDLTestPlotStatus.php",
							type: "POST",
							data : $("#httpTriggerDLTestPlotForm").serialize(),
						    success: function(data)
						    {
						    	data = JSON.parse(data);
						    	
						    	if(data["val"] == 0)
					    		{
						    		// not testing so need not to show busy icon
						    		$("#" + selectedRadioValue + "Status").html(data["message"]);
					    		}
						    	else if(data["val"] == 1)
					    		{
						    		// test is accepted so need to show busy icon
						    		$("#" + selectedRadioValue + "Status").html(data["message"] + "&nbsp;<img src='../images/busy.gif'></img>");
						    		statusTimer = setTimeout(statusChecker, 5000);
					    		}
						    	else if(data["val"] == 2)
					    		{
						    		// test is running so need to show busy icon
						    		$("#" + selectedRadioValue + "Status").html(data["message"] + "&nbsp;<img src='../images/busy.gif'></img>");
						    		statusTimer = setTimeout(statusChecker, 5000);
					    		}
						    	else if(data["val"] == 3)
					    		{
						    		// test is completed so need not to show busy icon
						    		$("#" + selectedRadioValue + "Status").html(data["message"]);
					    		}
						    },
						    error: function()
						    {
						    }
						});
					})();
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#serverMsg").empty();
				var errorDiv = "<div class='error content_notice transparent'><img src='../images/unsuccessful.png'></img>Communication error occured</div>"
				$("#serverMsg").html(errorDiv);
				
				setTimeout(function()
				{
					$(".error").fadeOut(1500);
			    }, 8000);				
			}
		});
	});
};

function httpULTestPlotFormSubmit()
{
	$("#httpTriggerULTestPlotForm").submit(function(event)
	{
		// prevent form to submit
		event.preventDefault();
		
		var selectedRadioValue	= $("input[name=plotName]:radio:checked").val();
		
		// XXX: IMPORTANT - remove space and dot from plot name
		selectedRadioValue		= selectedRadioValue.replace(/ /g, 'space');
		selectedRadioValue		= selectedRadioValue.replace(/\./g, 'dot');
		
		$.ajax(
		{
			url : "ajax/httpTriggerULTestPlot.php",
			type: "POST",
			data : $("#httpTriggerULTestPlotForm").serialize(),
			success: function(data, textStatus, jqXHR)
			{
				data = JSON.parse(data);
				
				if(data["error"] == 1)
				{
					$("#serverMsg").empty();
					var errorDiv = "<div class='error content_notice transparent'><img src='../images/unsuccessful.png'></img>" + data["message"] + "</div>"
					$("#serverMsg").html(errorDiv);
					
					setTimeout(function()
					{
						$(".error").fadeOut(1500);
				    }, 8000);
				}
				else if(data["error"] == 2)
				{
					$("#serverMsg").empty();
					var errorDiv = "<div class='error content_notice transparent'><img src='../images/unsuccessful.png'></img>" + data["message"] + "</div>"
					$("#serverMsg").html(errorDiv);
				}
				else
				{
					$("#serverMsg").empty();
					var successDiv = "<div class='success content_notice transparent'><img src='../images/successful.png'></img>" + data["message"] + "</div>"
					$("#serverMsg").html(successDiv);
					
					setTimeout(function()
					{
						$(".success").fadeOut(1500);
				    }, 8000);
					
					var statusTimer;
					
					(function statusChecker()
					{
						$.ajax({
							url: "ajax/httpULTestPlotStatus.php",
							type: "POST",
							data : $("#httpTriggerULTestPlotForm").serialize(),
						    success: function(data)
						    {
						    	data = JSON.parse(data);
						    	
						    	if(data["val"] == 0)
					    		{
						    		// not testing so need not to show busy icon
						    		$("#" + selectedRadioValue + "Status").html(data["message"]);
					    		}
						    	else if(data["val"] == 1)
					    		{
						    		// test is accepted so need to show busy icon
						    		$("#" + selectedRadioValue + "Status").html(data["message"] + "&nbsp;<img src='../images/busy.gif'></img>");
						    		statusTimer = setTimeout(statusChecker, 5000);
					    		}
						    	else if(data["val"] == 2)
					    		{
						    		// test is running so need to show busy icon
						    		$("#" + selectedRadioValue + "Status").html(data["message"] + "&nbsp;<img src='../images/busy.gif'></img>");
						    		statusTimer = setTimeout(statusChecker, 5000);
					    		}
						    	else if(data["val"] == 3)
					    		{
						    		// test is completed so need not to show busy icon
						    		$("#" + selectedRadioValue + "Status").html(data["message"]);
					    		}
						    },
						    error: function()
						    {
						    }
						});
					})();
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#serverMsg").empty();
				var errorDiv = "<div class='error content_notice transparent'><img src='../images/unsuccessful.png'></img>Communication error occured</div>"
				$("#serverMsg").html(errorDiv);
				
				setTimeout(function()
				{
					$(".error").fadeOut(1500);
			    }, 8000);				
			}
		});
	});
};

function httpTestPlotStatusCheck()
{
	$('input[type="radio"]').each(function()
	{
		var radioValue				= $(this).val();
		var radioValueWithoutSpace	= radioValue.replace(/ /g, 'space');
		radioValueWithoutSpace		= radioValueWithoutSpace.replace(/\./g, 'dot');
		var statusTimer				= null;
			
		(function statusChecker()
		{
			$.ajax({
				url: "ajax/httpTestPlotStatus.php",
				type: "POST",
				data : {"plotName": radioValue},
			    success: function(data)
			    {
			    	data = JSON.parse(data);
			    	
			    	if(data["val"] == 0)
		    		{
			    		// not testing so need not to show busy icon
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"]);
		    		}
			    	else if(data["val"] == 1)
		    		{
			    		// test is accepted so need to show busy icon
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"] + "&nbsp;<img src='../images/busy.gif'></img>");
			    		statusTimer = setTimeout(statusChecker, 5000);
		    		}
			    	else if(data["val"] == 2)
		    		{
			    		// test is running so need to show busy icon
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"] + "&nbsp;<img src='../images/busy.gif'></img>");
			    		statusTimer = setTimeout(statusChecker, 5000);
		    		}
			    	else if(data["val"] == 3)
		    		{
			    		// test is completed so need not to show busy icon
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"]);
		    		}
			    	else if(data["val"] == 4)
		    		{
			    		// XXX: IMPORTANT - host ip not confirmed yet
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"]);
		    		}
			    },
			    error: function()
			    {
			    }
			});
		})();
	});
};

function httpDLTestPlotStatusCheck()
{
	$('input[type="radio"]').each(function()
	{
		var radioValue				= $(this).val();
		var radioValueWithoutSpace	= radioValue.replace(/ /g, 'space');
		radioValueWithoutSpace		= radioValueWithoutSpace.replace(/\./g, 'dot');
		var statusTimer				= null;
			
		(function statusChecker()
		{
			$.ajax({
				url: "ajax/httpDLTestPlotStatus.php",
				type: "POST",
				data : {"plotName": radioValue},
			    success: function(data)
			    {
			    	data = JSON.parse(data);
			    	
			    	if(data["val"] == 0)
		    		{
			    		// not testing so need not to show busy icon
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"]);
		    		}
			    	else if(data["val"] == 1)
		    		{
			    		// test is accepted so need to show busy icon
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"] + "&nbsp;<img src='../images/busy.gif'></img>");
			    		statusTimer = setTimeout(statusChecker, 5000);
		    		}
			    	else if(data["val"] == 2)
		    		{
			    		// test is running so need to show busy icon
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"] + "&nbsp;<img src='../images/busy.gif'></img>");
			    		statusTimer = setTimeout(statusChecker, 5000);
		    		}
			    	else if(data["val"] == 3)
		    		{
			    		// test is completed so need not to show busy icon
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"]);
		    		}
			    	else if(data["val"] == 4)
		    		{
			    		// XXX: IMPORTANT - host ip not confirmed yet
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"]);
		    		}
			    },
			    error: function()
			    {
			    }
			});
		})();
	});
};

function httpULTestPlotStatusCheck()
{
	$('input[type="radio"]').each(function()
	{
		var radioValue				= $(this).val();
		var radioValueWithoutSpace	= radioValue.replace(/ /g, 'space');
		radioValueWithoutSpace		= radioValueWithoutSpace.replace(/\./g, 'dot');
		var statusTimer				= null;
			
		(function statusChecker()
		{
			$.ajax({
				url: "ajax/httpULTestPlotStatus.php",
				type: "POST",
				data : {"plotName": radioValue},
			    success: function(data)
			    {
			    	data = JSON.parse(data);
			    	
			    	if(data["val"] == 0)
		    		{
			    		// not testing so need not to show busy icon
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"]);
		    		}
			    	else if(data["val"] == 1)
		    		{
			    		// test is accepted so need to show busy icon
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"] + "&nbsp;<img src='../images/busy.gif'></img>");
			    		statusTimer = setTimeout(statusChecker, 5000);
		    		}
			    	else if(data["val"] == 2)
		    		{
			    		// test is running so need to show busy icon
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"] + "&nbsp;<img src='../images/busy.gif'></img>");
			    		statusTimer = setTimeout(statusChecker, 5000);
		    		}
			    	else if(data["val"] == 3)
		    		{
			    		// test is completed so need not to show busy icon
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"]);
		    		}
			    	else if(data["val"] == 4)
		    		{
			    		// XXX: IMPORTANT - host ip not confirmed yet
			    		$("#" + radioValueWithoutSpace + "Status").html(data["message"]);
		    		}
			    },
			    error: function()
			    {
			    }
			});
		})();
	});
};

function plotConfigToolTip()
{
	$("a[rel*=leanModal]").leanModal({"top": 200, "overlay": 0.4, "closeButton": ".modalClose" });
	
	$(".plotConfig").click(function(e)
	{
		e.preventDefault();
		
		$.ajax({
			url: "ajax/getConfigSettings.php",
			type: "POST",
			data : {"plotInfo": $(this).attr('id')},
		    success: function(data)
		    {
		    	$("#configContent").html('<a class="modalClose" href="#"></a><pre>' + data + '</pre>');
		    },
		    error: function()
		    {
		    	$("#configContent").html('<a class="modalClose" href="#"></a>' + 'Error: loading data');
		    }
		});
	});
	
	$(".modalClose").live('click', function(e)
	{
		$("#configContent").empty();
		$("#leanOverlay").hide();
		$("#configContent").hide();
	});
};

function httpDLPlotConfigToolTip()
{
	$("a[rel*=leanModal]").leanModal({"top": 200, "overlay": 0.4, "closeButton": ".modalClose" });
	
	$(".plotConfig").click(function(e)
	{
		e.preventDefault();
		
		$.ajax({
			url: "ajax/getHttpDLConfigSettings.php",
			type: "POST",
			data : {"plotInfo": $(this).attr('id')},
		    success: function(data)
		    {
		    	$("#configContent").html('<a class="modalClose" href="#"></a><pre>' + data + '</pre>');
		    },
		    error: function()
		    {
		    	$("#configContent").html('<a class="modalClose" href="#"></a>' + 'Error: loading data');
		    }
		});
	});
	
	$(".modalClose").live('click', function(e)
	{
		$("#configContent").empty();
		$("#leanOverlay").hide();
		$("#configContent").hide();
	});
};

function httpULPlotConfigToolTip()
{
	$("a[rel*=leanModal]").leanModal({"top": 200, "overlay": 0.4, "closeButton": ".modalClose" });
	
	$(".plotConfig").click(function(e)
	{
		e.preventDefault();
		
		$.ajax({
			url: "ajax/getHttpULConfigSettings.php",
			type: "POST",
			data : {"plotInfo": $(this).attr('id')},
		    success: function(data)
		    {
		    	$("#configContent").html('<a class="modalClose" href="#"></a><pre>' + data + '</pre>');
		    },
		    error: function()
		    {
		    	$("#configContent").html('<a class="modalClose" href="#"></a>' + 'Error: loading data');
		    }
		});
	});
	
	$(".modalClose").live('click', function(e)
	{
		$("#configContent").empty();
		$("#leanOverlay").hide();
		$("#configContent").hide();
	});
};

function checkHostOwnershipFile()
{
	$(".checkHostOwnership").live('click', function(e)
	{
		e.preventDefault();
		
		$.ajax({
			url: "ajax/checkHostIpOwnershipFile.php",
			type: "POST",
			data : {"plotInfo": $(this).attr('id')},
		    success: function(data)
		    {
		    	data = JSON.parse(data);
		    	
		    	if(data["error"])
	    		{
		    		jAlert(data["message"], "Error");
	    		}
		    	else
	    		{
		    		jAlert(data["message"], "Confirm Host", function(e)
		    		{
		    			if(e) window.location.reload();
		    		});
	    		}
		    },
		    error: function()
		    {
		    	jAlert('Error: Something happened wrong', "Alert!");
		    }
		});
	});
};

function checkHttpDLHostOwnershipFile()
{
	$(".checkHostOwnership").live('click', function(e)
	{
		e.preventDefault();
		
		$.ajax({
			url: "ajax/checkHttpDLHostIpOwnershipFile.php",
			type: "POST",
			data : {"plotInfo": $(this).attr('id')},
		    success: function(data)
		    {
		    	data = JSON.parse(data);
		    	
		    	if(data["error"])
	    		{
		    		jAlert(data["message"], "Error");
	    		}
		    	else
	    		{
		    		jAlert(data["message"], "Confirm Host", function(e)
		    		{
		    			if(e) window.location.reload();
		    		});
	    		}
		    },
		    error: function()
		    {
		    	jAlert('Error: Something happened wrong', "Alert!");
		    }
		});
	});
};

function checkHttpULHostOwnershipFile()
{
	$(".checkHostOwnership").live('click', function(e)
	{
		e.preventDefault();
		
		$.ajax({
			url: "ajax/checkHttpULHostIpOwnershipFile.php",
			type: "POST",
			data : {"plotInfo": $(this).attr('id')},
		    success: function(data)
		    {
		    	data = JSON.parse(data);
		    	
		    	if(data["error"])
	    		{
		    		jAlert(data["message"], "Error");
	    		}
		    	else
	    		{
		    		jAlert(data["message"], "Confirm Host", function(e)
		    		{
		    			if(e) window.location.reload();
		    		});
	    		}
		    },
		    error: function()
		    {
		    	jAlert('Error: Something happened wrong', "Alert!");
		    }
		});
	});
};

function plotEdit()
{
	$(".plotEdit").live('click', function(e)
	{
		e.preventDefault();
		
		window.location = 'httpEditTestPlot.php?plotName=' + $(this).attr('id');
	});
};

function httpDLPlotEdit()
{
	$(".plotEdit").live('click', function(e)
	{
		e.preventDefault();
		
		window.location = 'httpEditDLTestPlot.php?plotName=' + $(this).attr('id');
	});
};

function httpULPlotEdit()
{
	$(".plotEdit").live('click', function(e)
	{
		e.preventDefault();
		
		window.location = 'httpEditULTestPlot.php?plotName=' + $(this).attr('id');
	});
};

function plotDelete(title, deleteMsg)
{
	$(".plotDelete").live('click', function(e)
	{
		e.preventDefault();
		
		var plotName = $(this).attr('id');
		
		jConfirm(deleteMsg + plotName + '?', title, function(e)
		{
			if(e)
			{
				window.location = 'httpDeleteTestPlot.php?plotName=' + plotName;
			}
		});
	});
};

function httpDLPlotDelete(title, deleteMsg)
{
	$(".plotDelete").live('click', function(e)
	{
		e.preventDefault();
		
		var plotName = $(this).attr('id');
		
		jConfirm(deleteMsg + plotName + '?', title, function(e)
		{
			if(e)
			{
				window.location = 'httpDeleteDLTestPlot.php?plotName=' + plotName;
			}
		});
	});
};

function httpULPlotDelete(title, deleteMsg)
{
	$(".plotDelete").live('click', function(e)
	{
		e.preventDefault();
		
		var plotName = $(this).attr('id');
		
		jConfirm(deleteMsg + plotName + '?', title, function(e)
		{
			if(e)
			{
				window.location = 'httpDeleteULTestPlot.php?plotName=' + plotName;
			}
		});
	});
};

function undoHttpTestPlotDeletion()
{
	$(".undoHttpTestPlotDeletion").live('click', function(e)
	{
		e.preventDefault();
		
		$.ajax({
			url: "ajax/undoHttpTestPlotDeletion.php",
			type: "POST",
			data : {"plotName": $(this).attr('id')},
		    success: function(data)
		    {
		    	data = JSON.parse(data);
		    	
		    	if(data["error"])
	    		{
		    		jAlert(data["message"], "Error");
	    		}
		    	else
	    		{
		    		jAlert(data["message"], "Hurray!", function(e)
		    		{
		    			if(e) window.location = 'httpTriggerTestPlot.php';
		    		});
	    		}
		    },
		    error: function()
		    {
		    	jAlert('Error: Something happened wrong', "Alert!");
		    }
		});
	});
};

function undoHttpDLTestPlotDeletion()
{
	$(".undoHttpTestPlotDeletion").live('click', function(e)
	{
		e.preventDefault();
		
		$.ajax({
			url: "ajax/undoHttpDLTestPlotDeletion.php",
			type: "POST",
			data : {"plotName": $(this).attr('id')},
		    success: function(data)
		    {
		    	data = JSON.parse(data);
		    	
		    	if(data["error"])
	    		{
		    		jAlert(data["message"], "Error");
	    		}
		    	else
	    		{
		    		jAlert(data["message"], "Hurray!", function(e)
		    		{
		    			if(e) window.location = 'httpTriggerDLTestPlot.php';
		    		});
	    		}
		    },
		    error: function()
		    {
		    	jAlert('Error: Something happened wrong', "Alert!");
		    }
		});
	});
};

function undoHttpULTestPlotDeletion()
{
	$(".undoHttpTestPlotDeletion").live('click', function(e)
	{
		e.preventDefault();
		
		$.ajax({
			url: "ajax/undoHttpULTestPlotDeletion.php",
			type: "POST",
			data : {"plotName": $(this).attr('id')},
		    success: function(data)
		    {
		    	data = JSON.parse(data);
		    	
		    	if(data["error"])
	    		{
		    		jAlert(data["message"], "Error");
	    		}
		    	else
	    		{
		    		jAlert(data["message"], "Hurray!", function(e)
		    		{
		    			if(e) window.location = 'httpTriggerULTestPlot.php';
		    		});
	    		}
		    },
		    error: function()
		    {
		    	jAlert('Error: Something happened wrong', "Alert!");
		    }
		});
	});
};

function checkUploadFileSize(message, title)
{
	$('#uploadedFile').bind('change', function()
	{
		//check whether browser fully supports all File API
		if(window.File && window.FileReader && window.FileList && window.Blob)
		{
			var fsize = this.files[0].size;
			
			if(fsize > 10485760)
			{
				jAlert(message, title, function(e)
	    		{
					if(e)
					{
						$('#uploadedFile').val('');
					}
	    		});
			}
	    }
		else
		{
	        alert("Please upgrade your browser, because your current browser lacks some new features we need!");
	    }
	});
};