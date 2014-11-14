<?php
	
	include_once 'CDBLogin.php';
	include_once 'CXML.php';

	class CFormRules
	{
		public static function checkAlphaAtDot($value, $params)
		{
			if(preg_match("'[^A-Za-z0-9@.]|\s{2}'", $value))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		
		public static function checkInt($value, $params=array())
		{
			return ((!$value && $params[0]) || (intval($value) == strval($value) && $value !== '') ) ? true : false;
		}
		
		public static function checkFloat($value, $params=array())
		{
			return ((!$value && $params[0]) || (floatval($value) == strval($value) && $value !== '') ) ? true : false;
		}
		
		public static function checkUnixName($value, $params)
		{
			//UNIX style file/directory name only
			if(preg_match("'[^A-Za-z0-9_-]|\s{2}'", $value))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		
		public static function checkNumeric($value, $params=array())
		{
			if(is_numeric($value)) return true;
			else return false;
		}

		public static function checkDate($date, $params=array())
		{
			if( $date == '' || $date == 'yyyy-mm-dd' || $date == '0000-00-00' )
			{
				// Empty or default field. Use mandatory="yes" to make it mandatory.
				return true;
			}
			//if mandatory=yes, check below!
			if(!preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches))
			{
				//date not right format yyyy-mm-dd
				return false;
			}
			
			if(!checkdate($matches[2], $matches[3], $matches[1]))
			{
				//date not correct
				return false;
			}
			return true;
		}

		public static function checkDateMandatory($date, $params)
		{
			if(!preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches))
			{
				//date not right format yyyy-mm-dd
				return false;
			}
			
			if(!checkdate($matches[2], $matches[3], $matches[1]))
			{
				//date not correct
				return false;
			}
			return true;
		}

		public static function checkEmail($email, $params = array())
		{
			//sanitise args
			if(!is_array($params)) $params = array($params);
			if(!$email) return true; //succeeds on blank.
			//Create the syntactical validation regular expression
			$regexp = "/^([_a-z0-9-+]+)(\.[_a-z0-9-+]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
			//Validate the syntax
			if(preg_match($regexp, $email))
			{
				list($username, $domain) = explode("@",$email);
			}
			else
			{
				return false;
			}
			
			return true;
		}

		/**
		 * Check if value is a valid URL
		 *
		 * Params values:
		 * 	[0] - array<string>	- List of accepted protocols. Defaults to http & https.
		 *  [1] - boolean		- Host section is optional. Default is false.
		 *
		 * @param string $value
		 * @param array $params
		 * @return boolean
		 */
		public static function checkURL($value, $params=array())
		{
			if(!is_string($value)) return false;
			//get allowed protocols
			$protocol = ($params[0]) ? $params[0] : array('http', 'https');
		
			//based on regexp from RFC3986 Appendix B
			$matches = array();
			if(!preg_match('|^(?:([^:/?#]+):)?(?://([^/?#]*))?([^?#]*)(\?([^#]*))?(?:#(.*))?|',$value, $matches)) return false;
		
			//check protocol match
			if(!in_array($matches[1], $protocol)) return false;
		
			//check host is present
			if(!$params[1] && $matches[2] == '') return false;
		
			//everything passed ok
			return true;
		}
		
		public static function checkUserPrimaryEmail($value, $params){
			if(!empty($value) && (self::checkEmail($value))){
				if(CDBLogin::getCheckUserPrimaryEmail($value) == 0) return true;
				else return false;
			}else{
				return true;
			}
		}

		public static function checkImage($image, $params)
		{
			//params should be of format{maxwidth}x{maxheight}x{maxweight}x{mandatoryboolean} aka 400x300x10240x0, if unset, all sizes are allowed
			if (isset($params)) list($width, $height, $size, $mandatory) = explode('x', $params);
			else $mandatory = 1;
			//Ignore not uploaded not mandatory images
			if(!$mandatory && $image['error'] == 4 && $image['size'] == 0)
			{
				//Image not uploaded
				return true;
			}
			
			//Check if image is gif, png or jpg
			$imageinfo = getimagesize($image['tmp_name']);
			$imagesize = filesize($image['tmp_name']);
			if($imageinfo[2] != '1' && $imageinfo[2] != '2' && $imageinfo[2] != '3')
			{
				//Image isnt JPG, GIF nor PNG
				return false;
			}
			
			if(isset($params))
			{
				list($width, $height, $size, $mandatory) = explode('x', $params);
				if($imageinfo[0] > $width || $imageinfo[1] > $height)
				{
					//image out of allowed dimensions
					return false;
				}
				else if($imagesize > $size)
				{
					//check the file size
					return false;
				}
			}
			return true;
		}

		public static function checkRadioYes($value)
		{
			if(!$value || $value == 0) return false;
			else return true;
		}
		
		public static function checkFormTypeYes($value)
		{
			if(!$value || $value == 0) return false;
			else return true;
		}

		public static function checkSelect($value, $params)
		{
			if(is_array($value) && sizeof($value))
			{
				if(in_array('', $value)) return false;
				if(in_array('0', $value)) return true;
				if(isset($params) && (sizeof($value)<=$params)) return true;
				else if(!isset($params)) return true;
			}
			else
			{
				if($value != '') return true;
			}
				
			return false;
		}
		
		public static function checkUniqueUser($value, $params)
		{
			if(CDBLogin::getCheckUsername($value) == 0) return true;
			else return false;
		}

		public static function checkCaptcha($value, $params)
		{
			if(!isset($_SESSION['captcha']))
			{
				session_start();
			}
			
			if(!empty($value))
			{
			    if(empty($_SESSION['captcha']) || trim(strtolower($value)) != $_SESSION['captcha'])
			    {
			    	unset($_SESSION['captcha']);
			    	return false;
			    }
			    else
			    {
			    	unset($_SESSION['captcha']);
			        return true;
			    }
			}
			else
			{
				unset($_SESSION['captcha']);
				return false;
			}
		}
		
		public static function checkUserStatus($value)
		{
			global $USER_STATUS;
			
			if(array_key_exists($value, $USER_STATUS))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public static function checkHttpMethod($value)
		{
			global $HTTP_METHODS;
			
			if(array_key_exists($value, $HTTP_METHODS))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		public static function checkHttpContentType($value)
		{
			global $HTTP_CONTENT_TYPE;
			
			if(array_key_exists($value, $HTTP_CONTENT_TYPE))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public static function checkHttpAccept($value)
		{
			global $HTTP_ACCEPT;
			
			if(array_key_exists($value, $HTTP_ACCEPT))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public static function checkHttpCharset($value)
		{
			global $HTTP_CHARSET;
			
			if(array_key_exists($value, $HTTP_CHARSET))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public static function checkAccessLimit($value)
		{
			global $COCKPIT_SYSTEM_DEF;
			global $ACCESS_LIMIT_BY_ACCOUNT_TYPE;
			
			$accountType = CDBLogin::getAccountType($COCKPIT_SYSTEM_DEF['userId']);
			
			if($accountType == 0) return false;
			else
			{
				if($ACCESS_LIMIT_BY_ACCOUNT_TYPE[$accountType] < $value) return false;
				else return true;
			}
		}
		
		public static function checkResponseTimeoutLimit($value)
		{
			global $RESPONSE_TIMEOUT_LIMIT;
			
			if(array_key_exists($value, $RESPONSE_TIMEOUT_LIMIT))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public static function checkHttpAuthType($value)
		{
			global $HTTP_AUTH_TYPE;
				
			if(array_key_exists($value, $HTTP_AUTH_TYPE))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public static function checkHttpPlotExists($value)
		{
			include_once 'CDBHttpTestPlot.php';
			
			$isExists = CDBHttpTestPlot::isHttpTestPlotExists($value);

			if($isExists) return false;
			else return true;
		}

		public static function checkHttpPlotAvailable($value)
		{
			include_once 'CDBHttpTestPlot.php';
				
			$isExists = CDBHttpTestPlot::isHttpTestPlotExists($value);
		
			if($isExists) return true;
			else return false;
		}
		
		// check the plotStatus is 0->not testing or 3->test completed
		public static function checkPlotStatusAvailable($value)
		{
			include_once 'CDBHttpTestPlot.php';
				
			$isStatusOkToRun = CDBHttpTestPlot::isHttpTestPlotAvailableByStatus($value);
		
			if($isStatusOkToRun) return true;
			else return false;
		}
		
		public static function checkHttpDLPlotExists($value)
		{
			include_once 'CDBHttpTestPlot.php';
				
			$isExists = CDBHttpTestPlot::isHttpDLTestPlotExists($value);
		
			if($isExists) return false;
			else return true;
		}

		public static function checkHttpDLPlotAvailable($value)
		{
			include_once 'CDBHttpTestPlot.php';
		
			$isExists = CDBHttpTestPlot::isHttpDLTestPlotExists($value);
		
			if($isExists) return true;
			else return false;
		}
		
		public static function checkHttpULPlotExists($value)
		{
			include_once 'CDBHttpTestPlot.php';
		
			$isExists = CDBHttpTestPlot::isHttpULTestPlotExists($value);
		
			if($isExists) return false;
			else return true;
		}
		
		public static function checkHttpULPlotAvailable($value)
		{
			include_once 'CDBHttpTestPlot.php';
		
			$isExists = CDBHttpTestPlot::isHttpULTestPlotExists($value);
		
			if($isExists) return true;
			else return false;
		}
		
		// XXX: IMPORTANT - max. file size can be 10Mb
		public static function checkUploadFileSize($file, $params)
		{
			$fileSize = filesize($file['tmp_name']);
			
			if($fileSize > 10485760) return false;
			else return true;
		}
		
		public static function checkZeroFileSize($file, $params)
		{
			$fileSize = filesize($file['tmp_name']);
				
			if($fileSize == 0) return false;
			else return true;
		}
		
		public static function checkUploadFileType($image, $params)
		{
				
		}
		
		public static function checkPostQueryDataForHttpUpload($value)
		{
			parse_str($value, $output);
			
			if(!is_array($output)) return false;
			
			if(!in_array("File1", $output)) return false;
			
			return true;
		}
		
		public static function checkParseUrl($value)
		{
			$urlParts = parse_url($value);
			
			if(!isset($urlParts['scheme']))	return false;
			if(!isset($urlParts['host']))	return false;
			
			return true;
		}
		
		public static function checkDomainAndIP($value)
		{
			if(filter_var($value, FILTER_VALIDATE_IP))
			{
				return true;
			}
			else
			{
				if(filter_var(gethostbyname($value), FILTER_VALIDATE_IP))
				{
					return true;
				}
			}
				
			return false;
		}
		
		public static function checkTCPControlBits($value)
		{
			global $COCKPIT_TCP_CONTROL_BITS;
			
			$controlBitsArray = explode(',', $value);
			
			if($controlBitsArray == false) return false;
			
			foreach($controlBitsArray as $key=>$value)
			{
				$value = trim($value);
				
				if(!in_array($value, $COCKPIT_TCP_CONTROL_BITS))
				{
					return false;
				}
			}
			
			return true;
		}
		
		public static function checkPort($value)
		{
			if($value >= 1 && $value <= 65535) return true;
			
			return false;
		}
		
		public static function validXML($value, $params=array())
		{
			return CXML::isValid($value);
		}

		public static function validXMLString($value, $params=array())
		{
			return CXML::isValid('<t>' . $value . '</t>');
		}

		/**
		 * Checks if value is in array
		 *
		 * Params values:
		 * 	[0] - array		- array to check
		 * 	[1] - boolean	- empty value resolves to true
		 *
		 * @param mixed $value
		 * @param array $params
		 * @return boolean
		 */
		public static function inArray($value, $params=array())
		{
			if(!isset($params[0]) || !is_array($params[0])) return false;
			if(!$value && $params[1]) return true;
			if(!is_array($value)) $value = array($value);
			foreach( $value as $v )
			{
				if(!in_array($v, $params[0])) return false;
			}
			
			return true;
		}

		public static function arraySmallerThan($value, $params=array())
		{
			if(isset($params[0]) && is_numeric($params[0]) && isset($value) && is_array($value) && count($value) <= $params[0] ) return true;
			return false;
		}

		public static function inBetween($value, $params=array())
		{
			if(!isset($params[0]) || !isset($params[1]) || !is_numeric($value) || !is_numeric($params[0]) || !is_numeric($params[1])) return false;
			return ($value >= $params[0] && $value <= $params[1]);
		}

		/**
		 * Checks if value is string of valid length
		 *
		 * Params values:
		 *  [0] - int		- min size
		 * 	[1] - int		- max size
		 * 	[2] - boolean	- on True use byte count instead of character count
		 *
		 * @param string $value
		 * @param array $params
		 * @return boolean
		 */
		public static function strLengthBetween($value, $params=array())
		{
			if(!is_string($value)) return false;
			$len = (isset($params[2]) && $params[2]) ? strlen($value) : mb_strlen($value,'UTF-8');
			return self::inBetween($len, $params);
		}
	}
