<?php

	include_once 'form/Form.php';
	
	$form_contents = new Form("cpuMonita");
	
	$form_contents->configure(array("action" => "monitaAddCPU.php", "method" => "post"));

	$form_contents->addElement(
		new Element_HTMLExternal(
			'<error for="form">' . $lang->get('please fill in') . '</error>
			<fieldset style="margin-top:0px;"><legend>Create a CPU Monita</legend><div class="form_row">
		')
	);
	
	
	$form_contents->addElement(
		new Element_Textbox(
			'CPU Monita Name:', "cpuMonita", "left", array("id"=>"cpuMonita", "mandatory"=>"yes", "maxlength"=>"64")
			, "<error for='instance'>CPU Monita Name is Mandatory</error>"
		)
	);
	
	$form_contents->addElement(
		new Element_Textbox(
			'CPU Monita Graph Title' . ":", "graphTitle", "left", array("id"=>"graphTitle", "maxlength"=>"256")
		)
	);
	
	$form_contents->addElement(
		new Element_HTMLExternal(
			'<div class="form_row_full">
				<button class="register_black" type="submit" id="submit" name="submit">Create</button>
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
			include('CDBCPUMonita.php');
			
			$instanceId = CDBCPUMonita::createCPUMonita($_POST['cpuMonita'], $_POST['graphTitle']);
			
			if($instanceId)
			{
				$processor->error_no = 1;
			}
			else
			{
				// Instance Name and Host incorrect redisplay form
				$processor->error_no = -1;
				$processor->validate(false);
			}
		}
	}
	else
	{
		//Form initially displayed, no need to validate it
		$processor = new CFormValidator($form, false);
	}

?>