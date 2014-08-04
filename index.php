<?php 
	
	include_once 'bootstrap.php';
	include_once 'CDBSession.php';
	include_once 'CDBHttpTestPlot.php';
	
	global $SMITH_SYSTEM_DEF;
	
	include_once 'formValues' . $SMITH_SYSTEM_DEF['lang'] . '.php';
	include_once 'CLocalization.php';
	$lang = new CLocalization($SMITH_SYSTEM_DEF['lang'], 'index.php');
	
	if(!$SMITH_SYSTEM_DEF['userId'])
	{
		$loginForm = '
		<ul class="indexLoginHeader">
			<form name="signin" method="post" id="signin" action="signin.php" target="_self">
				<label for="username">
					<span>User</span>&nbsp;<input id="username" type="text" name="username" />
				</label>
				<label for="password">
					<span>Password</span>&nbsp;<input id="password" type="password" name="password" />
				</label>
				<button name="submit" id="submit" type="submit" class="indexSignInButton">Sign In</button>
				<a href="signup.php" style="font-size:12px;color:#FF4000;font-weight:bold">Sign Up</a>
			</form>
		</ul>
		<div class="clear10"></div>';
	}
	
	$css_files = array('style.css' => 'all');
	$js_string = "";
	
	if($SMITH_SYSTEM_DEF['userId'])
	{
		include_once 'userHeader.php';
		include_once 'dcontents/indexHeader.php';
	}
	else
	{
		include_once 'basicHeader.php';
	}
	
	$totalHttpPlotExecuted = CDBHttpTestPlot::getTotalNumberOfExecutedHttpPlot();
	
	// XXX: IMPORTANT - MAX big int number in mysql is '9223372036854775808', 64 build
	if($totalHttpPlotExecuted >= 9223372036854775807)
	{
		$totalHttpPlotExecuted = "9223372036854775807+";
	}
	
	if(!$SMITH_SYSTEM_DEF['userId'])
	{
?>
	<div id="main_container">
		<div id="content">
			<?php echo $loginForm; ?>
			<div class="indexHeader">
				<div class="indexBlock"><img src="../images/sperm.gif" class="spermImage"></img><br/><br/>Drive Spam To Your Site</div>
				<div class="indexBlock"><img src="../images/monita.png"></img><br/><br/>Monitor Your Machine</div>
				<div class="indexBlock"><img src="../images/analyze.png"></img><br/><br/>Analyze Your Log</div>
			</div>
<?php
	}
	else
	{
?>
		</div>
		<div class="indexHeader">
			<a href="controlPanel.php" target="_self">
				<div class="indexBlock"><img src="../images/sperm.gif" class="spermImage"></img><br/><br/>Drive Spam To Your Site</div>
			</a>
			<a href="machineDataControlPanel.php" target="_self">
				<div class="indexBlock"><img src="../images/monita.png"></img><br/><br/>Monitor Your Machine</div>
			</a>
			<a href="logDataControlPanel.php" target="_self">
				<div class="indexBlock"><img src="../images/analyze.png"></img><br/><br/>Analyze Your Log</div>
			</a>
		</div>
<?php
	}
?>
	<div class="indexPageShowWholeThreads">
		<div class="counter">
			<h1 class="threadRunningAtThisMoment"><?php echo $totalHttpPlotExecuted; ?></h1>
		</div>
	</div>
<?php
	if($SMITH_SYSTEM_DEF['userId'])
	{
		include_once 'userFooter.php';
	}
	else
	{
		include_once 'basicFooter.php';
	}
?>