<?php

	require_once('form/Form.php');
	
	$allHttpTestPlotName = '<table class="theTable">' . CDBHttpTestPlot::getAllHttpDLTestPlotHtmlNameByUserId() . '</table>';
	
	$form_contents = new Form("httpTriggerDLTestPlotForm");
	$form_contents->configure(array("action" => "httpTriggerDLTestPlot.php", "method" => "post"));

	$form_contents->addElement(new Element_HTMLExternal('<fieldset style="margin-top:0px;"><div class="form_row">'));
	
	$form_contents->addElement(new Element_HTMLExternal($allHttpTestPlotName));
	
	$form_contents->addElement(
		new Element_HTMLExternal(
			'<div class="form_row_full" style="text-align: center; margin-top:5px;">
				<button class="register_black" type="submit" id="submit" name="submit">'
				. 'Trigger The Plot' . '</button>
			</div>'
		)
	);
	
	$form_contents->addElement(new Element_HTMLExternal("</div></fieldset>"));
	$form = '<div class="login_form_container">';
	$form .= $form_contents->render(true);
	$form .= '</div>';
	
	//Form initially displayed, no need to validate it
	$processor = new CFormValidator($form, false);
?>
