<?php

	include_once 'form/Form.php';
	
	global $SMITH_USER_DEF;
	global $SMITH_SYSTEM_DEF;

	$form_contents = new Form("profileEdit");
	$form_contents->configure(array("action" => "profileEdit.php", "method" => "post"));
	
	//Block - Personal Credentials Start
	$form_contents->addElement(
		new Element_HTMLExternal('
			<error for="form">' . $lang->get('please fill in') . '</error>
			<fieldset style="margin-top:0px;"><legend>Personal Credentials</legend>
			<div class="form_row">
		')
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			$lang->get('firstName') . ":", "firstName", "left"
			, array(
				"id"=>"firstName", "value"=> "" . CHelperFunctions::xmlEscape($SMITH_USER_DEF['firstName'])."",
				"mandatory"=>"yes", "maxlength"=>"255"
			)
			, "<error for='firstName'>" . $lang->get('user first name') . "</error>"
		)
	);

	$form_contents->addElement(
		new Element_Textbox(
			$lang->get('lastName') . ":", "lastName", "right"
			, array(
				"id"=>"lastName", "value"=> "" . CHelperFunctions::xmlEscape($SMITH_USER_DEF['lastName'])."",
				"mandatory"=>"yes", "maxlength"=>"255"
			)
			, "<error for='lastName'>" . $lang->get('user last name') . "</error>"
		)
	);
	
	$form_contents->addElement(new Element_HTMLExternal("</div></fieldset>"));

	$form_contents->addElement(
		new Element_HTMLExternal('<fieldset><legend>Cable Contact Information</legend><div class="form_row">')
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			"Primary Email:", "primaryEmail", "left"
			, array(
				"id"=>"primaryEmail", "mandatory"=>"yes", "maxlength"=>"255",
				"validate"=>"email", "value"=> "" . $SMITH_USER_DEF['primaryEmail'] . "",
				"params"=>"6,255"
			)
			, "<error for='primaryEmail'>" . $lang->get('primary email error') . "</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			"Tele Phone:", "telePhone", "left"
			, array("id"=>"telePhone", "mandatory"=>"yes", "maxlength"=>"32", "value"=> "" . $SMITH_USER_DEF['telePhone'] . "")
			, "<error for='telePhone'>" . $lang->get('tele phone error') . "</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			"Cell Phone:", "mobilePhone", "right"
			, array("id"=>"mobilePhone", "maxlength"=>"32", "value"=> "" . $SMITH_USER_DEF['mobilePhone'] . "")
		)
	);
	
	$form_contents->addElement(new Element_HTMLExternal("</div></fieldset>"));

	$form_contents->addElement(
		new Element_HTMLExternal(
			'<div class="form_row_full" style="margin-top:12px;text-align:center;">
				<button class="register_black" type="submit" id="submit" name="submit" style="width:150px;">Update</button>
			</div>'
		)
	);
?>

<?php
	$form = '<div class="form_container">';
	$form .= $form_contents->render(true);
	$form .= '</div>';
	if(isset($_POST['submit'])){
		// Form has been submitted, validate the form
		$processor = new CFormValidator($form);
		if($processor->validate()){
			
			// TODO: working in progress
			$_POST['firstName']		= trim($_POST['firstName']);
			$_POST['lastName']		= trim($_POST['lastName']);
			$_POST['primaryEmail']	= trim($_POST['primaryEmail']);
			$_POST['telePhone']		= trim($_POST['telePhone']);
			$_POST['mobilePhone']	= trim($_POST['mobilePhone']);
			
			CDBUser::setUserBasicProfile($SMITH_SYSTEM_DEF['userId'], $_POST, true);
			$processor->error_no = 1;
			$processor->error_msg = "Your Profile Information are Stored Successfully";
		}
	}else{
		//Form initially displayed, no need to validate it
		$processor = new CFormValidator($form, false);
	}
?>