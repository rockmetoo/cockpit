<?php

	require_once('CDBQuery.php');
	require_once('CXML.php');

	class DBLocalization extends CDBQuery{

		public static function setLocalizationSource($localization_id) {
			global $db;

			$query_key = preg_replace('/(?<==).*?(&|(?:$))/','',$_SERVER['QUERY_STRING']);

			$url_key = $_SERVER['PHP_SELF'];
			if($query_key) $url_key .= '?' . $query_key;
			$param = array(
				'localization_id' => $localization_id,
				'url_key' => $url_key,
				'url' => $_SERVER['REQUEST_URI']
			);
			$query = "
				INSERT IGNORE INTO site_map (".implode(", ", array_keys($param)).")
				VALUES ('".implode("', '", sanitize(array_values($param), 0))."')
			";
			return $db->queryOther('localization', $query);
		}

		public static function setLocalizationSourceByKeyword($site_id, $keyword) {
			global $db;

			$res = $db->queryOther('localization', $db->quoteInto('SELECT localization_id FROM localization WHERE website_id=%s AND array_key=%s', array($site_id, $keyword)));
			if($foo = $res->fetch_assoc()) return DBLocalization::setLocalizationSource($foo['localization_id']);
			else return false;
		}

	}

?>