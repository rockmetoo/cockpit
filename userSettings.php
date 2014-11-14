<?php

	require_once('bootstrap.php');
	require_once('CDBSession.php');
	global $COCKPIT_SYSTEM_DEF;
	
	// Only allow employers who are logged in to view this page
	CDBSession::validateEmployer();
	require_once('CDBUser.php');
	require_once('CDBUserAcl.php');
	require_once('CFormValidator.php');
	require_once('CHelperFunctions.php');
	require_once('CUserMenu.php');
	require_once('form_values_' . $COCKPIT_SYSTEM_DEF['lang'] . '.php');
	require_once('CLocalization.php');
	$lang = new CLocalization($COCKPIT_SYSTEM_DEF['lang'], 'ec_cockpit_user_settings.php');
	
	/********************* Employer Reseource Allocation ****************************/
	
	$COCKPIT_EMPLOYER_DEF = CDBUser::getEmployerDetails($COCKPIT_SYSTEM_DEF['user_id']);
	$MY_AVAILABLE_SERVICE = CDBUser::getEmployerServiceACL($COCKPIT_SYSTEM_DEF['user_id']);
	CDBUser::checkValidServiceACL($COCKPIT_SYSTEM_DEF['user_id'], $_SERVER['SCRIPT_NAME']);
	
	/********************* Employer Reseource Allocation ****************************/

	// Include the form
	include('forms/ec_cockpit_user_settings.php');
	
	$js_files = array('helper.js');
	$js_string =
		'$(document).ready(function(){
			$("#fetch_acl").click(function(){
				var url = "opcode=fetch_user_settings&userid=" + $("select[name=user]").find(":selected").val();
				addLoadEvent();
				$.ajax({
					type : "POST",
					url : "ajax/ec_cockpit_user_settings.php",
					data : url,
					success : function(response){
						removeLoadEvent();
						$("#fetch_settings_container").html(response);
					}
				});
				return false;
			});
		});';
	
	$css_files = array('style.css' => 'all');
	include('employer_header.php');
	include('dcontents/indexHeader.php');
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
			$breadcrumb_data = array(
				'controlPanel.php' => $lang->get('my dashboard')
				, $_SERVER['REQUEST_URI'] => 'Check and Set Employee Settings'
			);
		?>
		<div class="clear10"></div>
		<?php CHelperFunctions::breadcrumb($breadcrumb_data); ?>
		<div class="clear10"></div>
		<div class="employer_full_content">
			<div class="form_holder">
				<div class="any_page_col2_container">
					<div class="any_page_col2_heading">
						<div class="left">
							<h2>
							<?php
								echo 'Various User\'s Settings' . ' - ';
								if(!empty($COCKPIT_EMPLOYER_DEF['employer_name_en'])){
									$emp_name = $COCKPIT_EMPLOYER_DEF['employer_name_en'];
									echo $emp_name;
								}else{
									echo $COCKPIT_SYSTEM_DEF['username'];
								}
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
					?>
				</div>
			</div>
		</div>
	<?php
		include('employer_footer.php');
	?>