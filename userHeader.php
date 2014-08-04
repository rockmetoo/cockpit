<?php

	global $SMITH_SYSTEM_DEF;

	//all global js to js array.
	$global_js_files = array('jquery.min.js', 'jquery.ui.js');
	
	if(isset($js_files) && is_array($js_files))
	{
		$js_files = array_unique(array_merge($global_js_files, $js_files));
	}
	else $js_files = &$global_js_files;

	//set js script text
	$js_string = trim($js_string);

	//set all css to css array
	$global_css = array('screen.css' => 'all');
	
	if(isset($css_files) && is_array($css_files))
	{
		$css_files = array_merge($global_css, $css_files);
	}
	else $css_files = &$global_css;

	//disable content type stuff. Net_UserAgent_Detect::getBrowser we need to use
	$is_std_browser = '';

	//Set content types depending on browser standard compliance.
	//This will generate more reliable output in
	//none IE browsers.
	$content_type = array(
		'script' => ($is_std_browser) ? 'application/javascript' : 'text/javascript',
		'html' => ($is_std_browser) ?  'application/xhtml+xml' : 'text/html'
	);

	//what type of documents we are serving
	mb_internal_encoding('UTF-8');
	header('Content-Type: ' . $content_type['html'] . '; charset=utf-8');
	$content_language = ($SMITH_SYSTEM_DEF['lang']) ? $COCKPIT_SYSTEM_DEF['lang'] : 'en';
	header('Content-Language: ' . $content_language);
	//prevent document caching
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml"
		xml:lang="<?php echo $content_language; ?>" lang="<?php echo $content_language; ?>"
	>
		<head>
			<meta http-equiv="Content-Type" content="<?php echo $content_type['html']; ?>;
				charset=utf-8" />
		<title>
			<?php
				if(isset($title)) echo $title . ' : ' . 'COCKPIT Control Panel';
				else
				{
					$title_id = 'title_' . date('w');
					echo 'COCKPIT Control Panel' . ' : ' . 'Best Control In the World';
				}
			?>
		</title>
		<meta content="all" name="audience" />
		<meta content="index,all" name="robots" />
		<meta content="2 days" name="revisit-after" />
		<meta name="Author" content="Mituz Car" />
		<meta name="description" content="<?php if(isset($meta_description)) echo $meta_description; else echo 'Car'; ?>" />
		<meta name="keywords" content="<?php if(isset($meta_keywords)) echo $meta_keywords; else echo 'best car'; ?>" />
		<?php
			foreach($js_files as $script_name)
			{
				if(strstr($script_name, 'maps.google'))
				{
		?>
		<script <?php if(!$is_std_browser) echo 'language="javascript"';?> src="<?php echo htmlspecialchars($script_name); ?>" type="<?php echo $content_type['script']; ?>">
		</script>
		<?php
				}
				else
				{
		?>
		<script
			<?php if(!$is_std_browser) echo 'language="javascript"';?>
			src="js/<?php echo htmlspecialchars($script_name);
			echo '?version=' . filemtime(CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'js/'
			. htmlspecialchars($script_name)); ?>" type="<?php echo $content_type['script']; ?>"
		></script>
	<?php
				}
		}
		
		if(!empty($custom_js_files))
		{
			foreach($custom_js_files as $script_name)
			{
			?>
				<script
					<?php if(!$is_std_browser) echo 'language="javascript"';?>
					src="<?php echo htmlspecialchars($script_name);
					echo '?version=' . filemtime(CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR
					. htmlspecialchars($script_name)); ?>" type="<?php echo $content_type['script']; ?>"
				></script>
			<?php
			}
		}
		
		if(isset($js_string))
		{
	?>
			<script <?php if(!$is_std_browser) echo 'language="javascript"'; ?>
				type="<?php echo $content_type['script']; ?>">
				<?php
					if($is_std_browser) echo "// <![CDATA[ \n";
					echo $js_string;
					if($is_std_browser) echo "// ]]>";
				?>
			</script>
		<?php
		}
		
		foreach($css_files as $file => $media)
		{
		?>
			<link rel="stylesheet" type="text/css"
				href="css/<?php echo htmlspecialchars($file); echo '?version=' .
				filemtime(CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'css/' . htmlspecialchars($file)); ?>"
				media="<?php if($media) echo $media; else echo 'all'; ?>"
			/>
		<?php
		}
		if(!empty($custom_css_files))
		{
			foreach($custom_css_files as $file => $media)
			{
			?>
				<link rel="stylesheet" type="text/css"
					href="<?php echo htmlspecialchars($file); echo '?version=' .
					filemtime(CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . htmlspecialchars($file)); ?>"
					media="<?php if($media) echo $media; else echo 'all'; ?>"
				/>
			<?php
			}
		}
		?>
</head>
<body>