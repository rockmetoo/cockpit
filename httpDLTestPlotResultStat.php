<?php

	include_once 'bootstrap.php';
	include_once 'CDBSession.php';
	global $COCKPIT_SYSTEM_DEF;
	
	// Only allow user who are logged in to view this page
	CDBSession::validateUser();
	
	include_once 'CDBHttpTestPlot.php';
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
	include('forms/httpDLTestPlotResultStat.php');
	
	$js_files = array(
		'RGraph.common.core.js', 'RGraph.common.dynamic.js', 'RGraph.pie.js', 'RGraph.line.js', 'RGraph.common.tooltips.js',
		'http://maps.google.com/maps/api/js?libraries=geometry&sensor=false'
	);
	
	$js_string =
		'
		$(document).ready(function()
		{
			$("#overAllAnalyzedData").hide();
			$("#lastAnalyzedData").show();
			$(".showHideOverAll").show();
			$(".showHideLast").show();
			
			$(".showHideOverAll").toggle(function()
			{
				$("#overAllAnalyzedData").slideDown(function()
				{
					$("#plusOverAll").text("-Overall Test Result-")
				});
			},function()
			{
				$("#overAllAnalyzedData").slideUp(function()
				{
					$("#plusOverAll").text("+Overall Test Result+")
				});
			});
			
			$(".showHideLast").toggle(function()
			{
				$("#lastAnalyzedData").slideDown(function()
				{
					$("#plusLast").text("-Last Test Result-")
				});
			},function()
			{
				$("#lastAnalyzedData").slideUp(function()
				{
					$("#plusLast").text("+Last Test Result+")
				});
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
			$breadcrumb_data = array('controlPanel.php' => $lang->get('my dashboard'), $_SERVER['REQUEST_URI'] => 'Result Statistics');
		?>
		<div class="clear10"></div>
		<?php CHelperFunctions::breadcrumb($breadcrumb_data); ?>
		<div class="clear10"></div>
		<div class="employer_full_content">
			<div class="form_holder">
				<?php
					// After Form Submission if any error/success occured
					if($processor->error_no === 1)
					{
						echo
						'<div class="success content_notice transparent">
							<img src="../images/successful.png"></img>'. $processor->error_msg .'
						</div>';
					}
					else if($processor->error_no === -1)
					{
						echo
						'<div class="error content_notice transparent">
							<img src="../images/unsuccessful.png"></img>'. $processor->error_msg .'
						</div>';
					}
					else if($processor->error_no === 0)
					{
						echo
						'<div class="error content_notice transparent">
							<img src="../images/unsuccessful.png"></img>'. $processor->error_msg .'
						</div>';
					}
				?>
				<div class="any_page_col2_container">
					<div class="any_page_col2_heading">
						<div class="left">
							<h2>
							<?php
								echo 'Http Test Plot';
							?>
							</h2>
						</div>
					</div>
				</div>
				<div class="clear"></div>
				<div class="any_page_col2_container">
					<?php
						//Output form
						$processor->display();
						
						if($processor->returnData)
						{
							echo $processor->returnData;
						}
					?>
				</div>
			</div>
		</div>
	<?php
		include_once 'userFooter.php';
	?>