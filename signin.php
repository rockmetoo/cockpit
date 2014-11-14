<?php

    include_once 'bootstrap.php';
	include_once 'CDBSession.php';
	global $COCKPIT_SYSTEM_DEF;
	
	if($COCKPIT_SYSTEM_DEF['userId'] > 0)
	{
		header('Location: controlPanel.php');
		exit;
	}

	include_once 'CDBLogin.php';
	include_once 'CHelperFunctions.php';
	include_once 'CFormValidator.php';
	include_once 'includes/formValues' . $COCKPIT_SYSTEM_DEF["lang"] . '.php';
	include_once 'CLocalization.php';
	
	$lang = new CLocalization($COCKPIT_SYSTEM_DEF['lang'], 'signin.php');

	//get the path for redirecting after login is successful
	if(!isset($_GET['path']) || !$_GET['path'])
	{
		$_GET['path'] = $_SERVER['SCRIPT_NAME'] . "?" . $_SERVER['QUERY_STRING'];
	}
	
	//Include the form
	include('forms/signin.php');
	
	$js_files = array(
		'jquery.json.js', 'jquery.form.validate.js', 'login.forms.js', 'capslock.jquery.js', 'socialite.js'
	);
	
	$js_string = '
		$(document).ready(function()
		{
			$(".socialButton").one("mouseenter", function()
			{
				Socialite.load($(this)[0]);
			});
					
			$("#signin").formValidate(' . json_encode($processor->rules) . ');
			
			var options = {
				caps_lock_on: function(){
					var alertMessage = "<span id=\'capslock\'>CapsLock key pressed! Passwords are case sensitive</span>";
					$("#caps_lock").html(alertMessage);
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
			$("#password").capslock(options);
		});
	';
	
	$css_files = array('style.css' => 'all');

	//Add page title, meta description and meta tags
	$title = $lang->get('sign in to cockpit');
	$meta_description = $lang->get('sign in to cockpit');
	$meta_keywords = $lang->get('sign in to cockpit');
	
	include_once 'basicHeader.php';
?>
<div id="main_container">
	<div id="content">
		<div id="header">
			<div class="header_logo">
				<a href="index.html"><img alt="" src="../images/diction.png" width="90px" /></a>
			</div>
			<ul class="header_list1">
				<li><a href="signup.php">Sign Up</a></li>
				<li class="last"><a href="support.php">Support</a></li>
		    </ul>
			<ul class="header_list1 socialButton">
				<li class="last">
					<a href="http://twitter.com/share" class="socialite twitter-share" data-text="spam your site" data-url="http://autobloom.com" data-count="vertical" rel="nofollow" target="_blank"><span class="vhidden">Share on Twitter</span></a>
				</li>
				<li class="last">
					<a href="https://plus.google.com/share?url=http://autobloom.com" class="socialite googleplus-one" data-size="tall" data-href="http://autobloom.com" rel="nofollow" target="_blank"><span class="vhidden">Share on Google+</span></a>
				</li>
				<li class="last">
					<a href="http://www.facebook.com/sharer.php?u=http://autobloom.com&t=spam your site" class="socialite facebook-like" data-href="http://autobloom.com" data-send="false" data-layout="box_count" data-width="60" data-show-faces="false" rel="nofollow" target="_blank"><span class="vhidden">Share on Facebook</span></a>
				</li>
				<li class="last">
					<a href="http://www.linkedin.com/shareArticle?mini=true&url=http://autobloom.com&title=spam your site" class="socialite linkedin-share" data-url="http://autobloom.com" data-counter="top" rel="nofollow" target="_blank"><span class="vhidden">Share on LinkedIn</span></a>
				</li>
			</ul>
		    <div class="clear"></div>
		</div>
		<div class="clear10"></div>
		<div class="form_full_content">
			<div class="form_holder">
				<?php
					if($processor->error_no === 0)
					{
						echo
							'<div class="error content_notice transparent">
								<img src="../images/unsuccessful.png"></img>'. $processor->error_msg .'
							';
						echo '</div>';
					}
					
					$processor->display();
				?>
			</div>
		</div>
<?php
	require_once('basicFooter.php');
?>