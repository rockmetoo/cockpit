<?php

	include_once 'CMail.php';
	include_once 'CFormRules.php';
	include_once 'CXML.php';
	include_once 'CLocalization.php';
	
	/**
	 * Reads forms rules, cleans up form mark up and optionaly validates form.
	 *
	 * @property-read array $elements (read-only)The form rules as specified by the mark-up.
	 */
	class CFormValidator
	{
		private $_email				= '';
		private $lang				= null;
		private $_email_subject 	= 'Form XML Parsing Error';

		private $_form_ml			= null;
		private $_elements			= array();
		private $_errors			= array();
		private $_info				= array();
		private $_output			= null;

		public $error_class			= 'invalid';
		public $error_no			= null;
		public $error_msg			= null;
		public $returnData			= null;

		// Properties used during XML parsing for the form() method
		private $_parser			= null;
		
		// Flag: should formML contents be copied to output?
		private $_copy				= true;
		
		private $_printed_errors	= array();
		private $_in_text_area		= false;
		private $_parent_select		= '';
		
		private $_entities			= array(
			'nbsp'=>'&#160;',
			'iexcl'=>'&#161;',
			'cent'=>'&#162;',
			'pound'=>'&#163;',
			'curren'=>'&#164;',
			'yen'=>'&#165;',
			'brvbar'=>'&#166;',
			'sect'=>'&#167;',
			'uml'=>'&#168;',
			'copy'=>'&#169;',
			'ordf'=>'&#170;',
			'laquo'=>'&#171;',
			'not'=>'&#172;',
			'shy'=>'&#173;',
			'reg'=>'&#174;',
			'macr'=>'&#175;',
			'deg'=>'&#176;',
			'plusmn'=>'&#177;',
			'sup2'=>'&#178;',
			'sup3'=>'&#179;',
			'acute'=>'&#180;',
			'micro'=>'&#181;',
			'para'=>'&#182;',
			'middot'=>'&#183;',
			'cedil'=>'&#184;',
			'sup1'=>'&#185;',
			'ordm'=>'&#186;',
			'raquo'=>'&#187;',
			'frac14'=>'&#188;',
			'frac12'=>'&#189;',
			'frac34'=>'&#190;',
			'iquest'=>'&#191;',
			'Agrave'=>'&#192;',
			'Aacute'=>'&#193;',
			'Acirc'=>'&#194;',
			'Atilde'=>'&#195;',
			'Auml'=>'&#196;',
			'Aring'=>'&#197;',
			'AElig'=>'&#198;',
			'Ccedil'=>'&#199;',
			'Egrave'=>'&#200;',
			'Eacute'=>'&#201;',
			'Ecirc'=>'&#202;',
			'Euml'=>'&#203;',
			'Igrave'=>'&#204;',
			'Iacute'=>'&#205;',
			'Icirc'=>'&#206;',
			'Iuml'=>'&#207;',
			'ETH'=>'&#208;',
			'Ntilde'=>'&#209;',
			'Ograve'=>'&#210;',
			'Oacute'=>'&#211;',
			'Ocirc'=>'&#212;',
			'Otilde'=>'&#213;',
			'Ouml'=>'&#214;',
			'times'=>'&#215;',
			'Oslash'=>'&#216;',
			'Ugrave'=>'&#217;',
			'Uacute'=>'&#218;',
			'Ucirc'=>'&#219;',
			'Uuml'=>'&#220;',
			'Yacute'=>'&#221;',
			'THORN'=>'&#222;',
			'szlig'=>'&#223;',
			'agrave'=>'&#224;',
			'aacute'=>'&#225;',
			'acirc'=>'&#226;',
			'atilde'=>'&#227;',
			'auml'=>'&#228;',
			'aring'=>'&#229;',
			'aelig'=>'&#230;',
			'ccedil'=>'&#231;',
			'egrave'=>'&#232;',
			'eacute'=>'&#233;',
			'ecirc'=>'&#234;',
			'euml'=>'&#235;',
			'igrave'=>'&#236;',
			'iacute'=>'&#237;',
			'icirc'=>'&#238;',
			'iuml'=>'&#239;',
			'eth'=>'&#240;',
			'ntilde'=>'&#241;',
			'ograve'=>'&#242;',
			'oacute'=>'&#243;',
			'ocirc'=>'&#244;',
			'otilde'=>'&#245;',
			'ouml'=>'&#246;',
			'divide'=>'&#247;',
			'oslash'=>'&#248;',
			'ugrave'=>'&#249;',
			'uacute'=>'&#250;',
			'ucirc'=>'&#251;',
			'uuml'=>'&#252;',
			'yacute'=>'&#253;',
			'thorn'=>'&#254;',
			'yuml'=>'&#255;',
			'quot'=>'&#34;',
			'amp'=>'&#38;',
			'lt'=>'&#60;',
			'gt'=>'&#62;',
			'OElig'=>'&#338;',
			'oelig'=>'&#339;',
			'Scaron'=>'&#352;',
			'scaron'=>'&#353;',
			'Yuml'=>'&#376;',
			'circ'=>'&#710;',
			'tilde'=>'&#732;',
			'ensp'=>'&#8194;',
			'emsp'=>'&#8195;',
			'thinsp'=>'&#8201;',
			'zwnj'=>'&#8204;',
			'zwj'=>'&#8205;',
			'lrm'=>'&#8206;',
			'rlm'=>'&#8207;',
			'ndash'=>'&#8211;',
			'mdash'=>'&#8212;',
			'lsquo'=>'&#8216;',
			'rsquo'=>'&#8217;',
			'sbquo'=>'&#8218;',
			'ldquo'=>'&#8220;',
			'rdquo'=>'&#8221;',
			'bdquo'=>'&#8222;',
			'dagger'=>'&#8224;',
			'Dagger'=>'&#8225;',
			'permil'=>'&#8240;',
			'lsaquo'=>'&#8249;',
			'rsaquo'=>'&#8250;',
			'euro'=>'&#8364;',
			'fnof'=>'&#402;',
			'Alpha'=>'&#913;',
			'Beta'=>'&#914;',
			'Gamma'=>'&#915;',
			'Delta'=>'&#916;',
			'Epsilon'=>'&#917;',
			'Zeta'=>'&#918;',
			'Eta'=>'&#919;',
			'Theta'=>'&#920;',
			'Iota'=>'&#921;',
			'Kappa'=>'&#922;',
			'Lambda'=>'&#923;',
			'Mu'=>'&#924;',
			'Nu'=>'&#925;',
			'Xi'=>'&#926;',
			'Omicron'=>'&#927;',
			'Pi'=>'&#928;',
			'Rho'=>'&#929;',
			'Sigma'=>'&#931;',
			'Tau'=>'&#932;',
			'Upsilon'=>'&#933;',
			'Phi'=>'&#934;',
			'Chi'=>'&#935;',
			'Psi'=>'&#936;',
			'Omega'=>'&#937;',
			'alpha'=>'&#945;',
			'beta'=>'&#946;',
			'gamma'=>'&#947;',
			'delta'=>'&#948;',
			'epsilon'=>'&#949;',
			'zeta'=>'&#950;',
			'eta'=>'&#951;',
			'theta'=>'&#952;',
			'iota'=>'&#953;',
			'kappa'=>'&#954;',
			'lambda'=>'&#955;',
			'mu'=>'&#956;',
			'nu'=>'&#957;',
			'xi'=>'&#958;',
			'omicron'=>'&#959;',
			'pi'=>'&#960;',
			'rho'=>'&#961;',
			'sigmaf'=>'&#962;',
			'sigma'=>'&#963;',
			'tau'=>'&#964;',
			'upsilon'=>'&#965;',
			'phi'=>'&#966;',
			'chi'=>'&#967;',
			'psi'=>'&#968;',
			'omega'=>'&#969;',
			'thetasym'=>'&#977;',
			'upsih'=>'&#978;',
			'piv'=>'&#982;',
			'bull'=>'&#8226;',
			'hellip'=>'&#8230;',
			'prime'=>'&#8242;',
			'Prime'=>'&#8243;',
			'oline'=>'&#8254;',
			'frasl'=>'&#8260;',
			'weierp'=>'&#8472;',
			'image'=>'&#8465;',
			'real'=>'&#8476;',
			'trade'=>'&#8482;',
			'alefsym'=>'&#8501;',
			'larr'=>'&#8592;',
			'uarr'=>'&#8593;',
			'rarr'=>'&#8594;',
			'darr'=>'&#8595;',
			'harr'=>'&#8596;',
			'crarr'=>'&#8629;',
			'lArr'=>'&#8656;',
			'uArr'=>'&#8657;',
			'rArr'=>'&#8658;',
			'dArr'=>'&#8659;',
			'hArr'=>'&#8660;',
			'forall'=>'&#8704;',
			'part'=>'&#8706;',
			'exist'=>'&#8707;',
			'empty'=>'&#8709;',
			'nabla'=>'&#8711;',
			'isin'=>'&#8712;',
			'notin'=>'&#8713;',
			'ni'=>'&#8715;',
			'prod'=>'&#8719;',
			'sum'=>'&#8721;',
			'minus'=>'&#8722;',
			'lowast'=>'&#8727;',
			'radic'=>'&#8730;',
			'prop'=>'&#8733;',
			'infin'=>'&#8734;',
			'ang'=>'&#8736;',
			'and'=>'&#8743;',
			'or'=>'&#8744;',
			'cap'=>'&#8745;',
			'cup'=>'&#8746;',
			'int'=>'&#8747;',
			'there4'=>'&#8756;',
			'sim'=>'&#8764;',
			'cong'=>'&#8773;',
			'asymp'=>'&#8776;',
			'ne'=>'&#8800;',
			'equiv'=>'&#8801;',
			'le'=>'&#8804;',
			'ge'=>'&#8805;',
			'sub'=>'&#8834;',
			'sup'=>'&#8835;',
			'nsub'=>'&#8836;',
			'sube'=>'&#8838;',
			'supe'=>'&#8839;',
			'oplus'=>'&#8853;',
			'otimes'=>'&#8855;',
			'perp'=>'&#8869;',
			'sdot'=>'&#8901;',
			'lceil'=>'&#8968;',
			'rceil'=>'&#8969;',
			'lfloor'=>'&#8970;',
			'rfloor'=>'&#8971;',
			'lang'=>'&#9001;',
			'rang'=>'&#9002;',
			'loz'=>'&#9674;',
			'spades'=>'&#9824;',
			'clubs'=>'&#9827;',
			'hearts'=>'&#9829;',
			'diams'=>'&#9830;'
		);

		/**
		 * Genereate a Form object using xml mark-up.
		 *
		 * @param string $form_ml XML form mark-up
		 * @param boolean $doValidate (Obsolete)Ignored.
		 * @param array $entities A list of enities to convert to values ($entity_name => $value)
		 */
		public function __construct($form_ml, $doValidate=true, $entities=array())
		{
			global $SMITH_SYSTEM_DEF;
			
			$this->lang = new CLocalization($SMITH_SYSTEM_DEF['lang'], 'CFormValidator.php');
			$this->_email = CSettings::$SYSTEM_MAIL_VALUES['tech'];
			$this->_entities = array_merge($this->_entities, $entities);
			$this->_form_ml = trim($form_ml);
			$this->_convertEntities();

			// Extract the validation rules
			$extractor = new FormRuleExtractor($this->_form_ml);
			if(!$extractor->run()) $this->_sendParserError($extractor->parse_error_string, $extractor->parse_error_line_num, $extractor->parse_error_byte_num);
			$this->_elements = $extractor->elements;
			foreach($extractor->messages as $element => $messages)
			{
				if(!isset($this->_elements[$element])) $this->_elements[$element] = array();
				$this->_elements[$element]['errors'] = $messages['error'];
				if(isset($messages['info'])) $this->_elements[$element]['info'] = $messages['info'];
			}
		}

		public function __get($name)
		{
			switch($name)
			{
				case 'rules':
					$rules = array();
					foreach($this->_elements as $element=>$data)
					{
						if(isset($data['rules']))
						{
							$rules[$element] = array();
							foreach($data['rules'] as $rule_name => $rule)
							{
								if(isset($data['params'][$rule_name])) $rule['params'] = $data['params'][$rule_name];
								$rule['name'] = $rule_name;
								$rules[$element][] = $rule;
							}
						}
					}
					return $rules;
				
				case 'data':
					return $this->_elements;
				
				default:
					return;
			}

		}

		public function validate()
		{
			if(!is_array($_REQUEST) && !is_array($_FILES))
			{
				return false;
			}

			//$this->_errors = array();
			foreach ($this->_elements as $el_name => $element)
			{
				if(!isset($element['rules'])) continue;
				foreach($element['rules'] as $rule_name => $rule)
				{
					// Check if mandatory field has not been filled
					if(isset($rule['mandatory']) && $rule['mandatory'] && (!isset($_FILES[$el_name]) && (!isset($_REQUEST[$el_name]) || trim($_REQUEST[$el_name]) == '')))
					{
						$this->setError($el_name, $rule_name);
						continue;
					}

					if(!isset($_REQUEST[$el_name]) && !isset($_FILES[$el_name]))
					{
						// Field not set, and it's not mandatory
						continue;
					}

					if(isset($_REQUEST[$el_name])) $value = $_REQUEST[$el_name];
					else $value = $_FILES[$el_name];

					// If a regular expression is specified, check using that
					if(isset($rule['regexp']) && !preg_match($rule['regexp'], trim($value)))
					{
						$this->setError($el_name, $rule_name);
						continue;
					}

					// If there is a mustmatch rule, check using that
					if(isset($rule['mustmatch']) && $value != $_REQUEST[$rule['mustmatch']])
					{
						$this->setError($el_name, $rule_name);
						continue;
					}

					// If there is a eitheror rule, check using that
					if(isset($rule['eitheror']) && (!trim($value) && !trim($_REQUEST[$rule['eitheror']])))
					{
						$this->setError($el_name, $rule_name);
						continue;
					}

					// If there is a maxvalue rule, check using that
					if(isset($rule['maxvalue']) && intval($value) > intval($rule['maxvalue']))
					{
						$this->setError($el_name, $rule_name);
						continue;
					}

					// If there is a maxlength rule, check using that
					if(isset($rule['maxlength']))
					{
						if(mb_detect_encoding($value) == 'UTF-8' && mb_strlen($value, 'utf-8') > $rule['maxlength'])
						{
							$this->setError($el_name, $rule_name);
							continue;
						}
						else if(mb_detect_encoding($value) != 'UTF-8' && strlen($value) > $rule['maxlength'])
						{
							$this->setError($el_name, $rule_name);
							continue;
						}
					}

					// If there is a calback rule, run that function
					if(isset($rule['callback']))
					{
						$callback = $rule['callback'];
						if(substr($callback, 0, 5) == 'this:')
						{
							$method = substr($callback, 5);
							if(!CFormRules::$method($value, $element['params'][$rule_name]))
							{
								$this->setError($el_name, $rule_name);
								continue;
							}
						}
						else
						{
							// It's just a normal function
							if(!call_user_func($callback, $value, $element['params'][$rule_name]))
							{
								$this->setError($el_name, $rule_name);
								continue;
							}
						}
					}
				}
			}

			if(count($this->_errors)) $this->setError('form', 'default');
			// All rules should now have been processed
			return $this->isValid();
		}

		public function display()
		{
			echo $this->form();
		}

		public function form()
		{
			// Returns the XHTML for the form, after processing
			$this->_output = null;
			$this->_printed_errors = array();
			$this->_parser = xml_parser_create('UTF-8');

			xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, false);
			xml_set_object($this->_parser, $this);
			xml_set_element_handler($this->_parser, 'tagOpenHandler', 'tagCloseHandler');
			xml_set_character_data_handler($this->_parser, 'cdataHandler');

			if(!xml_parse($this->_parser, $this->_form_ml))
			{
				$this->_sendParserError(xml_error_string(xml_get_error_code($this->_parser)), xml_get_current_line_number($this->_parser), xml_get_current_byte_index($this->_parser));
			}
			
			xml_parser_free($this->_parser);

			if(!$this->_output) $this->_output = CXML::startTag('div');
			return $this->_output->finalise()->toString();
		}

		/**
		 * Checks if any errors were detected during validation without performing validation again.
		 *
		 * Note: must call{@link CFormValidator::validate()} before running this function or you will always get false.
		 *
		 * @return boolean
		 */
		public function isValid()
		{
			return (count($this->_errors)) ? false : true;
		}

		private function tagOpenHandler($parser, $tag, $attr)
		{
			if(!$this->_output)
			{
				$this->_output = new CXML($tag, $attr, array(), CXML::OUTPUT_PRETTY);
				return;
			}
			
			if(!$this->_copy)
			{
				return;
			}

			// Kill unrequired attributes
			$killattrs = array('mandatory', 'validate', 'regexp', 'callback', 'mustmatch', 'eitheror', 'maxvalue', 'maxlength', 'errormsg', 'params');
			foreach ($killattrs as $a) unset($attr[$a]);

			switch($tag)
			{
				case 'info':
					if(isset($attr['for']))
					{
						if($attr['for'] == 'form')
						{
							if(isset($this->_info[$attr['for']])) $this->_output->tag('div', array('class'=>'form_feedback'))->raw($this->_elements[$attr['for']]['info']['default'])->closeTag(false);
						}
						else
						{
							$output_classes = array('feedback_list');
							if(!isset($this->_info[$attr['for']]) || count($this->_info[$attr['for']]) == 0) $output_classes[] = 'hidden';
							$this->_output->tag('ul', array('class'=>implode(' ', $output_classes), 'id'=>'feedback_list_' . $attr['for']));
							foreach($this->_elements[$attr['for']]['info'] as $rule_name => $info)
							{
								$el_attr = array('id'=>'feedback_item_' . $attr['for'] . '_' . $rule_name);
								if(!isset($this->_info[$attr['for']]) || array_search($rule_name, $this->_info[$attr['for']]) === false) $el_attr['class'] ='hidden';
								$this->_output->tag('li', $el_attr)->raw($info)->close();
							}
							$this->_output->close();
						}
					}
					$this->_copy = false;
					return;

				case 'error':
					if(isset($attr['for']) && array_search($attr['for'], $this->_printed_errors) === false && count($this->_elements[$attr['for']]['errors']))
					{
						if($attr['for'] == 'form')
						{
							$output_classes = array('error', 'form_error');
							if(!isset($this->_errors[$attr['for']])) $output_classes[] = 'hidden';
							$this->_output->tag('div', array('class'=>implode(' ', $output_classes)))->raw($this->_elements[$attr['for']]['errors']['default'])->closeTag(false);
						}
						else
						{
							$output_classes = array('list_of_error');
							if(!isset($this->_errors[$attr['for']]) || count($this->_errors[$attr['for']]) == 0) $output_classes[] = 'hidden';
							$this->_output->tag('ul', array('class'=>implode(' ', $output_classes), 'id'=>'list_of_error_' . $attr['for']));
							foreach($this->_elements[$attr['for']]['errors'] as $rule_name => $error)
							{
								$el_attr = array('id'=>'error_item_' . $attr['for'] . '_' . $rule_name, 'class'=>'hidden');
								if(isset($this->_errors[$attr['for']]) && in_array($rule_name, $this->_errors[$attr['for']]))
									unset($el_attr['class']); //show error
								if(isset($this->_errors[$attr['for']]) && $rule_name == 'default' && isset($this->_errors[$attr['for']]) && count(array_diff($this->_errors[$attr['for']], array_keys($this->_elements[$attr['for']]['errors']))))
									unset($el_attr['class']); //show error
								$this->_output->tag('li', $el_attr)->raw($error)->close();
							}
							$this->_output->close();
						}
						$this->_printed_errors[] = $attr['for'];
					}
					$this->_copy = false;
					return;

				case 'infomsg':
					
				case 'errormsg':
					$this->_copy = false;
					return;

				case 'errorlist':
					if(count($this->_errors))
					{
						$titles = array();
						foreach($this->_elements as $element_name => $element)
						{
							$titles[$element_name] = $element['title'];
						}
						asort($titles);

						$this->_output->tag('ul', array('class'=>'form_errorlist'));
						foreach($titles as $field_name => $title)
						{
							if(isset($this->_errors[$field_name]))
							{
								$this->_output->tag('li')->tag('h4')->text($title)->close()->tag('ul');
								foreach($this->_errors[$field_name] as $rule_name)
								{
									if(isset($this->_elements[$field_name]['errors'][$rule_name]))
										$this->_output->tag('li')->raw($this->_elements[$field_name]['errors'][$rule_name])->close();
								}
								$this->_output->close()->close();
							}
						}
						$this->_output->close();
					}
					$this->_copy = false;
					return;

				case 'input':
					// Add the value attribute, if redisplaying
					if(isset($attr['name']) && isset($_REQUEST[$attr['name']]))
					{
						// if password, still parse the value (but can be set to null, if we want to hide password)
						if($attr['type'] == 'password') $attr['value'] = stripslashes2($_REQUEST[$attr['name']]);
						// if radio, dont mess with the value
						else if($attr['type'] == 'radio') $attr['value'] = $attr['value'];
						// else keep the value as it's the value the user typed in
						else $attr['value'] = stripslashes2($_REQUEST[$attr['name']]);
					}
					
					// Add an error class if an error occured
					if(isset($attr['name']) && isset($this->_errors[$attr['name']]))
					{
						$attr['class'] = isset($attr['class']) ? $attr['class'].' '.$this->error_class : $this->error_class;
					}

					if(isset($_REQUEST[$attr['name']]) && ($attr['type'] == 'checkbox' || ($attr['type'] == 'radio' && $attr['value'] == $_REQUEST[$attr['name']]))) $attr['checked'] = 'checked';
					$this->_output->tag($tag, $attr);
					return;
					
				case 'select':
					// a select box isnt interesting, save the name for the following option fields
					$this->_parent_select = $attr['name'];
					// Add an error class if an error occured
					if(isset($attr['name']) && isset($this->_errors[$attr['name']]))
					{
						$attr['class'] = isset($attr['class']) ? $attr['class'].' '.$this->error_class : $this->error_class;
					}
					break;

				case 'option':
					// echo 'I belong to '.$this->_parent_select."<br>";
					// if multiple select, [] will be appended to the name of the field, so remove these before check if value exist
					if(isset($_REQUEST[substr($this->_parent_select,0,-2)]) && is_array($_REQUEST[substr($this->_parent_select,0,-2)]) && in_array($attr['value'], $_REQUEST[substr($this->_parent_select,0,-2)])){
						$attr['selected'] = 'selected';
					} else if(isset($_REQUEST[$this->_parent_select]) && $attr['value'] == $_REQUEST[$this->_parent_select]){
						$attr['selected'] = 'selected';
					}
					$this->_output->tag($tag, $attr);
					return;

				case 'textarea':
					$this->_in_text_area = $attr['name'];
					// Add an error class if an error occured
					if(isset($attr['name']) && isset($this->_errors[$attr['name']]))
					{
						$attr['class'] = isset($attr['class']) ? $attr['class'].' '.$this->error_class : $this->error_class;
					}
			}

			// Add tag to the output
			$this->_output->tag($tag, $attr);
		}

		private function cdataHandler($parser, $data)
		{
			if($this->_copy) $this->_output->text($data);
		}

		private function tagCloseHandler($parser, $tag)
		{
			switch($tag)
			{
				case 'error':
				
				case 'errormsg':
				
				case 'errorlist':
				
				case 'info':
				
				case 'infomsg':
					$this->_copy = true;
					break;
				
				case 'textarea':
					if(isset($_REQUEST[$this->_in_text_area])) $this->_output->text(stripslashes2($_REQUEST[$this->_in_text_area]));
					$this->_output->closeTag(false);
					$this->_in_text_area = false;
					break;
				
				case 'iframe':
				
				case 'div':
					if($this->_copy) $this->_output->closeTag(false);
					break;
				
				default:
					if($this->_copy) $this->_output->close();
			}
		}

		/**
		 * Sets Generated Error
		 *
		 * @param string $element_name
		 * @param string $rule_name
		 * @param string $data XML String. If provided, will overwrite whatever was set in the Form.
		 */
		public function setError($element_name, $rule_name, $data=null)
		{
			if(!is_null($data)) $this->_elements[$element_name]['errors'][$rule_name] = $data;

			if(!isset($this->_errors[$element_name])) $this->_errors[$element_name] = array();
			$this->_errors[$element_name][] = $rule_name;
		}

		/**
		 * Sets Generated Info Message
		 *
		 * @param string $element_name
		 * @param string $rule_name
		 * @param string $data XML String. If provided, will overwrite whatever was set in the From.
		 */
		public function setInfo($element_name, $rule_name, $data=null)
		{
			if(!is_null($data)) $this->_elements[$element_name]['info'][$rule_name] = $data;

			if(!isset($this->_info[$element_name])) $this->_info[$element_name] = array();
			$this->_info[$element_name][] = $rule_name;
		}

		/**
		 * Get a list of errors that have occured
		 *
		 * @return array
		 */
		public function getErrors()
		{
			return $this->_errors;
		}

		/**
	     * Count the number of bytes of a given string.
	     * Input string is expected to be ASCII or UTF-8 encoded.
	     * Warning: the function doesn't return the number of chars
	     * in the string, but the number of bytes.
	     *
	     * @param string $str The string to compute number of bytes
	     *
	     * @return The length in bytes of the given string.
	     */
	    private function _strBytes($str)
	    {
	      // STRINGS ARE EXPECTED TO BE IN ASCII OR UTF-8 FORMAT
	      $strlen_var = strlen($str);
	      $d = 0;

	     /*
	      * Iterate over every character in the string,
	      * escaping with a slash or encoding to UTF-8 where necessary
	      */
	      for ($c = 0; $c < $strlen_var; ++$c)
	      {
	          $ord_var_c = ord($str{$d});
	          
	          switch(true)
	          {
	              case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
	                  // characters U-00000000 - U-0000007F (same as ASCII)
	                  $d++;
	                  break;
	              
	              case (($ord_var_c & 0xE0) == 0xC0):
	                  // characters U-00000080 - U-000007FF, mask 110XXXXX
	                  // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
	                  $d+=2;
	                  break;
	              
	              case (($ord_var_c & 0xF0) == 0xE0):
	                  // characters U-00000800 - U-0000FFFF, mask 1110XXXX
	                  // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
	                  $d+=3;
	                  break;
	              
	              case (($ord_var_c & 0xF8) == 0xF0):
	                  // characters U-00010000 - U-001FFFFF, mask 11110XXX
	                  // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
	                  $d+=4;
	                  break;
	              
	              case (($ord_var_c & 0xFC) == 0xF8):
	                  // characters U-00200000 - U-03FFFFFF, mask 111110XX
	                  // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
	                  $d+=5;
	                  break;
	              
	              case (($ord_var_c & 0xFE) == 0xFC):
	                  // characters U-04000000 - U-7FFFFFFF, mask 1111110X
	                  // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
	                  $d+=6;
	                  break;
	              default:
	                $d++;
	          }
	      }

	      return $d;
	    }

		private function _convertEntities()
		{
			$entities = array();
			$values = array();
			while($entity = each($this->_entities))
			{
				$entities[] = '&' . $entity[0] . ';';
				$values[] = $entity[1];
			}
			$count_bytes = $this->_strBytes($this->_form_ml);
			$memory_limit = ini_get('memory_limit');
			$byte_type = strtolower(str_replace(range(0,9),'',$memory_limit));
			$memory_limit = intval($memory_limit);
			if($byte_type === 'm'){ $memory_limit *= 1048576; }
			if($byte_type === 'k'){ $memory_limit *= 1024; }
			if($byte_type === 'g'){ $memory_limit *= 1073741824; }
			$memory_limit = $memory_limit + $count_bytes;
			$memory_limit = round($memory_limit / 1024);
			ini_set('memory_limit', $memory_limit.'K'); // B not supported in PHP

			$this->_form_ml = str_replace($entities, $values, $this->_form_ml);
		}

		private function _sendParserError($error_string, $error_line_num, $error_byte_num)
		{
			global $SMITH_SYSTEM_DEF;
			
			$keep = array(
				'lang',
				'user_id',
				'staff_id',
				'REQUEST_URI',
				'HTTP_HOST',
				'HTTP_USER_AGENT',
				'HTTP_REFERER',
				'SCRIPT_FILENAME'
			);

			$vars = array_intersect_key(array_merge($SMITH_SYSTEM_DEF, $_SERVER), array_flip($keep));
			ob_start();
			var_dump($vars);
			$vars = ob_get_contents();
			ob_end_clean();

			$text = '';
			foreach(explode("\n", $this->_form_ml) as $line_num=>$line)
			{
				if($line_num > $error_line_num - 10 && $line_num < $error_line_num + 10)
					$text .= $line_num+1 . ":\t" . $line . "\n";
			}
			
			$tmpl_vars = array(
				'debug' => $vars,
				'xml' => $text,
				'error' => sprintf('%s on line %d', $error_string, $error_line_num)
			);
			
			$plain = CMail::prepareTemplate('form_parsing_error_en', $tmpl_vars, 0, 'no');
			CMail::send(
				array(CSettings::$SYSTEM_MAIL_VALUES['support'], 'Form Validator Engine'), $this->_email
				, $this->_email_subject, $plain
			);

			//replace form contents with error message.
			if(!$this->_output) $this->_output = CXML::startTag('div');
			if(!$this->lang)
			{
				$this->_output->tag('div', array('class' => 'error'))->tag('h4')
				->text('Internal Error - Form could not be loaded')->close()
				->tag('p')->text('COCKPIT technical team has been informed, please try again in 10 minutes. ')
				->tag('a', array('href' => 'javascript:history.go(-1);'))
				->text('Return to previous page')->close()->close()->close();
			}
			
			else
			{
				$this->_output->tag('div', array('class'=>'error'))->tag('h4')
				->text($this->lang->get('internal form error'))
				->close()->tag('p')->text($this->lang->get('it informed'))
				->tag('a', array('href'=>'javascript:history.go(-1);'))
				->text($this->lang->get('return previous page'))->close()->close()->close();
			}
		}

	} // End CFormValidator Class


	class FormRuleExtractor
	{
		// Extracts the validation rules from the formML.
		// Implementation note: the 'validate' attribute is a convenience, it is converted in
		// to either a regular expression rule or a callback
		private $_parse_error_string;
		private $_parse_error_line_num;
		private $_parse_error_byte_num;

		private $_elements = array();
		private $_msg = array();

		// Following variables used during XML parsing
		private $_parser;
		private $_msg_name;
		private $_msg_value;
		private $_collect_msg = false;

		public function __construct($form_ml)
		{
			$this->_parser = xml_parser_create('UTF-8');

			// Set XML parser to take the case of tags in to account
			xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, false);

			// Set XML parser callback functions
			xml_set_object($this->_parser, $this);
			xml_set_element_handler($this->_parser, 'tagOpenHandler', 'tagCloseHandler');
			xml_set_character_data_handler($this->_parser, 'cdataHandler');

			$this->_form_ml = $form_ml;
		}

		public function __get($name)
		{
			switch($name)
			{
				case 'elements':
					return $this->_elements;
					
				case 'messages':
					return $this->_msg;
			}
		}

		public function run()
		{
			if(!xml_parse($this->_parser, $this->_form_ml))
			{
				$this->_parse_error_string = xml_error_string(xml_get_error_code($this->_parser));
				$this->_parse_error_line_num = xml_get_current_line_number($this->_parser);
				$this->_parse_error_byte_num = xml_get_current_byte_index($this->_parser);
			}
			xml_parser_free($this->_parser);
			if($this->_parse_error_string) return false;
			return true;
		}

		public function tagOpenHandler($parser, $tag, $attr)
		{
			// First, the stuff to deal with the msg tag and contents
			if($tag == 'infomsg' || $tag == 'errormsg')
			{
				if(isset($attr['for']) && isset($attr['name']))
					$this->_msg_name = array($attr['for'],$attr['name']);
				else return;

				$this->_msg_value = '';
				$this->_collect_msg = true;
				return;
			}

			// Default error
			if($tag == 'error' || $tag == 'info')
			{
				if(isset($attr['for']) && !isset($this->_msg[$attr['for']][$tag]['default']))
					$this->_msg_name = array($attr['for'], 'default');
				else return;

				$this->_msg_value = '';
				$this->_collect_msg = true;
				return;
			}

			if($this->_collect_msg)
			{
				$this->_msg_value .= '<'.$tag.$this->_makeAttr($attr);
				if(in_array($tag, array('br', 'img')))
				{
					$this->_msg_value .= ' />';
				}
				else
				{
					$this->_msg_value .= '>';
				}
			}

			// Now the stuff to deal with everything else
			if(!in_array($tag, array('input', 'select', 'textarea'))) return;

			// Skip submit and reset fields
			if(isset($attr['type']) && in_array($attr['type'], array('submit', 'reset')))
			{
				return;
			}

			$element = array('rules'=>array());
			if(isset($attr['type'])) $element['type'] = $attr['type'];
			else $element['type'] = $tag;

			$element['title'] = (isset($attr['title'])) ? $attr['title'] : $attr['id'];

			$name = $attr['name'];
			if(substr($name,-2) == '[]') $name = substr($name,0,-2);

			// mandatory="yes"
			if(isset($attr['mandatory']) && $attr['mandatory'] == 'yes')
			{
				$element['rules']['mandatory'] = array('mandatory' => true);
			}

			//Get image size and select number
			if(isset($attr['params'])){
				if(substr($attr['params'],0,1) == '{') $element['params'] = json_decode($attr['params'],true);
				else $element['params'] = array('default'=>$attr['params']);
			}

			// validate="something"
			if(isset($attr['validate']))
			{
				if(substr($attr['validate'], 0, 1) == '{') $validate = json_decode($attr['validate'], true);
				else $validate = array('default'=>$attr['validate']);

				foreach($validate as $k=>$v)
				{
					if(!isset($element['rules'][$k])) $element['rules'][$k] = array();
					
					switch($v)
					{
						case 'checkFilledIn':
							$element['rules'][$k]['mandatory'] = true;
							break;

						case 'checkAlpha':
							$element['rules'][$k]['regexp'] = '|^[a-zA-Z ]*$|';
							break;

						case 'checkAlphaNumeric':
							if(isset($element['params'][$k])) $element['rules'][$k]['regexp'] = '|^[a-zA-Z0-9]{'.$element['params'][$k].'}$|';
							else $element['rules'][$k]['regexp'] = '|^[a-zA-Z0-9]*$|';
							break;

						case 'checkAlphaAtDot':
							$element['rules'][$k]['callback'] = 'this:checkAlphaAtDot';
							break;
						
						case 'checkInt':
							$element['rules'][$k]['callback'] = 'this:checkInt';
							break;
							
						case 'checkFloat':
							$element['rules'][$k]['callback'] = 'this:checkFloat';
							break;
								
						case 'checkUnixName':
							$element['rules'][$k]['callback'] = 'this:checkUnixName';
							break;

						case 'checkNumeric':
							$element['rules'][$k]['callback'] = 'this:checkNumeric';
							break;

						case 'checkDate':
							$element['rules'][$k]['callback'] = 'this:checkDate';
							break;

						case 'checkDateMandatory':
							$element['rules'][$k]['callback'] = 'this:checkDateMandatory';
							break;

						case 'checkEmail':
							$element['rules'][$k]['callback'] = 'this:checkEmail';
							break;
							
						case 'checkURL':
							$element['rules'][$k]['callback'] = 'this:checkURL';
							break;

						case 'checkUserPrimaryEmail':
							$element['rules'][$k]['callback'] = 'this:checkUserPrimaryEmail';
							break;

						case 'checkImage':
							$element['rules'][$k]['callback'] = 'this:checkImage';
							break;

						case 'checkRadioYes':
							$element['rules'][$k]['callback'] = 'this:checkRadioYes';
							break;

						case 'checkFormTypeYes':
							$element['rules'][$k]['callback'] = 'this:checkFormTypeYes';
							break;
							
						case 'checkSelect':
							$element['rules'][$k]['callback'] = 'this:checkSelect';
							break;

						case 'checkUniqueUser':
							$element['rules'][$k]['callback'] = 'this:checkUniqueUser';
							break;

						case 'checkChecked':
							$element['rules'][$k]['mandatory'] = true;
							break;

						case 'checkCaptcha':
							$element['rules'][$k]['callback'] = 'this:checkCaptcha';
							break;

						case 'checkUserStatus':
							$element['rules'][$k]['callback'] = 'this:checkUserStatus';
							break;
							
						case 'checkHttpMethod':
							$element['rules'][$k]['callback'] = 'this:checkHttpMethod';
							break;
						
						case 'checkHttpContentType':
							$element['rules'][$k]['callback'] = 'this:checkHttpContentType';
							break;
						
						case 'checkHttpAccept':
							$element['rules'][$k]['callback'] = 'this:checkHttpAccept';
							break;
						
						case 'checkHttpCharset':
							$element['rules'][$k]['callback'] = 'this:checkHttpCharset';
							break;
						
						case 'checkAccessLimit':
							$element['rules'][$k]['callback'] = 'this:checkAccessLimit';
							break;
							
						case 'checkResponseTimeoutLimit':
							$element['rules'][$k]['callback'] = 'this:checkResponseTimeoutLimit';
							break;
						
						case 'checkHttpAuthType':
							$element['rules'][$k]['callback'] = 'this:checkHttpAuthType';
							break;
								
						case 'checkHttpPlotExists':
							$element['rules'][$k]['callback'] = 'this:checkHttpPlotExists';
							break;
							
						case 'checkHttpPlotAvailable':
							$element['rules'][$k]['callback'] = 'this:checkHttpPlotAvailable';
							break;
							
						case 'checkPlotStatusAvailable':
							$element['rules'][$k]['callback'] = 'this:checkPlotStatusAvailable';
							break;
						
						case 'checkHttpDLPlotExists':
							$element['rules'][$k]['callback'] = 'this:checkHttpDLPlotExists';
							break;
							
						case 'checkHttpDLPlotAvailable':
							$element['rules'][$k]['callback'] = 'this:checkHttpDLPlotAvailable';
							break;
						
						case 'checkHttpULPlotExists':
							$element['rules'][$k]['callback'] = 'this:checkHttpULPlotExists';
							break;
						
						case 'checkHttpULPlotAvailable':
							$element['rules'][$k]['callback'] = 'this:checkHttpULPlotAvailable';
							break;
						
						case 'checkUploadFileSize':
							$element['rules'][$k]['callback'] = 'this:checkUploadFileSize';
							break;
							
						case 'checkZeroFileSize':
							$element['rules'][$k]['callback'] = 'this:checkZeroFileSize';
							break;
							
						case 'checkUploadFileType':
							$element['rules'][$k]['callback'] = 'this:checkUploadFileType';
							break;
						
						case 'checkPostQueryDataForHttpUpload':
							$element['rules'][$k]['callback'] = 'this:checkPostQueryDataForHttpUpload';
							break;
						
						case 'checkParseUrl':
							$element['rules'][$k]['callback'] = 'this:checkParseUrl';
							break;

						case 'checkDomainAndIP':
							$element['rules'][$k]['callback'] = 'this:checkDomainAndIP';
							break;
						
						case 'checkTCPControlBits':
							$element['rules'][$k]['callback'] = 'this:checkTCPControlBits';
							break;
						
						case 'checkPort':
							$element['rules'][$k]['callback'] = 'this:checkPort';
							break;
					}
				}
			}

			foreach(array('callback', 'regexp', 'mustmatch', 'eitheror', 'maxvalue', 'maxlength') as $attrib)
			{
				if(isset($attr[$attrib]))
				{
					if(substr($attr[$attrib],0,1) == '{') $callback = json_decode($attr[$attrib],true);
					else $callback = array('default'=>$attr[$attrib]);
					foreach($callback as $k=>$v)
					{
						if(!isset($element['rules'][$k])) $element['rules'][$k] = array();
						$element['rules'][$k][$attrib] = $v;
					}
				}
			}

			// Save the elements to $this->_elements
			$this->_elements[$name] = $element;
		}

		public function cdataHandler($parser, $data)
		{
			if($this->_collect_msg) $this->_msg_value .= htmlspecialchars($data);
		}

		public function tagCloseHandler($parser, $tag)
		{
			switch($tag)
			{
				case 'errormsg':

				case 'infomsg':
					$tag = substr($tag, 0, -3);
				case 'error':
				
				case 'info':
					if(!isset($this->_msg[$this->_msg_name[0]])) $this->_msg[$this->_msg_name[0]] = array();
					if(!isset($this->_msg[$this->_msg_name[0]][$tag])) $this->_msg[$this->_msg_name[0]][$tag] = array();
					$this->_msg[$this->_msg_name[0]][$tag][$this->_msg_name[1]] = $this->_msg_value;
					$this->_collect_msg = false;
					break;
				
				default:
					if($this->_collect_msg)
					{
						if(in_array($tag, array('br', 'img'))) $this->_msg_value .= '<'.$tag.'/>';
						else $this->_msg_value .= '</'.$tag.'>';
					}
			}
		}

		public function _makeAttr($attr)
		{
			$html = ' ';
			foreach ($attr as $name => $value) $html .= $name.'="'.htmlspecialchars($value).'" ';
			return substr($html, 0, -1); // Remove trailing space
		}
	} // End FormRuleExtractor Class