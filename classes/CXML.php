<?php
/**
 * OO XML Serialiser
 *
 */
class CXML{
	const OUTPUT_PRETTY = 2;
	const OUTPUT_DIRECT = 4;

	public $indent = "\t";

	private $_output;
	private $_ns;
	private $_stack;
	private $_attr;
	private $_flags;
	private $_global_flags;

	private $_flag_tag_writen = true;
	private $_flag_in_text = false;
	private $_flag_is_finalised = false;

	/**
	 * Serial XML writer.
	 *
	 * Writes xml data to a buffer stream or directly to the output stream.
	 *
	 * @param string $tag
	 * @param array $attr
	 * @param array $ns
	 * @param int $flags
	 * @return CXML
	 */
	public function __construct($tag, $attr=array(), $ns=array(), $flags=0) {
		$this->_output = ($flags & CXML::OUTPUT_DIRECT) ? fopen('php://output','rb') : fopen('php://temp', 'rwb');
		$this->_ns = $ns;
		foreach($ns as $ns_name=>$ns_uri) {
			if($ns_name == 'default') $attr['xmlns'] = $ns_uri;
			elseif(!preg_match('/^xml/i', $ns_name)) $attr['xmlns:' . $ns_name] = $ns_uri;
		}
		$this->_flags = array();
		$this->_global_flags = $flags & ( CXML::OUTPUT_PRETTY | CXML::OUTPUT_DIRECT );
		$this->_stack = array();
		$this->openTag($tag, null, $attr, $flags);
		return $this;
	}

	/**
	 * Opens a tag.
	 *
	 * Convinience function. See openTag for full details.
	 *
	 * @param string $tag
	 * @param array $attr
	 * @return CXML Chainable
	 */
	public function tag($tag, $attr=array()) {
		return $this->openTag($tag, null, $attr);
	}

	/**
	 * Closes current tag.
	 *
	 * Convinience function. See closeTag for full details.
	 *
	 * @return CXML Chainable.
	 */
	public function close() {
		return $this->closeTag();
	}

	/**
	 * Opens a tag.
	 *
	 * @param string $tag
	 * @param string $ns Namespace URI
	 * @param array $attr
	 * @param int $flags
	 * @return CXML
	 */
	public function openTag($tag, $ns=null, $attr=array(), $flags=0) {
		if($this->_flag_is_finalised) return $this;
		if(!$this->_flag_tag_writen) $this->_writeTag(false);
		if(!is_null($ns) && in_array($ns, $this->_ns)) $tag = $this->_ns[array_search($ns, $this->_ns)] . ':' . $tag;
		array_push($this->_stack, $tag);
		array_push($this->_flags, $flags);
		$this->_attr = $attr;
		$this->_flag_tag_writen = false;
		return $this;
	}

	/**
	 * Closes current tag
	 *
	 * @param boolean $allow_empty Allow short-form empty tags.
	 * @return CXML
	 */
	public function closeTag($allow_empty = true) {
		if($this->_flag_is_finalised) return $this;
		$empty = (!$this->_flag_tag_writen) ? $allow_empty : false;
		if(!$this->_flag_tag_writen) $this->_writeTag($empty);

		//if tag was not empty
		if(!$empty) {
			if(!$this->_flag_in_text && $this->_global_flags & CXML::OUTPUT_PRETTY) $this->_indent();
			$tag = array_pop($this->_stack);
			fwrite($this->_output, '</' . $tag . '>');
		}
		$this->_flag_in_text = false;
		return $this;
	}

	/**
	 * Encloses given text in CDATA tags.
	 *
	 * @param string $text
	 * @return CXML
	 */
	public function cdata($text) {
		if($this->_flag_is_finalised) return $this;
		if(!$this->_flag_tag_writen) $this->_writeTag(false);
		$this->_flag_in_text = true;
		fwrite($this->_output, '<![CDATA[');
		fwrite($this->_output, $text);
		fwrite($this->_output, ']]>');
		return $this;
	}

	/**
	 * Escape text and adds to document.
	 *
	 * @param string $text
	 * @return CXML
	 */
	public function text($text) {
		if($this->_flag_is_finalised) return $this;
		if(!$this->_flag_tag_writen) $this->_writeTag(false);
		$this->_flag_in_text = true;
		fwrite($this->_output, self::escape($text));
		return $this;
	}

	/**
	 * Write Raw XML string to object
	 *
	 * @param string $raw_xml
	 * @return CXML Chainable
	 */
	public function raw($raw_xml) {
		//validate as xml before doing anything else
		if(!CXML::isValid('<t>' . $raw_xml . '</t>')) return $this;
		if(!$this->_flag_tag_writen) $this->_writeTag(false);
		fwrite($this->_output, $raw_xml);
		return $this;
	}

	/**
	 * Close all remaining tags and set read-only.
	 *
	 * @return CXML
	 */
	public function finalise() {
		while(count($this->_stack)) {
			$this->closeTag();
		}
		$this->_flag_is_finalised = false;
		return $this;
	}

	/**
	 * Dumps document to output stream.
	 *
	 * @param boolean $output_header Add XML Header
	 * @return CXML
	 */
	public function dump($output_header=false) {
		//skip if not buffered
		if($this->_global_flags & CXML::OUTPUT_DIRECT) return $this;

		//write header
		if($output_header)
			echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		rewind($this->_output);

		fpassthru($this->_output);
		return $this;
	}

