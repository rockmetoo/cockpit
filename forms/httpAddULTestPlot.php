<?php

	include_once 'form/Form.php';
	
	$form_contents = new Form("addUploadTestPlot");
	$form_contents->configure(array("action" => "httpAddULTestPlot.php", "method" => "post", "enctype" => "multipart/form-data"));

	$form_contents->addElement(
		new Element_HTMLExternal(
			'<error for="form">' . $lang->get('please fill in') . '</error>
			<errormsg for="accessLimit" name="checkInteger">Please write an integer number</errormsg>
			<errormsg for="accessLimit" name="checkAccesLimitByUserType">You need to upgrade your maximum access limit by account type</errormsg>
			<errormsg for="plotName" name="checkPlotName">Plot name exists. Please Try Another Name.</errormsg>
			<errormsg for="baseAddress" name="checkBaseAddress">Malformed Base Address</errormsg>
			<errormsg for="File1" name="checkMaxFileSize">File Size Cannot Exceed 10MB</errormsg>
			<errormsg for="File1" name="checkZeroFile">Upload a File is Mandatory</errormsg>
			<errormsg for="queryData" name="checkQueryData">Please Write a Valid Post Data</errormsg>
			<fieldset style="margin-top:0px;"><legend>Create a Http Upload Test Plot</legend><div class="form_row">
		')
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			'Plot Name', "plotName", "left"
			, array("id"=>"plotName", "validate"=>'{"checkPlotName":"checkHttpULPlotExists"}', "mandatory"=>"yes", "maxlength"=>"255")
			, "<error for='plotName'>Plot Name is Mandatory</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Radio(
			"Form Type", "formType", "left spanFloatLeft", $HTTP_FORM_TYPE
			, array("id"=>"formType", "validate"=>"checkFormTypeYes", "value"=>"1")
			, "<error for=\"type\">Please select a valid http form type</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			"Base Address (i.e. http://example.com/upload.php or https://123.123.123.10/upload.php)", "baseAddress", "left"
			, array("id"=>"baseAddress", "mandatory"=>"yes", "maxlength"=>"2048", "validate"=>'{"checkBaseAddress":"checkParseUrl"}')
			, "<error for='baseAddress'>Base Address is Mandatory</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_File(
			"File1", "File1", "left"
			, array("id"=>"File1", "mandatory"=>"yes", "validate"=> '{"checkMaxFileSize":"checkUploadFileSize","checkZeroFile":"checkZeroFileSize"}')
			, "<error for='File1'></error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Textarea(
			'Post Data Query (i.e. fileAtrribute=File1&amp;filename=test.zip&amp;submit=submit)', "queryData", "left spanFloatLeft"
			, array(
				"id"=>"queryData", "maxlength"=>"4096", "mandatory"=>"yes", "validate"=> '{"checkQueryData":"checkPostQueryDataForHttpUpload"}'
				, "style"=> "height:200px"
			)
			, "<error for='queryData'>Set Post Data is Mandatory</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			'Simultaneous Upload', "accessLimit", "left"
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
			
			CDBHttpTestPlot::createHttpULTestPlot($_POST);
			$processor->error_no = 1;
		}
	}
	else
	{
		//Form initially displayed, no need to validate it
		$processor = new CFormValidator($form, false);
	}

?>
