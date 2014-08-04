<?php

	include_once 'form/Form.php';
	
	$plotData = CDBHttpTestPlot::getHttpTestPlotByName($_REQUEST['plotName']);
	
	// XXX: IMPORTANT - Hack check for plotName
	if(!$plotData)
	{
		$processor->error_no = 2;
		return false;
	}
	
	if($plotData['type'] == 1)		$plotData['baseAddress'] = 'http://' . $plotData['baseAddress'];
	else if($plotData['type'] == 2)	$plotData['baseAddress'] = 'https://' . $plotData['baseAddress'];
	
	$form_contents = new Form("web_instance");
	$form_contents->configure(array("action" => "httpEditTestPlot.php", "method" => "post"));

	$form_contents->addElement(
		new Element_HTMLExternal(
			'<error for="form">' . $lang->get('please fill in') . '</error>
			<errormsg for="baseAddress" name="checkBaseAddress">Malformed Base Address</errormsg>
			<errormsg for="accessLimit" name="checkInteger">Please write an integer number</errormsg>
			<errormsg for="accessLimit" name="checkAccesLimitByUserType">You need to upgrade your maximum access limit by account type</errormsg>
			<fieldset style="margin-top:0px;"><legend>Edit Http Test Plot</legend><div class="form_row">
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
		new Element_Select(
			"Select a Method", "method", "left", $HTTP_METHODS
			, array("id"=>"method", "validate"=>"checkHttpMethod", "value"=>"" . $plotData['method'] . "")
			, "<error for=\"method\">Please select a valid http method</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Select(
			"Content Type", "contentType", "right", $HTTP_CONTENT_TYPE
			, array("id"=>"contentType", "validate"=>"checkHttpContentType", "value"=>"" . $plotData['contentType'] . "")
			, "<error for=\"contentType\">Please select a valid content type</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Select(
			"Accept", "accept", "left", $HTTP_ACCEPT
			, array("id"=>"accept", "validate"=>"checkHttpAccept", "value"=>"" . $plotData['accept'] . "")
			, "<error for=\"accept\">Please select a valid accept type</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Select(
			"Charset", "charset", "right", $HTTP_CHARSET
			, array("id"=>"charset", "validate"=>"checkHttpCharset", "value"=>"" . $plotData['charset'] . "")
			, "<error for=\"charset\">Please select a valid character set</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			"Base Address (i.e. http://example.com/game.php or https://123.123.123.10/game.php)", "baseAddress", "left"
			, array(
				"id"=>"baseAddress", "mandatory"=>"yes", "maxlength"=>"2048", "value"=>$plotData['baseAddress']
				, "validate"=>'{"checkBaseAddress":"checkParseUrl"}'
			)
			, "<error for='baseAddress'>Base Address is Mandatory</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Textarea(
			'Query/Post Data', "queryData", "left spanFloatLeft",
			array(
				"id"=>"queryData", "maxlength"=>"4096", "style"=> "height:200px",
				"value"=>$plotData['queryData']
			)
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
		new Element_Select(
			"Authentication Type (if any)", "authType", "left", $HTTP_AUTH_TYPE
			, array("id"=>"authType", "validate"=>"checkHttpAuthType", "value"=>$plotData['authType'])
			, "<error for=\"authType\">Please select a valid auth type or none</error>"
		)
	);
	
	$form_contents->addElement(new Element_HTMLExternal('<div id="httpAuthDiv" style="clear:both">'));
	
	$form_contents->addElement(
		new Element_Textbox('Auth User:', "authUser", "left", array("id"=>"authUser", "maxlength"=>"255", "value"=>$plotData['authUser']))
	);

	$form_contents->addElement(
		new Element_Password('Auth Password:', "authPassword", "left", array("id"=>"authPassword", "maxlength"=>"255", "value"=>$plotData['authPassword']))
	);
	
	$form_contents->addElement(new Element_HTMLExternal('</div>'));
	
	$form_contents->addElement(new Element_HTMLExternal('<div id="httpOauthDiv" style="clear:both">'));
	
	$form_contents->addElement(
		new Element_Textbox('Consumer Key:', "consumerKey", "left", array("id"=>"consumerKey", "maxlength"=>"255", "value"=>$plotData['consumerKey']))
	);
	
	$form_contents->addElement(
		new Element_Textbox('Consumer Secret:', "consumerSecret", "left", array("id"=>"consumerSecret", "maxlength"=>"255", "value"=>$plotData['consumerSecret']))
	);
	
	$form_contents->addElement(
		new Element_Textbox('Token:', "token", "left", array("id"=>"token", "maxlength"=>"255", "value"=>$plotData['token']))
	);
	
	$form_contents->addElement(
		new Element_Textbox('Token Secret:', "tokenSecret", "left", array("id"=>"tokenSecret", "maxlength"=>"255", "value"=>$plotData['tokenSecret']))
	);
	
	$form_contents->addElement(new Element_HTMLExternal('</div>'));
	
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
			
			CDBHttpTestPlot::updateHttpTestPlot($_POST, true);
			$processor->error_no = 1;
		}
	}
	else
	{
		//Form initially displayed, no need to validate it
		$processor = new CFormValidator($form, false);
	}

?>
