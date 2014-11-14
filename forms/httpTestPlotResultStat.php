<?php

	require_once('form/Form.php');
	
	// TODO: check the plot is running or not. if running grayed it inside select box and put 'running' beside the name
	$allHttpTestPlotName = CDBHttpTestPlot::getAllHttpTestPlotNameByUserId();
	
	$allHttpTestPlotName = array("" => "Please select") + $allHttpTestPlotName;
	
	$form_contents = new Form("web_instance");
	$form_contents->configure(array("action" => "httpTestPlotResultStat.php", "method" => "post"));

	$form_contents->addElement(
		new Element_HTMLExternal(
			'<errormsg for="plotName" name="checkPlotAvailable">Please select a valid http test plot</errormsg>
			<fieldset style="margin-top:0px;"><div class="form_row">'
		)
	);
	
	// XXX: check is it a valid plot
	$form_contents->addElement(
		new Element_Select(
			"Select a test plot", "plotName", "left", $allHttpTestPlotName
			, array(
				"id"=>"plotName", "validate"=>'{"checkPlotAvailable":"checkHttpPlotAvailable"}',
				"value"=>isset($_REQUEST['plotName']) ? $_REQUEST['plotName'] : ''
			)
			, "<error for=\"plotName\"></error>"
		)
	);
	
	$form_contents->addElement(
		new Element_HTMLExternal(
			'<div class="form_row_right">
				<button class="register_black" type="submit" id="submit" name="submit">'
				. 'Show The Stats' . '</button>
			</div>'
		)
	);
	
	$form_contents->addElement(new Element_HTMLExternal("</div></fieldset>"));
	$form = '<div class="login_form_container">';
	$form .= $form_contents->render(true);
	$form .= '</div>';
	
	if(isset($_POST['submit']))
	{
		// Form has been submitted, validate the form
		$processor = new CFormValidator($form);
		
		if($processor->validate())
		{
			$processor->returnData	= "";
			$foo					= CDBHttpTestPlot::getHttpTestPlotByName($_POST['plotName']);
			
			// by default $overAllAnalyzedData contains last 5 records (cursor limit)
			list($lastAnalyzedData, $overAllAnalyzedData) = CDBHttpTestPlot::getHttpTestPlotAnalyzedData($COCKPIT_SYSTEM_DEF['userId'], $_POST['plotName'], 5);
			
			$countOverAllAnalyzedData = count($overAllAnalyzedData);
			
			if($countOverAllAnalyzedData > 1)
			{
				for($i=0; $i<$countOverAllAnalyzedData; $i++)
				{
					$timeAxis[]				= date('Y-m-d', $overAllAnalyzedData[$i]['dateCreated']);
					$maxHops[]				= $overAllAnalyzedData[$i]['maxHops'];
					$avgResponse200[]		= $overAllAnalyzedData[$i]['avgResponse200'];
					$nameResolutionTime[]	= $overAllAnalyzedData[$i]['nameResolutionTime'];
					$connectTime[]			= $overAllAnalyzedData[$i]['connectTime'];
				}

				$processor->returnData = '
				<div class="form_container">
					<a href="#" class="showHideOverAll" id="plusOverAll">+Overall Test Result+</a>
					<div id="overAllAnalyzedData" style="margin-top:5px">
						<script>
							function customTick(obj, data, value, index, x, y, color, prevX, prevY)
							{
								var ctx = obj.canvas.getContext("2d");
								ctx.fillStyle = "white";
								
								ctx.beginPath();
								ctx.arc(x, y, 3, 0, Math.PI*2, true);
								ctx.closePath();
								ctx.stroke();
								ctx.fill();
							}
						</script>
						' . CDBHttpTestPlot::generateLineGraph('maxHops', $timeAxis, $maxHops, 54, 'Hops up to ' . $foo['hostIpAddress'], 'No. of Hops') . '
						' . CDBHttpTestPlot::generateLineGraph('avgResponse200', $timeAxis, $avgResponse200, max($avgResponse200), 'Average response time for 200', 'millisecond') . '<br/>
						' . CDBHttpTestPlot::generateLineGraph('nameResolutionTime', $timeAxis, $nameResolutionTime, max($nameResolutionTime), 'Average name resolution time', 'millisecond') . '
						' . CDBHttpTestPlot::generateLineGraph('connectTime', $timeAxis, $connectTime, max($connectTime), 'Average connect time to ' . $foo['hostIpAddress'], 'millisecond') . '
					</div>
				</div>';
			}
				
			if($lastAnalyzedData)
			{
				$processor->returnData .= '
					<br/>
					<div class="form_container">
						<a href="#" class="showHideLast" id="plusLast">-Last Test Result-</a>
						<div id="lastAnalyzedData">
				';
				
				if(!empty($foo))
				{
					//  && isset($foo['hostIpAddress']) && is_double($foo['hostIpLatiTude']) && is_double($foo['hostIpLongiTude'])
					$processor->returnData .= '<div id="ipLocation"></div><br/>';
					// function to calculate distance
					
					$processor->returnData .=  "
					<script>
						var targetIpAddress = new google.maps.LatLng(" . $foo['hostIpLatiTude'] . ", " . $foo['hostIpLongiTude'] . ");
						var myOptions = {
							zoom:13,
							mapTypeId: google.maps.MapTypeId.ROADMAP,
							center: targetIpAddress
						}
						var map = new google.maps.Map(document.getElementById('ipLocation'), myOptions);
						var marker1 = new google.maps.Marker(
						{
							position: new google.maps.LatLng(" . $foo['hostIpLatiTude'] . ", " . $foo['hostIpLongiTude'] . "),
							map: map
						});
						
						var marker2 = new google.maps.Marker(
						{
							position: new google.maps.LatLng(35.6900, 139.6900),
							map: map
						});
						
						var targetIpAddress	= new google.maps.LatLng(" . $foo['hostIpLatiTude'] . ", " . $foo['hostIpLongiTude'] . ");
						var strIpAddress	= new google.maps.LatLng(35.6900, 139.6900);
						var kmdistance		= (google.maps.geometry.spherical.computeDistanceBetween(targetIpAddress, strIpAddress) / 1000).toFixed(2);
						
						
						var infoWindow = new google.maps.InfoWindow(
						{
							content: 'spherical distance ' + kmdistance + 'km'
						});
						
						google.maps.event.addListener(marker1, 'click', function()
						{
							infoWindow.open(map, marker1);
						});
								
						google.maps.event.addListener(marker2, 'click', function()
						{
							infoWindow.open(map, marker2);
						});
						
						var ipCoordinates = [targetIpAddress, strIpAddress];
						var line = new google.maps.Polyline(
						{
							path: ipCoordinates,
							geodesic: true,
							strokeColor: '#FF0000',
							strokeOpacity: 1.0,
							strokeWeight: 2
						});
						
						google.maps.event.addListener(line, 'mouseover', function()
						{
							infoWindow.open(map, line);
						});
						
						line.setMap(map);
					</script>";
					
				}
				
				$avgResponse200 = ($lastAnalyzedData['avgResponse200']) ? ($lastAnalyzedData['avgResponse200'] . 'ms') : 'N/A';
				
				$processor->returnData .= '
					<table border="1">
						<tr>
							<td>Maximum Hops: ' . $lastAnalyzedData['maxHops'] . '</td>
							<td>Average Response(200) Time: ' . $avgResponse200 . '</td>
						</tr>
						<tr>
							<td>Average Name Resolution Time: ' . $lastAnalyzedData['nameResolutionTime'] . 'ms</td>
							<td>Average Connect Time: ' . $lastAnalyzedData['connectTime'] . 'ms</td>
						</tr>
					</table><br/>
					';
				
				$processor->returnData .= CDBHttpTestPlot::generatePieGraph(
					'successFailed', array($lastAnalyzedData['successHit'], $lastAnalyzedData['failedHit']), array('Success Hits', 'Failed Hits')
				);
				
				if($lastAnalyzedData['response200'])
				{
					$processor->returnData .= CDBHttpTestPlot::generatePieGraph(
						'success200', array($lastAnalyzedData['totalHit'], $lastAnalyzedData['response200']), array('Total Hits', 'Res. 200')
					);
				}
				
				if($lastAnalyzedData['response401'])
				{
					$processor->returnData .= CDBHttpTestPlot::generatePieGraph(
						'response401', array($lastAnalyzedData['totalHit'], $lastAnalyzedData['response401']), array('Total Hits', 'Res. 401')
					);
				}
				
				if($lastAnalyzedData['responseTimeout'])
				{
					$processor->returnData .= CDBHttpTestPlot::generatePieGraph(
						'responseTimeout', array($lastAnalyzedData['totalHit'], $lastAnalyzedData['responseTimeout']), array('Total Hits', 'Timeout')
					);
				}
				
				$processor->returnData .= '</div></div>';
				
			}
			else
			{
				$processor->returnData = 'Report data not ready yet';
			}
		}
	}
	else
	{
		//Form initially displayed, no need to validate it
		$processor = new CFormValidator($form, false);
	}

?>
