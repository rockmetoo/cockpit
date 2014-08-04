<?php

	include_once 'form/Form.php';
	
	$form_contents = new Form("signin");
	$form_contents->configure(array("action" => "signin.php", "method" => "post"));

	$form_contents->addElement(
		new Element_HTMLExternal(
			'<error for="form">' . $lang->get('please fill in') . '</error>
			<fieldset style="margin-top:0px;"><legend>Login Credentials</legend><div class="form_row">
		')
	);
	
	if(isset($_REQUEST['password_reissued']))
	{
    	$form_contents->addElement(
			new Element_HTMLExternal('
				<div class="form_feedback"> ' . $lang->get('password reissued') . ' </div>
			')
		);
	}
	
	$form_contents->addElement(
		new Element_Hidden(
			"signinto", (isset($_GET['signinto'])) ? htmlspecialchars($_GET['signinto']) : ""
			, array("id"=>"signinto")
		)
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			$lang->get('username') . ":", "username", "left"
			, array("id"=>"username", "mandatory"=>"yes", "maxlength"=>"255")
			, "<error for='username'>" . $lang->get('username mandatory error') . "</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Password(
			$lang->get('password') . ":", "password", "left"
			, array(
				"id"=>"password", "mandatory"=>"yes", "maxlength"=>"32",
				"validate"=>"alphanumeric", "params"=>"6,32"
			)
			, "<error for='password'>" . $lang->get('password error') . "</error><div id='caps_lock'></div>"
		)
	);
	
	$form_contents->addElement(
		new Element_Checkbox(
			"", "keepSignIn", "full"
			, array("1"=>$lang->get('keep me signed in info'))
			, array("id"=>"keepSignIn", "value"=>"1")
			, ""
		)
	);
	
	$form_contents->addElement(
		new Element_HTMLExternal(
			'<div class="form_row_full">
				<button class="register_black" type="submit" id="submit" name="submit">'
				. $lang->get('sign in to cockpit') . '</button>
			</div>'
		)
	);
	
	$form_contents->addElement(new Element_HTMLExternal("</div></fieldset>"));
	$form = '<div class="login_form_container">';
	$form .= $form_contents->render(true);
	$form .= '</div>';
	
	if(isset($_POST['submit']))
	{
		//Form has been submitted, validate the form
		$processor = new CFormValidator($form);
		if($processor->validate())
		{
			$foo = CDBLogin::login($_POST['username'], $_POST['password']);
			
			if($foo['userId'])
			{
				CDBSession::sessionLinkUserId($foo['userId'], $foo['userStatus'], $_POST['keepSignIn']);
			    
				// Employers must update their password every three months
				if($foo['daysSincePasswordChange'] <= -90)
				{
					header(
						'Location: '. CSettings::$HTTP_PROTOCOL . $_SERVER['HTTP_HOST']
						. '/' . 'passwordSet.php'
					);
					
					exit;
				}
				else if(
					!empty($_POST['signinto']) && ($_POST['signinto'] != '/'
					|| $_POST['signinto'] != '/index.php')
				){
					header('Location: '. CSettings::$HTTP_PROTOCOL . $_SERVER['HTTP_HOST'] . $_POST['signinto']);
					
					exit;
				}
				else
				{
					header(
						'Location: ' . CSettings::$HTTP_PROTOCOL . $_SERVER['HTTP_HOST'] . '/'
						. 'controlPanel.php'
					);
					
					exit;
				}
			}
			else
			{
				if($foo['failedLoginCount'] >= 5)
				{
					$processor->error_no = 0;
					$processor->error_msg = $lang->get('username blocked');
					$processor->validate(false);
				}
				else
				{
					// Username or password are incorrect redisplay form
					$processor->error_no = 0;
					$processor->error_msg = $lang->get('username or password error');
					$processor->validate(false);
				}
			}
		}
	}
	else
	{
		//Form initially displayed, no need to validate it
		$processor = new CFormValidator($form, false);
	}

?>
