<?php
	require_once('form/Form.php');
	
	$user_list = CDBUser::getUserList();

	$form_contents = new Form("ec_cockpit_set_user_acl");
	$form_contents->configure(array("action" => "ec_cockpit_set_user_acl.php", "method" => "post"));
	
	//Block - Personal Credentials Start
	$form_contents->addElement(
		new Element_HTMLExternal('
			<error for="form">' . $lang->get('please fill in') . '</error>
			<fieldset style="margin-top:0px;"><legend>Set User\'s ACL in Cockpit</legend>
			<div class="form_row">
		')
	);
	
	$form_contents->addElement(
		new Element_Select(
			"Select a User", "user", "left", $user_list
			, array(
				"id"=>"user", "validate"=>"user"
				, "value"=>"" . $_REQUEST['user'] . ""
			)
			, "<error for=\"user\">" . $lang->get('not a user error') . "</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_HTMLExternal(
			'<div class="form_row_right" style="margin-top:12px;">
				<button class="register_black" type="button" id="fetch_acl" name="fetch_acl">   Fetch ACL   </button>
			</div>'
		)
	);
	
	$form_contents->addElement(new Element_HTMLExternal(
		"<div class='clear10'></div>
		<div id='fetch_acl_container'></div>
		</div></fieldset>"
	));
	
?>

<?php
	$form = '<div class="form_container">';
	$form .= $form_contents->render(true);
	$form .= '</div>';
	if(isset($_POST['submit'])){
		//Form has been submitted, validate the form
		$processor = new CFormValidator($form);
		if($processor->validate()){
			// TODO:
			echo "Nothing TO DO";
		}
	}else{
		//Form initially displayed, no need to validate it
		$processor = new CFormValidator($form, false);
	}
?>