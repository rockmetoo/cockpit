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
	
	$COCKPIT_USER_DEF		= CDBUser::getUserDetails($COCKPIT_SYSTEM_DEF['userId']);
	$MY_AVAILABLE_SERVICE	= CDBUser::getUserServiceACL($COCKPIT_SYSTEM_DEF['userId']);
	
	/********************* User Reseource Allocation ****************************/
	
	// Include the form
	include 'forms/monitaCPUList.php';
	
	$js_files = array('jquery.leanModal.min.js', 'jquery.alert.js', 'helper.js');
	
	$js_string =
	'$(function()
	{
		httpTestPlotFormSubmit();
		plotConfigToolTip();
		plotEdit();
		plotDelete("Alert!", "Are you sure you want to delete this plot: ");
	});';
	
	$css_files	= array('style.css' => 'all', 'jquery.alert.css' => 'all');
	$title		= 'List Of CPU Monita';
	
	include_once 'userHeader.php';
	include_once 'dcontents/indexHeader.php';
?>
			<div class="top_menupanel">
				<div id="menupanel">
				<?php
					echo CUserMenu::topMenuPanel($lang, 3);
				?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<?php
			$breadcrumb_data = array('machineDataControlPanel.php' => $lang->get('my dashboard'), $_SERVER['REQUEST_URI'] => 'List Of CPU Monita');
		?>
		<div class="clear10"></div>
		<?php CHelperFunctions::breadcrumb($breadcrumb_data); ?>
		<div class="clear10"></div>
		<div class="employer_full_content">
			<div class="form_holder">
				<div id="serverMsg"></div>
				<div class="clear"></div>
				<div class="any_page_col2_container">
					<?php
						//Output form
						$processor->display();
					?>
				</div>
			</div>
		</div>
		<div id="configContent">test lean</div>
	<?php
		include_once 'userFooter.php';
	?>