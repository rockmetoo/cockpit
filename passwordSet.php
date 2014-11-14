<?php

	include_once 'bootstrap.php';
	include_once 'CDBSession.php';
	global $COCKPIT_SYSTEM_DEF;
	
	// Only allow employers who are logged in to view this page
	CDBSession::validateUser();
	
	include_once 'CDBUser.php';
	include_once 'CDBUserAcl.php';
	include_once 'CFormValidator.php';
	include_once 'CHelperFunctions.php';
	include_once 'CUserMenu.php';
	include_once 'formValues' . $COCKPIT_SYSTEM_DEF['lang'] . '.php';
	include_once 'CLocalization.php';
	$lang = new CLocalization($COCKPIT_SYSTEM_DEF['lang'], 'passwordSet.php');
	
	/********************* Employer Reseource Allocation ****************************/
	
	$COCKPIT_USER_DEF = CDBUser::getUserDetails($COCKPIT_SYSTEM_DEF['userId']);
	$MY_AVAILABLE_SERVICE = CDBUser::getUserServiceACL($COCKPIT_SYSTEM_DEF['userId']);
	
	/********************* Employer Reseource Allocation ****************************/

	// Include the form
	include('forms/passwordSet.php');
	
	$js_files = array('capslock.jquery.js');
	$js_string =
		'$(document).ready(function(){
			var options = {
				caps_lock_on: function(){
					var alertMessage = "<span id=\'capslock\'>CapsLock key pressed</span>";
					$("#caps_lock").text("CapsLock Key Pressed");
					$("#caps_lock").fadeIn("slow");
				},
				caps_lock_off: function(){
					$("#caps_lock").text("");
					$("#caps_lock").fadeOut("slow");
				},
				caps_lock_undetermined: function(){
					$("#caps_lock").text("");
					$("#caps_lock").fadeOut("slow");
				},
				debug: true
			};
			$("#old_password").capslock(options);
			$("#new_password").capslock(options);
			$("#re_new_password").capslock(options);
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
			$breadcrumb_data = array(
				'controlPanel.php' => 'Dashboard', $_SERVER['REQUEST_URI'] => 'Reset Your Password'
			);
		?>
		<div class="clear10"></div>
		<?php CHelperFunctions::breadcrumb($breadcrumb_data); ?>
		<div class="clear10"></div>
		<div class="employer_full_content">
			<div class="form_holder">
				<?php
					//After Form Submission if any error/success occured from Daemon
					if($processor->error_no === 1){
						echo
							'<div class="success content_notice transparent">
								<img src="../images/successful.png"></img>'. $processor->error_msg .'
							';
						echo '</div>';
					}else if($processor->error_no === 0){
						echo
							'<div class="error content_notice transparent">
								<img src="../images/unsuccessful.png"></img>'. $processor->error_msg .'
							';
						echo '</div>';
					}
					
					//Output form
					$processor->display();
				?>
			</div>
		</div>
	<?php
		include_once 'userFooter.php';
	?>