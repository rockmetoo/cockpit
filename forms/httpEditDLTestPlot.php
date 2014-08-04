<?php

	include_once 'form/Form.php';
	
	$plotData = CDBHttpTestPlot::getHttpDLTestPlotByName($_REQUEST['plotName']);
	
	// XXX: IMPORTANT - Hack check for plotName
	if(!$plotData)
	{
		$processor->error_no = 2;
		return false;
	}
	
	if($plotData['type'] == 1)		$plotData['baseAddress'] = 'http://' . $plotData['baseAddress'];
	else if($plotData['type'] == 2)	$plotData['baseAddress'] = 'https://' . $plotData['baseAddress'];
	
	$form_contents = new Form("editDLTestPlotForm");
	$form_contents->configure(array("action" => "httpEditDLTestPlot.php", "method" => "post"));

	$form_contents->addElement(
		new Element_HTMLExternal(
			'<error for="form">' . $lang->get('please fill in') . '</error>
			<errormsg for="baseAddress" name="checkBaseAddress">Malformed Base Address</errormsg>
			<errormsg for="accessLimit" name="checkInteger">Please write an integer number</errormsg>
			<errormsg for="accessLimit" name="checkAccesLimitByUserType">You need to upgrade your maximum access limit by account type</errormsg>
			<fieldset style="margin-top:0px;"><legend>Edit Download Test Plot</legend><div class="form_row">
		')
	);
	
	// XXX: IMPORTANT - cannot change plot name
	$form_contents->addElement(
		new Element_Hidden(
			"plotName", (isset($_REQUEST['plotName'])) ? htmlspecialchars($_REQUEST['plotName']) : ""
			, array("id"=>"plotName")
		)
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			"Base Address (i.e. http://example.com/012345abcdef89 or https://123.123.123.10/test.zip)", "baseAddress", "left"
			, array(
				"id"=>"baseAddress", "mandatory"=>"yes", "maxlength"=>"2048", "value"=>$plotData['baseAddress']
				, "validate"=>'{"checkBaseAddress":"checkParseUrl"}'
			)
			, "<error for='baseAddress'>Base Address is Mandatory</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			'Simultaneous Access', "accessLimit", "left"
			, array(
				"id"=>"accessLimit", "validate"=> '{"checkInteger":"checkInt","checkAccesLimitByUserType":"checkAccessLimit"}',
				"mandatory"=>"yes", "maxlength"=>"10", "value"=>$plotData['accessLimit']
			), "<error for='accessLimit'>Simultaneous Access is Mandatory</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Select(
			"Response Timeout (sec)", "responseTimeoutLimit", "left", $RESPONSE_TIMEOUT_LIMIT
			, array("id"=>"responseTimeoutLimit", "validate"=>"checkResponseTimeoutLimit", "value"=>$plotData['responseTimeoutLimit'])
			, "<error for=\"responseTimeoutLimit\">Please select a valid response timeout</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_HTMLExternal(
			'<div class="form_row_full">
				<button class="register_black" type="submit" id="submit" name="submit">'
				. 'Update Test Plot' . '</button>
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
			
			CDBHttpTestPlot::updateHttpDLTestPlot($_POST, true);
			$processor->error_no = 1;
		}
	}
	else
	{
		//Form initially displayed, no need to validate it
		$processor = new CFormValidator($form, false);
	}

?>
