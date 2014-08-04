<?php

	$USER_STATUS = array(
		'1'		=> "Active",
		'2'		=> "Freeze",
		'3'		=> "Inactive",
		'4'		=> "Under Surveillance",
	);

	$iso_languages = array(
		"en"	=> "English",//American
		"bn"	=> "Bengali",
		"ja"	=> "日本語",
	);

	$SYSTEM_MONTH = array(
		"01" => "January",
		"02" => "February",
		"03" => "March",
		"04" => "April",
		"05" => "May",
		"06" => "June",
		"07" => "July",
		"08" => "August",
		"09" => "September",
		"10" => "October",
		"11" => "November",
		"12" => "December"
	);

	$HTTP_METHODS = array(
		'1' => 'GET',
		'2' => 'POST'
	);
	
	$HTTP_TYPE = array(
		'1' => 'http',
		'2' => 'https'
	);
	
	$HTTP_FORM_TYPE = array(
		'1' => 'multipart/form-data'
	);
	
	$HTTP_CONTENT_TYPE = array(
		'1' => 'text/html',
		'2' => 'text/plain',
		'3' => 'text/xml',
		'4' => 'text/json'
	);
	
	$HTTP_ACCEPT = array(
		'1' => 'text/html',
		'2' => 'text/plain',
		'3' => 'text/xml',
		'4' => 'text/json'
	);
	
	$HTTP_CHARSET = array(
		'1' => 'utf-8',
		'2' => 'shift_jis',
		'3' => 'euc-jp',
		'4' => 'iso-2022-jp',
		'5' => 'x-mac-japanese'
	);
	
	$ACCESS_LIMIT_BY_ACCOUNT_TYPE = array(
		'1' => 5000,
		'2' => 50000,
		'3' => 1000000
	);
	
	$RESPONSE_TIMEOUT_LIMIT = array(
		'5'		=> 5,
		'10'	=> 10,
		'30'	=> 30,
		'60' 	=> 60
	);
	
	$HTTP_AUTH_TYPE = array(
		'0' => 'None',
		'1' => 'Http Auth',
		'2' => 'Oauth'
	);
	
	/*$SMITH_APPLICATION_PROTOCOL = array(
		'1'		=> 'XCAP',
		'2'		=> 'Binary',
		'3'		=> 'Raw Binary',
		'4'		=> 'TLV'
	);*/
	
	$SMITH_TRANSPORT_PROTOCOL = array(
		'1'		=> 'TCP',
		'2'		=> 'UDP',
		'3'		=> 'ICMP',
		'4'		=> 'ARP'
	);
	
	$SMITH_TCP_CONTROL_BITS = array(
		'1'		=> 'ACK',
		'2'		=> 'PSH',
		'3'		=> 'RST',
		'4'		=> 'SYN',
		'5'		=> 'FIN',
		'6'		=> 'CWR',
		'7'		=> 'URG',
		'8'		=> 'ECN',
		'9'		=> 'NONE'
	);
	
	// XXX: IMPORTANT READ IT - Check CDBUser (getUserDashboardPosition) if you made any change as follows:
	$QUICK_LINK_COLUMNS = array(
		array(
			'profileManager'			=> '#298A08',
			'httpTestPlot'				=> '#0B4C5F',
			'httpDwTestPlot'			=> '#0489B1',
			'httpUpTestPlot'			=> '#8A0808',
			'tcpTestPlot'				=> '#08088A',
			'udpTestPlot'				=> '#088A85'
		)
	);
	
	$EC_CONTROL_PANEL = array(
		'profileManager' 				=> 'Profile Manager',
		'httpTestPlot'					=> 'Http Spam',
		'httpDwTestPlot'				=> 'Http Spam - Download',
		'httpUpTestPlot'				=> 'Http Spam - Upload',
		'tcpTestPlot'					=> 'TCP Spam',
		'udpTestPlot'					=> 'UDP Spam'
	);

	$EC_CONTROL_PANEL_SUB['profileManager'] = array(
		"profileEdit.php"				=> "Edit Profile",
		"passwordSet.php" 				=> "Reset Password"
	);

	$EC_CONTROL_PANEL_SUB['httpTestPlot'] = array(
		"httpAddTestPlot.php"			=> "Add Spam Plot",
		"httpTriggerTestPlot.php"		=> "Trigger Spam Plot",
		"httpTestPlotResultStat.php"	=> "Result Statistics"
	);
	
	$EC_CONTROL_PANEL_SUB['httpDwTestPlot'] = array(
		"httpAddDLTestPlot.php"			=> "Add Spam Plot",
		"httpTriggerDLTestPlot.php"		=> "Trigger Spam Plot",
		"httpDLTestPlotResultStat.php"	=> "Result Statistics"
	);
	
	$EC_CONTROL_PANEL_SUB['httpUpTestPlot'] = array(
		"httpAddULTestPlot.php"			=> "Add Spam Plot",
		"httpTriggerULTestPlot.php"		=> "Trigger Spam Plot",
		"httpULTestPlotResultStat.php"	=> "Result Statistics"
	);
	
	$EC_CONTROL_PANEL_SUB['tcpTestPlot'] = array(
		"tcpAddTestPlot.php"			=> "Add Spam Plot",
		"tcpTriggerTestPlot.php"		=> "Trigger Spam Plot",
		"tcpTestPlotResultStat.php"		=> "Result Statistics"
	);
	
	$EC_CONTROL_PANEL_SUB['udpTestPlot'] = array(
		"udpAddTestPlot.php"			=> "Add Spam Plot",
		"udpTriggerTestPlot.php"		=> "Trigger Spam Plot",
		"udpTestPlotResultStat.php"		=> "Result Statistics"
	);
	
	// XXX: IMPORTANT READ IT - Check CDBUser (getMachineDataDashboardPosition) if you made any change as follows:
	$MACHINEDATA_QUICK_LINK = array(
		array(
			'CPU'						=> '#298A08',
			'Memory'					=> '#0B4C5F',
			'HDD'						=> '#0489B1',
			'I/O'						=> '#8A0808',
			'Network'					=> '#08088A',
			'Port'						=> '#000000'
		)
	);
	
	$MACHINEDATA_CONTROL_PANEL = array(
		'CPU' 							=> 'CPU Data',
		'Memory'						=> 'Memory Data',
		'HDD'							=> 'HDD Data',
		'I/O'							=> 'Input/Output Data',
		'Network'						=> 'Network Data',
		'Port'							=> 'Port Data'
	);
	
	$MACHINEDATA_CONTROL_PANEL_SUB['CPU'] = array(
		"cpuAddPlot.php"				=> "Add a CPU",
		"cpuListPlot.php"				=> "List Of CPU",
		"cpuPlotResultStat.php"			=> "Result Statistics"
	);
	
	$MACHINEDATA_CONTROL_PANEL_SUB['Memory'] = array(
		"memoryAddPlot.php"				=> "Add a Memory",
		"memoryListPlot.php"			=> "List Of Memory",
		"memoryPlotResultStat.php"		=> "Result Statistics"
	);
	
	$MACHINEDATA_CONTROL_PANEL_SUB['HDD'] = array(
		"hddAddPlot.php"				=> "Add a HDD",
		"hddListPlot.php"				=> "List Of HDD",
		"hddPlotResultStat.php"			=> "Result Statistics"
	);
	
	$MACHINEDATA_CONTROL_PANEL_SUB['I/O'] = array(
		"ioAddPlot.php"					=> "Add a I/O",
		"ioListPlot.php"				=> "List Of I/O",
		"ioPlotResultStat.php"			=> "Result Statistics"
	);
	
	$MACHINEDATA_CONTROL_PANEL_SUB['Network'] = array(
		"networkAddPlot.php"			=> "Add a Network",
		"networkListPlot.php"			=> "List Of Network",
		"networkPlotResultStat.php"		=> "Result Statistics"
	);
	
	$MACHINEDATA_CONTROL_PANEL_SUB['Port'] = array(
		"portAddPlot.php"				=> "Add a Port",
		"portListPlot.php"				=> "List Of Port",
		"portPlotResultStat.php"		=> "Result Statistics"
	);
	
	
	// XXX: IMPORTANT READ IT - Check CDBUser (getLogDataDashboardPosition) if you made any change as follows:
	$LOGDATA_QUICK_LINK = array(
		array(
			'WebLog'					=> '#298A08',
			'SysLog'					=> '#0B4C5F',
			'MailLog'					=> '#0489B1',
			'WhateverLog'				=> '#8A0808'
		)
	);
	
	$LOGDATA_CONTROL_PANEL = array(
		'WebLog' 						=> 'Web Server Log',
		'SysLog'						=> 'SysLog',
		'MailLog'						=> 'Mail Server Log',
		'WhatEverLog'					=> 'WhatEver Log'
	);
	
	$LOGDATA_CONTROL_PANEL_SUB['WebLog'] = array(
		"weblogAddPlot.php"				=> "Add a WebLog",
		"weblogListPlot.php"			=> "List Of WebLog",
		"weblogPlotResultStat.php"		=> "Result Statistics"
	);
	
	$LOGDATA_CONTROL_PANEL_SUB['SysLog'] = array(
		"syslogAddPlot.php"				=> "Add a SysLog",
		"syslogListPlot.php"			=> "List Of SysLog",
		"syslogPlotResultStat.php"		=> "Result Statistics"
	);
	
	$LOGDATA_CONTROL_PANEL_SUB['MailLog'] = array(
		"maillogAddPlot.php"			=> "Add a MailLog",
		"maillogListPlot.php"			=> "List Of MailLog",
		"maillogPlotResultStat.php"		=> "Result Statistics"
	);
	
	$LOGDATA_CONTROL_PANEL_SUB['WhatEverLog'] = array(
		"whateverlogAddPlot.php"		=> "Add a WhatEverLog",
		"whateverlogListPlot.php"		=> "List Of WhatEverLog",
		"whateverlogPlotResultStat.php"	=> "Result Statistics"
	);
?>