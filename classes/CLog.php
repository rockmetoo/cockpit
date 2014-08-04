<?php

	class CLog
	{
		const ERROR		= 1;
		const WARNING	= 2;
		const INFO		= 3;
		const DEBUG		= 5;

		public $filename;
		public $file;
		/**
		 * @var boolean if true, does not close file after write and reuses file resource.
		 */
		public $keepopen = false;
		/**
		 * @var int Level at which logging is started.
		 */
		public $level = CLog::ERROR;

		private $levels = array(
			0				=> 'None',
			CLog::ERROR		=> 'Error',
			CLog::WARNING	=> 'Warning',
			CLog::INFO		=> 'Info',
			CLog::DEBUG		=> 'Debug'
		);

		/**
		 * A simple message logger
		 *
		 * @param string $logname
		 * @param string $path
		 */
		public function __construct($logname = 'default', $path = null)
		{
			$path = (is_null($path)) ? CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'log' : $path;
			$this->filename = sprintf('%s%s%s.log.%s.txt', $path, DIRECTORY_SEPARATOR, $logname, date('Y-m-d'));
			if(!file_exists($this->filename))
			{
				fclose(fopen($this->filename, 'at'));
				chmod($this->filename, 0666);
			}
			
			if(defined('LOG_LEVEL')) $this->level = LOG_LEVEL;
		}

		/**
		 * Write message to log file
		 *
		 * @param int $level
		 * @param string $format
		 * @param mixed $vars
		 */
		public function log($level, $format, $vars=array())
		{
			if($this->level < $level) return;

			if($this->file) $file = $this->file;
			if(!$file) $file = fopen($this->filename, 'at');
			if($this->keepopen && !$this->file) $this->file = $file;

			$info = array(date('Y-m-d H:i:s'), $this->levels[$level]);
			if(!is_array($vars)) $vars = array($vars);
			$vars = array_merge($info, $vars);
			$debug_str = vsprintf('[%s](%s) ' . $format ."\n", $vars);
			fputs($file, $debug_str);
			if(!$this->keepopen) fclose($file);
			else fflush($file);
		}

		/**
		 * Write ERROR level message to log file
		 *
		 * @param string $format
		 * @param mixed $vars
		 */
		public function err($format, $vars=array())
		{
			$this->log(CLog::ERROR, $format, $vars);
		}

		/**
		 * Write WARNING level message to log file
		 *
		 * @param string $format
		 * @param mixed $vars
		 */
		public function warn($format, $vars=array())
		{
			$this->log(CLog::WARNING, $format, $vars);
		}

		/**
		 * Write INFO level message to log file
		 *
		 * @param string $format
		 * @param mixed $vars
		 */
		public function info($format, $vars=array())
		{
			$this->log(CLog::INFO, $format, $vars);
		}

		/**
		 * Write DEBUG level message to log file
		 *
		 * @param string $format
		 * @param mixed $vars
		 */
		public function debug($format, $vars=array())
		{
			$this->log(CLog::DEBUG, $format, $vars);
		}

		/**
		 * Close file resource.
		 *
		 */
		public function close()
		{
			if($this->file) fclose($this->file);
		}
	}
?>