	/**
	 * Writes document to string.
	 *
	 * @param boolean $output_header Add XML Header
	 * @return string
	 */
	public function toString($output_header=false) {
		//skip if not buffered
		if($this->_global_flags & CXML::OUTPUT_DIRECT) return '';

		//write header
		$output = '';
		if($output_header)
			$output .= '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		rewind($this->_output);

		while($str = fread($this->_output,1024)) {
			$output .= $str;
		}
		return $output;
	}

	/**
	 * Write opening tag.
	 *
	 * @param boolean $empty Write empty tag.
	 */
	private function _writeTag($empty) {
		$attr_str = '';
		$attr = self::escape($this->_attr);
		foreach($attr as $name=>$value) {
			$attr_str .= ' ' . $name . '="' . $value . '"';
		}

		if(!$this->_flag_in_text && $this->_global_flags & CXML::OUTPUT_PRETTY) $this->_indent();
		$tag = ($empty) ? array_pop($this->_stack) : end($this->_stack);
		fwrite($this->_output, '<' . $tag . $attr_str);
		if($empty) fwrite($this->_output,'/>');
		else fwrite($this->_output, '>');
		$this->_flag_tag_writen = true;
	}

	/**
	 * Add indentation.
	 */
	private function _indent() {
		if(ftell($this->_output)) {
			fwrite($this->_output, "\n");
			for($i=1; $i < count($this->_stack); $i++) {
				fwrite($this->_output , $this->indent);
			}
		}
	}

	/**
	 * Creates and returns a new CXML object.
	 *
	 * Simplifies writing single tags.
	 *
	 * @param string $tag
	 * @param array $attr
	 * @param int $flags
	 * @return CXML
	 */
	public static function startTag($tag, $attr=array(), $flags=0) {
		return new self($tag, $attr, array(), $flags);
	}

	/**
	 * Escape variable for use in XML.
	 *
	 * The return type is the same as the input type, but with all values and keys escaped.
	 *
	 * @param mixed $input
	 * @return mixed Escaped variable.
	 */
	public static function escape($input) {
		if(is_array($input)) {
			$res = array();
			foreach($input as $k => $v) {
				$res[htmlspecialchars($k)] = self::escape($v);
			}
		}
		else $res = htmlspecialchars($input);
		return $res;
	}

	/**
	 * Validate XML String
	 *
	 * @param string $xml_string
	 * @return boolean True if XML is valid, False otherwise.
	 */
	public static function isValid($xml_string) {
		$parser = xml_parser_create('UTF-8');

		// Set XML parser to take the case of tags in to account
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);

		// try running the XML parser, return fail on false.
		$valid = (xml_parse($parser, $xml_string)) ? true : false;

		xml_parser_free($parser);
		return $valid;
	}

	/**
	 * Parses a string to make sure it is valid UTF8
	 *
	 * Returns an assoc array with 2 keys:
	 * 	'parsed'	- A valid UTF8 string
	 *	'errors'	- Array of Character positions where an error occurred.
	 * 				Note these are character positions in the UTF8 string, not
	 * 				the input php string or the parsed php string.
	 *
	 * @param string $string
	 * @return array
	 */
	public static function parseUTF8($string) {
		$errors = array();
		$parsed = '';
		$cache = '';
		$expecting = 0;
		$char_count = 0;

		for($i = 0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$ord = ord($char);
			$ones = 0;
			while(($ord >> 7-$ones) & 1) $ones++;
			if($ones == 0) {
				if($expecting != 0) $errors[] = $char_count;
				$expecting = 0;
				$cache = '';
				$parsed .= $char;
				$char_count++;
			}
			elseif($ones == 1) {
				if($expecting > 0) {
					$cache .= $char;
					$expecting--;
					if($expecting == 0) {
						$parsed .= $cache;
						$cache = '';
						$char_count++;
					}
				}
				else {
					$errors[] = $char_count;
					$cache = '';
				}
			}
			elseif($ones > 1 && $ones < 7) {
				if($expecting > 0) $errors[] = $char_count;
				$expecting = $ones-1;
				$cache = $char;
			}
			else {
				$errors[] = $char_count;
				$expecting = 0;
				$cache = '';
			}
		}

		return array('parsed'=>$parsed, 'errors'=>$errors);
	}

	/**
	 * Produces valid XML from pseudo XML.
	 *
	 * Not 100% accurate, but will work most of the time
	 *
	 * @param string $string pseudo XML string
	 * @return string valid XML string
	 */
	public static function cleanPseudoXML($string) {
		$string = preg_replace('/&(?!\#\d{3,4};|lt;|gt;|amp;|rsquo;)/', '&amp;',$string);
		$string = preg_replace('-<(?!(?:/\w+>)|(?:\w+(?:\s+\w+=".*?")*\s*/?>))-', '&lt;', $string);
		$string = preg_replace('.(?<![/a-zA-Z"]|"\s)>.', '&gt;', $string);
		return $string;
	}

	/**
	 * Converts valid HTML entities into valid XML entities
	 *
	 * @param string $string XML string with HTML entities
	 * @return string XML string with valid XML entities
	 */
	public static function HTMLEntities2XML($string) {
		$entity_map = array(
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
		$entities = array();
		$values = array();
		while($entity = each($entity_map)){
			$entities[] = '&' . $entity[0] . ';';
			$values[] = $entity[1];
		}
		return str_replace($entities, $values, $string);
	}
}
?>