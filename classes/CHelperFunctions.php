<?php

	include_once 'CXML.php';

	class CHelperFunctions
	{
		//Returns a file's mimetype based on its extension
    	public static function mimeType($filename, $default = 'application/octet-stream')
    	{
        	$mime_types = array(
	        	'323' => 'text/h323',
				'acx' => 'application/internet-property-stream',
				'ai' => 'application/postscript',
				'aif' => 'audio/x-aiff',
				'aifc' => 'audio/x-aiff',
				'aiff' => 'audio/x-aiff',
				'asf' => 'video/x-ms-asf',
				'asr' => 'video/x-ms-asf',
				'asx' => 'video/x-ms-asf',
				'au' => 'audio/basic',
				'avi' => 'video/x-msvideo',
				'axs' => 'application/olescript',
				'bas' => 'text/plain',
				'bcpio' => 'application/x-bcpio',
				'bin' => 'application/octet-stream',
				'bmp' => 'image/bmp',
				'c' => 'text/plain',
				'cat' => 'application/vnd.ms-pkiseccat',
				'cdf' => 'application/x-cdf',
				'cer' => 'application/x-x509-ca-cert',
				'class' => 'application/octet-stream',
				'clp' => 'application/x-msclip',
				'cmx' => 'image/x-cmx',
				'cod' => 'image/cis-cod',
				'cpio' => 'application/x-cpio',
				'crd' => 'application/x-mscardfile',
				'crl' => 'application/pkix-crl',
				'crt' => 'application/x-x509-ca-cert',
				'csh' => 'application/x-csh',
				'css' => 'text/css',
				'dcr' => 'application/x-director',
				'der' => 'application/x-x509-ca-cert',
				'dir' => 'application/x-director',
				'dll' => 'application/x-msdownload',
				'dms' => 'application/octet-stream',
				'doc' => 'application/msword',
				'dot' => 'application/msword',
				'dvi' => 'application/x-dvi',
				'dxr' => 'application/x-director',
				'eps' => 'application/postscript',
				'etx' => 'text/x-setext',
				'evy' => 'application/envoy',
				'exe' => 'application/octet-stream',
				'fif' => 'application/fractals',
				'flac' => 'audio/flac',
				'flr' => 'x-world/x-vrml',
				'gif' => 'image/gif',
				'gtar' => 'application/x-gtar',
				'gz' => 'application/x-gzip',
				'h' => 'text/plain',
				'hdf' => 'application/x-hdf',
				'hlp' => 'application/winhlp',
				'hqx' => 'application/mac-binhex40',
				'hta' => 'application/hta',
				'htc' => 'text/x-component',
				'htm' => 'text/html',
				'html' => 'text/html',
				'htt' => 'text/webviewhtml',
				'ico' => 'image/x-icon',
				'ief' => 'image/ief',
				'iii' => 'application/x-iphone',
				'ins' => 'application/x-internet-signup',
				'isp' => 'application/x-internet-signup',
				'jfif' => 'image/pipeg',
				'jpe' => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'jpg' => 'image/jpeg',
				'js' => 'application/x-javascript',
				'latex' => 'application/x-latex',
				'lha' => 'application/octet-stream',
				'lsf' => 'video/x-la-asf',
				'lsx' => 'video/x-la-asf',
				'lzh' => 'application/octet-stream',
				'm13' => 'application/x-msmediaview',
				'm14' => 'application/x-msmediaview',
				'm3u' => 'audio/x-mpegurl',
				'man' => 'application/x-troff-man',
				'mdb' => 'application/x-msaccess',
				'me' => 'application/x-troff-me',
				'mht' => 'message/rfc822',
				'mhtml' => 'message/rfc822',
				'mid' => 'audio/mid',
				'mny' => 'application/x-msmoney',
				'mov' => 'video/quicktime',
				'movie' => 'video/x-sgi-movie',
				'mp2' => 'video/mpeg',
				'mp3' => 'audio/mpeg',
				'mpa' => 'video/mpeg',
				'mpe' => 'video/mpeg',
				'mpeg' => 'video/mpeg',
				'mpg' => 'video/mpeg',
				'mpp' => 'application/vnd.ms-project',
				'mpv2' => 'video/mpeg',
				'ms' => 'application/x-troff-ms',
				'mvb' => 'application/x-msmediaview',
				'nws' => 'message/rfc822',
				'oda' => 'application/oda',
				'oga' => 'audio/ogg',
				'ogg' => 'audio/ogg',
				'ogv' => 'video/ogg',
				'ogx' => 'application/ogg',
				'p10' => 'application/pkcs10',
				'p12' => 'application/x-pkcs12',
				'p7b' => 'application/x-pkcs7-certificates',
				'p7c' => 'application/x-pkcs7-mime',
				'p7m' => 'application/x-pkcs7-mime',
				'p7r' => 'application/x-pkcs7-certreqresp',
				'p7s' => 'application/x-pkcs7-signature',
				'pbm' => 'image/x-portable-bitmap',
				'pdf' => 'application/pdf',
				'pfx' => 'application/x-pkcs12',
				'pgm' => 'image/x-portable-graymap',
				'pko' => 'application/ynd.ms-pkipko',
				'pma' => 'application/x-perfmon',
				'pmc' => 'application/x-perfmon',
				'pml' => 'application/x-perfmon',
				'pmr' => 'application/x-perfmon',
				'pmw' => 'application/x-perfmon',
				'pnm' => 'image/x-portable-anymap',
				'pot' => 'application/vnd.ms-powerpoint',
				'ppm' => 'image/x-portable-pixmap',
				'pps' => 'application/vnd.ms-powerpoint',
				'ppt' => 'application/vnd.ms-powerpoint',
				'prf' => 'application/pics-rules',
				'ps' => 'application/postscript',
				'pub' => 'application/x-mspublisher',
				'qt' => 'video/quicktime',
				'ra' => 'audio/x-pn-realaudio',
				'ram' => 'audio/x-pn-realaudio',
				'ras' => 'image/x-cmu-raster',
				'rgb' => 'image/x-rgb',
				'rmi' => 'audio/mid',
				'roff' => 'application/x-troff',
				'rtf' => 'application/rtf',
				'rtx' => 'text/richtext',
				'scd' => 'application/x-msschedule',
				'sct' => 'text/scriptlet',
				'setpay' => 'application/set-payment-initiation',
				'setreg' => 'application/set-registration-initiation',
				'sh' => 'application/x-sh',
				'shar' => 'application/x-shar',
				'sit' => 'application/x-stuffit',
				'snd' => 'audio/basic',
				'spc' => 'application/x-pkcs7-certificates',
				'spl' => 'application/futuresplash',
				'src' => 'application/x-wais-source',
				'sst' => 'application/vnd.ms-pkicertstore',
				'stl' => 'application/vnd.ms-pkistl',
				'stm' => 'text/html',
				'svg' => "image/svg+xml",
				'sv4cpio' => 'application/x-sv4cpio',
				'sv4crc' => 'application/x-sv4crc',
				't' => 'application/x-troff',
				'tar' => 'application/x-tar',
				'tcl' => 'application/x-tcl',
				'tex' => 'application/x-tex',
				'texi' => 'application/x-texinfo',
				'texinfo' => 'application/x-texinfo',
				'tgz' => 'application/x-compressed',
				'tif' => 'image/tiff',
				'tiff' => 'image/tiff',
				'tr' => 'application/x-troff',
				'trm' => 'application/x-msterminal',
				'tsv' => 'text/tab-separated-values',
				'txt' => 'text/plain',
				'uls' => 'text/iuls',
				'ustar' => 'application/x-ustar',
				'vcf' => 'text/x-vcard',
				'vrml' => 'x-world/x-vrml',
				'wav' => 'audio/x-wav',
				'wcm' => 'application/vnd.ms-works',
				'wdb' => 'application/vnd.ms-works',
				'wks' => 'application/vnd.ms-works',
				'wmf' => 'application/x-msmetafile',
				'wps' => 'application/vnd.ms-works',
				'wri' => 'application/x-mswrite',
				'wrl' => 'x-world/x-vrml',
				'wrz' => 'x-world/x-vrml',
				'xaf' => 'x-world/x-vrml',
				'xbm' => 'image/x-xbitmap',
				'xla' => 'application/vnd.ms-excel',
				'xlc' => 'application/vnd.ms-excel',
				'xlm' => 'application/vnd.ms-excel',
				'xls' => 'application/vnd.ms-excel',
				'xlt' => 'application/vnd.ms-excel',
				'xlw' => 'application/vnd.ms-excel',
				'xof' => 'x-world/x-vrml',
				'xpm' => 'image/x-xpixmap',
				'xwd' => 'image/x-xwindowdump',
				'z' => 'application/x-compress',
				'zip' => 'application/zip'
	        );
	        $ext = pathinfo($filename, PATHINFO_EXTENSION);
	        return isset($mime_types[$ext]) ? $mime_types[$ext] : $default;
    	}
		/**
		 * Clean HTML Content
		 *
		 * @param string $content
		 * @return string
		 */
		public static function cleanHTMLContent($content){
			//load HTMLPurifier
			require_once('HTMLPurifier.auto.php');
			$config = HTMLPurifier_Config::createDefault();
			$config->set('HTML', 'ForbiddenElements', array('img', 'object', 'iframe', 'style', 'script'));
			$config->set('HTML', 'ForbiddenAttributes', array('style', 'xml:lang', 'lang'));
			$config->set('HTML', 'TidyLevel', 'heavy');
			$purifier = new HTMLPurifier($config);
			return $purifier->purify($content);
		}

		/**
		 * Returns a slice (section) of an associative array
		 *
		 * @param any type with Associate keys $array
		 * @param int $start
		 * @param int $end
		 * @return array
		 */
		public static function associativeArraySlice($array, $start, $end) {
			if(!is_array($array)) return array();
		    if($start < 0) $start = 0;
		    if($end > count($array)) $end = count($array);
		    $new = array();
		    $i = 0;
		    foreach($array as $key => $value){
		        if($i >= $start && $i < $end){
		            $new[$key] = $value;
		        }
		        $i++;
		    }
		    return $new;
		}

		/**
		 * Convert BR tags to nl
		 *
		 * @param string $string 	The string to convert
		 * @param boolean $literal	If true returns literal '\n' characters, i.e. required for JavaScript alerts...
		 * @return string
		 */
		public static function br2nl($string, $literal=false) {
			if($literal) {
				return preg_replace('/\<br(\s*)?\/?\>/i', '\n', $string);
			}
			else {
				return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
			}
		}

		/**
		 * Build a HTTP GET string.
		 *
		 * @param array $params $key=>$value pairs
		 * @return string
		 */
		public static function buildGetString($params) {
			$escaped_pairs = array();
			foreach($params as $k=>$v) {
				if(is_array($v)) {
					$std_array = true;
					foreach(array_keys($v) as $array_key) {
						if(!is_int($array_key)) {
							$std_array = false;
							break;
						}
					}

					if($std_array)
						foreach($v as $v2)
							$escaped_pairs[] = sprintf('%s[]=%s', urlencode($k), urlencode($v2));
					else
						foreach($v as $k2=>$v2)
							$escaped_pairs[] = sprintf('%s[%d]=%s', urlencode($k), urlencode($k2), urlencode($v2));
				}
				else
					$escaped_pairs[] = sprintf('%s=%s', urlencode($k), urlencode($v));
			}
			return implode('&', $escaped_pairs);
		}

		/**
		 * Build a SQL where string segment from a keyword query string
		 *
		 * @param string $keyword_str query string
		 * @param string $column db column to do keyword search on
		 * @return string SQL where segment
		 */
		public static function buildKeywordWhere($keyword_str, $column) {
			//init vars
			$where = '';
			$op = '&&';
			$like = 'LIKE';
			$keywords = array();

			//split query into keywords, with quoted(") strings counting as one keyword.
			//first split by quotes
			$quoted = explode('"', $keyword_str);

			//process
			foreach($quoted as $k=>$v) {
				//even elements and the last element are to be space seprated
				if(!fmod($k, 2) || count($quoted) == $k + 1) {
					$v = str_replace('　', ' ', $v);
					$to_add = explode(' ', $v);
					//$to_add = array_unique($to_add);
				}
				//odd elements are with quotes, so they are not
				else $to_add = array($v);

				//clean up empty strings before returning
				foreach($to_add as $k=>$v) {
					// clean keywords (recruiters hunting for direct contact details)
					$v = str_replace("090", '', $v);
					$v = str_replace("080", '', $v);
					$v = str_replace("@", '', $v);
		    		$v = str_replace("hotmail", '', $v);
		    		$v = str_replace("gmail", '', $v);
		    		$v = str_replace("yahoo", '', $v);
					$v = str_replace(".com", '', $v);
					$v = str_replace(".co", '', $v);
					$v = str_replace("email", '', $v);
					$v = str_replace(".jp", '', $v);

					$v = str_replace("\\", '', $v);
					$v = str_replace('\+', '+', $v);
					$v = str_replace('\*', '*', $v);
					$v = str_replace('\?', '?', $v);
					$v = str_replace('\-', '-', $v);
					$v = str_replace('\(', '(', $v);
					$v = str_replace('\)', ')', $v);
					$v = str_replace('\{', '{', $v);
					$v = str_replace('\}', '}', $v);

					// TODO: Search for acronyms (Need to consider how to do this best...)
					if(strtolower($v) == 'hr') $v = $v . ' ';
					if(strtolower($v) == 'it') $v = $v . ' ';
					if(strtolower($v) == 'sap') $v = $v . ' ';

					if($v === '') unset($to_add[$k]);
					else $to_add[$k] = $v;
				}
				//merge
				$keywords = array_merge($keywords, $to_add);
			}
			//go through each keyword and generate sql from keywords.
			foreach($keywords as $k => $keyword) {
				$keyword = addcslashes(strtolower(CCHelperFunctions::stripPunctuation($keyword)), '\'\\%');
				switch($keyword){
					case 'and':
						$op = '&&';
						$like = 'LIKE';
						break;
					case 'or':
						$op = '||';
						$like = 'LIKE';
						break;
					case 'not':
						$like = 'NOT LIKE';
						break;
					default:
						if($where) $where .= " $op $column $like '%$keyword%'";
						else $where .= "$column $like '%$keyword%'";
						$op = '&&';
				}
			}
			if($where) $where = '(' . $where .')';
			return $where;
		}

		/**
		 * Creates a GET variable link used for content table results
		 *
		 * @param string $web_page
		 * @param string $value
		 * @param string $type
		 * @param array $variables
		 * @param string $field
		 * @param string $order_by
		 * @param int $order
		 * @param string $default
		 * @return string
		 */
		public static function buildLinkOrder($web_page, $value, $type, $variables, $field, $order_by, $order, $default) {

			unset($variables['order_by']);
			unset($variables['page']);
			$variables['order_by'] = $field;
			unset($variables['order']);
			$variables['order'] = $order;

			$query_str = '?';
			while (list($k,$v) = each($variables)) {
				$query_str .= $k . '=' . $v . '&';
			}

			if($type == 'image'){
				return CCXML::startTag('div',
					array('class' => implode(' ', array(
						'left',
						CCHelperFunctions::showOrder($field, $order_by, $order, $default)
					)))
				)->tag('a', array('href'=>"$web_page$query_str"))->tag('img', array('alt'=>'', 'src'=>$value))->finalise()->toString();
			}
			else return CCXML::startTag('a', array('href'=>"$web_page$query_str", 'title'=>$value, 'class'=>CCHelperFunctions::showOrder($field, $order_by, $order, $default)))->text($value)->finalise()->toString();
		}

		/**
		 * Build an array from which a SQL statement where can then be build
		 *
		 * @param array $values
		 * @param string $column_name
		 * @param string $type
		 * @return array
		 */
		public static function buildSQLParts($values, $column_name, $type='none')
		{
			global $db;
			if(!is_array($values) || !count($values)) return array();
			if($values[0] == '' || $values[0] == 0) array_shift($values);
			$parts = array();
			while(list(,$v) = each($values))
			{
				switch($type)
				{
					case 'int':
						$v = intval($v);
						break;
					case 'string':
						$v = "$v";
						break;
					case 'float':
						$v = floatval($v);
						break;
					default:
						break;
				}

				$parts[] = $db->quoteInto("$column_name=%s", array($v));
			}
			
			return $parts;
		}

		/**
		 * Generate a psudo time-based hash
		 *
		 * @param string $string
		 * @param string $key
		 * @return string
		 */
		public static function createPseudoSecretHash($string, $key)
		{
			 return md5($string . date('m ym Y d H') . $key);
		}

		/**
		 * Determine if supplied string is a valid GUID
		 *
		 * @param string $md5 String to validate
		 * @return boolean
		 * */
		public static function isValidMd5($md5)
		{
			return !empty($md5) && preg_match('/^[a-f0-9]{32}$/', $md5);
		}

		/**
		 * Test if given variable can be considered empty
		 *
		 * A value is considered empty is it strictly equals '' or is an array empty values.
		 * Note: 0 and '0' are not considered empty values.
		 *
		 * @param mixed $test
		 * @return boolean True if empty, false otherwise.
		 */
		public static function isEmpty($test)
		{
			if(is_array($test))
			{
				reset($test);
				while(list($k,$v) = each($test))
				{
					if(!self::isEmpty($v)) return false;
				}
				
				return true;
			}
			return ($test === '') ? true : false;
		}

		public static function passwordGenerate($nice = 1, $length=0, $allowchars = "")
		{
			//Find random password length
			if(!$length) $length = mt_rand(5, 9);

			//pronouncable password
			if($nice == 1) return CCHelperFunctions::passwordGeneratePronouncable($length);
				//lowercase only, fix similar
			else if($nice == 2) return CCHelperFunctions::passwordGenerateAdvanced($length, 0, 1, 0, 0, 1, $allowchars);
			//lowercase and numbers only, fix similar
			else if($nice == 3) return CCHelperFunctions::passwordGenerateAdvanced($length, 0, 1, 1, 0, 1, $allowchars);
			//both lower and uppercase chars and numbers , fix similar
			else if($nice == 4) return CCHelperFunctions::passwordGenerateAdvanced($length, 1, 1, 1, 0, 1, $allowchars);
			//all types of letters, including special chars, fix similar
			else if($nice == 5) return CCHelperFunctions::passwordGenerateAdvanced($length, 1, 1, 1, 1, 1, $allowchars);
			//oh my :) the real deal - get it all and dont fix similars
			else if($nice == 6) return CCHelperFunctions::passwordGenerateAdvanced($length, 1, 1, 1, 1, 0, $allowchars);

			//$nice contained illegal value, go for the easy 3
			else return CCHelperFunctions::passwordGenerateAdvanced($length, 1, 1, 1, 0, 1);
		}

		public static function passwordGenerateAdvanced($length = 8, $allow_uppercase = 1, $allow_lowercase = 1, $allow_numbers = 1, $allow_special = 1, $fix_similar = 0, $valid_charset = "") {
			//Create a list of usable chars based upon the parameters
			if(!$valid_charset) {
				if($allow_uppercase) $valid_charset .= 'ABCDEFGHIJKLMNOPQRSTUVXYZ';
				if($allow_lowercase) $valid_charset .= 'abcdefghijklmnopqrstuvxyz';
				if($allow_numbers) $valid_charset .= '0123456789';
				// if lowercase and uppercase, the chance for a number is less, and thus doubled in the char array
				if($allow_numbers && $allow_lowercase && $allow_uppercase) $valid_charset .= '0123456789';
				if($allow_special) $valid_charset .= '!//$%&()*+-./;<=>@\_';
			}

			//Find the charset length
			$charset_length = strlen($valid_charset);

			//If no chars is allowed, return false
			if($charset_length == 0) return false;

			//Initialize the password and loop till we have all
			$password = "";
			while(strlen($password) < $length)
			{
				//Pull out a random char
				$char = $valid_charset[mt_rand(0, ($charset_length-1))];

				//If similar is true, check if string contains mistakeable chars, add if accepted
				if(($fix_similar && !strpos('O01lI5S', $char)) || !$fix_similar) $password .= $char;
			}

			//Return password
			return $password;
		}

		public static function passwordGeneratePronouncable($length = 8)
		{
			//Initialize valid char lists
			$valid_consonant = 'bcddffghjkmnnprrssttv';
			$valid_vowel = 'aaeeiioouy';
			$valid_numbers = '0123456789';

			//Find the charset length
			$consonant_length = strlen($valid_consonant);
			$vowel_length = strlen($valid_vowel);
			$numbers_length = strlen($valid_numbers);

			//Initialize the password and loop till we have all
			$password = "";
			while(strlen($password) < $length) {
				//Pull out a random set of pronouncable chars
				if(mt_rand(0, 3) != 1) $password .= $valid_consonant[mt_rand(0, ($consonant_length-1))].$valid_vowel[mt_rand(0, ($vowel_length-1))].$valid_consonant[mt_rand(0, ($consonant_length-1))];
				else $password .= $valid_numbers[mt_rand(0, ($numbers_length-1))];
			}

			return substr($password, 0, $length);
		}

		public static function populateCurrentSelect($values, $marker) {
			reset($values);
			$out = '';
			while(list($k,$v) = each($values)) {
				if($k >= $marker && $k < ($marker+99)) $out .= CCXML::startTag('option', array('value'=>$k))->text($v);
			}

			return $out;
		}

		public static function populatePreparedSelectOptGroup($prepared, $selected) {
			return str_replace('value="'.$selected.'"', 'value="'.$selected.'" selected="selected"', $prepared);
		}

		public static function populateSelect($values, $selected){
			reset($values);
			if(!is_array($selected)) $selected = array($selected);
			//clean selected array to prevent incorrect selects
			foreach($selected as $k=>$v) { if($v === '') unset($selected[$k]); };
			$out = '';
			while(list($k,$v) = each($values)){
				$attr = array('value'=>$k, 'title'=>$v);
				if($k === 0 || $k === '0') { if(in_array(0, $selected, true) || in_array('0', $selected, true)) $attr['selected'] = 'selected'; }
				elseif($k === '') { if(in_array($k, $selected, true)) $attr['selected'] = 'selected'; }
				elseif(in_array($k, $selected)) $attr['selected'] = 'selected';

				$xml = new CXML('option', $attr);
				$out .= $xml->text($v)->finalise()->toString();
			}
			return $out;
		}

		public static function populateSelectOptGroup($values, $selected, $showall=1) {
			if(is_array($selected)) {
				for ($count = 0; $count < sizeof($selected); $count++) {
					if((substr($selected[$count],2,2) == '00')) {
						$keys = array_keys($values);
						$sizeof_keys = sizeof($keys);
			  			for($ii=0; $ii<$sizeof_keys; $ii++) {
				  			if(substr($selected[$count],0,2) == substr($keys[$ii],0,2)) {
				  				$optgroup_all[] = $keys[$ii];
				  			}
			  			}
					}
				}
			}
			if($optgroup_all) {
				unset($selected);
				$selected = $optgroup_all;
			}
			reset($values);
			$out = '';
			$j = 1;
			$rk = $rv = 0;
			if($COCKPIT_SYSTEM_DEF['lang'] == 'ja') $all = '全て';
			else $all = 'All';

			$keys = array_keys($values);
			$sizeof_keys = sizeof($keys);
  			for($i=0; $i<$sizeof_keys; $i++) {
				$k = $keys[$i];
				$v = $values[$keys[$i]];

				$option_attr = array('value'=>"{$k}", 'title'=>"{$v}");
				// set selected
  			    if(is_array($selected)) {
					for ($l = 0; $l < sizeof($selected); $l++) {
						if (($k !== '' && $k == $selected[$l]) || $k === $selected[$l]) $option_attr['selected'] = 'selected';
					}
  			    }
  			    else {
				    if(($k !== '' && $k == $selected) || $k === $selected) $option_attr['selected'] = 'selected';
  			    }
				if((substr($k,2,2) == "00" && strlen($k) == 4) || $k == 0) {
					// if not first line, close optgroup
					if($j != 1) $out .= $xml->finalise()->toString();

					// main category, open optgroup
					$xml = new CCXML('optgroup', array('label'=>"{$v}", 'title'=>"{$v}"));

					if($showall) {
						$xml->tag('option', $option_attr)->text("{$v} ($all)")->close();
					}
					else if(count($keys) > $i + 1 && substr($keys[($i+1)],2,2) == "00" && (strlen(($keys[$i+1])) == 4) || !isset($keys[$i+1])) {
						$xml->tag('option', $option_attr)->text("{$v}")->close();
					}
				}
				else {
					$xml->tag('option', $option_attr)->text("{$v}")->close();
				}
				// increment i, so we know if this is the first line or not
				$j++;
			}

			// ensure proper closure of optgroup and show the last group (all)
			$out .= $xml->finalise()->toString();
			return $out;
		}

		public static function populateSelectValueOnly($values, $selected) {
			reset($values);
			if(!is_array($selected)) $selected = array($selected);

			$out = '';
			while(list(,$v) = each($values)) {
				$attr = array('value'=>$v);
				for($i = 0; $i < sizeof($selected); $i++) {
					if(strval($v) === strval($selected[$i])) {
						$attr['selected'] = 'selected';
					}
				}
				$xml = new CCXML('option', $attr);
				$out .= $xml->text($v)->finalise()->toString();
			}
			return $out;
		}

		public static function prepareSelectOptGroup($values) {
			reset($values);
			$out = '';
			$i = 1;
			$rk = $rv = 0;
			if($COCKPIT_SYSTEM_DEF['lang'] == 'ja') $all = '全て';
			else $all = 'All';

			while(list($k,$v) = each($values)) {
				$opt_attr = array('value'=>"{$k}", 'title'=>"$title");
				if((substr($k,2,2) == "00" && strlen($k) == 4) || $k == 0) {
					if($i != 1) {
						// if not first line, add (all) option and close optgroup
						$out .= $xml->finalise()->toString();
					}

					// main category, open optgroup
					$xml = new CCXML('optgroup', array('label'=>"{$v}",'title'=>"{$v}"));
					$xml->tag('option', $opt_attr)->text("{$v} ($all)")->close();
				}
				else {
					// this is a normal option, display the proper code
					$xml->tag('option', $opt_attr)->text("{$v}")->close();
				}

				// increment i, so we know if this is the first line or not
				$i++;
			}

			// ensure proper closure of optgroup and show the last group (all)
			$out .= $xml->finalise()->toString();

			return $out;
		}

		/**
		 * Strip quote and control character from serialized array.
		 *
		 * @param 	string serialized array string
		 * @return	string
 		 */
		public static function replaceQuotGt($string) {
			$string = str_replace("&gt;", ">", $string);
			$string = str_replace("&lt;", "<", $string);
			$string = str_replace("&quot;", '"', $string);
			$string = str_replace("&rsquo;", "'", $string);
			/* When working with serialized objects that have been utf-8 encoded.
			"\0" became 2 characters, instead of the zero ascii character. */
			$string = preg_replace('!s:(\d+):"(.*?)";!se', '"s:".strlen("$2").":\"$2\";"', $string);
			return $string;
		}

		/**
		 * Randomly shuffles an array, preserving the key=>value association.
		 *
		 * @param array $array
		 */
		public static function shuffleWithKeys($array){
			$aux = array();
			$keys = array_keys($array);
			shuffle($keys);
			foreach($keys as $key){
				$aux[$key] = $array[$key];
				// Remove element from the old array to save memory?
				//unset($array[$key]);
			}
			return $aux;
		}

	 	/**
		 * Strip punctuation from text.
		 *
		 * @param 	text	utf8 string
		 * @return	string	minus all punctuation or other characters.
 		 */
		public static function stripPunctuation($text){
			$urlbrackets    = '\[\]\(\)';
			$urlspacebefore = ':;\'_\*%@&?!' . $urlbrackets;
			$urlspaceafter  = '\.,:;\'\-_\*@&\/\\\\\?!#' . $urlbrackets;
			$urlall         = '\.,:;\'\-_\*%@&\/\\\\\?!#' . $urlbrackets;

			$specialquotes  = '\'"\*<>';

			$fullstop       = '\x{002E}\x{FE52}\x{FF0E}';
			$comma          = '\x{002C}\x{FE50}\x{FF0C}';
			$arabsep        = '\x{066B}\x{066C}';
			$numseparators  = $fullstop . $comma . $arabsep;

			$numbersign     = '\x{0023}\x{FE5F}\x{FF03}';
			$percent        = '\x{066A}\x{0025}\x{066A}\x{FE6A}\x{FF05}\x{2030}\x{2031}';
			$prime          = '\x{2032}\x{2033}\x{2034}\x{2057}';
			$nummodifiers   = $numbersign . $percent . $prime;

			return preg_replace(
				array(
				// Remove separator, control, formatting, surrogate,
				// open/close quotes.
					'/[\p{Z}\p{Cc}\p{Cf}\p{Cs}\p{Pi}\p{Pf}]/u',
				// Remove other punctuation except special cases
					'/\p{Po}(?<![' . $specialquotes .
						$numseparators . $urlall . $nummodifiers . '])/u',
				// Remove non-URL open/close brackets, except URL brackets.
					'/[\p{Ps}\p{Pe}](?<![' . $urlbrackets . '])/u',
				// Remove special quotes, dashes, connectors, number
				// separators, and URL characters followed by a space
					'/[' . $specialquotes . $numseparators . $urlspaceafter .
						'\p{Pd}\p{Pc}]+((?=)|$)/u',
				// Remove special quotes, connectors, and URL characters
				// preceded by a space
					'/((?<=)|^)[' . $specialquotes . $urlspacebefore . '\p{Pc}]+/u',
				// Remove dashes preceded by a space, but not followed by a number
					'/((?<=)|^)\p{Pd}+(?![\p{N}\p{Sc}])/u',
				// Remove consecutive spaces
					'/ +/',
				),
				' ',
				$text);
		}

		public static function stripRomaji($str){
			mb_internal_encoding("UTF-8");
			$block = range('a', 'z');
			$block[] = ',';
			$out = '';
			for ($i=0; $i<mb_strlen($str); $i++) if(!in_array(strtolower(mb_substr($str, $i, 1)), $block)) $out .= mb_substr($str, $i, 1);
			return str_replace('--', '', str_replace('- ', '', str_replace(' -', '', $out)));
		}

		/**
		 * Convert SQL Datetime string into a timestamp.
		 *
		 * @param string $datetime_str 'yy-mm-dd hh:mm:ss' formated string
		 * @return int
		 */
		public static function timestampFromDateTime($datetime_str) {
			$date = sscanf($datetime_str, '%02d-%02d-%02d %02d:%02d:%02d');
			return mktime($date[3], $date[4], $date[5], $date[1], $date[2], $date[0]);
		}

		public static function xmlEscape($input, $except=null) {
			// Clean up dirty data, to be removed
			$trans = array(
				'&rsquo;'=>"'",
				'&quot;'=>"'",
				'&amp;'=>"&",
				'&middot;'=>'-',
				'&lt;'=>'<',
				'&gt;'=>'>',
				'&iquest;'=>'',
				'&chi;'=>'-',
				'&divide;'=>'',
				'&THORN;'=>'P',
				'&thorn;'=>'p',
				'&Agrave;'=>'A',
				'&Aacute;'=>'A',
				'&Aring;'=>'A',
				'&Atilde;'=>'A',
				'&Auml;'=>'A',
				'&Ccedil;'=>'C',
				'&Egrave;'=>'E',
				'&Eacute;'=>'E',
				'&Ecirc;'=>'E',
				'&Euml;'=>'E',
				'&Igrave;'=>'I',
				'&Iacute;'=>'I',
				'&Icirc;'=>'I',
				'&Iuml;'=>'I',
				'&ETH;'=>'D',
				'&Ntilde;'=>'N',
				'&Ograve;'=>'O',
				'&Oacute;'=>'O',
				'&Ocirc;'=>'O',
				'&Otilde;'=>'O',
				'&Ouml;'=>'O',
				'&Ugrave;'=>'U',
				'&Uacute;'=>'U',
				'&Ucirc;'=>'U',
				'&Uuml;'=>'U',
				'&Yacute;'=>'Y',
				'&agrave;'=>'a',
				'&aacute;'=>'a',
				'&acirc;'=>'a',
				'&atilde;'=>'a',
				'&auml;'=>'a',
				'&ccedil;'=>'c',
				'&egrave;'=>'e',
				'&eacute;'=>'e',
				'&ecirc;'=>'e',
				'&euml;'=>'e',
				'&igrave;'=>'i',
				'&iacute;'=>'i',
				'&icirc;'=>'i',
				'&ium'=>'i',
				'&ntilde;'=>'n',
				'&eth;'=>'o',
				'&ograve;'=>'o',
				'&oacute;'=>'o',
				'&ocirc;'=>'o',
				'&otilde;'=>'o',
				'&ouml;'=>'o',
				'&ugrave;'=>'u',
				'&uacute;'=>'u',
				'&ucirc;'=>'u',
				'&uuml;'=>'u',
				'&yacute;'=>'y',
				'&yuml;'=>'y',
				'&#11;'=>'-',
				'&#12;'=>'-',
			);

			if(is_array($input)){
				$res = array();
				foreach($input as $k => $v){
					if(!$except || ($except && !in_array($k, $except))) $res[htmlspecialchars($k)] = CCHelperFunctions::xmlEscape($v);
					else $res[$k] = $v;
				}
			}
			else {
				// clean up content
				$input = strtr($input, $trans);
				$res = htmlspecialchars($input);
			}
			return $res;
		}

		public static function breadcrumb($breadcrumb_data){
			if(is_array($breadcrumb_data)){
				echo '<ul id="crumbs">';
				$i = 1;
				$size = count($breadcrumb_data);
				while(list($k, $v) = each($breadcrumb_data)){
					if($size == $i){
						echo '<li>' . $v . '</li>';
					}else{
						echo '<li><a href="' . htmlspecialchars($k) . '">' . $v . '</a></li>';
					}
					$i++;
				}
				echo '</ul>';
			}
		}
		/**
		 * This method can be thought of as a hybrid between PHP's `array_merge()`
		 * and `array_merge_recursive()`.  The difference to the two is that if an
		 * array key contains another array then the function behaves recursive
		 * (unlike `array_merge()`) but does not do if for keys containing strings
		 * (unlike `array_merge_recursive()`).  Please note: This function will work
		 * with an unlimited amount of arguments and typecasts non-array parameters
		 * into arrays.
		 *
		 * @param array $array1 The base array.
		 * @param array $array2 The array to be merged on top of the base array.
		 * @return array Merged array of all passed params.
		 */
		public static function merge(array $array1, array $array2)
		{
			$args = array($array1, $array2);
			if(!$array1 || !$array2)
			{
				return $array1 ? $array1 : $array2;
			}
			
			$result = (array) current($args);
			while(($arg = next($args)) !== false)
			{
				foreach((array) $arg as $key => $val)
				{
					if(is_array($val) && isset($result[$key]) && is_array($result[$key]))
					{
						$result[$key] = self::merge($result[$key], $val);
					}
					elseif (is_int($key))
					{
						$result[] = $val;
					}
					else
					{
						$result[$key] = $val;
					}
				}
			}
			
			return $result;
		}

		/**
		 * method masks an email address
		 *
		 * @param string $email the email address to mask
		 * @param string $mask_char the character to use to mask with
		 * @param int $percent the percent of the username to mask
		 */
		public static function maskEmail($email, $mask_char = '*', $percent = 50)
		{
			list($user, $domain) = preg_split("/@/", $email);
			$len = strlen($user);
			$mask_count = floor($len * $percent /100);
			$offset = floor(($len - $mask_count ) / 2);
			$masked = substr($user, 0, $offset)
						. str_repeat($mask_char, $mask_count)
	                	.substr($user, $mask_count+$offset);
			return($masked . '@' . $domain);
		}
		
		public static function getHostOwnershipFileContent($host, $fileName)
		{
			$content = file_get_contents($host . '/' . $fileName);
			return $content;
		}
		
		public static function fixJSON($j)
		{
			$j = trim($j);
			$j = ltrim($j, '(');
			$j = rtrim($j, ')');
			$a = preg_split('#(?<!\\\\)\"#', $j);

			for($i=0; $i < count($a); $i+=2)
			{
				$s		= $a[$i];
				$s		= preg_replace('#([^\s\[\]\{\}\:\,]+):#', '"\1":', $s);
				$a[$i]	= $s;
			}
			
			$j = implode( '"', $a );
			
			return $j;
		}
	}