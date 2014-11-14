<?php

	include_once 'bootstrap.php';
	include_once 'CDBSession.php';
	global $COCKPIT_SYSTEM_DEF;
	
	// Only allow user who are logged in to view this page
	CDBSession::validateUser();
	
	include_once 'CDBUser.php';
	include_once 'CDBUserAcl.php';
	include_once 'CFormValidator.php';
	include_once 'CHelperFunctions.php';
	include_once 'CUserMenu.php';
	include_once 'formValues' . $COCKPIT_SYSTEM_DEF['lang'] . '.php';
	include_once 'CLocalization.php';
	$lang = new CLocalization($COCKPIT_SYSTEM_DEF['lang'], 'controlPanel.php');
	
	/********************* User Reseource Allocation ****************************/
	
	$COCKPIT_USER_DEF			= CDBUser::getUserDetails($COCKPIT_SYSTEM_DEF['userId']);
	$MY_AVAILABLE_SERVICE	= CDBUser::getUserServiceACL($COCKPIT_SYSTEM_DEF['userId']);
	
	/********************* User Reseource Allocation ****************************/
	
	// Include the form
	include('forms/httpAddDLTestPlot.php');
	
	$js_string =
		'$(function()
		{
			$(".addOnceMore").live("click", function(e)
			{
				e.preventDefault();
				
				window.location = "httpAddDLTestPlot.php";
			});
			
			$(".backToTriggerList").live("click", function(e)
			{
				e.preventDefault();
				
				window.location = "httpTriggerDLTestPlot.php";
			});
		});';
	
	$css_files = array('style.css' => 'all');
	
	include_once 'userHeader.php';
	include_once 'dcontents/indexHeader.php';
?>
			<div class="top_menupanel">
				<div id="menupanel">
<?php
					echo CUserMenu::topMenuPanel($lang, 2);
?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<?php
			$breadcrumb_data = array('controlPanel.php' => $lang->get('my dashboard') , $_SERVER['REQUEST_URI'] => 'Add a Http Download Test Plot');
		?>
		<div class="clear10"></div>
		<?php CHelperFunctions::breadcrumb($breadcrumb_data); ?>
		<div class="clear10"></div>
		<div class="employer_full_content">
			<div class="form_holder">
				<?php
					//After Form Submission if any error/success occured from Daemon
					if($processor->error_no == 1)
					{
						echo
							'<div class="success content_notice transparent">
								<img src="../images/successful.png"></img>Http Download Test Plot Created Successfully
							</div><br/>
							<button class="register_black addOnceMore" type="submit" name="backToEdit">Add Once More</button>
							<button class="register_black backToTriggerList" type="submit" name="backToTriggerList">Back To Http Test Plot</button>';
					}
					else
					{
						//Output form
						$processor->display();
					}
				?>
			</div>
		</div>
	<?php
		include_once 'userFooter.php';
	?>