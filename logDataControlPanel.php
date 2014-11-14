<?php

	require_once('bootstrap.php');
	require_once('CDBSession.php');
	global $COCKPIT_SYSTEM_DEF;
	
	// Only allow user who are logged in to view this page
	CDBSession::validateUser();
	
	require_once('CDBUser.php');
	require_once('CDBUserAcl.php');
	require_once('CHelperFunctions.php');
	require_once('CUserMenu.php');
	require_once('formValues' . $COCKPIT_SYSTEM_DEF['lang'] . '.php');
	require_once('CLocalization.php');
	$lang = new CLocalization($COCKPIT_SYSTEM_DEF['lang'], 'controlPanel.php');

	/********************* Reseource Allocation ****************************/

	$MY_AVAILABLE_SERVICE = CDBUser::getLogDataServiceACL($COCKPIT_SYSTEM_DEF['userId']);
	
	/********************* Reseource Allocation ****************************/

	// Rearrange Employer dashboard according to their change of widget
	$quickLinkColumnsRearrange = CDBUser::rearrangeLogDataDashboard($COCKPIT_SYSTEM_DEF['userId'], array_keys($MY_AVAILABLE_SERVICE));
	
	// load quick link data
	$quickLinkData = array();

	// This one is drag and dropable menu widget items
	
	reset($LOGDATA_CONTROL_PANEL);
	
	foreach($LOGDATA_CONTROL_PANEL as $section => $section_text)
	{
		if(!isset($quickLinkData[$section])) $quickLinkData[$section] = array();
		
		if(!isset($quickLinkData[$section]['name']))
		{
			if(array_key_exists($section, $MY_AVAILABLE_SERVICE))
			{
				$quickLinkData[$section]['name'] = $section_text;
			}
		}

		if(!isset($quickLinkData[$section]['contents'])) $quickLinkData[$section]['contents'] = array();
		
		foreach($LOGDATA_CONTROL_PANEL_SUB[$section] as $content_link => $content_text)
		{
			if(in_array($content_link, $MY_AVAILABLE_SERVICE[$section]))
			{
				if(!isset($quickLinkData[$section]['contents'][$content_link]))
				{
					$quickLinkData[$section]['contents'][$content_link] = array($content_text, false);
				}
			}
		}
	}
	
	$title = $lang->get('my dashboard');

	$js_files = array('jquery.dragsort.js', 'helper.js');
	$js_string =
		'$(document).ready(function()
		{
			// Initially, hide them all
			hideAllMessages();
			// if submit button is clicked
			$("#storeDasboardPos").click(function()
			{
				//organize the data properly
				var data = "pos=" + $("input[name=list1SortOrder]").val();
				//show the loading sign
        		$(".loading").show();
        		$.ajax({
        			url: "ajax/logDataDashboardSet.php",
        			type: "POST",
        			data: data,
        			cache: false,
        			timeout: 10000,
        			error: function(){
        				//retry
        				$(".loading").hide();
        			},
        			success: function(html){
        				if(html >= 1)
        				{
        					//hide the div
        					$(".loading").hide();
        					$(".various_messages").animate({top: -$(".various_messages").outerHeight()}, 1500);
        				}
						else
						{
        					$(".loading").hide();
        					alert("Sorry, unexpected error. Please try again later.");
        				}
					}
				});
			
				//cancel the submit button default behaviours
				return false;
			});
			
			$("#nothanks").click(function()
			{
				$(".loading").hide();
        		$(".various_messages").animate({top: -$(".various_messages").outerHeight()}, 500);
				return false;
			});
		});';
	
	$css_files = array('style.css' => 'all');

	include_once 'userHeader.php';
	include_once 'dcontents/indexHeader.php';
?>
			<div class="top_menupanel">
				<div id="menupanel">
<?php
					echo CUserMenu::topMenuPanel($lang, 4);
?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<div class="clear10"></div>
		<div class="info various_messages transparent">
			<h3><?php echo $lang->get('dashboard changed head'); ?></h3>
			<p>
				<?php echo $lang->get('dashboard position changed'); ?>
				<div class="form_col_group">
					<input type="submit" name="storeDasboardPos" id="storeDasboardPos" value="Save Position"/>
					<input type="submit" name="nothanks" id="nothanks" value="No, Thanks"/>
					<div class="loading"></div>
				</div>
			</p>
		</div>
		<div class="employer_full_content">
			<div class="form_holder">
				<div class="testerDashboard">
					<h2>
						<span><?php echo $lang->get('dashboard control panel'); ?></span>
					</h2>
					<div class="testerDasboardHolder">
						<ul id="list1">
							<?php
								$dashPosIndex = 0;
								foreach($quickLinkColumnsRearrange[0] as $section)
								{
									$class	= (isset($quickLinkData[$section]['enabled'])) ? '' : 'disabled';
									$text	= $title = htmlspecialchars($quickLinkData[$section]['name']);
									
									if($section == 'p2pTestPlot')
									{
										$text = $text . ' <font size="1" color="red">(Seeking Investment)</font>';
									}
									
									if(count($quickLinkData[$section]['contents']) > 0)
									{
							?>
										<li>
											<div class="curved_230_top"><?php echo $dashPosIndex; $dashPosIndex += 1; ?></div>
											<div class="curved_230">
												<div class="leftmenu">
													<h3 style="color:<?php echo $LOGDATA_QUICK_LINK[0][$section]; ?>;"><?php echo $text; ?></h3>
											<?php
												foreach($quickLinkData[$section]['contents'] as $key => $data)
												{
													$class = (isset($data[1])) ? '' : 'disabled';
													$text = $title = htmlspecialchars($data[0]);
													if(isset($data[2]))
													{
														$class = 'new ' . $class;
														//$text .= ' <img src="/images/new.png" width="30" height="15" alt="" class="new" />';
													}
													printf('<a href="%1$s" title="%3$s" class="%4$s">%2$s</a><br/>', $key, $text, $title, $class);
												}
											?>
												</div>
												<div class="clear10"></div>
											</div>
											<div class="curved_230_bottom"></div>
										</li>
							<?php
									}
								}
							?>
						</ul>
					</div>
				</div>
				<div class="testerDasboardQuickStat">
					<h2><span><?php echo 'Total Log Analyzing'; ?></span></h2>
					<div class="testerDasboardHolder">
						<h1 class="threadRunningAtThisMoment">0</h1>
					</div>
				</div>
			</div>
		</div>
		<input name="list1SortOrder" type="hidden" />
		<script type="text/javascript">
			$("#list1").dragsort({ dragSelector: "div", dragBetween: true, dragEnd: saveOrder, placeHolderTemplate: "<li class='placeHolder'><div></div></li>" });
			function saveOrder()
			{
				var data = $("#list1 li").map(function() { return $(this).children().html(); }).get();
				$("input[name=list1SortOrder]").val(data.join("|"));
				$('.' + myMessages[0]).animate({top:"0"}, 500);
			};
		</script>
<?php
		include_once 'userFooter.php';
?>