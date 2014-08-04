<?php

	$this->registered_sql = array(
		"select employer" => "SELECT * FROM employer WHERE employer_id=%s",
		"select winner" => "SELECT *, (YEAR(CURDATE())-YEAR(birth_date))-(RIGHT(CURDATE(),5)<RIGHT(birth_date, 5)) AS age FROM winner WHERE winner_id=%s",
		"select project list html" => "SELECT * FROM project_info WHERE employer_id=%s",
		"select unique project " => "SELECT project_info_id FROM project_info WHERE employer_id = %s AND project_name = %s",
		"select employer acl" => "SELECT service_type, allow_no FROM employer_service_acl WHERE employer_id=%s ORDER BY service_type",
	);
