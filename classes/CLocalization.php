<?php

	include_once 'CMail.php';

	class CLocalization
	{
		var $lang;
		var $page_name;
		var $translation	= array();
		var $common			= array();

		function CLocalization($lang, $page_name)
		{
			$this->lang = $lang;
			$this->page_name = $page_name;
			$this->_init();
		}

		function _init()
		{
			include_once('localization' . DIRECTORY_SEPARATOR . $this->lang . DIRECTORY_SEPARATOR . $this->page_name);
			include_once('localization' . DIRECTORY_SEPARATOR . $this->lang . DIRECTORY_SEPARATOR . 'common.php');
			$this->translation = array_merge($this->translation, $this->common);
		}

		function get($index, $val = false)
		{
			if(!$val && isset($this->translation[$index])) return $this->translation[$index];
			else if(is_array($val) && isset($this->translation[$index])) return vsprintf($this->translation[$index], $val);
			else if($val && isset($this->translation[$index])) return sprintf($this->translation[$index], $val);
			else
			{
				$message = "[ MISSING LOCALIZATION ] ".$this->lang. "\n";
				$message .= $_SERVER['HTTP_HOST'] . "/" . $_SERVER['REQUEST_URI'] . "\n";
				$message .= "--------------------------------------\nIndex : ".$index."\n";
				$trace = debug_backtrace();
				$message .= "File : ".$trace[0]['file']."\n";
				$message .= "Line : ".$trace[0]['line']."\n";
				
				CMail::send(
					array(CSettings::$SYSTEM_MAIL_VALUES['noreply'], 'COCKPIT @ Gateway')
					, array(CSettings::$SYSTEM_MAIL_VALUES['tech'], 'IT Gods')
					, 'COCKPIT - Localization Error'
					, $message, '', array(), array()
				);
				
				return $index;
			}
		}

		function getLang()
		{
			return $this->lang;
		}
	}
?>