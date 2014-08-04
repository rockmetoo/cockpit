<?php
	
	include_once 'bootstrap.php';
	include_once 'CDBSession.php';
	global $SMITH_SYSTEM_DEF;
	
	// Only allow user who are logged in to view this page
	CDBSession::validateUser();
	
	include_once 'CDBUser.php';
	include_once 'CDBUserAcl.php';
	include_once 'CFormValidator.php';
	include_once 'CHelperFunctions.php';
	include_once 'CUserMenu.php';
	include_once 'formValues' . $SMITH_SYSTEM_DEF['lang'] . '.php';
	include_once 'CLocalization.php';
	$lang = new CLocalization($SMITH_SYSTEM_DEF['lang'], 'profileEdit.php');
	
	/********************* User Reseource Allocation ****************************/
	
	$SMITH_USER_DEF			= CDBUser::getUserDetails($SMITH_SYSTEM_DEF['userId']);
	$MY_AVAILABLE_SERVICE	= CDBUser::getUserServiceACL($SMITH_SYSTEM_DEF['userId']);
	
	/********************* User Reseource Allocation ****************************/

	$title = 'Edit Profile';

	//Include the form
	include('forms/profileEdit.php');
	$js_string =
		'$(document).ready(function(){
			
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
			$breadcrumb_data = array('controlPanel.php' => 'Dashboard', $_SERVER['REQUEST_URI'] => $title);
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
							';
						echo '</div>';
					}
					else if($processor->error_no === 0)
					{
						echo
							'<div class="error content_notice transparent">
								<img src="../images/unsuccessful.png"></img>'. $processor->error_msg .'
							';
						echo '</div>';
					}
				
					// Output form
					$processor->display();
				?>
			</div>
		</div>
<?php
		include_once 'userFooter.php';
?>