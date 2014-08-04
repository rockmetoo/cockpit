<?php

	include_once("class.phpmailer.php");
	include_once('CSettings.php');

	/**
	 * Custom extensions of class.phpmailer.php
	 *
	 * Hack 1 to handle windows on local machines
	 * Line 412
	 * $result = (strtolower(substr(php_uname('s'), 0, 3)) === 'win') ? $this->SmtpSend($header, $body) : $this->SendmailSend($header, $body);
	 *
	 * Hack 2 to multibyte from addresses
	 * Line 732
	 *   else if(mb_detect_encoding($addr[1]) != 'ASCII') {
	 *   	$formatted = mb_encode_mimeheader($this->SecureHeader($addr[1]), $this->CharSet, "B") . ' <' . $this->SecureHeader($addr[0]) . '>';
	 *   }
	 */

	class CMail
	{
		protected static $_mailer_select = 2; //1 for google smtp, 2 for local mail server, etc.

		/* PUBLIC FUNCTIONS */
		/**
		 * Send Email
		 *
		 * @param array|string $sender [email, name] | email
		 * @param array|string $receiver [email, name] | email
		 * @param string $subject
		 * @param string $plain Email text-only content
		 * @param string|boolean $html if non-empty string used as main body with plain as alt body
		 * @param array $cc [email, ..]
		 * @param array $bcc [email, ..]
		 * @param string $attachment file path
		 * @param string $attachment_name
		 * @return boolean|string true on success, the error msg on error.
		 */
		public static function send($sender, $receiver, $subject, $plain, $html = '', $cc = '', $bcc = '')
		{
			global $allowed_doc_types;
			
			$mail = new PHPMailer();
			$sender = (Array)$sender;
			$mail->AddReplyTo($sender[0]);
			$mail->From     = $sender[0];
			if(isset($sender[1]))
			{
				$mail->FromName = mb_convert_encoding($sender[1], 'ISO-2022-JP', 'UTF-8');
			}
			
			if(self::$_mailer_select == 1)
			{
				if(!preg_match('/^.*@'.CSettings::$SYSTEM_DOMAIN_VALUES['preg_gmail'].'$/', $sender[0]))
				{
					$mail->AddCustomHeader('Sender: CockPit <'.CSettings::$SYSTEM_MAIL_VALUES['info'].'>');
				}
			}
			else
			{
				if(!preg_match('/^.*@'.CSettings::$SYSTEM_DOMAIN_VALUES['preg_ec'].'$/', $sender[0]))
				{
					$mail->AddCustomHeader('Sender: CockPit <'.CSettings::$SYSTEM_MAIL_VALUES['tech'].'>');
				}
			}

			if($receiver && is_array($receiver)) $mail->AddAddress($receiver[0], mb_convert_encoding($receiver[1], 'ISO-2022-JP', 'UTF-8'));
			else if($receiver) $mail->AddAddress($receiver);

			$ccs = array();
			if($cc && is_array($cc) && is_array($cc[0])) $ccs = $cc;
			else if($cc && is_array($cc)) $ccs[] = $cc;
			else if($cc)
				foreach(explode(',', $cc) as $email)
					if(trim($email)) $ccs[] = array($email);
			foreach($ccs as $cc_addr) $mail->AddCC($cc_addr[0], mb_convert_encoding($cc_addr[1], 'ISO-2022-JP', 'UTF-8'));

			$bccs = array();
			if($bcc && is_array($bcc) && is_array($bcc[0])) $bccs = $bcc;
			elseif($bcc && is_array($bcc)) $bccs[] = $bcc;
			elseif($bcc)
				foreach(explode(',', $bcc) as $email)
					if(trim($email)) $bccs[] = array($email);
			foreach($bccs as $bcc_addr) $mail->AddBCC($bcc_addr[0], mb_convert_encoding($bcc_addr[1], 'ISO-2022-JP', 'UTF-8'));

			if(self::$_mailer_select == 1)
			{
				$mail->Host = CSettings::$SYSTEM_DOMAIN_VALUES['google_smtp_server'];
				$mail->IsSMTP();
				$mail->Port = CSettings::$SYSTEM_DOMAIN_VALUES['google_smtp_port'];
				$mail->SMTPAuth = true;
				$mail->SMTPSecure = 'ssl';
				$mail->Username = "";
				$mail->Password = "";
				$mail->From = "";
				$mail->AddReplyTo("", "CockPit");
			}
			else
			{
				$mail->Host = CSettings::$SYSTEM_DOMAIN_VALUES["smtp_server"];
				$mail->Mailer = "sendmail";
			}
			
			$mail->Subject = mb_convert_encoding($subject, 'ISO-2022-JP', 'UTF-8');

			//unset html for keitai emails
			if(CMail::checkKeitaiEmail($receiver)) $html = '';
			
			if($html)
			{
				$mail->Body = mb_convert_encoding($html, 'ISO-2022-JP', 'UTF-8');
				$mail->AltBody = mb_convert_encoding($plain, 'ISO-2022-JP', 'UTF-8');
			}
			else
			{
				$mail->Body = mb_convert_encoding($plain, 'ISO-2022-JP', 'UTF-8');
			}

			if(!defined('STOP_MAIL') || !STOP_MAIL)
			{
				if(!$mail->Send())
				{
					return $mail->ErrorInfo;
				}
				else
				{
					return true;
				}
			}
			else
			{
			}
		}

		/**
		 * Prepare Message Body (or part of)
		 *
		 * @param string $template
		 * @param array $values
		 * @param boolean $html
		 * @param string $language
		 * @return string
		 */
		public static function prepareBody($template, $values, $html=1, $language='')
		{
			global $SMITH_SYSTEM_DEF;
			
			//if language is not set default to the language set in $SMITH_SYSTEM_DEF
			if($language == '' || $SMITH_SYSTEM_DEF['lang'] == $language)
			{
				global $lang;
			}
			else
			{
				include_once 'CLocalization.php';
				$lang = new CLocalization($language);
			}
			
			//retrieve template
			$t = '';
			if(is_array($template))
			{
				$sizeof_template = sizeof($template);
				for($i=0; $i<$sizeof_template; $i++)
				{
					if($html) $t .= file_get_contents(CSettings::$BASE_DIRECTORY .'/templates/'. $template[$i] .'_html.txt');
					else $t .= file_get_contents(CSettings::$BASE_DIRECTORY .'/templates/'. $template[$i] .'_plain.txt');
				}
			}
			else
			{
				if($html) $t = file_get_contents(CSettings::$BASE_DIRECTORY . '/templates/' . $template . '_html.txt');
				else $t = file_get_contents(CSettings::$BASE_DIRECTORY . '/templates/' . $template . '_plain.txt');
			}

			// change variables in template
			if(is_array($values))
			{
				foreach($values as $k => $v)
				{
					if(!is_array($v)) $t = preg_replace("/\{\{$k\}\}/ui", unsanitize($v), $t);
				}
			}
			return str_replace('&;', '&', $t);
		}

		/**
		 * Prepare template for sending
		 *
		 * @param string $template
		 * @param array $values
		 * @param bolean $html
		 * @param string $add_header
		 * @param string $language
		 * @param bolean $employer
		 * @return string
		 */
		public static function prepareTemplate($template, $values, $html = 1, $add_header = 'yes', $language = '', $employer = '')
		{
			global $SMITH_SYSTEM_DEF;
				
			//if language is not set default to the language set in SESSION_DEFS
			if(!$language)
			{
				$language = $SMITH_SYSTEM_DEF['lang'];
			}
			
			include_once 'CLocalization.php';
			$lang = new CLocalization($language, 'CMail.php');
		
			$out = array();
			
			if($html && $add_header != 'no')
			{
				
				$out[0] = file_get_contents(
					CSettings::$BASE_DIRECTORY . '/templates/template_header_' . $language . '_html.html'
				);
				
				$out[2] = file_get_contents(
					CSettings::$BASE_DIRECTORY . '/templates/template_footer_' . $language . '_html.html'
				);
			}
			else
			{
				$out[2] = file_get_contents(
					CSettings::$BASE_DIRECTORY . '/templates/template_footer_' . $language . '_plain.txt'
				);
			}
		
			foreach($out as $k=>$v)
			{
				preg_match_all("/\[\[([^\]]+)\]\]/", $v, $matches);
				if(sizeof($matches))
				{
					$total = sizeof($matches[0]);
						
					for($i=0; $i<$total; $i++)
					{
						$out[$k] = str_replace($matches[0][$i], $lang->get(strtolower($matches[1][$i])), $out[$k]);
					}
				}
				
				$out[$k] = strtr($out[$k], array('|lang|' => $language));
			}
		
			//retrieve template
			$t = '';
			if(is_array($template) || strlen($template) < 50)
			{
				if(is_array($template))
				{
					$sizeof_template = sizeof($template);
					for($i=0; $i<$sizeof_template; $i++)
					{
						if($html)
						{
							$t .= file_get_contents(
									CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR
									. $template[$i] . '_html.txt'
							);
						}
						else
						{
							$t .= file_get_contents(
								CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template[$i] . '_plain.txt'
							);
						}
					}
				}
				else if($template)
				{
					if($html)
					{
						$t = file_get_contents(
							CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template . '_html.txt'
						);
					}
					else
					{
						$t = file_get_contents(CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template . '_plain.txt');
					}
				}
			
				//change variables in template
				preg_match_all("/\[\[([^\]]+)\]\]/", $t, $matches);
			
				if(sizeof($matches))
				{
					$sizeof_matches_zero = sizeof($matches[0]);
					for($i=0; $i<$sizeof_matches_zero; $i++)
					{
						$t = str_replace($matches[0][$i], $lang->get(strtolower($matches[1][$i])), $t);
					}
				}
				if(is_array($values))
				{
					foreach($values as $k => $v)
					{
						if(!is_array($v)) $t = preg_replace("/\{\{$k\}\}/ui", unsanitize($v), $t);
					}
				}
			}
			else
			{
				$t = $template; //full of precompiled content
				preg_match_all("/\[\[([^\]]+)\]\]/", $t, $matches);
				
				if(sizeof($matches))
				{
					$sizeof_matches_zero = sizeof($matches[0]);
					for($i=0; $i<$sizeof_matches_zero; $i++)
					{
						$t = str_replace($matches[0][$i], $lang->get(strtolower($matches[1][$i])), $t);
					}
				}
			}
			
			$out[1] = $t;
			//$out = implode('', $out);
			//was printing out of order... 0, 2, 1 (header, footer, body)
			$out = $out[0] . $out[1] . $out[2];
			return str_replace('&;', '&', $out);
		}
					
		private static function encodeAddress($email, $name='')
		{
			if($name) return $name.' <'.$email.'>';
			else return $email;
		}

		public static function checkKeitaiEmail($receiver)
		{
			// List of Japanese keitai email domains
			$keitai_domains = array(
				'0'=>'docomo.ne.jp',
				'1'=>'ezweb.ne.jp',
				'2'=>'softbank.ne.jp',
				'3'=>'i.softbank.jp',
				'4'=>'disney.ne.jp',
				'5'=>'d.vodafone.ne.jp',
				'6'=>'h.vodafone.ne.jp',
				'7'=>'t.vodafone.ne.jp',
				'8'=>'c.vodafone.ne.jp',
				'9'=>'r.vodafone.ne.jp',
				'10'=>'k.vodafone.ne.jp',
				'11'=>'n.vodafone.ne.jp',
				'12'=>'s.vodafone.ne.jp',
				'13'=>'q.vodafone.ne.jp',
				'14'=>'jp-d.ne.jp',
				'15'=>'jp-h.ne.jp',
				'16'=>'jp-t.ne.j',
				'17'=>'jp-c.ne.jp',
				'18'=>'jp-r.ne.jp',
				'19'=>'jp-k.ne.jp',
				'20'=>'jp-n.ne.jp',
				'21'=>'jp-s.ne.jp',
				'22'=>'jp-q.ne.jp',
				'23'=>'ezweb.ne.jp',
				'24'=>'XXX.biz.ezweb.ne.jp',
				'25'=>'ido.ne.jp',
				'26'=>'sky.tkk.ne.jp',
				'27'=>'sky.tkc.ne.jp',
				'28'=>'sky.tu-ka.ne.jp',
				'29'=>'pdx.ne.jp',
				'30'=>'emnet.ne.jp',
				'31'=>'softbank.jp',
			);

			// Get domain
			if($receiver && is_array($receiver))
			{
				list(,$domain) = explode("@",$receiver[0]);
			}
			else if($receiver)
			{
				list(,$domain) = explode("@",$receiver);
			}
			
			if(in_array($domain, $keitai_domains)) return true;
			else return false;
		}
	}
?>