<?php

	include_once 'form/Form.php';
	
	$form_contents = new Form("addDownloadTestPlot");
	$form_contents->configure(array("action" => "httpAddDLTestPlot.php", "method" => "post"));

	$form_contents->addElement(
		new Element_HTMLExternal(
			'<error for="form">' . $lang->get('please fill in') . '</error>
			<errormsg for="baseAddress" name="checkBaseAddress">Malformed Base Address</errormsg>
			<errormsg for="accessLimit" name="checkInteger">Please write an integer number</errormsg>
			<errormsg for="accessLimit" name="checkAccesLimitByUserType">You need to upgrade your maximum access limit by account type</errormsg>
			<errormsg for="plotName" name="checkPlotName">Plot name exists. Please try another name.</errormsg>
			<fieldset style="margin-top:0px;"><legend>Create a Http Download Test Plot</legend><div class="form_row">
		')
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			'Plot Name', "plotName", "left"
			, array("id"=>"plotName", "validate"=>'{"checkPlotName":"checkHttpDLPlotExists"}', "mandatory"=>"yes", "maxlength"=>"255")
			, "<error for='plotName'>Plot Name is Mandatory</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			"Base Address (i.e. http://example.com/012345abcdef89 or https://123.123.123.10/test.zip)", "baseAddress", "left"
			, array("id"=>"baseAddress", "mandatory"=>"yes", "maxlength"=>"2048", "validate"=>'{"checkBaseAddress":"checkParseUrl"}')
			, "<error for='baseAddress'>Base Address is Mandatory</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			'Simultaneous Download', "accessLimit", "left"
			, array(
				"id"=>"accessLimit", "validate"=> '{"checkInteger":"checkInt","checkAccesLimitByUserType":"checkAccessLimit"}',
				"mandatory"=>"yes", "maxlength"=>"10"
			), "<error for='accessLimit'>Simultaneous Access is Mandatory</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Select(
			"Response Timeout (sec)", "responseTimeoutLimit", "left", $RESPONSE_TIMEOUT_LIMIT
			, array("id"=>"responseTimeoutLimit", "validate"=>"checkResponseTimeoutLimit", "value"=>"" . $_REQUEST['responseTimeoutLimit'] . "")
			, "<error for=\"responseTimeoutLimit\">Please select a valid response timeout</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_HTMLExternal(
			'<div class="form_row_full">
				<button class="register_black" type="submit" id="submit" name="submit">'
				. 'Craete a Test Plot' . '</button>
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
			include_once 'CDBHttpTestPlot.php';
			
			CDBHttpTestPlot::createHttpDLTestPlot($_POST);
			$processor->error_no = 1;
		}
	}
	else
	{
		//Form initially displayed, no need to validate it
		$processor = new CFormValidator($form, false);
	}

?>
