<?php

	require_once('form/Form.php');
	$form_contents = new Form("set_password");
	$form_contents->configure(array("action" => "passwordSet.php", "method" => "post"));

	$form_contents->addElement(
		new Element_HTMLExternal(
			'<error for="form">' . $lang->get('please fill in') . '</error><div id="caps_lock"></div>
			<fieldset style="margin-top:0px;"><legend>Reset Your Password</legend><div class="form_row">
		')
	);
	
	$form_contents->addElement(
		new Element_Password(
			'Old Password' . ":", "old_password", "left"
			, array("id"=>"old_password", "mandatory"=>"yes", "maxlength"=>"32")
			, "<error for='old_password'>Old Password is Mandatory</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Password(
			'New Password' . ":", "new_password", "left"
			, array("id"=>"new_password", "mandatory"=>"yes", "maxlength"=>"32", "validate"=>"alphanumeric", "params"=>"6,32")
			, "<error for='new_password'>" . $lang->get('password error') . "</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Password(
			'Retype New Password' . ":", "re_new_password", "left"
			, array("id"=>"re_new_password", "mandatory"=>"yes", "maxlength"=>"32", "validate"=>"alphanumeric", "params"=>"6,32")
			, "<error for='re_new_password'>" . $lang->get('password error') . "</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_HTMLExternal(
			'<div class="form_row_full">
				<button class="register_black" type="submit" id="submit" name="submit">'
				. 'Reset New Password' . '</button>
			</div>'
		)
	);
	$form_contents->addElement(new Element_HTMLExternal("</div></fieldset>"));
	$form = '<div class="login_form_container">';
	$form .= $form_contents->render(true);
	$form .= '</div>';
	if(isset($_POST['submit'])){
		
		// Form has been submitted, validate the form
		$processor = new CFormValidator($form);
		if($processor->validate()){
			
			list($ret, $message) = CDBUser::setPassword($_POST['old_password'], $_POST['new_password'], $_POST['re_new_password']);
			
			if($ret){
				$processor->error_no = 1;
				$processor->error_msg = $message;
			}else{
				$processor->error_no = 0;
				$processor->error_msg = $message;
				$processor->validate(false);
			}
		}
	}else{
		//Form initially displayed, no need to validate it
		$processor = new CFormValidator($form, false);
	}

?>